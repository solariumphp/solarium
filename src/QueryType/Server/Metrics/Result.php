<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Metrics;

use Butschster\Prometheus\Ast\SchemaNode;
use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Metrics query result.
 */
class Result extends BaseResult
{
    protected SchemaNode $metricSet;

    /**
     * Get Solr response data.
     *
     * {@internal Overrides the parent to handle Prometheus/OpenMetrics responses.}
     *
     * @return array
     */
    public function getData(): array
    {
        return match ($this->query->getResponseWriter()) {
            Query::WT_PROMETHEUS,
            Query::WT_OPENMETRICS => ['metrics' => $this->response->getBody()],
            default => parent::getData()
        };
    }

    /**
     * Return the MetricSet.
     *
     * @return SchemaNode
     */
    public function getMetricSet(): SchemaNode
    {
        $this->parseResponse();

        return $this->metricSet;
    }
}
