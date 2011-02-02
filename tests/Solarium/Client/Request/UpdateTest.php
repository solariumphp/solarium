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

class Solarium_Client_Request_UpdateTest extends PHPUnit_Framework_TestCase
{

    protected $_query;

    protected $_options = array(
        'host' => '127.0.0.1',
        'port' => 80,
        'path' => '/solr',
        'core' => null,
    );

    public function setUp()
    {
        $this->_query = new Solarium_Query_Update;
    }

    public function testBuildAddXmlNoParamsSingleDocument()
    {
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => 1)));

        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<add><doc><field name="id">1</field></doc></add>',
            $request->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithParams()
    {
        $command = new Solarium_Query_Update_Command_Add(array('overwrite' => true,'commitwithin' => 100));
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => 1)));

        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<add overwrite="true" commitWithin="100"><doc><field name="id">1</field></doc></add>',
            $request->buildAddXml($command)
        );
    }

    public function testBuildAddXmlSpecialCharacters()
    {
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => 1, 'text' => 'test < 123 > test')));

        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<add><doc><field name="id">1</field><field name="text">test &lt; 123 &gt; test</field></doc></add>',
            $request->buildAddXml($command)
        );
    }

    public function testBuildAddXmlSingleDocumentWithBoost()
    {
        $doc = new Solarium_Document_ReadWrite(array('id' => 1));
        $doc->setBoost(2.5);
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument($doc);

        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<add><doc boost="2.5"><field name="id">1</field></doc></add>',
            $request->buildAddXml($command)
        );
    }

    public function testBuildAddXmlSingleDocumentWithFieldBoost()
    {
        $doc = new Solarium_Document_ReadWrite(array('id' => 1));
        $doc->setFieldBoost('id',2.1);
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument($doc);

        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<add><doc><field name="id" boost="2.1">1</field></doc></add>',
            $request->buildAddXml($command)
        );
    }

    public function testBuildAddXmlMultipleDocuments()
    {
        $command = new Solarium_Query_Update_Command_Add;
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => 1)));
        $command->addDocument(new Solarium_Document_ReadWrite(array('id' => 2)));
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<add><doc><field name="id">1</field></doc><doc><field name="id">2</field></doc></add>',
            $request->buildAddXml($command)
        );
    }

    public function testBuildDeleteXml()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<delete></delete>',
            $request->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlSingleId()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addId(123);
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<delete><id>123</id></delete>',
            $request->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlMultipleIds()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addId(123);
        $command->addId(456);
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<delete><id>123</id><id>456</id></delete>',
            $request->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlSingleQuery()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addQuery('*:*');
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<delete><query>*:*</query></delete>',
            $request->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlMultipleQueries()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addQuery('published:false');
        $command->addQuery('id:[10 TO 20]');
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<delete><query>published:false</query><query>id:[10 TO 20]</query></delete>',
            $request->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlIdsAndQueries()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addId(123);
        $command->addId(456);
        $command->addQuery('published:false');
        $command->addQuery('id:[10 TO 20]');
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<delete><id>123</id><id>456</id><query>published:false</query><query>id:[10 TO 20]</query></delete>',
            $request->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlIdAndQuerySpecialChars()
    {
        $command = new Solarium_Query_Update_Command_Delete;
        $command->addId('special<char>id');
        $command->addQuery('id:special<char>id');
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<delete><id>special&lt;char&gt;id</id><query>id:special&lt;char&gt;id</query></delete>',
            $request->buildDeleteXml($command)
        );
    }

    public function testBuildOptimizeXml()
    {
        $command = new Solarium_Query_Update_Command_Optimize;
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<optimize/>',
            $request->buildOptimizeXml($command)
        );
    }

    public function testBuildOptimizeXmlWithParams()
    {
        $command = new Solarium_Query_Update_Command_Optimize(array('waitflush'=>true,'waitsearcher'=>false,'maxsegments'=>10));
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<optimize waitFlush="true" waitSearcher="false" maxSegments="10"/>',
            $request->buildOptimizeXml($command)
        );
    }

    public function testBuildCommitXml()
    {
        $command = new Solarium_Query_Update_Command_Commit;
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<commit/>',
            $request->buildCommitXml($command)
        );
    }

    public function testBuildCommitXmlWithParams()
    {
        $command = new Solarium_Query_Update_Command_Commit(array('waitflush'=>true,'waitsearcher'=>false,'expungedeletes'=>true));
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<commit waitFlush="true" waitSearcher="false" expungeDeletes="true"/>',
            $request->buildCommitXml($command)
        );
    }

    public function testBuildRollbackXml()
    {
        $command = new Solarium_Query_Update_Command_Rollback;
        $request = new Solarium_Client_Request_Update($this->_options, $this->_query);

        $this->assertEquals(
            '<rollback/>',
            $request->buildRollbackXml($command)
        );
    }

    public function testCompleteRequest()
    {
        $query = new Solarium_Query_Update;
        $query->addDeleteById(1);
        $query->addRollback();
        $query->addDeleteQuery('*:*');
        $query->addDocument(new Solarium_Document_ReadWrite(array('id' => 1)));
        $query->addCommit();
        $query->addOptimize();

        $request = new Solarium_Client_Request_Update($this->_options, $query);
        $this->assertEquals(
            '<update>'
            . '<delete><id>1</id></delete>'
            . '<rollback/>'
            . '<delete><query>*:*</query></delete>'
            . '<add><doc><field name="id">1</field></doc></add>'
            . '<commit/>'
            . '<optimize/>'
            . '</update>',
            $request->getPostData()
        );
    }

    public function testInvalidCommandInRequest()
    {
        $query = new Solarium_Query_Update;
        $query->add('invalidcommand',new InvalidCommand);

        $this->setExpectedException('Solarium_Exception');
        new Solarium_Client_Request_Update($this->_options, $query);
    }
}


class InvalidCommand extends StdClass
{
    public function getType()
    {
        return 'invalid';
    }
}