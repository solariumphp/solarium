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
namespace Solarium\Core;
use Solarium\Core\Client\Client;
use Solarium\Core\Configurable;
use Solarium\Core\Query\Query;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\Result;

/**
 * Base class for plugins
 */
abstract class Plugin extends Configurable implements PluginInterface
{

    /**
     * Client instance
     *
     * @var Client
     */
    protected $client;

    /**
     * Initialize
     *
     * This method is called when the plugin is registered to a client instance
     *
     * @param Client $client
     * @param array  $options
     */
    public function initPlugin($client, $options)
    {
        $this->client = $client;
        parent::__construct($options);

        $this->initPluginType();
    }

    /**
     * Plugin init function
     *
     * This is an extension point for plugin implementations.
     * Will be called as soon as $this->client and options have been set.
     *
     * @return void
     */
    protected function initPluginType()
    {

    }

    /**
     * preCreateRequest hook
     *
     * @param  Query        $query
     * @return void|Request
     */
    public function preCreateRequest($query)
    {
    }

    /**
     * postCreateRequest hook
     *
     * @param  Query   $query
     * @param  Request $request
     * @return void
     */
    public function postCreateRequest($query, $request)
    {
    }

    /**
     * preExecuteRequest hook
     *
     * @param  Request       $request
     * @return void|Response
     */
    public function preExecuteRequest($request)
    {
    }

    /**
     * postExecuteRequest hook
     *
     * @param  Request  $request
     * @param  Response $response
     * @return void
     */
    public function postExecuteRequest($request, $response)
    {
    }

    /**
     * preCreateResult hook
     *
     * @param  Query       $query
     * @param  Response    $response
     * @return void|Result
     */
    public function preCreateResult($query, $response)
    {
    }

    /**
     * postCreateResult hook
     *
     * @param  Query    $query
     * @param  Response $response
     * @param  Result   $result
     * @return void
     */
    public function postCreateResult($query, $response, $result)
    {
    }

    /**
     * preExecute hook
     *
     * @param  Query       $query
     * @return void|Result
     */
    public function preExecute($query)
    {
    }

    /**
     * postExecute hook
     *
     * @param  Query  $query
     * @param  Result $result
     * @return void
     */
    public function postExecute($query, $result)
    {
    }

    /**
     * preCreateQuery hook
     *
     * @param  string     $type
     * @param  mixed      $options
     * @return void|Query
     */
    public function preCreateQuery($type, $options)
    {
    }

    /**
     * postCreateQuery hook
     *
     * @param string $type
     * @param mixed  $options
     * @param Query
     * @return void
     */
    public function postCreateQuery($type, $options, $query)
    {
    }

}
