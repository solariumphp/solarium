<?php

namespace Solarium\Tests\QueryType\Update\RequestBuilder;

use CBOR\CBOREncoder;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Command\AbstractCommand;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Query\Command\Commit as CommitCommand;
use Solarium\QueryType\Update\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\Update\Query\Command\Optimize as OptimizeCommand;
use Solarium\QueryType\Update\Query\Command\RawXml as RawXmlCommand;
use Solarium\QueryType\Update\Query\Command\Rollback as RollbackCommand;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\RequestBuilder\Cbor as CborRequestBuilder;

class CborTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var CborRequestBuilder
     */
    protected $builder;

    public function setUp(): void
    {
        $this->query = new Query();
        $this->query->setRequestFormat(Query::REQUEST_FORMAT_CBOR);

        $this->builder = new CborRequestBuilder();
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
            Request::CONTENT_TYPE_APPLICATION_CBOR,
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
     * aren't supported by the CBOR request format.
     *
     * @see https://www.rfc-editor.org/rfc/rfc8949#section-3.1-2.8
     * @see https://www.rfc-editor.org/rfc/rfc8949#section-5.3.1-2.4
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
        $this->expectExceptionMessage('CBOR requests can only contain UTF-8 strings');
        $this->builder->build($this->query);
    }

    /**
     * @dataProvider unsupportedCommandProvider
     */
    public function testBuildWithUnsupportedCommandType(AbstractCommand $command)
    {
        $this->query->add(null, $command);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported command type, CBOR queries can only be used to add documents');
        $this->builder->build($this->query);
    }

    public static function unsupportedCommandProvider(): array
    {
        return [
            [new CommitCommand()],
            [new DeleteCommand()],
            [new OptimizeCommand()],
            [new RawXmlCommand()],
            [new RollbackCommand()],
        ];
    }

    public function testBuildAddCborNoParamsSingleDocument()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 1]));
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithScalarValues()
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
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1,
                    "noid": -5,
                    "name": "test",
                    "price": 3.14,
                    "discount": -2.72,
                    "visible": true,
                    "forsale": false,
                    "UTF8": "\u0391\u0392\u0393\u03b1\u03b2\u03b3 \u0410\u0411\u0412\u0430\u0431\u0432 \u0623\u0628\u062c\u062f \u05d0\u05d1\u05d2 \u30ab\u30bf\u30ab\u30ca \u6f22\u5b57"
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithEmptyValues()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 0, 'empty_string' => '', 'empty_array' => [], 'array_of_empty_string' => [''], 'null' => null]));
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        // Empty strings must be added to the document as empty fields.
        // Empty arrays and NULL values can be (but don't have to be) skipped because Solr ignores them anyway.
        $this->assertEquals(
            json_decode('[
                {
                    "id": 0,
                    "empty_string": "",
                    "empty_array": [],
                    "array_of_empty_string": [""]
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithParams()
    {
        $command = new AddCommand(['overwrite' => true, 'commitwithin' => 100]);
        $command->addDocument(new Document(['id' => 1]));
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertSame('true', $request->getParam('overwrite'));
        $this->assertSame(100, $request->getParam('commitWithin'));
        $this->assertEquals(
            json_decode('[
                {
                    "id": 1
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborMultipleCommandsWithDifferentParams()
    {
        $command = new AddCommand(['overwrite' => true, 'commitwithin' => 100]);
        $command->addDocument(new Document(['id' => 1]));
        $this->query->add(null, $command);
        $command = new AddCommand(['overwrite' => false, 'commitwithin' => 500]);
        $command->addDocument(new Document(['id' => 2]));
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        // last occurrence determines which params will be sent
        $this->assertSame('false', $request->getParam('overwrite'));
        $this->assertSame(500, $request->getParam('commitWithin'));
        $this->assertEquals(
            json_decode('[
                {
                    "id": 1
                },
                {
                    "id": 2
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborMultipleCommandsWithAndWithoutParams()
    {
        $command = new AddCommand(['overwrite' => true, 'commitwithin' => 100]);
        $command->addDocument(new Document(['id' => 1]));
        $this->query->add(null, $command);
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 2]));
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        // first occurrence is kept if no further params are set
        $this->assertSame('true', $request->getParam('overwrite'));
        $this->assertSame(100, $request->getParam('commitWithin'));
        $this->assertEquals(
            json_decode('[
                {
                    "id": 1
                },
                {
                    "id": 2
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborMultivalueField()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => [1, 2, 3], 'text' => ['test < 123 '.chr(8).' test', 'test '.chr(15).' 123 > test']]));
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": [1, 2, 3],
                    "text": ["test < 123 \b test", "test \u000f 123 > test"]
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborMultivalueFieldWithEmptyArray()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => [1, 2, 3], 'text' => []]));
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": [1, 2, 3],
                    "text": []
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborMultivalueFieldWithNonConsecutiveArrayIndices()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => [0 => 1, 4 => 2, 6 => 3], 'text' => [1 => 'a', 2 => 'b', 3 => 'c']]));
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": [1, 2, 3],
                    "text": ["a", "b", "c"]
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithEmptyStrings()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => '', 'text' => ['']]));
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": "",
                    "text": [""]
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithSingleNestedDocument()
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
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": {
                        "nested_id": 42,
                        "customer_ids": [15, 16]
                    },
                    "text": "test < 123 > test"
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithNestedDocuments()
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
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
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
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithSingleAnonymouslyNestedDocument()
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
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1701,
                    "cat": ["A", "D"],
                    "text": ":=._,<^>",
                    "_childDocuments_": {
                        "id": "1701-D",
                        "cat": ["D"]
                    }
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithAnonymouslyNestedDocuments()
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
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
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
            ]', true),
            $object
        );
    }

    /**
     * Document boosts aren't supported in CBOR update requests.
     *
     * @deprecated No longer supported since Solr 7
     */
    public function testBuildAddCborSingleDocumentWithBoost()
    {
        $doc = new Document(['id' => 1]);
        $doc->setBoost(2.5);
        $command = new AddCommand();
        $command->addDocument($doc);
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1
                }
            ]', true),
            $object
        );
    }

    /**
     * Field boosts aren't supported in CBOR update requests.
     */
    public function testBuildAddCborSingleDocumentWithFieldBoost()
    {
        $doc = new Document(['id' => 1]);
        $doc->setFieldBoost('id', 2.1);
        $command = new AddCommand();
        $command->addDocument($doc);
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborMultipleDocuments()
    {
        $command = new AddCommand();
        $command->addDocument(new Document(['id' => 1]));
        $command->addDocument(new Document(['id' => 2]));
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1
                },
                {
                    "id": 2
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithFieldModifiers()
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
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1,
                    "category": { "add": 123 },
                    "name": { "set": "test" },
                    "skills": { "set": null },
                    "parts": { "set": [] },
                    "stock": { "inc": 2 }
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithFieldModifiersAndMultivalueFields()
    {
        $doc = new Document();
        $doc->setKey('id', 1);
        $doc->addField('category', 123, null, Document::MODIFIER_ADD);
        $doc->addField('category', 234, null, Document::MODIFIER_ADD);
        $doc->addField('name', 'test', 2.3, Document::MODIFIER_SET);
        $doc->setField('stock', 2, null, Document::MODIFIER_INC);

        $command = new AddCommand();
        $command->addDocument($doc);
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1,
                    "category": { "add": [123, 234] },
                    "name": { "set": "test" },
                    "stock": { "inc": 2 }
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithVersionedDocument()
    {
        $doc = new Document(['id' => 1]);
        $doc->setVersion(42);

        $command = new AddCommand();
        $command->addDocument($doc);
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1,
                    "_version_": 42
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithVersionMustNotExist()
    {
        $doc = new Document(['id' => 1]);
        $doc->setVersion(Document::VERSION_MUST_NOT_EXIST);

        $command = new AddCommand();
        $command->addDocument($doc);
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1,
                    "_version_": -1
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithDateTime()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'datetime' => new \DateTime('2013-01-15 14:41:58', new \DateTimeZone('+02:00'))])
        );
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1,
                    "datetime": "2013-01-15T12:41:58Z"
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithDateTimeImmutable()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'datetime' => new \DateTimeImmutable('2013-01-15 14:41:58', new \DateTimeZone('-06:00'))])
        );
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1,
                    "datetime": "2013-01-15T20:41:58Z"
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithMultivalueDateTimes()
    {
        $command = new AddCommand();
        $command->addDocument(
            new Document(['id' => 1, 'datetime' => [new \DateTime('2013-01-15 14:41:58', new \DateTimeZone('-02:00')), new \DateTimeImmutable('2014-02-16 15:42:59', new \DateTimeZone('+06:00'))]])
        );
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "id": 1,
                    "datetime": [
                        "2013-01-15T16:41:58Z",
                        "2014-02-16T09:42:59Z"
                    ]
                }
            ]', true),
            $object
        );
    }

    public function testBuildAddCborWithFieldModifierAndNullValue()
    {
        $doc = new Document();
        $doc->setKey('employeeId', '05991');
        $doc->addField('skills', null, null, Document::MODIFIER_SET);

        $command = new AddCommand();
        $command->addDocument($doc);
        $this->query->add(null, $command);

        $request = $this->builder->build($this->query);
        $rawData = $request->getRawData();
        $object = CBOREncoder::decode($rawData);

        $this->assertEquals(
            json_decode('[
                {
                    "employeeId": "05991",
                    "skills": { "set": null }
                }
            ]', true),
            $object
        );
    }
}
