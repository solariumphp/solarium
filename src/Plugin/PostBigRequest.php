<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin;

use Solarium\Core\Client\Request;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest;
use Solarium\Core\Plugin\AbstractPlugin;

/**
 * PostBigRequest plugin.
 *
 * If you reach the url/header length limit of your servlet container your queries will fail.
 * You can increase the limit in the servlet container, but if that's not possible this plugin can automatically
 * convert big GET requests into POST requests. A POST request (usually) has a much higher limit.
 *
 * The default maximum querystring length is 1024. This doesn't include the base url or headers.
 * For most servlet setups this limit leaves enough room for that extra data. Adjust the limit if needed.
 */
class PostBigRequest extends AbstractPlugin
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'maxquerystringlength' => 1024,
    ];

    /**
     * Set maxquerystringlength enabled option.
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setMaxQueryStringLength(int $value): self
    {
        $this->setOption('maxquerystringlength', $value);

        return $this;
    }

    /**
     * Get maxquerystringlength option.
     *
     * @return int|null
     */
    public function getMaxQueryStringLength(): ?int
    {
        return $this->getOption('maxquerystringlength');
    }

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

        if (
            Request::METHOD_GET === $request->getMethod()
            && \strlen($queryString) > $this->getMaxQueryStringLength()
        ) {
            $charset = $request->getParam('ie') ?? 'utf-8';

            $request->setMethod(Request::METHOD_POST);
            $request->setContentType(Request::CONTENT_TYPE_APPLICATION_X_WWW_FORM_URLENCODED, ['charset' => $charset]);
            $request->setRawData($queryString);
            $request->clearParams();
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
            // PostBigRequest has to act on PRE_EXECUTE_REQUEST before Loadbalancer (priority 0). Set priority to 10.
            $dispatcher->addListener(Events::PRE_EXECUTE_REQUEST, [$this, 'preExecuteRequest'], 10);
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
