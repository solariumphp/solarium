<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin;

use Solarium\Core\Client\Adapter\ConnectionTimeoutAwareInterface;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Adapter\TimeoutAwareInterface;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Exception\HttpException;

/**
 * NoWaitForResponseRequest plugin.
 *
 * Long-running requests like suggest.buildAll might exceed timeouts.
 * This plugin "tries" to convert the request in a kind of fire-and-forget.
 * Most reliable if using the Curl adapter.
 */
class NoWaitForResponseRequest extends AbstractPlugin
{
    /**
     * Event hook to adjust client settings just before query execution.
     *
     * @param object $event
     *
     * @return self Provides fluent interface
     *
     * @throws \Solarium\Exception\HttpException
     */
    public function preExecuteRequest($event): self
    {
        // We need to accept event proxies or decorators.
        /** @var PreExecuteRequest $event */
        $request = $event->getRequest();
        $queryString = $request->getQueryString();

        if (Request::METHOD_GET === $request->getMethod()) {
            // GET requests usually expect a result. Since the purpose of this
            // plugin is to trigger a long-running command and to not wait for
            // its result, POST is the correct method.
            // Depending on the HTTP configuration, GET requests could be
            // cached. If this plugin is used, someone usually wants to build a
            // dictionary or suggester and caching has to be avoided. Even if
            // Solr accepts GET requests for these tasks, POST is the correct
            // method.
            $charset = $request->getParam('ie') ?? 'utf-8';
            $request->setMethod(Request::METHOD_POST);
            $request->setContentType(Request::CONTENT_TYPE_APPLICATION_X_WWW_FORM_URLENCODED, ['charset' => $charset]);
            $request->setRawData($queryString);
            $request->clearParams();
        }

        $timeout = TimeoutAwareInterface::DEFAULT_TIMEOUT;
        if ($this->client->getAdapter() instanceof TimeoutAwareInterface) {
            $timeout = $this->client->getAdapter()->getTimeout();
            if (($this->client->getAdapter() instanceof ConnectionTimeoutAwareInterface) && ($this->client->getAdapter()->getConnectionTimeout() > 0)) {
                $this->client->getAdapter()->setTimeout($this->client->getAdapter()->getConnectionTimeout() + TimeoutAwareInterface::FAST_TIMEOUT);
            } else {
                $this->client->getAdapter()->setTimeout(TimeoutAwareInterface::FAST_TIMEOUT);
            }
        }

        if ($this->client->getAdapter() instanceof Curl) {
            $this->client->getAdapter()->setOption('return_transfer', false);
        }

        $exception = null;
        $microtime1 = microtime(true);
        try {
            $this->client->getAdapter()->execute($request, $event->getEndpoint());
        } catch (HttpException $e) {
            // We expect to run into a timeout.
            $microtime2 = microtime(true);

            if (($this->client->getAdapter() instanceof Curl) && (CURLE_OPERATION_TIMEDOUT != $e->getCode())) {
                // An unexpected exception occurred.
                $exception = $e;
            } else {
                $time_passed = $microtime2 - $microtime1;
                if (($this->client->getAdapter() instanceof ConnectionTimeoutAwareInterface) && ($time_passed > $this->client->getAdapter()->getConnectionTimeout()) && ($time_passed < ($this->client->getAdapter()->getConnectionTimeout() + TimeoutAwareInterface::FAST_TIMEOUT))) {
                    // A connection timeout occurred, so the POST request has not been sent.
                    $exception = $e;
                }
            }
        } catch (\Exception $exception) {
            // Throw this exception after resetting the adapter.
        }

        if ($this->client->getAdapter() instanceof TimeoutAwareInterface) {
            // Restore the previous timeout.
            $this->client->getAdapter()->setTimeout($timeout);
        }

        if ($this->client->getAdapter() instanceof Curl) {
            $this->client->getAdapter()->setOption('return_transfer', true);
        }

        if ($exception) {
            throw $exception;
        }

        $response = new Response('', ['HTTP/1.0 200 OK']);
        $event->setResponse($response);

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
            // NoWaitForResponseRequest has to act on PRE_EXECUTE_REQUEST before Loadbalancer (priority 0)
            // and after PostBigRequest (priority 10). Set priority to 5.
            $dispatcher->addListener(Events::PRE_EXECUTE_REQUEST, [$this, 'preExecuteRequest'], 5);
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
        }
    }
}
