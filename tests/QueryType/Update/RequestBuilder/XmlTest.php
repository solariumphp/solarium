<?php

namespace Solarium\Tests\QueryType\Update\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Command\AbstractCommand;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Query\Command\Commit as CommitCommand;
use Solarium\QueryType\Update\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\Update\Query\Command\Optimize as OptimizeCommand;
use Solarium\QueryType\Update\Query\Command\RawXml as RawXmlCommand;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\RequestBuilder\Xml as XmlRequestBuilder;

class XmlTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var XmlRequestBuilder
     */
    protected $builder;

    public function setUp(): void
    {
        $this->query = new Query();
        $this->query->setRequestFormat(Query::REQUEST_FORMAT_XML);

        $this->builder = new XmlRequestBuilder();
    }

    public function testGetMethod()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(
            Request::METHOD_POST,
            $request->getMethod()
        );
    }

    public function testGetContentType()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(
            Request::CONTENT_TYPE_APPLICATION_XML,
            $request->getContentType()
        );
    }

    public function testGetUri()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(
            'update?omitHeader=false&wt=json&json.nl=flat',
            $request->getUri()
        );
    }

    public function testBuildWithUnsupportedCommandType()
    {
        $this->query->add(null, new UnsupportedCommand());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported command type');
        $this->builder->build($this->query);
    }

    public function testBuildAddXmlNoParamsSingleDocument()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 1]));

        $this->assertSame(
            '<add><doc><field name="id">1</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithBooleanValues()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 1, 'visible' => true, 'forsale' => false]));

        $this->assertSame(
            '<add><doc><field name="id">1</field><field name="visible">true</field><field name="forsale">false</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithEmptyValues()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 0, 'empty_string' => '', 'empty_array' => [], 'array_of_empty_string' => [''], 'null' => null]));

        // Empty strings must be added to the document as empty fields, empty arrays and NULL values are skipped.
        $this->assertSame(
            '<add><doc><field name="id">0</field><field name="empty_string"></field><field name="array_of_empty_string"></field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithParams()
    {
        $command = new AddCommand(['overwrite' => true, 'commitwithin' => 100]);
        $command->addDocument(new Document(['id' => 1]));

        $this->assertSame(
            '<add overwrite="true" commitWithin="100"><doc><field name="id">1</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlFilterControlCharacters()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 1, 'text' => 'test '.chr(15).' 123 '.chr(8).' test']));

        $this->assertSame(
            '<add><doc><field name="id">1</field><field name="text">test   123   test</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlEscapeCharacters()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 1, 'text' => 'test < 123 > test']));

        $this->assertSame(
            '<add><doc><field name="id">1</field><field name="text">test &lt; 123 &gt; test</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlMultivalueField()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => [1, 2, 3], 'text' => ['test < 123 '.chr(8).' test', 'test '.chr(15).' 123 > test']]));

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<field name="id">1</field>'.
            '<field name="id">2</field>'.
            '<field name="id">3</field>'.
            '<field name="text">test &lt; 123   test</field>'.
            '<field name="text">test   123 &gt; test</field>'.
            '</doc>'.
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlMultivalueFieldWithEmptyArray()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => [1, 2, 3], 'text' => []]));

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<field name="id">1</field>'.
            '<field name="id">2</field>'.
            '<field name="id">3</field>'.
            '</doc>'.
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlMultivalueFieldWithNonConsecutiveArrayIndices()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => [0 => 1, 4 => 2, 6 => 3], 'text' => [1 => 'a', 2 => 'b', 3 => 'c']]));

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<field name="id">1</field>'.
            '<field name="id">2</field>'.
            '<field name="id">3</field>'.
            '<field name="text">a</field>'.
            '<field name="text">b</field>'.
            '<field name="text">c</field>'.
            '</doc>'.
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithEmptyStrings()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => '', 'text' => ['']]));

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<field name="id"></field>'.
            '<field name="text"></field>'.
            '</doc>'.
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithSingleNestedDocument()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(
                [
                    'id' => [
                        'nested_id' => 42,
                        'customer_ids' => [
                            15,
                            16,
                        ],
                    ],
                    'text' => 'test < 123 > test',
                ]
            )
        );

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<doc name="id">'.
            '<field name="nested_id">42</field>'.
            '<field name="customer_ids">15</field>'.
            '<field name="customer_ids">16</field>'.
            '</doc>'.
            '<field name="text">test &lt; 123 &gt; test</field>'.
            '</doc>'.
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithNestedDocuments()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(
                [
                    'id' => [
                        [
                            'nested_id' => 42,
                            'customer_ids' => [
                                15,
                                16,
                            ],
                        ],
                        [
                            'nested_id' => 'XLII',
                            'customer_ids' => [
                                17,
                                18,
                            ],
                        ],
                        2,
                        'foo',
                    ],
                    'text' => 'test < 123 > test',
                ]
            )
        );

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<field name="id">'.
            '<doc>'.
            '<field name="nested_id">42</field>'.
            '<field name="customer_ids">15</field>'.
            '<field name="customer_ids">16</field>'.
            '</doc>'.
            '<doc>'.
            '<field name="nested_id">XLII</field>'.
            '<field name="customer_ids">17</field>'.
            '<field name="customer_ids">18</field>'.
            '</doc>'.
            '</field>'.
            '<field name="id">2</field>'.
            '<field name="id">foo</field>'.
            '<field name="text">test &lt; 123 &gt; test</field>'.
            '</doc>'.
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithSingleAnonymouslyNestedDocument()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(
                [
                    'id' => 1701,
                    'cat' => ['A', 'D'],
                    'text' => ':=._,<^>',
                    '_childDocuments_' => [
                        'id' => '1701-D',
                        'cat' => ['D'],
                    ],
                ]
            )
        );

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<field name="id">1701</field>'.
            '<field name="cat">A</field>'.
            '<field name="cat">D</field>'.
            '<field name="text">:=._,&lt;^&gt;</field>'.
            '<doc>'.
            '<field name="id">1701-D</field>'.
            '<field name="cat">D</field>'.
            '</doc>'.
            '</doc>'.
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithAnonymouslyNestedDocuments()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(
                [
                    'id' => 1701,
                    'cat' => ['A', 'D'],
                    'text' => ':=._,<^>',
                    '_childDocuments_' => [
                        [
                            'id' => '1701-A',
                            'cat' => ['A'],
                        ],
                        [
                            'id' => '1701-D',
                            'cat' => ['D'],
                        ],
                    ],
                ]
            )
        );

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<field name="id">1701</field>'.
            '<field name="cat">A</field>'.
            '<field name="cat">D</field>'.
            '<field name="text">:=._,&lt;^&gt;</field>'.
            '<doc>'.
            '<field name="id">1701-A</field>'.
            '<field name="cat">A</field>'.
            '</doc>'.
            '<doc>'.
            '<field name="id">1701-D</field>'.
            '<field name="cat">D</field>'.
            '</doc>'.
            '</doc>'.
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    /**
     * @deprecated No longer supported since Solr 7
     */
    public function testBuildAddXmlSingleDocumentWithBoost()
    {
        $doc = new Document(['id' => 1]);
        $doc->setBoost(2.5);
        $command = new AddCommand();
        $command->addDocument($doc);

        $this->assertSame(
            '<add><doc boost="2.5"><field name="id">1</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlSingleDocumentWithFieldBoost()
    {
        $doc = new Document(['id' => 1]);
        $doc->setFieldBoost('id', 2.1);
        $command = new AddCommand();
        $command->addDocument($doc);

        $this->assertSame(
            '<add><doc><field name="id" boost="2.1">1</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlMultipleDocuments()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 1]));
        $command->addDocument(new Document(['id' => 2]));

        $this->assertSame(
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
        $doc->setField('skills', null, null, Document::MODIFIER_SET);
        $doc->setField('parts', [], null, Document::MODIFIER_SET);
        $doc->setField('stock', 2, null, Document::MODIFIER_INC);

        $command = new AddCommand();
        $command->addDocument($doc);

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<field name="id">1</field>'.
            '<field name="category" update="add">123</field>'.
            '<field name="name" boost="2.5" update="set">test</field>'.
            '<field name="skills" update="set" null="true"></field>'.
            '<field name="parts" update="set" null="true"></field>'.
            '<field name="stock" update="inc">2</field>'.
            '</doc>'.
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

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<field name="id">1</field>'.
            '<field name="category" update="add">123</field>'.
            '<field name="category" update="add">234</field>'.
            '<field name="name" boost="2.3" update="set">test</field>'.
            '<field name="stock" update="inc">2</field>'.
            '</doc>'.
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithVersionedDocument()
    {
        $doc = new Document(['id' => 1]);
        $doc->setVersion(Document::VERSION_MUST_NOT_EXIST);

        $command = new AddCommand();
        $command->addDocument($doc);

        $this->assertSame(
            '<add><doc><field name="id">1</field><field name="_version_">-1</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithDateTime()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'datetime' => new \DateTime('2013-01-15 14:41:58', new \DateTimeZone('+02:00'))])
        );

        $this->assertSame(
            '<add><doc><field name="id">1</field><field name="datetime">2013-01-15T12:41:58Z</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithDateTimeImmutable()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'datetime' => new \DateTimeImmutable('2013-01-15 14:41:58', new \DateTimeZone('-06:00'))])
        );

        $this->assertSame(
            '<add><doc><field name="id">1</field><field name="datetime">2013-01-15T20:41:58Z</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithMultivalueDateTimes()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'datetime' => [new \DateTime('2013-01-15 14:41:58', new \DateTimeZone('-02:00')), new \DateTimeImmutable('2014-02-16 15:42:59', new \DateTimeZone('+06:00'))]])
        );

        $this->assertSame(
            '<add><doc><field name="id">1</field><field name="datetime">2013-01-15T16:41:58Z</field><field name="datetime">2014-02-16T09:42:59Z</field></doc></add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildAddXmlWithFieldModifierAndNullValue()
    {
        $doc = new Document();
        $doc->setKey('employeeId', '05991');
        $doc->addField('skills', null, null, Document::MODIFIER_SET);

        $command = new AddCommand();
        $command->addDocument($doc);

        $this->assertSame(
            '<add>'.
            '<doc>'.
            '<field name="employeeId">05991</field>'.
            '<field name="skills" update="set" null="true"></field>'.
            '</doc>'.
            '</add>',
            $this->builder->buildAddXml($command)
        );
    }

    public function testBuildDeleteXmlEmpty()
    {
        $command = new DeleteCommand();

        $this->assertSame(
            '<delete></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlSingleId()
    {
        $command = new DeleteCommand();
        $command->addId(123);

        $this->assertSame(
            '<delete><id>123</id></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlMultipleIds()
    {
        $command = new DeleteCommand();
        $command->addId(123);
        $command->addId(456);

        $this->assertSame(
            '<delete><id>123</id><id>456</id></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlSingleQuery()
    {
        $command = new DeleteCommand();
        $command->addQuery('*:*');

        $this->assertSame(
            '<delete><query>*:*</query></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlMultipleQueries()
    {
        $command = new DeleteCommand();
        $command->addQuery('published:false');
        $command->addQuery('id:[10 TO 20]');

        $this->assertSame(
            '<delete><query>published:false</query><query>id:[10 TO 20]</query></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlIdsAndQueries()
    {
        $command = new DeleteCommand();
        $command->addId(123);
        $command->addId(456);
        $command->addQuery('published:false');
        $command->addQuery('id:[10 TO 20]');

        $this->assertSame(
            '<delete><id>123</id><id>456</id><query>published:false</query><query>id:[10 TO 20]</query></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildDeleteXmlIdAndQuerySpecialChars()
    {
        $command = new DeleteCommand();
        $command->addId('special<char>id');
        $command->addQuery('id:special<char>id');

        $this->assertSame(
            '<delete><id>special&lt;char&gt;id</id><query>id:special&lt;char&gt;id</query></delete>',
            $this->builder->buildDeleteXml($command)
        );
    }

    public function testBuildOptimizeXml()
    {
        $command = new OptimizeCommand();

        $this->assertSame(
            '<optimize/>',
            $this->builder->buildOptimizeXml($command)
        );
    }

    public function testBuildOptimizeXmlWithParams()
    {
        $command = new OptimizeCommand(['softcommit' => true, 'waitsearcher' => false, 'maxsegments' => 10]);

        $this->assertSame(
            '<optimize softCommit="true" waitSearcher="false" maxSegments="10"/>',
            $this->builder->buildOptimizeXml($command)
        );
    }

    public function testBuildCommitXml()
    {
        $command = new CommitCommand();

        $this->assertSame(
            '<commit/>',
            $this->builder->buildCommitXml($command)
        );
    }

    public function testBuildCommitXmlWithParams()
    {
        $command = new CommitCommand(['softcommit' => true, 'waitsearcher' => false, 'expungedeletes' => true]);

        $this->assertSame(
            '<commit softCommit="true" waitSearcher="false" expungeDeletes="true"/>',
            $this->builder->buildCommitXml($command)
        );
    }

    public function testBuildRollbackXml()
    {
        $this->assertSame(
            '<rollback/>',
            $this->builder->buildRollbackXml()
        );
    }

    public function testBuildRawXmlXmlSingleCommand()
    {
        $command = new RawXmlCommand();
        $command->addCommand('<add><doc><field name="id">1</field></doc></add>');

        $this->assertSame(
            '<add><doc><field name="id">1</field></doc></add>',
            $this->builder->buildRawXmlXml($command)
        );
    }

    public function testBuildRawXmlXmlMultipleCommands()
    {
        $command = new RawXmlCommand();
        $command->addCommand('<add><doc><field name="id">1</field></doc></add>');
        $command->addCommand('<add><doc><field name="id">2</field></doc></add>');

        $this->assertSame(
            '<add><doc><field name="id">1</field></doc></add><add><doc><field name="id">2</field></doc></add>',
            $this->builder->buildRawXmlXml($command)
        );
    }

    public function testBuildRawXmlXmlGroupedCommands()
    {
        $command = new RawXmlCommand();
        $command->addCommand('<update><add><doc><field name="id">1</field></doc></add><add><doc><field name="id">2</field></doc></add></update>');

        $this->assertSame(
            '<add><doc><field name="id">1</field></doc></add><add><doc><field name="id">2</field></doc></add>',
            $this->builder->buildRawXmlXml($command)
        );
    }

    public function testBuildRawXmlXmlGroupedCommandsWithCommentsInsignificantWhitespace()
    {
        $command = new RawXmlCommand();
        $command->addCommand(' <update ><add><doc><field name="id">1</field></doc></add><add><doc><field name="id">2</field></doc></add></update> ');

        $this->assertSame(
            '<add><doc><field name="id">1</field></doc></add><add><doc><field name="id">2</field></doc></add>',
            $this->builder->buildRawXmlXml($command)
        );
    }

    public function testBuildRawXmlXmlGroupedCommandsWithComments()
    {
        $command = new RawXmlCommand();
        $command->addCommand('<!-- comment --><update><add><doc><field name="id">1</field></doc></add><add><doc><field name="id">2</field></doc></add></update><!-- -->');

        $this->assertSame(
            '<add><doc><field name="id">1</field></doc></add><add><doc><field name="id">2</field></doc></add>',
            $this->builder->buildRawXmlXml($command)
        );
    }

    public function testCompleteRequest()
    {
        $this->query->addDeleteById(1);
        $this->query->addRollback();
        $this->query->addDeleteQuery('*:*');
        $this->query->addDocument(new Document(['id' => 1]));
        $this->query->addRawXmlCommand('<add><doc><field name="id">2</field></doc></add>');
        $this->query->addCommit();
        $this->query->addOptimize();

        $this->assertSame(
            '<update>'
            .'<delete><id>1</id></delete>'
            .'<rollback/>'
            .'<delete><query>*:*</query></delete>'
            .'<add><doc><field name="id">1</field></doc></add>'
            .'<add><doc><field name="id">2</field></doc></add>'
            .'<commit/>'
            .'<optimize/>'
            .'</update>',
            $this->builder->getRawData($this->query)
        );
    }
}

class UnsupportedCommand extends AbstractCommand
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'unsupported';
    }
}
