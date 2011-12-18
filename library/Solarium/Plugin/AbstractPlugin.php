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
 * @namespace
 */
namespace Solarium\Plugin;

/**
 * Base class for plugins
 *
 * @package Solarium
 * @subpackage Plugin
 */
abstract class AbstractPlugin extends \Solarium\Configurable
{

    /**
     * Client instance
     *
     * @var Solarium\Client
     */
    protected $_client;

    /**
     * Initialize
     *
     * This method is called when the plugin is registered to a client instance
     *
     * @param Solarium\Client $client
     * @param array $options
     */
    public function init($client, $options)
    {
        $this->_client = $client;
        parent::__construct($options);

        $this->_initPlugin();
    }

    /**
     * Plugin init function
     *
     * This is an extension point for plugin implemenations.
     * Will be called as soon as $this->_client and options have been set.
     *
     * @return void
     */
    protected function _initPlugin()
    {

    }

    /**
     * preCreateRequest hook
     *
     * @param Solarium\Query $query
     * @return void|Solarium\Client\Request
     */
    public function preCreateRequest($query)
    {
    }

    /**
     * postCreateRequest hook
     *
     * @param Solarium\Query $query
     * @param Solarium\Client\Request $request
     * @return void
     */
    public function postCreateRequest($query, $request)
    {
    }

    /**
     * preExecuteRequest hook
     *
     * @param Solarium\Client\Request $request
     * @return void|Solarium\Client\Response
     */
    public function preExecuteRequest($request)
    {
    }

    /**
     * postExecuteRequest hook
     *
     * @param Solarium\Client\Request $request
     * @param Solarium\Client\Response $response
     * @return void
     */
    public function postExecuteRequest($request, $response)
    {
    }

    /**
     * preCreateResult hook
     *
     * @param Solarium\Query $query
     * @param Solarium\Client\Response $response
     * @return void|Solarium\Result
     */
    public function preCreateResult($query, $response)
    {
    }

    /**
     * postCreateResult hook
     *
     * @param Solarium\Query $query
     * @param Solarium\Client\Response $response
     * @param Solarium\Result $result
     * @return void
     */
    public function postCreateResult($query, $response, $result)
    {
    }

    /**
     * preExecute hook
     *
     * @param Solarium\Query $query
     * @return void|Solarium\Result
     */
    public function preExecute($query)
    {
    }

    /**
     * postExecute hook
     * 
     * @param Solarium\Query $query
     * @param Solarium\Result $result
     * @return void
     */
    public function postExecute($query, $result)
    {
    }
    
    /**
     * preCreateQuery hook
     *
     * @param string $type
     * @param mixed $options
     * @return void|Solarium\Query
     */
    public function preCreateQuery($type, $options)
    {
    }

    /**
     * postCreateQuery hook
     * 
     * @param string $type
     * @param mixed $options
     * @param Solarium\Query
     * @return void
     */
    public function postCreateQuery($type, $options, $query)
    {
    }

}