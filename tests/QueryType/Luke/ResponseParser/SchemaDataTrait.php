<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

/**
 * Default schema data used in Schema test.
 */
trait SchemaDataTrait
{
    public function getSchemaData(): array
    {
        return [
            'fields' => [
                'flags_a' => [
                    'type' => 'type_untokenized',
                    'flags' => 'I-S-U-V-p-O-P-L-f-',
                    'copyDests' => [],
                    'copySources' => [],
                ],
                'flags_b' => [
                    'type' => 'type_tokenized',
                    'flags' => '-T-D-M-o-y-F-H-B-l',
                    'copyDests' => [],
                    'copySources' => [],
                ],
                'required' => [
                    'type' => 'type_untokenized',
                    'flags' => 'I-SDU-------------',
                    'required' => true,
                    'copyDests' => [],
                    'copySources' => [],
                ],
                'default' => [
                    'type' => 'type_untokenized',
                    'flags' => 'I-SDU-------------',
                    'default' => '0.0',
                    'copyDests' => [],
                    'copySources' => [],
                ],
                'uniquekey' => [
                    'type' => 'type_untokenized',
                    'flags' => 'I-SDU-------------',
                    'uniqueKey' => true,
                    'copyDests' => [],
                    'copySources' => [],
                ],
                'pos_inc_gap' => [
                    'type' => 'type_tokenized',
                    'flags' => 'ITSDU-------------',
                    'positionIncrementGap' => 100,
                    'copyDests' => [],
                    'copySources' => [],
                ],
                'copy_from' => [
                    'type' => 'type_untokenized',
                    'flags' => 'ITSDU-------------',
                    'copyDests' => [
                        'copy_to',
                        'dynamic_copy_to',
                    ],
                    'copySources' => [],
                ],
                'copy_to' => [
                    'type' => 'type_untokenized',
                    'flags' => 'ITSDU-------------',
                    'copyDests' => [],
                    'copySources' => [
                        'copy_from',
                        'dynamic_copy_from',
                        '*_copy_from',
                        '*_wildcard',
                    ],
                ],
            ],
            'dynamicFields' => [
                '*_pos_inc_gap' => [
                    'type' => 'type_tokenized',
                    'flags' => 'ITSDU-------------',
                    'positionIncrementGap' => 200,
                    'copyDests' => [],
                    'copySources' => [],
                ],
                '*_copy_from' => [
                    'type' => 'type_untokenized',
                    'flags' => 'I-SDU-------------',
                    'copyDests' => [
                        'copy_to',
                        'dynamic_copy_to',
                        '*_copy_to',
                    ],
                    'copySources' => [],
                ],
                '*_copy_to' => [
                    'type' => 'type_untokenized',
                    'flags' => 'I-SDU-------------',
                    'copyDests' => [],
                    'copySources' => [
                        '*_copy_from',
                        '*_wildcard',
                    ],
                ],
            ],
            'uniqueKeyField' => 'uniquekey',
            'similarity' => [
                'className' => 'org.example.SchemaSimilarityFactory$SchemaSimilarity',
                'details' => 'Similarity details.',
            ],
            'types' => [
                'type_untokenized' => [
                    'fields' => [
                        'flags_a',
                        'required',
                        'default',
                        'uniquekey',
                        'copy_from',
                        'copy_to',
                        '*_copy_from',
                        '*_copy_to',
                    ],
                    'tokenized' => false,
                    'className' => 'org.example.TestField',
                    'indexAnalyzer' => [
                        'className' => 'org.example.TestFieldType$DefaultAnalyzer',
                    ],
                    'queryAnalyzer' => [
                        'className' => 'org.example.TestFieldType$DefaultAnalyzer',
                    ],
                    'similarity' => [],
                ],
                'type_tokenized' => [
                    'fields' => [
                        'flags_b',
                        'pos_inc_gap',
                        '*_pos_inc_gap',
                    ],
                    'tokenized' => true,
                    'className' => 'org.example.TokenizedField',
                    'indexAnalyzer' => [
                        'className' => 'org.example.TokenizerChain',
                        'charFilters' => [
                            'FirstCharFilterFactory' => [
                                'args' => [
                                    'class' => 'solr.FirstCharFilterFactory',
                                    'luceneMatchVersion' => '1.2.3',
                                ],
                                'className' => 'org.example.FirstCharFilterFactory',
                            ],
                            'NextCharFilterFactory' => [
                                'args' => [
                                    'class' => 'solr.NextCharFilterFactory',
                                    'luceneMatchVersion' => '1.2.3',
                                ],
                                'className' => 'org.example.NextCharFilterFactory',
                            ],
                        ],
                        'tokenizer' => [
                            'className' => 'org.example.TestTokenizerFactory',
                            'args' => [
                                'class' => 'solr.TestTokenizerFactory',
                                'luceneMatchVersion' => '1.2.3',
                            ],
                        ],
                        'filters' => [
                            'FirstFilterFactory' => [
                                'args' => [
                                    'class' => 'solr.FirstFilterFactory',
                                    'luceneMatchVersion' => '1.2.3',
                                ],
                                'className' => 'org.example.FirstFilterFactory',
                            ],
                            'NextFilterFactory' => [
                                'args' => [
                                    'class' => 'solr.NextFilterFactory',
                                    'luceneMatchVersion' => '1.2.3',
                                ],
                                'className' => 'org.example.NextFilterFactory',
                            ],
                        ],
                    ],
                    'queryAnalyzer' => [
                        'className' => 'org.example.TokenizerChain',
                        'charFilters' => [
                            'FirstCharFilterFactory' => [
                                'args' => [
                                    'class' => 'solr.FirstCharFilterFactory',
                                    'luceneMatchVersion' => '1.2.3',
                                ],
                                'className' => 'org.example.FirstCharFilterFactory',
                            ],
                            'NextCharFilterFactory' => [
                                'args' => [
                                    'class' => 'solr.NextCharFilterFactory',
                                    'luceneMatchVersion' => '1.2.3',
                                ],
                                'className' => 'org.example.NextCharFilterFactory',
                            ],
                        ],
                        'tokenizer' => [
                            'className' => 'org.example.TestTokenizerFactory',
                            'args' => [
                                'class' => 'solr.TestTokenizerFactory',
                                'luceneMatchVersion' => '1.2.3',
                            ],
                        ],
                        'filters' => [
                            'FirstFilterFactory' => [
                                'args' => [
                                    'class' => 'solr.FirstFilterFactory',
                                    'luceneMatchVersion' => '1.2.3',
                                ],
                                'className' => 'org.example.FirstFilterFactory',
                            ],
                            'NextFilterFactory' => [
                                'args' => [
                                    'class' => 'solr.NextFilterFactory',
                                    'luceneMatchVersion' => '1.2.3',
                                ],
                                'className' => 'org.example.NextFilterFactory',
                            ],
                        ],
                    ],
                    'similarity' => [],
                ],
                'type_similarity' => [
                    'fields' => null,
                    'tokenized' => true,
                    'className' => 'org.example.SimilarityField',
                    'indexAnalyzer' => [
                        'className' => 'org.example.TestAnalyzedField$TestAnalyzedAnalyzer',
                    ],
                    'queryAnalyzer' => [
                        'className' => 'org.example.TokenizerChain',
                        'tokenizer' => [
                            'className' => 'org.example.TestTokenizerFactory',
                            'args' => [
                                'class' => 'solr.TestTokenizerFactory',
                                'luceneMatchVersion' => '1.2.3',
                            ],
                        ],
                    ],
                    'similarity' => [
                        'className' => 'org.example.TestSimilarity',
                        'details' => 'Type similarity details.',
                    ],
                ],
            ],
        ];
    }
}
