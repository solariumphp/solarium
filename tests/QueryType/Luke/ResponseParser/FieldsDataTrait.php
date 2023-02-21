<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

/**
 * Default fields data used in Fields test.
 */
trait FieldsDataTrait
{
    public function getFieldsData(): array
    {
        return [
            'field' => [
                'type' => 'type_a',
                'schema' => 'I-S-U-----OF-----l',
                'index' => 'ITS-------OF------',
                'docs' => 25,
                'distinct' => 70,
                'topTerms' => [
                    'a',
                    18,
                    'b',
                    7,
                ],
                'histogram' => [
                    '1',
                    0,
                    '2',
                    1,
                    '4',
                    2,
                ],
            ],
            'field_unstored' => [
                'type' => 'type_b',
                'schema' => 'I---U-----OF-----l',
                'index' => '(unstored field)',
                'docs' => 20,
                'distinct' => 16,
                'topTerms' => [
                    'c',
                    12,
                    'a',
                    8,
                ],
                'histogram' => [
                    '1',
                    4,
                    '2',
                    1,
                ],
            ],
            'field_unindexed' => [
                'type' => 'type_c',
                'schema' => '---D--------------',
            ],
            'dynamic_field' => [
                'type' => 'type_a',
                'schema' => 'I-S-U-----OF-----l',
                'dynamicBase' => '*_field',
            ],
        ];
    }
}
