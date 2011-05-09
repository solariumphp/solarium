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

class Solarium_ResultTest extends PHPUnit_Framework_TestCase
{
    
    protected $_client, $_query, $_response, $_result;

    public function setUp()
    {
        $this->_client = new Solarium_Client();
        $this->_query = new Solarium_Query_Select();
        $this->_headers = array('HTTP/1.0 304 Not Modified');
        $data = '{"responseHeader":{"status":0,"QTime":1,"params":{"wt":"json","q":"xyz"}},"response":{"numFound":0,"start":0,"docs":[]}}';
        $this->_response = new Solarium_Client_Response($data, $this->_headers);
        
        $this->_result = new Solarium_Result($this->_client, $this->_query, $this->_response);
    }

    public function testResultWithErrorResponse()
    {
        $headers = array('HTTP/1.0 404 Not Found');
        $response = new Solarium_Client_Response('', $headers);

        $this->setExpectedException('Solarium_Exception');
        new Solarium_Result($this->_client, $this->_query, $response);
    }

    public function testGetResponse()
    {
        $this->assertEquals($this->_response, $this->_result->getResponse());
    }

    public function testGetQuery()
    {
        $this->assertEquals($this->_query, $this->_result->getQuery());
    }

    public function testGetData()
    {
        $data = array(
            'responseHeader' => array('status' => 0, 'QTime' => 1, 'params' => array('wt' => 'json', 'q' => 'xyz')),
            'response' => array('numFound' => 0, 'start' => 0, 'docs' => array())
        );

        $this->assertEquals($data, $this->_result->getData());
    }

    public function testGetInvalidData()
    {
        $data = 'invalid';
        $this->_response = new Solarium_Client_Response($data, $this->_headers);
        $this->_result = new Solarium_Result($this->_client, $this->_query, $this->_response);

        $this->setExpectedException('Solarium_Exception');
        $this->_result->getData();
    }
    
}