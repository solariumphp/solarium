<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\ResponseParser;

use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Luke\Result\Fields\FieldInfo;
use Solarium\QueryType\Luke\Result\FlagList;
use Solarium\QueryType\Luke\Result\Result;

/**
 * Parse Luke fields response data.
 */
class Fields extends Index
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

        // parse 'info' first so 'fields' can use it for flag lookups
        $data['infoResult'] = $this->parseInfo($data['info']);
        $data['fieldsResult'] = $this->parseFields($data['fields']);

        return $data;
    }

    /**
     * @param array $fieldsData
     *
     * @return FieldInfo[]
     */
    protected function parseFields(array $fieldsData): array
    {
        $fields = [];

        foreach ($fieldsData as $name => $info) {
            $field = new FieldInfo($name);

            $field->setType($info['type']);
            $field->setSchema(new FlagList($info['schema'], $this->key));
            $field->setDynamicBase($info['dynamicBase'] ?? null);

            // index isn't set if field isn't indexed or if includeIndexFieldFlags=false was set on the query
            if (isset($info['index'])) {
                $index = $info['index'];

                // the response can have '(unstored field)' in lieu of an actual flags string
                if ('(unstored field)' !== $index) {
                    $index = new FlagList($index, $this->key);
                }

                $field->setIndex($index);
            }

            $field->setDocs($info['docs'] ?? null);
            $field->setDistinct($info['distinct'] ?? null);

            if (isset($info['topTerms'])) {
                $field->setTopTerms($this->convertToKeyValueArray($info['topTerms']));
            }

            if (isset($info['histogram'])) {
                $field->setHistogram($this->convertToKeyValueArray($info['histogram']));
            }

            $fields[$name] = $field;
        }

        return $fields;
    }
}
