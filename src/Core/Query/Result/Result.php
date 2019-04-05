<?php

namespace Solarium\Core\Query\Result;

use Solarium\Core\Client\Response;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Exception\HttpException;
use Solarium\Exception\RuntimeException;
use Solarium\Exception\UnexpectedValueException;

/**
 * Query result.
 *
 * This base class provides access to the response and decoded data. If you need more functionality
 * like resultset parsing use one of the subclasses
 */
class Result implements ResultInterface
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

        // check status for error (range of 400 and 500)
        $statusNum = floor($response->getStatusCode() / 100);
        if (4 == $statusNum || 5 == $statusNum) {
            throw new HttpException(
                $response->getStatusMessage(),
                $response->getStatusCode(),
                $response->getBody()
            );
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
                    $this->data = unserialize($this->response->getBody(), [false]);
                    break;
                case AbstractQuery::WT_JSON:
                    $this->data = json_decode($this->response->getBody(), true);
                    break;
                default:
                    throw new RuntimeException('Responseparser cannot handle '.$this->query->getResponseWriter());
            }

            if (null === $this->data) {
                throw new UnexpectedValueException(
                    'Solr JSON response could not be decoded'
                );
            }
        }

        return $this->data;
    }
}
