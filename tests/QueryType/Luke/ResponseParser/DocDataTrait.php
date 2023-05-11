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
     * by {@see getDocData()}.
     *
     * @return string
     */
    public function getRawDocData(): string
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

    public function getDocData(): array
    {
        return json_decode($this->getRawDocData(), true);
    }
}
