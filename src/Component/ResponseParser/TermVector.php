<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\ResponseParser;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Result\TermVector\Document;
use Solarium\Component\Result\TermVector\Field;
use Solarium\Component\Result\TermVector\Result;
use Solarium\Component\Result\TermVector\Term;
use Solarium\Component\Result\TermVector\Warnings;
use Solarium\Component\TermVector as TermVectorComponent;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractResponseParser;
use Solarium\Exception\InvalidArgumentException;

/**
 * Parse Term Vector response data.
 */
class TermVector extends AbstractResponseParser implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param AbstractQuery       $query
     * @param TermVectorComponent $component
     * @param array               $data
     *
     * @return Result|null
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $component, array $data): ?Result
    {
        if (!$query) {
            throw new InvalidArgumentException('A valid query object needs to be provided.');
        }

        $responseWriter = $query->getResponseWriter();

        if (isset($data['termVectors'])) {
            $warnings = null;
            $documents = [];

            // if there are warnings they're always the first item in the result data
            if ($query::WT_JSON === $responseWriter) {
                if ('warnings' === $data['termVectors'][0]
                    && null !== $warnings = $this->parseWarnings($this->convertToKeyValueArray($data['termVectors'][1]))
                ) {
                    array_shift($data['termVectors']);
                    array_shift($data['termVectors']);
                }
            } else {
                if ('warnings' === array_key_first($data['termVectors'])
                    && null !== $warnings = $this->parseWarnings($data['termVectors']['warnings'])
                ) {
                    unset($data['termVectors']['warnings']);
                }
            }

            if ($query::WT_JSON === $responseWriter) {
                $data['termVectors'] = $this->convertToKeyValueArray($data['termVectors']);
            }

            foreach ($data['termVectors'] as $key => $document) {
                $parsedDocument = $this->parseDocument($responseWriter, $document);

                if (null !== $warnings && $query::WT_PHPS === $responseWriter) {
                    $uniqueKey = $parsedDocument->getUniqueKey();

                    if (null !== $uniqueKey && $key !== $uniqueKey) {
                        // key was mangled by Solr's ResponseWriter to avoid repeats in the output
                        $key = $uniqueKey;
                    }
                }

                $documents[$key] = $parsedDocument;
            }

            return new Result($documents, $warnings);
        }

        return null;
    }

    /**
     * Parse warnings data.
     *
     * Returns null if other data was passed. This occurs if there are no warnings in
     * the result data and the first document's unique key happens to be 'warnings'.
     *
     * @param array $warningsData
     *
     * @return Warnings|null
     */
    protected function parseWarnings(array $warningsData): ?Warnings
    {
        // true 'warnings' only contains arrays of strings
        if (array_filter(
            $warningsData,
            fn ($val) => \is_array($val) && \count($val) === array_sum(array_map('\is_string', $val))
        ) !== $warningsData) {
            return null;
        }

        $noTermVectors = $warningsData['noTermVectors'] ?? null;
        $noPositions = $warningsData['noPositions'] ?? null;
        $noOffsets = $warningsData['noOffsets'] ?? null;
        $noPayloads = $warningsData['noPayloads'] ?? null;

        return new Warnings($noTermVectors, $noPositions, $noOffsets, $noPayloads);
    }

    /**
     * Parse document data.
     *
     * @param string $responseWriter
     * @param array  $documentData
     *
     * @return Document
     */
    protected function parseDocument(string $responseWriter, array $documentData): Document
    {
        $uniqueKey = null;
        $fields = [];

        // if there is a uniqueKey it's always the first item in the document data
        if (AbstractQuery::WT_JSON === $responseWriter) {
            if ('uniqueKey' === $documentData[0] && \is_string($documentData[1])) {
                $uniqueKey = $documentData[1];

                array_shift($documentData);
                array_shift($documentData);
            }
        } else {
            if ('uniqueKey' === array_key_first($documentData) && \is_string($documentData['uniqueKey'])) {
                $uniqueKey = $documentData['uniqueKey'];

                unset($documentData['uniqueKey']);
            }
        }

        if (AbstractQuery::WT_JSON === $responseWriter) {
            $documentData = $this->convertToKeyValueArray($documentData);
        }

        foreach ($documentData as $name => $fieldData) {
            $terms = [];

            if (AbstractQuery::WT_JSON === $responseWriter) {
                $fieldData = $this->convertToKeyValueArray($fieldData);
            } elseif ('uniqueKey 1' === $name) {
                // field name was mangled by Solr's ResponseWriter to avoid repeats in the output
                $name = 'uniqueKey';
            }

            foreach ($fieldData as $term => $termData) {
                if (AbstractQuery::WT_JSON === $responseWriter) {
                    $termData = $this->convertToKeyValueArray($termData);

                    if (isset($termData['positions'])) {
                        $termData['positions'] = $this->convertToValueArray($termData['positions']);
                    }

                    if (isset($termData['offsets'])) {
                        $termData['offsets'] = $this->convertToValueArray($termData['offsets']);
                    }

                    if (isset($termData['payloads'])) {
                        $termData['payloads'] = $this->convertToValueArray($termData['payloads']);
                    }
                } else {
                    if (isset($termData['positions'])) {
                        $termData['positions'] = array_values($termData['positions']);
                    }

                    if (isset($termData['offsets'])) {
                        $termData['offsets'] = array_values($termData['offsets']);
                    }

                    if (isset($termData['payloads'])) {
                        $termData['payloads'] = array_values($termData['payloads']);
                    }
                }

                $tf = $termData['tf'] ?? null;
                $positions = $termData['positions'] ?? null;
                $offsets = null;
                $payloads = $termData['payloads'] ?? null;
                $df = $termData['df'] ?? null;
                $tfIdf = $termData['tf-idf'] ?? null;

                if (isset($termData['offsets'])) {
                    $dataCount = \count($termData['offsets']);
                    $offsets = [];

                    for ($i = 0; $i < $dataCount; $i += 2) {
                        $start = $termData['offsets'][$i];
                        $end = $termData['offsets'][$i + 1];

                        $offsets[] = ['start' => $start, 'end' => $end];
                    }
                }

                $terms[$term] = new Term($term, $tf, $positions, $offsets, $payloads, $df, $tfIdf);
            }

            $fields[$name] = new Field($name, $terms);
        }

        return new Document($uniqueKey, $fields);
    }
}
