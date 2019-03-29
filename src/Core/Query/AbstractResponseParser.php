<?php

namespace Solarium\Core\Query;

/**
 * Abstract class for response parsers.
 *
 * Base class with shared functionality for querytype responseparser implementations
 */
abstract class AbstractResponseParser
{
    /**
     * Converts a flat key-value array (alternating rows) as used in Solr JSON results to a real key value array.
     *
     * @param array $data
     *
     * @return array
     */
    public function convertToKeyValueArray(array $data): array
    {
        // key counter to convert values to arrays when keys are re-used
        $keys = [];

        $dataCount = count($data);
        $result = [];
        for ($i = 0; $i < $dataCount; $i += 2) {
            $key = $data[$i];
            $value = $data[$i + 1];
            if (array_key_exists($key, $keys)) {
                if (1 === $keys[$key]) {
                    $result[$key] = [$result[$key]];
                }
                $result[$key][] = $value;
                ++$keys[$key];
            } else {
                $keys[$key] = 1;
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Parses header data (if available) and adds it to result data.
     *
     * @param array $data
     * @param array $result
     *
     * @return array
     */
    public function addHeaderInfo(array $data, array $result): array
    {
        $status = null;
        $queryTime = null;

        if (isset($data['responseHeader'])) {
            $status = $data['responseHeader']['status'];
            $queryTime = $data['responseHeader']['QTime'];
        }

        $result['status'] = $status;
        $result['queryTime'] = $queryTime;

        return $result;
    }
}
