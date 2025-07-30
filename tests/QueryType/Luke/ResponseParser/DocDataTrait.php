<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

/**
 * Default doc data used in Doc test.
 */
trait DocDataTrait
{
    /**
     * Returns a raw JSON string as it would be output by Solr.
     *
     * The $.doc.lucene node of a Luke response contains duplicate keys for
     * multiValued fields. We can't fully represent these in the array returned
     * by {@see getDocJsonData()}.
     *
     * @return string
     */
    public function getRawDocJsonData(): string
    {
        return <<<'JSON'
            {
                "docId": 1701,
                "lucene": {
                    "id": {
                        "type": "string",
                        "schema": "I-S-U-----OF-----l",
                        "flags": "ITS-------OF------",
                        "value": "NCC-1701",
                        "internal": "NCC-1701",
                        "docFreq": 1
                    },
                    "name": {
                        "type": "text_general",
                        "schema": "ITS-U-------------",
                        "flags": "ITS---------------",
                        "value": "Enterprise document",
                        "internal": "Enterprise document",
                        "docFreq": 0,
                        "termVector": {
                            "enterprise": 2,
                            "document": 1
                        }
                    },
                    "cat": {
                        "type": "string",
                        "schema": "I-S-UM----OF-----l",
                        "flags": "ITS-------OF------",
                        "value": "Constitution",
                        "internal": "Constitution",
                        "docFreq": 12
                    },
                    "cat": {
                        "type": "string",
                        "schema": "I-S-UM----OF-----l",
                        "flags": "ITS-------OF------",
                        "value": "Galaxy",
                        "internal": "Galaxy",
                        "docFreq": 6
                    },
                    "price": {
                        "type": "pfloat",
                        "schema": "I-SDU-----OF------",
                        "flags": "-TS---------------",
                        "value": "92.0",
                        "internal": "92.0"
                    },
                    "flagship": {
                        "type": "boolean",
                        "schema": "I-S-U-----OF-----l",
                        "flags": "ITS-------OF------",
                        "value": "true",
                        "internal": "T",
                        "docFreq": 1
                    },
                    "insignia": {
                        "type": "binary",
                        "schema": "I-S-U------F------",
                        "flags": "-TS------------B--",
                        "value": "PS9cPQ==",
                        "internal": null,
                        "binary": "PS9cPQ==",
                        "docFreq": 0
                    }
                },
                "solr": {
                    "id": "NCC-1701",
                    "name": "Enterprise document",
                    "cat": [
                        "Constitution",
                        "Galaxy"
                    ],
                    "price": 3.59,
                    "flagship": true,
                    "insignia": "PS9cPQ=="
                }
            }
        JSON;
    }

    public function getDocJsonData(): array
    {
        return json_decode($this->getRawDocJsonData(), true);
    }

    /**
     * Returns a raw PHPS string as it would be output by Solr.
     *
     * This string contains a syntax error just like Solr's actual output does.
     *
     * @see https://github.com/apache/solr/pull/2114
     *
     * @return string
     */
    public function getRawDocPhpsData(): string
    {
        $phpsData = 'a:3:{s:5:"docId";i:1701;s:6:"lucene";a:7:{'.
            's:2:"id";a:6:{s:4:"type";s:6:"string";s:6:"schema";s:18:"I-S-U-----OF-----l";s:5:"flags";s:18:"ITS-------OF------";s:5:"value";s:8:"NCC-1701";s:8:"internal";s:8:"NCC-1701";s:7:"docFreq";i:1;}'.
            's:4:"name";a:7:{s:4:"type";s:12:"text_general";s:6:"schema";s:18:"ITS-U-------------";s:5:"flags";s:18:"ITS---------------";s:5:"value";s:19:"Enterprise document";s:8:"internal";s:19:"Enterprise document";s:7:"docFreq";i:0;s:10:"termVector";a:2:{s:10:"enterprise";i:2;s:8:"document";i:1;}}'.
            's:3:"cat";a:6:{s:4:"type";s:6:"string";s:6:"schema";s:18:"I-S-UM----OF-----l";s:5:"flags";s:18:"ITS-------OF------";s:5:"value";s:12:"Constitution";s:8:"internal";s:12:"Constitution";s:7:"docFreq";i:12;}'.
            's:5:"cat 1";a:6:{s:4:"type";s:6:"string";s:6:"schema";s:18:"I-S-UM----OF-----l";s:5:"flags";s:18:"ITS-------OF------";s:5:"value";s:6:"Galaxy";s:8:"internal";s:6:"Galaxy";s:7:"docFreq";i:6;}'.
            's:5:"price";a:5:{s:4:"type";s:6:"pfloat";s:6:"schema";s:18:"I-SDU-----OF------";s:5:"flags";s:18:"-TS---------------";s:5:"value";s:4:"92.0";s:8:"internal";s:4:"92.0";}'.
            's:8:"flagship";a:6:{s:4:"type";s:7:"boolean";s:6:"schema";s:18:"I-S-U-----OF-----l";s:5:"flags";s:18:"ITS-------OF------";s:5:"value";s:4:"true";s:8:"internal";s:1:"T";s:7:"docFreq";i:1;}'.
            's:8:"insignia";a:7:{s:4:"type";s:6:"binary";s:6:"schema";s:18:"I-S-U------F------";s:5:"flags";s:18:"-TS------------B--";s:5:"value";s:8:"PS9cPQ==";s:8:"internal";N;s:6:"binary";s:8:"PS9cPQ==";s:7:"docFreq";i:0;}'.
            '}s:4:"solr";i:0;a:6:{s:2:"id";s:8:"NCC-1701";s:4:"name";s:19:"Enterprise document";s:3:"cat";a:2:{i:0;s:12:"Constitution";i:1;s:6:"Galaxy";}s:5:"price";d:3.59;s:8:"flagship";b:1;s:8:"insignia";s:8:"PS9cPQ==";}}';
        // the i:0; here ^^^^ is superfluous data that causes unserialize() to fail as discussed in https://github.com/apache/solr/pull/2114

        return $phpsData;
    }
}
