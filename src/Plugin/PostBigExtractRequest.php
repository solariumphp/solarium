<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin;

use Solarium\Core\Client\Adapter\AdapterHelper;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostCreateRequest as PostCreateRequestEvent;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\QueryType\Extract\Query as ExtractQuery;

/**
 * PostBigExtractRequest plugin.
 *
 * If you reach the url/header length limit of your servlet container your queries will fail.
 * You can increase the limit in the servlet container, but if that's not possible this plugin can automatically
 * convert big literals query string into multipart POST parameters. POST parameters (usually) has a much higher limit.
 *
 * The default maximum querystring length is 1024. This doesn't include the base url or headers.
 * For most servlet setups this limit leaves enough room for that extra data. Adjust the limit if needed.
 */
class PostBigExtractRequest extends AbstractPlugin
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
     * @param PostCreateRequestEvent $event
     *
     * @return self Provides fluent interface
     */
    public function postCreateRequest(PostCreateRequestEvent $event): self
    {
        $request = $event->getRequest();
        $queryString = $request->getQueryString();
        $query = $event->getQuery();
        if ($query instanceof ExtractQuery && \strlen($queryString) > $this->getMaxQueryStringLength()) {
            $charset = $request->getParam('ie') ?? 'UTF-8';
            if ($request->getFileUpload()) {
                $body = '';

                foreach ($request->getParams() as $key => $value) {
                    if (is_iterable($value)) {
                        foreach ($value as $arrayVal) {
                            if (\is_string($arrayVal)) {
                                $additionalBodyHeader = "\r\nContent-Type: text/plain;charset={$charset}";
                            } else {
                                $additionalBodyHeader = '';
                            }
                            $body .= "--{$request->getHash()}\r\n";
                            $body .= "Content-Disposition: form-data; name=\"{$key}\"";
                            $body .= $additionalBodyHeader;
                            $body .= "\r\n\r\n";
                            $body .= $arrayVal;
                            $body .= "\r\n";
                        }
                    } else {
                        if (\is_string($value)) {
                            $additionalBodyHeader = "\r\nContent-Type: text/plain;charset={$charset}";
                        } else {
                            $additionalBodyHeader = '';
                        }
                        $body .= "--{$request->getHash()}\r\n";
                        $body .= "Content-Disposition: form-data; name=\"{$key}\"";
                        $body .= $additionalBodyHeader;
                        $body .= "\r\n\r\n";
                        $body .= $value;
                        $body .= "\r\n";
                    }
                }

                $body .= AdapterHelper::buildUploadBodyFromRequest($request); //must be the last automatically include closing boundary

                $request->setRawData($body);
                $request->setOption('file', null); // this prevent solarium from call AdapterHelper::buildUploadBodyFromRequest for setting body request
                $request->clearParams();
            }
        }

        return $this;
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
