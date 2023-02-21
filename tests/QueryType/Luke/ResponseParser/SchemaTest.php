<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Luke\Query;
use Solarium\QueryType\Luke\ResponseParser\Schema as ResponseParser;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Info\Info;
use Solarium\QueryType\Luke\Result\Result;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicBasedField;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicField;
use Solarium\QueryType\Luke\Result\Schema\Field\Field;
use Solarium\QueryType\Luke\Result\Schema\Field\WildcardField;
use Solarium\QueryType\Luke\Result\Schema\Schema;
use Solarium\QueryType\Luke\Result\Schema\Type\IndexAnalyzer;
use Solarium\QueryType\Luke\Result\Schema\Type\QueryAnalyzer;

class SchemaTest extends TestCase
{
    use IndexDataTrait;
    use InfoDataTrait;
    use SchemaDataTrait;

    public function testParse(): Schema
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'schema' => $this->getSchemaData(),
            'info' => $this->getInfoData(),
        ];

        $query = new Query();
        $query->setShow(Query::SHOW_SCHEMA);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertInstanceOf(Index::class, $result['indexResult']);
        $this->assertInstanceOf(Schema::class, $result['schemaResult']);
        $this->assertNull($result['docResult']);
        $this->assertNull($result['fieldsResult']);
        $this->assertInstanceOf(Info::class, $result['infoResult']);

        return $result['schemaResult'];
    }

    /**
     * @depends testParse
     */
    public function testFields(Schema $schema)
    {
        $fields = $schema->getFields();

        $this->assertCount(8, $fields);

        $this->assertSame('flags_a', $fields['flags_a']->getName());

        $this->assertSame('type_untokenized', (string) $fields['flags_a']->getType());

        $this->assertSame('I-SDU-------------', (string) $fields['uniquekey']->getFlags());

        $this->assertNull($fields['flags_a']->getRequired());
        $this->assertFalse($fields['flags_a']->isRequired());
        $this->assertTrue($fields['required']->getRequired());
        $this->assertTrue($fields['required']->isRequired());

        $this->assertNull($fields['flags_a']->getDefault());
        $this->assertSame('0.0', $fields['default']->getDefault());

        $this->assertNull($fields['flags_a']->getUniqueKey());
        $this->assertFalse($fields['flags_a']->isUniqueKey());
        $this->assertTrue($fields['uniquekey']->getUniqueKey());
        $this->assertTrue($fields['uniquekey']->isUniqueKey());

        $this->assertNull($fields['flags_a']->getPositionIncrementGap());
        $this->assertSame(100, $fields['pos_inc_gap']->getPositionIncrementGap());

        $this->assertSame([], $fields['copy_to']->getCopyDests());
        $this->assertSame('copy_to,dynamic_copy_to', implode(',', $fields['copy_from']->getCopyDests()));

        $this->assertSame([], $fields['copy_from']->getCopySources());
        $this->assertSame('copy_from,dynamic_copy_from,*_copy_from,*_wildcard', implode(',', $fields['copy_to']->getCopySources()));

        $this->assertSame('flags_a', (string) $fields['flags_a']);
    }

    /**
     * @depends testParse
     */
    public function testFieldFlags(Schema $schema)
    {
        $flags = $schema->getField('flags_a')->getFlags();

        $this->assertSame('I-S-U-V-p-O-P-L-f-', (string) $flags);

        $this->assertTrue($flags->isIndexed());
        $this->assertFalse($flags->isTokenized());
        $this->assertTrue($flags->isStored());
        $this->assertFalse($flags->isDocValues());
        $this->assertTrue($flags->isUninvertible());
        $this->assertFalse($flags->isMultiValued());
        $this->assertTrue($flags->isTermVectors());
        $this->assertFalse($flags->isTermOffsets());
        $this->assertTrue($flags->isTermPositions());
        $this->assertFalse($flags->isTermPayloads());
        $this->assertTrue($flags->isOmitNorms());
        $this->assertFalse($flags->isOmitTermFreqAndPositions());
        $this->assertTrue($flags->isOmitPositions());
        $this->assertFalse($flags->isStoreOffsetsWithPositions());
        $this->assertTrue($flags->isLazy());
        $this->assertFalse($flags->isBinary());
        $this->assertTrue($flags->isSortMissingFirst());
        $this->assertFalse($flags->isSortMissingLast());

        $flags = $schema->getField('flags_b')->getFlags();

        $this->assertSame('-T-D-M-o-y-F-H-B-l', (string) $flags);

        $this->assertFalse($flags->isIndexed());
        $this->assertTrue($flags->isTokenized());
        $this->assertFalse($flags->isStored());
        $this->assertTrue($flags->isDocValues());
        $this->assertFalse($flags->isUninvertible());
        $this->assertTrue($flags->isMultiValued());
        $this->assertFalse($flags->isTermVectors());
        $this->assertTrue($flags->isTermOffsets());
        $this->assertFalse($flags->isTermPositions());
        $this->assertTrue($flags->isTermPayloads());
        $this->assertFalse($flags->isOmitNorms());
        $this->assertTrue($flags->isOmitTermFreqAndPositions());
        $this->assertFalse($flags->isOmitPositions());
        $this->assertTrue($flags->isStoreOffsetsWithPositions());
        $this->assertFalse($flags->isLazy());
        $this->assertTrue($flags->isBinary());
        $this->assertFalse($flags->isSortMissingFirst());
        $this->assertTrue($flags->isSortMissingLast());
    }

    /**
     * @depends testParse
     */
    public function testFieldCopyDests(Schema $schema)
    {
        $copyDests = $schema->getField('copy_from')->getCopyDests();

        $this->assertInstanceOf(Field::class, $copyDests[0]);
        $this->assertInstanceOf(DynamicBasedField::class, $copyDests[1]);
    }

    /**
     * @depends testParse
     */
    public function testFieldCopySources(Schema $schema)
    {
        $copySources = $schema->getField('copy_to')->getCopySources();

        $this->assertInstanceOf(Field::class, $copySources[0]);
        $this->assertInstanceOf(DynamicBasedField::class, $copySources[1]);
        $this->assertInstanceOf(DynamicField::class, $copySources[2]);
        $this->assertInstanceOf(WildcardField::class, $copySources[3]);
    }

    /**
     * @depends testParse
     */
    public function testDynamicFields(Schema $schema)
    {
        $dynamicFields = $schema->getDynamicFields();

        $this->assertCount(3, $dynamicFields);

        $this->assertSame('*_pos_inc_gap', $dynamicFields['*_pos_inc_gap']->getName());

        $this->assertSame('type_tokenized', (string) $dynamicFields['*_pos_inc_gap']->getType());

        $this->assertSame('ITSDU-------------', (string) $dynamicFields['*_pos_inc_gap']->getFlags());

        $this->assertNull($dynamicFields['*_pos_inc_gap']->getRequired());
        $this->assertFalse($dynamicFields['*_pos_inc_gap']->isRequired());

        $this->assertNull($dynamicFields['*_pos_inc_gap']->getDefault());

        $this->assertNull($dynamicFields['*_pos_inc_gap']->getUniqueKey());
        $this->assertFalse($dynamicFields['*_pos_inc_gap']->isUniqueKey());

        $this->assertNull($dynamicFields['*_copy_to']->getPositionIncrementGap());
        $this->assertSame(200, $dynamicFields['*_pos_inc_gap']->getPositionIncrementGap());

        $this->assertSame([], $dynamicFields['*_copy_to']->getCopyDests());
        $this->assertSame('copy_to,dynamic_copy_to,*_copy_to', implode(',', $dynamicFields['*_copy_from']->getCopyDests()));

        $this->assertSame([], $dynamicFields['*_copy_from']->getCopySources());
        $this->assertSame('*_copy_from,*_wildcard', implode(',', $dynamicFields['*_copy_to']->getCopySources()));

        $this->assertSame('*_pos_inc_gap', (string) $dynamicFields['*_pos_inc_gap']);
    }

    /**
     * @depends testParse
     */
    public function testDynamicFieldFlags(Schema $schema)
    {
        // flags are covered exhaustively in testFieldFlags()
        $this->assertTrue($schema->getDynamicField('*_pos_inc_gap')->getFlags()->isTokenized());
        $this->assertFalse($schema->getDynamicField('*_copy_to')->getFlags()->isTokenized());
    }

    /**
     * @depends testParse
     */
    public function testDynamicFieldCopyDests(Schema $schema)
    {
        $copyDests = $schema->getDynamicField('*_copy_from')->getCopyDests();

        $this->assertInstanceOf(Field::class, $copyDests[0]);
        $this->assertInstanceOf(DynamicBasedField::class, $copyDests[1]);
        $this->assertInstanceOf(DynamicField::class, $copyDests[2]);
    }

    /**
     * @depends testParse
     */
    public function testDynamicFieldCopySources(Schema $schema)
    {
        $copySources = $schema->getDynamicField('*_copy_to')->getCopySources();

        $this->assertInstanceOf(DynamicField::class, $copySources[0]);
        $this->assertInstanceOf(WildcardField::class, $copySources[1]);
    }

    /**
     * @depends testParse
     */
    public function testUniqueKeyField(Schema $schema)
    {
        $this->assertSame('uniquekey', (string) $schema->getUniqueKeyField());
    }

    /**
     * @depends testParse
     */
    public function testSimilarity(Schema $schema)
    {
        $similarity = $schema->getSimilarity();

        $this->assertSame('org.example.SchemaSimilarityFactory$SchemaSimilarity', $similarity->getClassName());
        $this->assertSame('Similarity details.', $similarity->getDetails());
    }

    /**
     * @depends testParse
     */
    public function testTypes(Schema $schema)
    {
        $types = $schema->getTypes();

        $this->assertCount(3, $types);

        $this->assertSame('type_tokenized', $types['type_tokenized']->getName());

        $this->assertSame([], $types['type_similarity']->getFields());
        $this->assertSame('flags_b,pos_inc_gap,*_pos_inc_gap', implode(',', $types['type_tokenized']->getFields()));

        $this->assertFalse($types['type_untokenized']->isTokenized());
        $this->assertTrue($types['type_tokenized']->isTokenized());

        $this->assertSame('org.example.TestField', $types['type_untokenized']->getClassName());

        $this->assertSame('org.example.TestFieldType$DefaultAnalyzer', (string) $types['type_untokenized']->getIndexAnalyzer());

        $this->assertSame('org.example.TokenizerChain', (string) $types['type_tokenized']->getQueryAnalyzer());

        $this->assertSame('', (string) $types['type_tokenized']->getSimilarity());
        $this->assertSame('org.example.TestSimilarity', (string) $types['type_similarity']->getSimilarity());

        $this->assertSame('type_tokenized', (string) $types['type_tokenized']);
    }

    /**
     * @depends testParse
     */
    public function testIndexAnalyzer(Schema $schema): IndexAnalyzer
    {
        $indexAnalyzer = $schema->getType('type_untokenized')->getIndexAnalyzer();

        $this->assertSame('org.example.TestFieldType$DefaultAnalyzer', $indexAnalyzer->getClassName());
        $this->assertSame([], $indexAnalyzer->getCharFilters());
        $this->assertNull($indexAnalyzer->getTokenizer());
        $this->assertSame([], $indexAnalyzer->getFilters());

        $indexAnalyzer = $schema->getType('type_tokenized')->getIndexAnalyzer();

        $this->assertSame('org.example.TokenizerChain', $indexAnalyzer->getClassName());
        $this->assertSame('FirstCharFilterFactory,NextCharFilterFactory', implode(',', $indexAnalyzer->getCharFilters()));
        $this->assertSame('org.example.TestTokenizerFactory', (string) $indexAnalyzer->getTokenizer());
        $this->assertSame('FirstFilterFactory,NextFilterFactory', implode(',', $indexAnalyzer->getFilters()));

        return $indexAnalyzer;
    }

    /**
     * @depends testIndexAnalyzer
     */
    public function testIndexAnalyzerCharFilters(IndexAnalyzer $indexAnalyzer)
    {
        $charFilter = $indexAnalyzer->getCharFilters()['FirstCharFilterFactory'];

        $this->assertSame(
            [
                'class' => 'solr.FirstCharFilterFactory',
                'luceneMatchVersion' => '1.2.3',
            ],
            $charFilter->getArgs()
        );
        $this->assertSame('org.example.FirstCharFilterFactory', $charFilter->getClassName());
    }

    /**
     * @depends testIndexAnalyzer
     */
    public function testIndexAnalyzerTokenizer(IndexAnalyzer $indexAnalyzer)
    {
        $tokenizer = $indexAnalyzer->getTokenizer();

        $this->assertSame('org.example.TestTokenizerFactory', $tokenizer->getClassName());
        $this->assertSame(
            [
                'class' => 'solr.TestTokenizerFactory',
                'luceneMatchVersion' => '1.2.3',
            ],
            $tokenizer->getArgs()
        );
    }

    /**
     * @depends testIndexAnalyzer
     */
    public function testIndexAnalyzerFilters(IndexAnalyzer $indexAnalyzer)
    {
        $filter = $indexAnalyzer->getFilters()['FirstFilterFactory'];

        $this->assertSame(
            [
                'class' => 'solr.FirstFilterFactory',
                'luceneMatchVersion' => '1.2.3',
            ],
            $filter->getArgs()
        );
        $this->assertSame('org.example.FirstFilterFactory', $filter->getClassName());
    }

    /**
     * @depends testParse
     */
    public function testQueryAnalyzer(Schema $schema): QueryAnalyzer
    {
        $queryAnalyzer = $schema->getType('type_untokenized')->getQueryAnalyzer();

        $this->assertSame('org.example.TestFieldType$DefaultAnalyzer', $queryAnalyzer->getClassName());
        $this->assertSame([], $queryAnalyzer->getCharFilters());
        $this->assertNull($queryAnalyzer->getTokenizer());
        $this->assertSame([], $queryAnalyzer->getFilters());

        $queryAnalyzer = $schema->getType('type_tokenized')->getQueryAnalyzer();

        $this->assertSame('org.example.TokenizerChain', $queryAnalyzer->getClassName());
        $this->assertSame('FirstCharFilterFactory,NextCharFilterFactory', implode(',', $queryAnalyzer->getCharFilters()));
        $this->assertSame('org.example.TestTokenizerFactory', (string) $queryAnalyzer->getTokenizer());
        $this->assertSame('FirstFilterFactory,NextFilterFactory', implode(',', $queryAnalyzer->getFilters()));

        return $queryAnalyzer;
    }

    /**
     * @depends testQueryAnalyzer
     */
    public function testQueryAnalyzerCharFilters(QueryAnalyzer $queryAnalyzer)
    {
        $charFilter = $queryAnalyzer->getCharFilters()['FirstCharFilterFactory'];

        $this->assertSame(
            [
                'class' => 'solr.FirstCharFilterFactory',
                'luceneMatchVersion' => '1.2.3',
            ],
            $charFilter->getArgs()
        );
        $this->assertSame('org.example.FirstCharFilterFactory', $charFilter->getClassName());
    }

    /**
     * @depends testQueryAnalyzer
     */
    public function testQueryAnalyzerTokenizer(QueryAnalyzer $queryAnalyzer)
    {
        $tokenizer = $queryAnalyzer->getTokenizer();

        $this->assertSame('org.example.TestTokenizerFactory', $tokenizer->getClassName());
        $this->assertSame(
            [
                'class' => 'solr.TestTokenizerFactory',
                'luceneMatchVersion' => '1.2.3',
            ],
            $tokenizer->getArgs()
        );
    }

    /**
     * @depends testQueryAnalyzer
     */
    public function testQueryAnalyzerFilters(QueryAnalyzer $queryAnalyzer)
    {
        $filter = $queryAnalyzer->getFilters()['FirstFilterFactory'];

        $this->assertSame(
            [
                'class' => 'solr.FirstFilterFactory',
                'luceneMatchVersion' => '1.2.3',
            ],
            $filter->getArgs()
        );
        $this->assertSame('org.example.FirstFilterFactory', $filter->getClassName());
    }

    /**
     * @depends testParse
     */
    public function testTypeSimilarity(Schema $schema)
    {
        $similarity = $schema->getType('type_untokenized')->getSimilarity();

        $this->assertNull($similarity->getClassName());
        $this->assertNull($similarity->getDetails());

        $similarity = $schema->getType('type_similarity')->getSimilarity();

        $this->assertSame('org.example.TestSimilarity', $similarity->getClassName());
        $this->assertSame('Type similarity details.', $similarity->getDetails());
    }

    /**
     * @depends testParse
     */
    public function testReferences(Schema $schema)
    {
        $this->assertSame($schema->getType('type_untokenized'), $schema->getField('flags_a')->getType());

        $this->assertSame($schema->getField('copy_to'), $schema->getField('copy_from')->getCopyDests()[0]);
        $this->assertSame($schema->getDynamicField('*_copy_to'), $schema->getField('copy_from')->getCopyDests()[1]->getDynamicBase());

        $this->assertSame($schema->getField('copy_from'), $schema->getField('copy_to')->getCopySources()[0]);
        $this->assertSame($schema->getDynamicField('*_copy_from'), $schema->getField('copy_to')->getCopySources()[1]->getDynamicBase());
        $this->assertSame($schema->getDynamicField('*_copy_from'), $schema->getField('copy_to')->getCopySources()[2]);

        $this->assertSame($schema->getType('type_tokenized'), $schema->getDynamicField('*_pos_inc_gap')->getType());

        $this->assertSame($schema->getField('copy_to'), $schema->getDynamicField('*_copy_from')->getCopyDests()[0]);
        $this->assertSame($schema->getDynamicField('*_copy_to'), $schema->getDynamicField('*_copy_from')->getCopyDests()[1]->getDynamicBase());
        $this->assertSame($schema->getDynamicField('*_copy_to'), $schema->getDynamicField('*_copy_from')->getCopyDests()[2]);

        $this->assertSame($schema->getDynamicField('*_copy_from'), $schema->getDynamicField('*_copy_to')->getCopySources()[0]);

        // dynamic based fields and wildcards don't have a direct definition to reference in the schema, but multiple occurrences still reference a single instance
        $this->assertSame($schema->getField('copy_from')->getCopyDests()[1], $schema->getDynamicField('*_copy_from')->getCopyDests()[1]);
        $this->assertSame($schema->getField('copy_to')->getCopySources()[3], $schema->getDynamicField('*_copy_to')->getCopySources()[1]);

        $this->assertSame($schema->getField('uniquekey'), $schema->getUniqueKeyField());

        $this->assertSame($schema->getField('flags_b'), $schema->getType('type_tokenized')->getFields()[0]);
        $this->assertSame($schema->getDynamicField('*_pos_inc_gap'), $schema->getType('type_tokenized')->getFields()[2]);
    }

    /**
     * A schema isn't required to have a uniqueKey field.
     */
    public function testParseSchemaWithoutUniqueKey()
    {
        $schemaData = $this->getSchemaData();
        $schemaData['uniqueKeyField'] = null;
        unset($schemaData['fields']['uniquekey']['uniqueKey']);

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'schema' => $schemaData,
            'info' => $this->getInfoData(),
        ];

        $query = new Query();
        $query->setShow(Query::SHOW_SCHEMA);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertNull($result['schemaResult']->getUniqueKeyField());
    }

    /**
     * Test a copyField dest or source that isn't a wildcard pattern and doesn't match an explicit field or dynamicField.
     *
     * This will never happen with real data because Solr refuses to load a schema with such a copyField definition, stating:
     *  'undefined_field' is not a glob and doesn't match any explicit field or dynamicField.
     *
     * We still need a code path covering this eventuality to satisfy the static analysis rules.
     *
     * @testWith ["copy_from", "copyDests"]
     *           ["copy_to", "copySources"]
     */
    public function testParseUndefinedCopyField(string $fieldName, string $destOrSource)
    {
        $schemaData = $this->getSchemaData();
        $schemaData['fields'][$fieldName][$destOrSource][] = 'undefined_field';

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'schema' => $schemaData,
            'info' => $this->getInfoData(),
        ];

        $query = new Query();
        $query->setShow(Query::SHOW_SCHEMA);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Field name undefined_field doesn\'t match a dynamicField name.');
        $parser = new ResponseParser();
        $parser->parse($resultStub);
    }
}
