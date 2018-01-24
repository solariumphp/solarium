<?php

namespace Solarium\Plugin;

use Solarium\Core\Client\Request;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostCreateRequest as PostCreateRequestEvent;
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
    public function setMaxQueryStringLength($value)
    {
        return $this->setOption('maxquerystringlength', $value);
    }

    /**
     * Get maxquerystringlength option.
     *
     * @return int
     */
    public function getMaxQueryStringLength()
    {
        return $this->getOption('maxquerystringlength');
    }

    /**
     * Event hook to adjust client settings just before query execution.
     *
     * @param PostCreateRequestEvent $event
     */
    public function postCreateRequest($event)
    {
        $request = $event->getRequest();
        $queryString = $request->getQueryString();
        if (Request::METHOD_GET == $request->getMethod() &&
            strlen($queryString) > $this->getMaxQueryStringLength()) {
            $request->setMethod(Request::METHOD_POST);
            $request->setRawData($queryString);
            $request->clearParams();
            $request->addHeader('Content-Type: application/x-www-form-urlencoded');
        }
    }

    /**
     * Plugin init function.
     *
     * Register event listeners
     */
    protected function initPluginType()
    {
        $dispatcher = $this->client->getEventDispatcher();
        $dispatcher->addListener(Events::POST_CREATE_REQUEST, [$this, 'postCreateRequest']);
    }
}
