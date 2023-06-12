<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query\Result;

use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Exception\UnexpectedValueException;

/**
 * QueryType result.
 */
class QueryType extends Result
{
    /**
     * Lazy load parsing indicator.
     *
     * @var bool
     */
    protected $parsed = false;

    /**
     * Response header returned by Solr.
     *
     * @var array
     */
    protected $responseHeader;

    /**
     * Get Solr status code.
     *
     * This is not the HTTP status code! The normal value for success is 0.
     *
     * {@internal No return typehint until deprecated inheriting
     *            methods that are not covariant are removed from
     *            Solarium\QueryType\Server\Collections\Result classes.}
     *
     * @return int|null
     */
    public function getStatus()
    {
        $this->parseResponse();

        return $this->responseHeader['status'] ?? null;
    }

    /**
     * Get Solr query time.
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @return int|null
     */
    public function getQueryTime(): ?int
    {
        $this->parseResponse();

        return $this->responseHeader['QTime'] ?? null;
    }

    /**
     * Parse response into result objects.
     *
     * Only runs once.
     *
     * @throws UnexpectedValueException
     */
    protected function parseResponse()
    {
        if (!$this->parsed) {
            $responseParser = $this->query->getResponseParser();
            if (!$responseParser || !($responseParser instanceof ResponseParserInterface)) {
                throw new UnexpectedValueException(sprintf('No responseparser returned by querytype: %s', $this->query->getType()));
            }

            $this->mapData($responseParser->parse($this));

            // don't override if ResponseParser already parsed this
            if (null === $this->responseHeader) {
                $this->responseHeader = $this->data['responseHeader'] ?? null;
            }

            $this->parsed = true;
        }
    }

    /**
     * Map parser data into properties.
     *
     * @param array $mapData
     */
    protected function mapData(array $mapData)
    {
        foreach ($mapData as $key => $data) {
            $this->{$key} = $data;
        }
    }
}
