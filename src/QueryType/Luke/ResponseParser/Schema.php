<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\ResponseParser;

use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Luke\Result\FlagList;
use Solarium\QueryType\Luke\Result\Result;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicBasedField;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicField;
use Solarium\QueryType\Luke\Result\Schema\Field\Field;
use Solarium\QueryType\Luke\Result\Schema\Field\WildcardField;
use Solarium\QueryType\Luke\Result\Schema\Schema as SchemaResult;
use Solarium\QueryType\Luke\Result\Schema\Similarity;
use Solarium\QueryType\Luke\Result\Schema\Type\AbstractAnalyzer;
use Solarium\QueryType\Luke\Result\Schema\Type\CharFilter;
use Solarium\QueryType\Luke\Result\Schema\Type\Filter;
use Solarium\QueryType\Luke\Result\Schema\Type\IndexAnalyzer;
use Solarium\QueryType\Luke\Result\Schema\Type\QueryAnalyzer;
use Solarium\QueryType\Luke\Result\Schema\Type\Tokenizer;
use Solarium\QueryType\Luke\Result\Schema\Type\Type;
use Solarium\Support\Utility;

/**
 * Parse Luke schema response data.
 */
class Schema extends Index
{
    use InfoTrait;

    /**
     * Get result data for the response.
     *
     * @param Result $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = parent::parse($result);

        // parse 'info' first so 'schema' can use it for flag lookups
        $data['infoResult'] = $this->parseInfo($data['info']);
        $data['schemaResult'] = $this->parseSchema($data['schema']);

        return $data;
    }

    /**
     * @param array $schemaData
     *
     * @return SchemaResult
     */
    protected function parseSchema(array $schemaData): SchemaResult
    {
        $schema = new SchemaResult();

        $fields = $this->parseFields($schemaData['fields']);
        $dynamicFields = $this->parseFields($schemaData['dynamicFields'], true);
        $uniqueKeyField = $schemaData['uniqueKeyField'];

        $similarity = new Similarity();
        $similarity->setClassName($schemaData['similarity']['className']);
        $similarity->setDetails($schemaData['similarity']['details']);

        $types = $this->parseTypes($schemaData['types']);

        // linking it all together

        // link type first because it's necessary for DynamicField::createField()
        $this->linkFieldType($fields, $schemaData['fields'], $types);
        $this->linkFieldType($dynamicFields, $schemaData['dynamicFields'], $types);
        $this->linkTypeFields($types, $schemaData['types'], $fields, $dynamicFields);

        // store copyField sources that aren't in $fields or $dynamicFields
        $dynamicBasedFields = [];
        $wildcardFields = [];

        $this->linkFields($fields, $schemaData['fields'], $fields, $dynamicFields, $dynamicBasedFields, $wildcardFields);
        $this->linkFields($dynamicFields, $schemaData['dynamicFields'], $fields, $dynamicFields, $dynamicBasedFields, $wildcardFields);

        $schema->setFields($fields);
        $schema->setDynamicFields($dynamicFields);

        // a schema isn't required to have a <uniqueKey> field
        if (null !== $uniqueKeyField) {
            $schema->setUniqueKeyField($fields[$uniqueKeyField]);
        }

        $schema->setSimilarity($similarity);
        $schema->setTypes($types);

        return $schema;
    }

    /**
     * @param array $fieldData
     * @param bool  $dynamic
     *
     * @return Field[]|DynamicField[]
     */
    protected function parseFields(array $fieldData, bool $dynamic = false): array
    {
        $fieldClass = $dynamic ? DynamicField::class : Field::class;
        $fields = [];

        foreach ($fieldData as $name => $details) {
            $field = new $fieldClass($name);

            $field->setFlags(new FlagList($details['flags'], $this->key));
            $field->setRequired($details['required'] ?? null);
            $field->setDefault($details['default'] ?? null);
            $field->setUniqueKey($details['uniqueKey'] ?? null);
            $field->setPositionIncrementGap($details['positionIncrementGap'] ?? null);

            $fields[$name] = $field;
        }

        return $fields;
    }

    /**
     * @param array $typeData
     *
     * @return Type[]
     */
    protected function parseTypes(array $typeData): array
    {
        $types = [];

        foreach ($typeData as $name => $details) {
            $type = new Type($name);

            $indexAnalyzer = $this->parseAnalyzer($details['indexAnalyzer'], IndexAnalyzer::class);
            $queryAnalyzer = $this->parseAnalyzer($details['queryAnalyzer'], QueryAnalyzer::class);

            // similarity is empty for types that don't have a specific similarity
            $similarity = new Similarity();
            $similarity->setClassName($details['similarity']['className'] ?? null);
            $similarity->setDetails($details['similarity']['details'] ?? null);

            $type->setTokenized($details['tokenized']);
            $type->setClassName($details['className']);
            $type->setIndexAnalyzer($indexAnalyzer);
            $type->setQueryAnalyzer($queryAnalyzer);
            $type->setSimilarity($similarity);

            $types[$name] = $type;
        }

        return $types;
    }

    /**
     * @param array  $analyzerData
     * @param string $analyzerClass
     *
     * @return IndexAnalyzer|QueryAnalyzer
     */
    protected function parseAnalyzer(array $analyzerData, string $analyzerClass): AbstractAnalyzer
    {
        $analyzer = new $analyzerClass($analyzerData['className']);

        if (isset($analyzerData['charFilters'])) {
            $charFilters = $this->parseFilters($analyzerData['charFilters'], CharFilter::class);

            $analyzer->setCharFilters($charFilters);
        }

        if (isset($analyzerData['tokenizer'])) {
            $tokenizer = new Tokenizer($analyzerData['tokenizer']['className']);
            $tokenizer->setArgs($analyzerData['tokenizer']['args']);

            $analyzer->setTokenizer($tokenizer);
        }

        if (isset($analyzerData['filters'])) {
            $filters = $this->parseFilters($analyzerData['filters'], Filter::class);

            $analyzer->setFilters($filters);
        }

        return $analyzer;
    }

    /**
     * @param array  $filterData
     * @param string $filterClass
     *
     * @return CharFilter[]|Filter[]
     */
    protected function parseFilters(array $filterData, string $filterClass): array
    {
        $filters = [];

        foreach ($filterData as $name => $details) {
            $filter = new $filterClass($name);
            $filter->setArgs($details['args']);
            $filter->setClassName($details['className']);

            $filters[$name] = $filter;
        }

        return $filters;
    }

    /**
     * Link fields with their {@see Type}.
     *
     * @param Field[]|DynamicField[] $fields
     * @param array                  $fieldData
     * @param Type[]                 $types
     */
    protected function linkFieldType(array &$fields, array &$fieldData, array &$types): void
    {
        foreach ($fields as $name => $field) {
            $field->setType($types[$fieldData[$name]['type']]);
        }
    }

    /**
     * Link fields with their copy destinations and sources.
     *
     * @param Field[]|DynamicField[] $fieldsToLink
     * @param array                  $fieldData
     * @param Field[]                $fields
     * @param DynamicField[]         $dynamicFields
     * @param DynamicBasedField[]    $dynamicBasedFields
     * @param WildcardField[]        $wildcardFields
     */
    protected function linkFields(array &$fieldsToLink, array &$fieldData, array &$fields, array &$dynamicFields, array &$dynamicBasedFields, array &$wildcardFields): void
    {
        foreach ($fieldsToLink as $name => $field) {
            foreach ($fieldData[$name]['copyDests'] as $copyDest) {
                if (Utility::isWildcardPattern($copyDest)) {
                    $field->addCopyDest($dynamicFields[$copyDest]);
                } elseif (isset($fields[$copyDest])) {
                    $field->addCopyDest($fields[$copyDest]);
                } else {
                    if (!isset($dynamicBasedFields[$copyDest])) {
                        $dynamicBasedField = $dynamicFields[$this->findDynamicField($copyDest, array_keys($dynamicFields))]->createField($copyDest);
                        $dynamicBasedFields[$copyDest] = $dynamicBasedField;
                    }
                    $dynamicBasedFields[$copyDest]->addCopySource($fieldsToLink[$name]);
                    $field->addCopyDest($dynamicBasedFields[$copyDest]);
                }
            }

            foreach ($fieldData[$name]['copySources'] as $copySource) {
                if (Utility::isWildcardPattern($copySource)) {
                    if (isset($dynamicFields[$copySource])) {
                        $field->addCopySource($dynamicFields[$copySource]);
                    } else {
                        if (!isset($wildcardFields[$copySource])) {
                            $wildcardField = new WildcardField($copySource);
                            $wildcardFields[$copySource] = $wildcardField;
                        }
                        $wildcardFields[$copySource]->addCopyDest($fieldsToLink[$name]);
                        $field->addCopySource($wildcardFields[$copySource]);
                    }
                } elseif (isset($fields[$copySource])) {
                    $field->addCopySource($fields[$copySource]);
                } else {
                    if (!isset($dynamicBasedFields[$copySource])) {
                        $dynamicBasedField = $dynamicFields[$this->findDynamicField($copySource, array_keys($dynamicFields))]->createField($copySource);
                        $dynamicBasedFields[$copySource] = $dynamicBasedField;
                    }
                    $dynamicBasedFields[$copySource]->addCopyDest($fieldsToLink[$name]);
                    $field->addCopySource($dynamicBasedFields[$copySource]);
                }
            }
        }
    }

    /**
     * Link types with their associated fields and dynamic fields.
     *
     * @param Type[]         $types
     * @param array          $typeData
     * @param Field[]        $fields
     * @param DynamicField[] $dynamicFields
     */
    protected function linkTypeFields(array &$types, array &$typeData, array &$fields, array &$dynamicFields): void
    {
        foreach ($types as $typeName => $type) {
            // if a type has no associated fields, Solr returns null rather than an empty array
            if (null !== $typeData[$typeName]['fields']) {
                foreach ($typeData[$typeName]['fields'] as $fieldName) {
                    if (Utility::isWildcardPattern($fieldName)) {
                        $type->addField($dynamicFields[$fieldName]);
                    } else {
                        $type->addField($fields[$fieldName]);
                    }
                }
            }
        }
    }

    /**
     * Find the dynamic field in a list that matches the field name.
     *
     * @param string   $fieldName
     * @param string[] $dynamicFields
     *
     * @throws RuntimeException
     *
     * @return string
     */
    protected function findDynamicField(string $fieldName, array $dynamicFields): string
    {
        foreach ($dynamicFields as $dynamicField) {
            if (Utility::fieldMatchesWildcard($dynamicField, $fieldName)) {
                return $dynamicField;
            }
        }

        throw new RuntimeException(sprintf('Field name %s doesn\'t match a dynamicField name.', $fieldName));
    }
}
