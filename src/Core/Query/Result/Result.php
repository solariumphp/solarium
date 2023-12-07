<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query\Result;

use Solarium\Core\Client\Response;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\Status4xxNoExceptionInterface;
use Solarium\Exception\HttpException;
use Solarium\Exception\RuntimeException;
use Solarium\Exception\UnexpectedValueException;

/**
 * Query result.
 *
 * This base class provides access to the response and decoded data. If you need more functionality
 * like resultset parsing use one of the subclasses
 */
class Result implements ResultInterface, \JsonSerializable
{
    /**
     * Response object.
     *
     * @var Response
     */
    protected $response;

    /**
     * Decoded response data.
     *
     * This is lazy loaded, {@link getData()}
     *
     * @var array
     */
    protected $data;

    /**
     * Query used for this request.
     *
     * @var AbstractQuery
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param AbstractQuery $query
     * @param Response      $response
     *
     * @throws HttpException
     */
    public function __construct(AbstractQuery $query, Response $response)
    {
        $this->query = $query;
        $this->response = $response;

        // by default, a status of 400 or above is considered an error
        $errorStatus = 400;

        // some query types expect 4xx statuses as a valid response
        if ($query instanceof Status4xxNoExceptionInterface) {
            $errorStatus = 500;
        }

        // check status for error
        if ($response->getStatusCode() >= $errorStatus) {
            throw new HttpException($response->getStatusMessage(), $response->getStatusCode(), $response->getBody());
        }
    }

    /**
     * Get response object.
     *
     * This is the raw HTTP response object, not the parsed data!
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Get query instance.
     *
     * @return AbstractQuery
     */
    public function getQuery(): AbstractQuery
    {
        return $this->query;
    }

    /**
     * Get Solr response data.
     *
     * Includes a lazy loading mechanism: JSON body data is decoded on first use and then saved for reuse.
     *
     * @throws UnexpectedValueException
     * @throws RuntimeException
     *
     * @return array
     */
    public function getData(): array
    {
        if (null === $this->data) {
            switch ($this->query->getResponseWriter()) {
                case AbstractQuery::WT_PHPS:
                    $this->data = unserialize($this->response->getBody(), ['allowed_classes' => false]);

                    if (false === $this->data) {
                        throw new UnexpectedValueException('Solr PHPS response could not be unserialized');
                    }

                    break;
                case AbstractQuery::WT_JSON:
                    $this->data = json_decode($this->response->getBody(), true);

                    if (null === $this->data) {
                        throw new UnexpectedValueException('Solr JSON response could not be decoded');
                    }

                    break;
                default:
                    throw new RuntimeException(sprintf('Responseparser cannot handle %s', $this->query->getResponseWriter()));
            }
        }

        return $this->data;
    }

    #[\ReturnTypeWillChange]
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->getData();
    }
}
