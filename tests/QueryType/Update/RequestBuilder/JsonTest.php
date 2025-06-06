<?php

namespace Solarium\Tests\QueryType\Update\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Query\Command\Commit as CommitCommand;
use Solarium\QueryType\Update\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\Update\Query\Command\Optimize as OptimizeCommand;
use Solarium\QueryType\Update\Query\Command\RawXml as RawXmlCommand;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\RequestBuilder\Json as JsonRequestBuilder;

class JsonTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var JsonRequestBuilder
     */
    protected $builder;

    public function setUp(): void
    {
        $this->query = new Query();
        $this->query->setRequestFormat(Query::REQUEST_FORMAT_JSON);

        $this->builder = new JsonRequestBuilder();
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
            Request::CONTENT_TYPE_APPLICATION_JSON,
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

    /**
     * Update queries with a different input encoding than the default UTF-8
     * aren't supported by the JSON request format.
     *
     * @see https://www.rfc-editor.org/rfc/rfc8259#section-8.1
     */
    public function testBuildWithInputEncoding()
    {
        // not setting an input encoding is fine
        $this->builder->build($this->query);

        $this->query->setInputEncoding('utf-8');

        // setting UTF-8 input encoding explicitly is fine (but superfluous)
        $this->builder->build($this->query);

        $this->query->setInputEncoding('us-ascii');

        // setting a different input encoding is prohibited
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('JSON requests can only be UTF-8');
        $this->builder->build($this->query);
    }

    public function testBuildWithUnsupportedCommandType()
    {
        $this->query->add(null, new RawXmlCommand());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported command type');
        $this->builder->build($this->query);
    }

    public function testBuildAddJsonNoParamsSingleDocument()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 1]));
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithScalarValues()
    {
        $command = new AddCommand();
        $command->addDocument(new Document([
            'id' => 1,
            'noid' => -5,
            'name' => 'test',
            'price' => 3.14,
            'discount' => -2.72,
            'visible' => true,
            'forsale' => false,
            'UTF8' => 'ΑΒΓαβγ АБВабв أبجد אבג カタカナ 漢字',
        ]));
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "noid": -5,
                        "name": "test",
                        "price": 3.14,
                        "discount": -2.72,
                        "visible": true,
                        "forsale": false,
                        "UTF8": "\u0391\u0392\u0393\u03b1\u03b2\u03b3 \u0410\u0411\u0412\u0430\u0431\u0432 \u0623\u0628\u062c\u062f \u05d0\u05d1\u05d2 \u30ab\u30bf\u30ab\u30ca \u6f22\u5b57"
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithEmptyValues()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 0, 'empty_string' => '', 'empty_array' => [], 'array_of_empty_string' => [''], 'null' => null]));
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        // Empty strings must be added to the document as empty fields.
        // Empty arrays and NULL values can be (but don't have to be) skipped because Solr ignores them anyway.
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 0,
                        "empty_string": "",
                        "empty_array": [],
                        "array_of_empty_string": [""]
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithParams()
    {
        $command = new AddCommand(['overwrite' => true, 'commitwithin' => 100]);
        $command->addDocument(new Document(['id' => 1]));
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1
                    },
                    "overwrite": true,
                    "commitWithin": 100
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonMultivalueField()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => [1, 2, 3], 'text' => ['test < 123 '.chr(8).' test', 'test '.chr(15).' 123 > test']]));
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add":{
                    "doc": {
                        "id": [1, 2, 3],
                        "text": ["test < 123 \b test", "test \u000f 123 > test"]
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonMultivalueFieldWithEmptyArray()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => [1, 2, 3], 'text' => []]));
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": [1, 2, 3],
                        "text": []
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonMultivalueFieldWithNonConsecutiveArrayIndices()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => [0 => 1, 4 => 2, 6 => 3], 'text' => [1 => 'a', 2 => 'b', 3 => 'c']]));
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": [1, 2, 3],
                        "text": ["a", "b", "c"]
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithEmptyStrings()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => '', 'text' => ['']]));
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": "",
                        "text": [""]
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithSingleNestedDocument()
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
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": {
                            "nested_id": 42,
                            "customer_ids": [15, 16]
                        },
                        "text": "test < 123 > test"
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithNestedDocuments()
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
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": [
                            {
                                "nested_id": 42,
                                "customer_ids": [15, 16]
                            },
                            {
                                "nested_id": "XLII",
                                "customer_ids": [17, 18]
                            },
                            2,
                            "foo"
                        ],
                        "text": "test < 123 > test"
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithSingleAnonymouslyNestedDocument()
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
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1701,
                        "cat": ["A", "D"],
                        "text": ":=._,<^>",
                        "_childDocuments_": {
                            "id": "1701-D",
                            "cat": ["D"]
                        }
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithAnonymouslyNestedDocuments()
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
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1701,
                        "cat": ["A", "D"],
                        "text": ":=._,<^>",
                        "_childDocuments_": [
                            {
                                "id": "1701-A",
                                "cat": ["A"]
                            },
                            {
                                "id": "1701-D",
                                "cat": ["D"]
                            }
                        ]
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    /**
     * Document boosts aren't supported in JSON update requests.
     *
     * @deprecated No longer supported since Solr 7
     */
    public function testBuildAddJsonSingleDocumentWithBoost()
    {
        $doc = new Document(['id' => 1]);
        $doc->setBoost(2.5);
        $command = new AddCommand();
        $command->addDocument($doc);
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    /**
     * Field boosts aren't supported in JSON update requests.
     */
    public function testBuildAddJsonSingleDocumentWithFieldBoost()
    {
        $doc = new Document(['id' => 1]);
        $doc->setFieldBoost('id', 2.1);
        $command = new AddCommand();
        $command->addDocument($doc);
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonMultipleDocuments()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 1]));
        $command->addDocument(new Document(['id' => 2]));
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(2, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1
                    }
                }
            }',
            '{'.$json[0].'}'
        );
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 2
                    }
                }
            }',
            '{'.$json[1].'}'
        );
    }

    public function testBuildAddJsonWithFieldModifiers()
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
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "category": { "add": 123 },
                        "name": { "set": "test" },
                        "skills": { "set": null },
                        "parts": { "set": [] },
                        "stock": { "inc": 2 }
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithFieldModifiersAndMultivalueFields()
    {
        $doc = new Document();
        $doc->setKey('id', 1);
        $doc->addField('category', 123, null, Document::MODIFIER_ADD);
        $doc->addField('category', 234, null, Document::MODIFIER_ADD);
        $doc->addField('name', 'test', 2.3, Document::MODIFIER_SET);
        $doc->setField('stock', 2, null, Document::MODIFIER_INC);

        $command = new AddCommand();
        $command->addDocument($doc);
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "category": { "add": [123, 234] },
                        "name": { "set": "test" },
                        "stock": { "inc": 2 }
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithVersionedDocument()
    {
        $doc = new Document(['id' => 1]);
        $doc->setVersion(42);

        $command = new AddCommand();
        $command->addDocument($doc);
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "_version_": 42
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithVersionMustNotExist()
    {
        $doc = new Document(['id' => 1]);
        $doc->setVersion(Document::VERSION_MUST_NOT_EXIST);

        $command = new AddCommand();
        $command->addDocument($doc);
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "_version_": -1
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithDateTime()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'datetime' => new \DateTime('2013-01-15 14:41:58', new \DateTimeZone('+02:00'))])
        );
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "datetime": "2013-01-15T12:41:58Z"
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithDateTimeImmutable()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'datetime' => new \DateTimeImmutable('2013-01-15 14:41:58', new \DateTimeZone('-06:00'))])
        );
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "datetime": "2013-01-15T20:41:58Z"
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithMultivalueDateTimes()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'datetime' => [new \DateTime('2013-01-15 14:41:58', new \DateTimeZone('-02:00')), new \DateTimeImmutable('2014-02-16 15:42:59', new \DateTimeZone('+06:00'))]])
        );
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "datetime": [
                            "2013-01-15T16:41:58Z",
                            "2014-02-16T09:42:59Z"
                        ]
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithJsonSerializableObject()
    {
        $value = new class() implements \JsonSerializable {
            public function jsonSerialize(): mixed
            {
                return 'My value';
            }
        };

        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'my_field' => $value])
        );
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "my_field": "My value"
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithStringableObject()
    {
        $value = new class() implements \Stringable {
            public function __toString(): string
            {
                return 'My value';
            }
        };

        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'my_field' => $value])
        );
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "my_field": "My value"
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    /**
     * Test that \Stringable takes precedence over \JsonSerializable for
     * consistency across request format.
     */
    public function testBuildAddJsonWithJsonSerializableAndStringableObject()
    {
        $value = new class() implements \JsonSerializable, \Stringable {
            public function jsonSerialize(): mixed
            {
                return 'My JSON value';
            }

            public function __toString(): string
            {
                return 'My string value';
            }
        };

        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'my_field' => $value])
        );
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "my_field": "My string value"
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    /**
     * Test that the \Stringable precedence on an also \JsonSerializable object
     * can be overridden by explicitly calling jsonSerialize().
     */
    public function testBuildAddJsonWithJsonSerializableAndStringableObjectWithExplicitJsonSerialize()
    {
        $value = new class() implements \JsonSerializable, \Stringable {
            public function jsonSerialize(): mixed
            {
                return 'My JSON value';
            }

            public function __toString(): string
            {
                return 'My string value';
            }
        };

        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'my_field' => $value->jsonSerialize()])
        );
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "id": 1,
                        "my_field": "My JSON value"
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildAddJsonWithFieldModifierAndNullValue()
    {
        $doc = new Document();
        $doc->setKey('employeeId', '05991');
        $doc->addField('skills', null, null, Document::MODIFIER_SET);

        $command = new AddCommand();
        $command->addDocument($doc);
        $json = [];

        $this->builder->buildAddJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "add": {
                    "doc": {
                        "employeeId": "05991",
                        "skills": { "set": null }
                    }
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildDeleteJsonEmpty()
    {
        $command = new DeleteCommand();
        $json = [];

        $this->builder->buildDeleteJson($command, $json);

        $this->assertCount(0, $json);
    }

    public function testBuildDeleteJsonSingleId()
    {
        $command = new DeleteCommand();
        $command->addId(123);
        $json = [];

        $this->builder->buildDeleteJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "delete": [123]
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildDeleteJsonMultipleIds()
    {
        $command = new DeleteCommand();
        $command->addId(123);
        $command->addId(456);
        $json = [];

        $this->builder->buildDeleteJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "delete": [123, 456]
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildDeleteJsonSingleQuery()
    {
        $command = new DeleteCommand();
        $command->addQuery('*:*');
        $json = [];

        $this->builder->buildDeleteJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "delete": {
                    "query": "*:*"
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildDeleteJsonMultipleQueries()
    {
        $command = new DeleteCommand();
        $command->addQuery('published:false');
        $command->addQuery('id:[10 TO 20]');
        $json = [];

        $this->builder->buildDeleteJson($command, $json);

        $this->assertCount(2, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "delete": {
                    "query": "published:false"
                }
            }',
            '{'.$json[0].'}'
        );
        $this->assertJsonStringEqualsJsonString(
            '{
                "delete": {
                    "query": "id:[10 TO 20]"
                }
            }',
            '{'.$json[1].'}'
        );
    }

    public function testBuildDeleteJsonIdsAndQueries()
    {
        $command = new DeleteCommand();
        $command->addId(123);
        $command->addId(456);
        $command->addQuery('published:false');
        $command->addQuery('id:[10 TO 20]');
        $json = [];

        $this->builder->buildDeleteJson($command, $json);

        $this->assertCount(3, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "delete": [123, 456]
            }',
            '{'.$json[0].'}'
        );
        $this->assertJsonStringEqualsJsonString(
            '{
                "delete": {
                    "query": "published:false"
                }
            }',
            '{'.$json[1].'}'
        );
        $this->assertJsonStringEqualsJsonString(
            '{
                "delete": {
                    "query": "id:[10 TO 20]"
                }
            }',
            '{'.$json[2].'}'
        );
    }

    public function testBuildOptimizeJson()
    {
        $command = new OptimizeCommand();
        $json = [];

        $this->builder->buildOptimizeJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "optimize": {}
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildOptimizeJsonWithParams()
    {
        $command = new OptimizeCommand(['softcommit' => true, 'waitsearcher' => false, 'maxsegments' => 10]);
        $json = [];

        $this->builder->buildOptimizeJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "optimize": {
                    "softCommit": true,
                    "waitSearcher": false,
                    "maxSegments": 10
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildCommitJson()
    {
        $command = new CommitCommand();
        $json = [];

        $this->builder->buildCommitJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "commit": {}
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildCommitJsonWithParams()
    {
        $command = new CommitCommand(['softcommit' => true, 'waitsearcher' => false, 'expungedeletes' => true]);
        $json = [];

        $this->builder->buildCommitJson($command, $json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "commit": {
                    "expungeDeletes": true,
                    "softCommit": true,
                    "waitSearcher": false
                }
            }',
            '{'.$json[0].'}'
        );
    }

    public function testBuildRollbackJson()
    {
        $json = [];

        $this->builder->buildRollbackJson($json);

        $this->assertCount(1, $json);
        $this->assertJsonStringEqualsJsonString(
            '{
                "rollback": {}
            }',
            '{'.$json[0].'}'
        );
    }

    public function testCompleteRequest()
    {
        $this->query->addDeleteById(1);
        $this->query->addDeleteById(2);
        $this->query->addRollback();
        $this->query->addDeleteQuery('*:*');
        $this->query->addDocument(new Document(['id' => 1]));
        $this->query->addCommit();
        $this->query->addOptimize();

        $this->assertJsonStringEqualsJsonString(
            '{
                "delete": [1, 2],
                "rollback": {},
                "delete": {
                    "query": "*:*"
                },
                "add": {
                    "doc": {
                        "id": 1
                    }
                },
                "commit": {},
                "optimize": {}
            }',
            $this->builder->getRawData($this->query)
        );
    }
}
