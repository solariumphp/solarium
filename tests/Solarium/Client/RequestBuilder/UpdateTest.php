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

class Solarium_Client_RequestBuilder_UpdateTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Update
     */
    protected $_query;

    /**
     * @var Solarium_Client_RequestBuilder_Update
     */
    protected $_builder;

    public function setUp()
    {
        $this->_query = new Solarium_Query_Update;
        $this->_builder = new Solarium_Client_RequestBuilder_Update;
    }

    public function testGetMethod()
    {
        $request = $this->_builder->build($this->_query);
        $this->assertEquals(
            Solarium_Client_Request::METHOD_POST,
            $request->getMethod()
        );
    }

    public function testGetUri()
    {
        $request = $this->_builder->build($this->_query);
        $this->assertEquals(
            'update?wt=json',
            $request->getUri()
        );
    }

    public function testBuildAddXmlNoParamsSingleDocument()
    {
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => 1)));
        
        $this->assertEquals(
            '<add><doc><field name="id">1</field></doc></add>',
            $this->_builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithParams()
    {
        $command = new Solarium_Query_Update_Command_Add(array('overwrite' => true,'commitwithin' => 100));
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => 1)));

        $this->assertEquals(
            '<add overwrite="true" commitWithin="100"><doc><field name="id">1</field></doc></add>',
            $this->_builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlSpecialCharacters()
    {
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => 1, 'text' => 'test < 123 > test')));

        $this->assertEquals(
            '<add><doc><field name="id">1</field><field name="text">test &lt; 123 &gt; test</field></doc></add>',
            $this->_builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlMultivalueField()
    {
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => array(1,2,3), 'text' => 'test < 123 > test')));

        $this->assertEquals(
            '<add><doc><field name="id">1</field><field name="id">2</field><field name="id">3</field><field name="text">test &lt; 123 &gt; test</field></doc></add>',
            $this->_builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlSingleDocumentWithBoost()
    {
        $doc = new Solarium_Document_ReadWrite(array('id' => 1));
        $doc->setBoost(2.5);
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument($doc);

        $this->assertEquals(
            '<add><doc boost="2.5"><field name="id">1</field></doc></add>',
            $this->_builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlSingleDocumentWithFieldBoost()
    {
        $doc = new Solarium_Document_ReadWrite(array('id' => 1));
        $doc->setFieldBoost('id',2.1);
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument($doc);

        $this->assertEquals(
            '<add><doc><field name="id" boost="2.1">1</field></doc></add>',
            $this->_builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlMultipleDocuments()
    {
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => 1)));
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => 2)));

        $this->assertEquals(
            '<add><doc><field name="id">1</field></doc><doc><field name="id">2</field></doc></add>',
            $this->_builder->buildAddXml($command)
        );
    }

    public function testBuildDeleteXml()
    {
        $command = new Solarium_Query_Update_Command_Delete;

        $this->assertEquals(
            '<delete></delete>',
            $this->_builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlSingleId()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addId(123);

        $this->assertEquals(
            '<delete><id>123</id></delete>',
            $this->_builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlMultipleIds()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addId(123);
        $command->addId(456);

        $this->assertEquals(
            '<delete><id>123</id><id>456</id></delete>',
            $this->_builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlSingleQuery()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addQuery('*:*');

        $this->assertEquals(
            '<delete><query>*:*</query></delete>',
            $this->_builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlMultipleQueries()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addQuery('published:false');
        $command->addQuery('id:[10 TO 20]');

        $this->assertEquals(
            '<delete><query>published:false</query><query>id:[10 TO 20]</query></delete>',
            $this->_builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlIdsAndQueries()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addId(123);
        $command->addId(456);
        $command->addQuery('published:false');
        $command->addQuery('id:[10 TO 20]');

        $this->assertEquals(
            '<delete><id>123</id><id>456</id><query>published:false</query><query>id:[10 TO 20]</query></delete>',
            $this->_builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlIdAndQuerySpecialChars()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addId('special<char>id');
        $command->addQuery('id:special<char>id');

        $this->assertEquals(
            '<delete><id>special&lt;char&gt;id</id><query>id:special&lt;char&gt;id</query></delete>',
            $this->_builder->buildDeleteXml($command)
        );
    }

    public function testBuildOptimizeXml()
    {
        $command = new Solarium_Query_Update_Command_Optimize;

        $this->assertEquals(
            '<optimize/>',
            $this->_builder->buildOptimizeXml($command)
        );
    }

    public function testBuildOptimizeXmlWithParams()
    {
        $command = new Solarium_Query_Update_Command_Optimize(array('waitflush'=>true,'waitsearcher'=>false,'maxsegments'=>10));

        $this->assertEquals(
            '<optimize waitFlush="true" waitSearcher="false" maxSegments="10"/>',
            $this->_builder->buildOptimizeXml($command)
        );
    }

    public function testBuildCommitXml()
    {
        $command = new Solarium_Query_Update_Command_Commit;

        $this->assertEquals(
            '<commit/>',
            $this->_builder->buildCommitXml($command)
        );
    }

    public function testBuildCommitXmlWithParams()
    {
        $command = new Solarium_Query_Update_Command_Commit(array('waitflush'=>true,'waitsearcher'=>false,'expungedeletes'=>true));

        $this->assertEquals(
            '<commit waitFlush="true" waitSearcher="false" expungeDeletes="true"/>',
            $this->_builder->buildCommitXml($command)
        );
    }

    public function testBuildRollbackXml()
    {
        $command = new Solarium_Query_Update_Command_Rollback;

        $this->assertEquals(
            '<rollback/>',
            $this->_builder->buildRollbackXml($command)
        );
    }

    public function testCompleteRequest()
    {
        $this->_query->addDeleteById(1);
        $this->_query->addRollback();
        $this->_query->addDeleteQuery('*:*');
        $this->_query->addDocument(new Solarium_Document_ReadWrite(array('id' => 1)));
        $this->_query->addCommit();
        $this->_query->addOptimize();

        $this->assertEquals(
            '<update>'
            . '<delete><id>1</id></delete>'
            . '<rollback/>'
            . '<delete><query>*:*</query></delete>'
            . '<add><doc><field name="id">1</field></doc></add>'
            . '<commit/>'
            . '<optimize/>'
            . '</update>',
            $this->_builder->getRawData($this->_query)
        );
    }

    public function testInvalidCommandInRequest()
    {
        $this->_query->add('invalidcommand',new InvalidCommand);

        $this->setExpectedException('Solarium_Exception');
        $this->_builder->build($this->_query);
    }
}


class InvalidCommand extends StdClass
{
    public function getType()
    {
        return 'invalid';
    }
}