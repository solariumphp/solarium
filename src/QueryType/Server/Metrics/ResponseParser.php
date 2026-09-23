<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Metrics;

use Butschster\Prometheus\ParserFactory;
use Composer\InstalledVersions;
use Solarium\Core\Query\AbstractResponseParser;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Exception\RuntimeException;

/**
 * Parse metrics response data.
 */
class ResponseParser extends AbstractResponseParser implements ResponseParserInterface
{
    /**
     * Parse response data.
     *
     * @param Result|ResultInterface $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        if (!InstalledVersions::isInstalled('butschster/prometheus-parser')) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('butschster/prometheus-parser is not available, install it to parse Metrics API responses');
            // @codeCoverageIgnoreEnd
        }

        $data = $result->getResponse()->getBody();

        $parser = ParserFactory::create();

        $metricSet = $parser->parse($data);

        return ['metricSet' => $metricSet];
    }
}
