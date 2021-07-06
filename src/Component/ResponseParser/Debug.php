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
use Solarium\Component\Debug as DebugComponent;
use Solarium\Component\Result\Debug\Detail;
use Solarium\Component\Result\Debug\Document;
use Solarium\Component\Result\Debug\DocumentSet;
use Solarium\Component\Result\Debug\Result;
use Solarium\Component\Result\Debug\Timing;
use Solarium\Component\Result\Debug\TimingPhase;

/**
 * Parse select component Debug result from the data.
 */
class Debug implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param ComponentAwareQueryInterface     $query
     * @param DebugComponent|AbstractComponent $component
     * @param array                            $data
     *
     * @return Result|null
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $component, array $data): ?Result
    {
        $result = null;

        if (isset($data['debug'])) {
            $debug = $data['debug'];

            // get basic values from data
            $queryString = $debug['querystring'] ?? '';
            $parsedQuery = $debug['parsedquery'] ?? '';
            $queryParser = $debug['QParser'] ?? '';
            $otherQuery = $debug['otherQuery'] ?? '';

            // parse explain data
            if (isset($debug['explain']) && \is_array($debug['explain'])) {
                $explain = $this->parseDocumentSet($debug['explain']);
            } else {
                $explain = new DocumentSet([]);
            }

            // parse explainOther data
            if (isset($debug['explainOther']) && \is_array($debug['explainOther'])) {
                $explainOther = $this->parseDocumentSet($debug['explainOther']);
            } else {
                $explainOther = new DocumentSet([]);
            }

            // parse timing data
            $timing = null;
            if (isset($debug['timing']) && \is_array($debug['timing'])) {
                $time = null;
                $timingPhases = [];
                foreach ($debug['timing'] as $key => $timingData) {
                    switch ($key) {
                        case 'time':
                            $time = $timingData;
                            break;
                        case \is_array($timingData):
                            $timingPhases[$key] = $this->parseTimingPhase($key, $timingData);
                            break;
                    }
                }
                $timing = new Timing($time, $timingPhases);
            }

            // create result object
            $result = new Result(
                $queryString,
                $parsedQuery,
                $queryParser,
                $otherQuery,
                $explain,
                $explainOther,
                $timing
            );
        }

        return $result;
    }

    /**
     * Parse data into a documentset.
     *
     * Used for explain and explainOther
     *
     * @param array $data
     *
     * @return DocumentSet
     */
    protected function parseDocumentSet(array $data): DocumentSet
    {
        $docs = [];
        foreach ($data as $key => $documentData) {
            $details = [];
            if (isset($documentData['details']) && \is_array($documentData['details'])) {
                $details = $this->parseDetails($documentData['details']);
            }

            $docs[$key] = new Document(
                $key,
                $documentData['match'],
                $documentData['value'],
                $documentData['description'],
                $details
            );
        }

        return new DocumentSet($docs);
    }

    /**
     * Parse details.
     *
     * @param array $data
     *
     * @return Detail[]
     */
    protected function parseDetails(array $data): array
    {
        $details = [];
        foreach ($data as $key => $detailData) {
            $detail = new Detail(
                $detailData['match'],
                $detailData['value'],
                $detailData['description']
            );

            if (isset($detailData['details']) && \is_array($detailData['details'])) {
                $detail->setSubDetails($this->parseDetails($detailData['details']));
            }
            $details[] = $detail;
        }

        return $details;
    }

    /**
     * Parse raw timing phase data into a result class.
     *
     * @param string $name
     * @param array  $data
     *
     * @return TimingPhase
     */
    protected function parseTimingPhase(string $name, array $data): TimingPhase
    {
        $time = 0.0;
        $classes = [];
        foreach ($data as $key => $timingData) {
            switch ($key) {
                case 'time':
                    $time = $timingData;
                    break;
                default:
                    $classes[$key] = $timingData['time'];
            }
        }

        return new TimingPhase($name, $time, $classes);
    }
}
