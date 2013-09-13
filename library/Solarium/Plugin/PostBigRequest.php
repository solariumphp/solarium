<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\Plugin;

use Solarium\Client;
use Solarium\Core\Plugin\Plugin;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostCreateRequest as PostCreateRequestEvent;

/**
 * PostBigRequest plugin
 *
 * If you reach the url/header length limit of your servlet container your queries will fail.
 * You can increase the limit in the servlet container, but if that's not possible this plugin can automatically
 * convert big GET requests into POST requests. A POST request (usually) has a much higher limit.
 *
 * The default maximum querystring length is 1024. This doesn't include the base url or headers.
 * For most servlet setups this limit leaves enough room for that extra data. Adjust the limit if needed.
 */
class PostBigRequest extends Plugin
{
    /**
     * Default options
     *
     * @var array
     */
    protected $options = array(
        'maxquerystringlength' => 1024,
    );

    /**
     * Plugin init function
     *
     * Register event listeners
     *
     * @return void
     */
    protected function initPluginType()
    {
        $dispatcher = $this->client->getEventDispatcher();
        $dispatcher->addListener(Events::POST_CREATE_REQUEST, array($this, 'postCreateRequest'));
    }

    /**
     * Set maxquerystringlength enabled option
     *
     * @param  integer $value
     * @return self    Provides fluent interface
     */
    public function setMaxQueryStringLength($value)
    {
        return $this->setOption('maxquerystringlength', $value);
    }

    /**
     * Get maxquerystringlength option
     *
     * @return integer
     */
    public function getMaxQueryStringLength()
    {
        return $this->getOption('maxquerystringlength');
    }

    /**
     * Event hook to adjust client settings just before query execution
     *
     * @param  PostCreateRequestEvent $event
     * @return void
     */
    public function postCreateRequest($event)
    {
        $request = $event->getRequest();
        $queryString = $request->getQueryString();
        if ($request->getMethod() == Request::METHOD_GET &&
            strlen($queryString) > $this->getMaxQueryStringLength()) {

            $request->setMethod(Request::METHOD_POST);
            $request->setRawData($queryString);
            $request->clearParams();
            $request->addHeader('Content-Type: application/x-www-form-urlencoded');
        }
    }
}
