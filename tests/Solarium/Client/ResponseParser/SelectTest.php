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

class Solarium_Client_ResponseParser_SelectTest extends PHPUnit_Framework_TestCase
{

    public function testParse()
    {
        $data = array(
            'response' => array(
                'docs' => array(
                    array('fieldA' => 1, 'fieldB' => 'Test'),
                    array('fieldA' => 2, 'fieldB' => 'Test2')
                ),
                'numFound' => 503
            ),
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 13,
            )
        );

        $query = new Solarium_Query_Select(array('documentclass' => 'Solarium_Document_ReadWrite'));
        $query->getFacetSet();

        $resultStub = $this->getMock('Solarium_Result_Select', array(), array(), '', false);
        $resultStub->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->once())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new Solarium_Client_ResponseParser_Select;
        $result = $parser->parse($resultStub);

        $this->assertEquals(1, $result['status']);
        $this->assertEquals(13, $result['queryTime']);
        $this->assertEquals(503, $result['numfound']);

        $docs = array(
            new Solarium_Document_ReadWrite(array('fieldA' => 1, 'fieldB' => 'Test')),
            new Solarium_Document_ReadWrite(array('fieldA' => 2, 'fieldB' => 'Test2')) 
        );
        $this->assertEquals($docs, $result['documents']);

        $components = array(
            Solarium_Query_Select::COMPONENT_FACETSET => new Solarium_Result_Select_FacetSet(array())
        );
        $this->assertEquals($components, $result['components']);
    }

    public function testParseWithoutNumFound()
    {
        $data = array(
            'response' => array(
                'docs' => array(
                    array('fieldA' => 1, 'fieldB' => 'Test'),
                    array('fieldA' => 2, 'fieldB' => 'Test2')
                ),
            ),
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 13,
            )
        );

        $query = new Solarium_Query_Select(array('documentclass' => 'Solarium_Document_ReadWrite'));
        $query->getFacetSet();

        $resultStub = $this->getMock('Solarium_Result_Select', array(), array(), '', false);
        $resultStub->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->once())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new Solarium_Client_ResponseParser_Select;
        $result = $parser->parse($resultStub);

        $this->assertEquals(1, $result['status']);
        $this->assertEquals(13, $result['queryTime']);
        $this->assertEquals(null, $result['numfound']);
    }

}
