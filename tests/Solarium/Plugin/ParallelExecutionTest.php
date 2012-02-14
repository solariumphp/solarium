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

class Solarium_Plugin_ParallelExecutionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Solarium_Plugin_ParallelExecution
     */
    protected $_plugin;

    public function setUp()
    {
        $this->_plugin = new Solarium_Plugin_ParallelExecution();

    }

    public function testAddAndGetQueries()
    {
        $client1 = new Solarium_Client();
        $client2 = new Solarium_Client(array(
            'adapter' => 'MyAdapter',
                    'adapteroptions' => array(
                        'host' => 'myhost',
                    )
            )
        );
        $this->_plugin->init($client1, array());

        $query1 = $client1->createSelect()->setQuery('test1');
        $query2 = $client1->createSelect()->setQuery('test2');

        $this->_plugin->addQuery(1, $query1);
        $this->_plugin->addQuery(2, $query2, $client2);

        $this->assertEquals(
            array(
                1 => array('query' => $query1, 'client' => $client1),
                2 => array('query' => $query2, 'client' => $client2),
            ),
            $this->_plugin->getQueries()
        );
    }

    public function testClearQueries()
        {
            $client = new Solarium_Client();
            $this->_plugin->init($client, array());

            $query1 = $client->createSelect()->setQuery('test1');
            $query2 = $client->createSelect()->setQuery('test2');

            $this->_plugin->addQuery(1, $query1);
            $this->_plugin->addQuery(2, $query2);
            $this->_plugin->clearQueries();

            $this->assertEquals(
                array(),
                $this->_plugin->getQueries()
            );
        }

}