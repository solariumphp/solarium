<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin;

use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Adapter\TimeoutAwareInterface;
use Solarium\Core\Client\Adapter\TimeoutAwareTrait;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Exception\HttpException;

/**
 * NoResponseRequest plugin.
 *
 * Long-running requests like suggest.buildAll might exceed timeouts.
 * This plugin "tries" to convert the request in a kind of fire-and-forget.
 */
class NoResponseRequest extends AbstractPlugin
{
    use TimeoutAwareTrait;

    /**
     * Event hook to adjust client settings just before query execution.
     *
     * @param object $event
     *
     * @return self Provides fluent interface
     */
    public function preExecuteRequest($event): self
    {
        // We need to accept event proxies or decorators.
        /** @var PreExecuteRequest $event */
        $request = $event->getRequest();
        $queryString = $request->getQueryString();

        if (Request::METHOD_GET === $request->getMethod()) {
            $charset = $request->getParam('ie') ?? 'utf-8';

            $request->setMethod(Request::METHOD_POST);
            $request->setContentType(Request::CONTENT_TYPE_APPLICATION_X_WWW_FORM_URLENCODED, ['charset' => $charset]);
            $request->setRawData($queryString);
            $request->clearParams();
        }

        if ($this->client->getAdapter() instanceof TimeoutAwareInterface) {
            $this->setTimeout($this->client->getAdapter()->getTimeout());
            $this->client->getAdapter()->setTimeout(TimeoutAwareInterface::MINIMUM_TIMEOUT);
        }

        if ($this->client->getAdapter() instanceof Curl) {
            $this->client->getAdapter()->setOption('return_transfer', false);
        }

        try {
            $this->client->getAdapter()->execute($request, $event->getEndpoint());
        }
        catch (HttpException $e) {
            // We expect to run into a timeout.
        }

        $response = new Response('', ['HTTP 1.0 200 OK']);
        $event->setResponse($response);

        return $this;
    }

    /**
     * Event hook to adjust client settings after query execution.
     *
     * @param object $event
     *
     * @return self Provides fluent interface
     */
    public function postExecuteRequest($event): self
    {
        if ($this->client->getAdapter() instanceof TimeoutAwareInterface) {
            // Restore the previous timeout.
            $this->client->getAdapter()->setTimeout($this->getTimeout());
        }

        if ($this->client->getAdapter() instanceof Curl) {
            $this->client->getAdapter()->setOption('return_transfer', true);
        }

        return $this;
    }

    /**
     * Plugin init function.
     *
     * Register event listeners.
     */
    protected function initPluginType()
    {
        $dispatcher = $this->client->getEventDispatcher();
        if (is_subclass_of($dispatcher, '\Symfony\Component\EventDispatcher\EventDispatcherInterface')) {
            // NoResponseRequest has to act on PRE_EXECUTE_REQUEST before Loadbalancer (priority 0)
            // and after PostBigRequest (priority 10). Set priority to 5.
            $dispatcher->addListener(Events::PRE_EXECUTE_REQUEST, [$this, 'preExecuteRequest'], 5);
            $dispatcher->addListener(Events::POST_EXECUTE_REQUEST, [$this, 'postExecuteRequest']);
        }
    }

    /**
     * Plugin cleanup function.
     *
     * Unregister event listeners.
     */
    public function deinitPlugin()
    {
        $dispatcher = $this->client->getEventDispatcher();
        if (is_subclass_of($dispatcher, '\Symfony\Component\EventDispatcher\EventDispatcherInterface')) {
            $dispatcher->removeListener(Events::PRE_EXECUTE_REQUEST, [$this, 'preExecuteRequest']);
            $dispatcher->removeListener(Events::POST_EXECUTE_REQUEST, [$this, 'postExecuteRequest']);
        }
    }
}
