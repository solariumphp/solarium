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
 */

namespace Solarium\Tests\Core\Event;

use Solarium\Core\Event\PreExecuteRequest;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;

class PreExecuteRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndGetters()
    {
        $client = new Client;
        $request = new Request;
        $request->addParam('testparam', 'test value');
        $endpoint = $client->getEndpoint();

        $event = new PreExecuteRequest($request, $endpoint);

        $this->assertEquals($request, $event->getRequest());
        $this->assertEquals($endpoint, $event->getEndpoint());

        return $event;
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreExecuteRequest $event
     */
    public function testSetAndGetQuery($event)
    {
        $response = new Response('', array('HTTP 1.0 200 OK'));
        $event->setResponse($response);
        $this->assertEquals($response, $event->getResponse());
    }
}
