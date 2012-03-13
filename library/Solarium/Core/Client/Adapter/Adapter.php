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
 * @subpackage Core
 */

/**
 * @namespace
 */
namespace Solarium\Core\Client\Adapter;
use Solarium\Core\Configurable;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Client\Endpoint;

/**
 * Base class for all adapters
 *
 * The goal of an adapter is to accept a query, execute it and return the right
 * result object. This is actually quite a complex task as it involves the
 * handling of all Solr communication.
 *
 * The adapter structure allows for varying implementations of this task.
 *
 * Most adapters will use some sort of HTTP client. In that case the
 * Solarium\Client\Request request builders and Solarium\Client\Response
 * response parsers can be used to simplify HTTP communication.
 * See {@link Solarium\Client\Adapter\Http} as an example.
 *
 * However an adapter may also implement all logic by itself if needed.
 *
 * @package Solarium
 * @subpackage Core
 */
abstract class Adapter extends Configurable
{

    /**
     * Execute a request
     *
     * Abstract method to require an implementation inside all adapters.
     *
     * @abstract
     * @param Request $request
     * @param Endpoint $endpoint
     * @return Response
     */
    abstract public function execute($request, $endpoint);

}