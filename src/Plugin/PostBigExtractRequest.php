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
//use Solarium\Core\Client\Request;


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
        'charset' => 'UTF-8',
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
     * Set charset enabled option.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setCharset(string $value): self
    {
        $this->setOption('charset', $value);
        return $this;
    }

    /**
     * Get charset option.
     *
     * @return string|null
     */
    public function getCharset(): ?string
    {
        return $this->getOption('charset');
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
        if ('update/extract'==$request->getHandler() && strlen($queryString) > $this->getMaxQueryStringLength()) {
            
            if ($request->getFileUpload()) {
                $body = '';
                
                $params = $request->getParams();
                if( !empty($params) && count($params)>0 ):
                    foreach($params as $key=>$value):
                        if( is_countable($value) ):
                            foreach ( $value as $array_key => $array_val ):
                                $additional_body_header;
                                if( is_string ( $array_val ) ):
                                    $additional_body_header = "\r\nContent-Type: text/plain;charset=" . $this->getCharset();//$value = urlencode($value);
                                else:
                                    $additional_body_header = '';
                                endif;
                                $body .= "--{$request->getHash()}\r\n";
                                $body .= 'Content-Disposition: form-data; name="' . $key . '"';
                                $body .= $additional_body_header;
                                $body .= "\r\n\r\n";
                                $body .= $array_val;
                                $body .= "\r\n";
                            endforeach;
                        else:
                            $additional_body_header;
                            if( is_string ( $value ) ):
                                $additional_body_header = "\r\nContent-Type: text/plain;charset=" . $this->getCharset();//$value = urlencode($value);
                            else:
                                $additional_body_header = '';
                            endif;
                            $body .= "--{$request->getHash()}\r\n";
                            $body .= 'Content-Disposition: form-data; name="' . $key . '"';
                            $body .= $additional_body_header;
                            $body .= "\r\n\r\n";
                            $body .= $value;
                            $body .= "\r\n";
                        endif;
                    endforeach;
                endif;
                
                $body .= AdapterHelper::buildUploadBodyFromRequest( $request ); //must be the last automatically include closing boundary
                
                $request->setRawData( $body );
                $request->setOption('file', null); // this prevent solarium from call AdapterHelper::buildUploadBodyFromRequest for setting body request
                $request->clearParams();
                //$request->setMethod(Request::METHOD_POST);
                
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
