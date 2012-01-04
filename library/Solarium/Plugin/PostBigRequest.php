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
 *
 * @package Solarium
 */

/**
 * PostBigRequest plugin
 *
 * If you reach the url/header length limit of your servlet container your queries will fail.
 * You can increase the limit in the servlet container, but if that's not possible this plugin can automatically
 * convert big GET requests into POST requests. A POST request (usually) has a much higher limit.
 *
 * The default maximum querystring length is 1024. This doesn't include the base url or headers.
 * For most servlet setups this limit leaves enough room for that extra data. Adjust the limit if needed.
 *
 * @package Solarium
 * @subpackage Plugin
 */
class Solarium_Plugin_PostBigRequest extends Solarium_Plugin_Abstract
{

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'maxquerystringlength' => 1024,
    );

    /**
     * Set maxquerystringlength enabled option
     *
     * @param integer $value
     * @return self Provides fluent interface
     */
    public function setMaxQueryStringLength($value)
    {
        return $this->_setOption('maxquerystringlength', $value);
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
     * @param Solarium_Query $query
     * @param Solarium_Client_Request $request
     * @return void
     */
    public function postCreateRequest($query, $request)
    {
        $queryString = $request->getQueryString();
        if ($request->getMethod() == Solarium_Client_Request::METHOD_GET &&
            strlen($queryString) > $this->getMaxQueryStringLength()) {

            $request->setMethod(Solarium_Client_Request::METHOD_POST);
            $request->setRawData($queryString);
            $request->clearParams();
            $request->addHeader('Content-Type: application/x-www-form-urlencoded');
        }
    }

}