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

namespace Solarium\Tests\QueryType\Update;

use Solarium\Core\Client\Request;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Query\Command\Commit as CommitCommand;
use Solarium\QueryType\Update\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\Update\Query\Command\Optimize as OptimizeCommand;
use Solarium\QueryType\Update\Query\Command\Rollback as RollbackCommand;
use Solarium\QueryType\Update\Query\Document\Document;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\RequestBuilder;

class RequestBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var RequestBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->query = new Query;
        $this->builder = new RequestBuilder;
    }

    public function testGetMethod()
    {
        $request = $this->builder->build($this->query);
        $this->assertEquals(
            Request::METHOD_POST,
            $request->getMethod()
        );
    }

    public function testGetUri()
    {
        $request = $this->builder->build($this->query);
        $this->assertEquals(
            'update?omitHeader=false&wt=json&json.nl=flat',
            $request->getUri()
        );
    }

    public function testBuildAddXmlNoParamsSingleDocument()
    {
        $command = new AddCommand;
        $command->addDocument(new Document(array('id' => 1)));

        $this->assertEquals(
            '<add><doc><field name="id">1</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithBooleanValues()
    {
        $command = new AddCommand;
        $command->addDocument(new Document(array('id' => 1, 'visible' => true, 'forsale' => false)));

        $this->assertEquals(
            '<add><doc><field name="id">1</field><field name="visible">true</field><field name="forsale">false</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithParams()
    {
        $command = new AddCommand(array('overwrite' => true, 'commitwithin' => 100));
        $command->addDocument(new Document(array('id' => 1)));

        $this->assertEquals(
            '<add overwrite="true" commitWithin="100"><doc><field name="id">1</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlSpecialCharacters()
    {
        $command = new AddCommand;
        $command->addDocument(new Document(array('id' => 1, 'text' => 'test < 123 > test')));

        $this->assertEquals(
            '<add><doc><field name="id">1</field><field name="text">test &lt; 123 &gt; test</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlMultivalueField()
    {
        $command = new AddCommand;
        $command->addDocument(new Document(array('id' => array(1, 2, 3), 'text' => 'test < 123 > test')));

        $this->assertEquals(
            '<add>' .
            '<doc>' .
            '<field name="id">1</field>' .
            '<field name="id">2</field>' .
            '<field name="id">3</field>' .
            '<field name="text">test &lt; 123 &gt; test</field>' .
            '</doc>' .
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithNestedDocuments()
    {
        $command = new AddCommand;
        $command->addDocument(
            new Document(
                array(
                    'id' => array(
                        array(
                            'nested_id' => 42,
                            'customer_ids' => array(
                                15,
                                16
                            )
                        ),
                        2,
                        'foo'
                    ),
                    'text' => 'test < 123 > test'
                )
            )
        );

        $this->assertEquals(
            '<add>' .
            '<doc>' .
            '<doc>' .
            '<field name="nested_id">42</field>' .
            '<field name="customer_ids">15</field>' .
            '<field name="customer_ids">16</field>' .
            '</doc>' .
            '<field name="id">2</field>' .
            '<field name="id">foo</field>' .
            '<field name="text">test &lt; 123 &gt; test</field>' .
            '</doc>' .
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlSingleDocumentWithBoost()
    {
        $doc = new Document(array('id' => 1));
        $doc->setBoost(2.5);
        $command = new AddCommand;
        $command->addDocument($doc);

        $this->assertEquals(
            '<add><doc boost="2.5"><field name="id">1</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlSingleDocumentWithFieldBoost()
    {
        $doc = new Document(array('id' => 1));
        $doc->setFieldBoost('id', 2.1);
        $command = new AddCommand;
        $command->addDocument($doc);

        $this->assertEquals(
            '<add><doc><field name="id" boost="2.1">1</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlMultipleDocuments()
    {
        $command = new AddCommand;
        $command->addDocument(new Document(array('id' => 1)));
        $command->addDocument(new Document(array('id' => 2)));

        $this->assertEquals(
            '<add><doc><field name="id">1</field></doc><doc><field name="id">2</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithFieldModifiers()
    {
        $doc = new Document();
        $doc->setKey('id', 1);
        $doc->addField('category', 123, null, Document::MODIFIER_ADD);
        $doc->addField('name', 'test', 2.5, Document::MODIFIER_SET);
        $doc->setField('stock', 2, null, Document::MODIFIER_INC);

        $command = new AddCommand();
        $command->addDocument($doc);

        $this->assertEquals(
            '<add>' .
            '<doc>' .
            '<field name="id">1</field>' .
            '<field name="category" update="add">123</field>' .
            '<field name="name" boost="2.5" update="set">test</field>' .
            '<field name="stock" update="inc">2</field>' .
            '</doc>' .
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithFieldModifiersAndMultivalueFields()
    {
        $doc = new Document();
        $doc->setKey('id', 1);
        $doc->addField('category', 123, null, Document::MODIFIER_ADD);
        $doc->addField('category', 234, null, Document::MODIFIER_ADD);
        $doc->addField('name', 'test', 2.3, Document::MODIFIER_SET);
        $doc->setField('stock', 2, null, Document::MODIFIER_INC);

        $command = new AddCommand();
        $command->addDocument($doc);

        $this->assertEquals(
            '<add>' .
            '<doc>' .
            '<field name="id">1</field>' .
            '<field name="category" update="add">123</field>' .
            '<field name="category" update="add">234</field>' .
            '<field name="name" boost="2.3" update="set">test</field>' .
            '<field name="stock" update="inc">2</field>' .
            '</doc>' .
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithVersionedDocument()
    {
        $doc = new Document(array('id' => 1));
        $doc->setVersion(Document::VERSION_MUST_NOT_EXIST);

        $command = new AddCommand;
        $command->addDocument($doc);

        $this->assertEquals(
            '<add><doc><field name="id">1</field><field name="_version_">-1</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithDateTime()
    {
        $command = new AddCommand;
        $command->addDocument(
            new Document(array('id' => 1, 'datetime' => new \DateTime('2013-01-15 14:41:58', new \DateTimeZone('Europe/London'))))
        );

        $this->assertEquals(
            '<add><doc><field name="id">1</field><field name="datetime">2013-01-15T14:41:58Z</field></doc></add>',
            $this->builder->buildAddXml($command, $this->query)
        );
    }

    public function testBuildAddXmlWithFieldModifierAndNullValue()
    {
        $doc = new Document();
        $doc->setKey('employeeId', '05991');
        $doc->addField('skills', null, null, Document::MODIFIER_SET);

        $command = new AddCommand();
        $command->addDocument($doc);

        $this->assertEquals(
            '<add>' .
            '<doc>' .
            '<field name="employeeId">05991</field>' .
            '<field name="skills" update="set" null="true"></field>' .
            '</doc>' .
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildDeleteXml()
    {
        $command = new DeleteCommand;

        $this->assertEquals(
            '<delete></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlSingleId()
    {
        $command = new DeleteCommand;
        $command->addId(123);

        $this->assertEquals(
            '<delete><id>123</id></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlMultipleIds()
    {
        $command = new DeleteCommand();
        $command->addId(123);
        $command->addId(456);

        $this->assertEquals(
            '<delete><id>123</id><id>456</id></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlSingleQuery()
    {
        $command = new DeleteCommand;
        $command->addQuery('*:*');

        $this->assertEquals(
            '<delete><query>*:*</query></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlMultipleQueries()
    {
        $command = new DeleteCommand;
        $command->addQuery('published:false');
        $command->addQuery('id:[10 TO 20]');

        $this->assertEquals(
            '<delete><query>published:false</query><query>id:[10 TO 20]</query></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlIdsAndQueries()
    {
        $command = new DeleteCommand;
        $command->addId(123);
        $command->addId(456);
        $command->addQuery('published:false');
        $command->addQuery('id:[10 TO 20]');

        $this->assertEquals(
            '<delete><id>123</id><id>456</id><query>published:false</query><query>id:[10 TO 20]</query></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlIdAndQuerySpecialChars()
    {
        $command = new DeleteCommand;
        $command->addId('special<char>id');
        $command->addQuery('id:special<char>id');

        $this->assertEquals(
            '<delete><id>special&lt;char&gt;id</id><query>id:special&lt;char&gt;id</query></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildOptimizeXml()
    {
        $command = new OptimizeCommand();

        $this->assertEquals(
            '<optimize/>',
            $this->builder->buildOptimizeXml($command)
        );
    }

    public function testBuildOptimizeXmlWithParams()
    {
        $command = new OptimizeCommand(array('softcommit' => true, 'waitsearcher' => false, 'maxsegments' => 10));

        $this->assertEquals(
            '<optimize softCommit="true" waitSearcher="false" maxSegments="10"/>',
            $this->builder->buildOptimizeXml($command)
        );
    }

    public function testBuildCommitXml()
    {
        $command = new CommitCommand;

        $this->assertEquals(
            '<commit/>',
            $this->builder->buildCommitXml($command)
        );
    }

    public function testBuildCommitXmlWithParams()
    {
        $command = new CommitCommand(array('softcommit' => true, 'waitsearcher' => false, 'expungedeletes' => true));

        $this->assertEquals(
            '<commit softCommit="true" waitSearcher="false" expungeDeletes="true"/>',
            $this->builder->buildCommitXml($command)
        );
    }

    public function testBuildRollbackXml()
    {
        $command = new RollbackCommand;

        $this->assertEquals(
            '<rollback/>',
            $this->builder->buildRollbackXml($command)
        );
    }

    public function testCompleteRequest()
    {
        $this->query->addDeleteById(1);
        $this->query->addRollback();
        $this->query->addDeleteQuery('*:*');
        $this->query->addDocument(new Document(array('id' => 1)));
        $this->query->addCommit();
        $this->query->addOptimize();

        $this->assertEquals(
            '<update>'
            . '<delete><id>1</id></delete>'
            . '<rollback/>'
            . '<delete><query>*:*</query></delete>'
            . '<add><doc><field name="id">1</field></doc></add>'
            . '<commit/>'
            . '<optimize/>'
            . '</update>',
            $this->builder->getRawData($this->query)
        );
    }

    public function testInvalidCommandInRequest()
    {
        $this->query->add('invalidcommand', new InvalidCommand);

        $this->setExpectedException('Solarium\Exception\RuntimeException');
        $this->builder->build($this->query);
    }
}

class InvalidCommand extends \stdClass
{
    public function getType()
    {
        return 'invalid';
    }
}
