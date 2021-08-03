<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Configsets\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Server\AbstractServerQuery;
use Solarium\QueryType\Server\Configsets\Query\Action\Create;
use Solarium\QueryType\Server\Configsets\Query\Action\Delete;
use Solarium\QueryType\Server\Configsets\Query\Action\ListConfigsets;
use Solarium\QueryType\Server\Configsets\Query\Action\Upload;
use Solarium\QueryType\Server\Configsets\RequestBuilder;
use Solarium\QueryType\Server\Query\Action\ActionInterface;
use Solarium\QueryType\Server\Query\ResponseParser;

/**
 * Collections query.
 *
 * Can be used to perform an action on the Configsets API admin endpoint
 */
class Query extends AbstractServerQuery
{
    /**
     * The list command fetches the names of the configsets that are available for use during collection creation.
     */
    const ACTION_LIST = 'LIST';

    /**
     * Upload a configset, which is sent as a zipped file, or replace a single file of a configset that has been previously uploaded.
     */
    const ACTION_UPLOAD = 'UPLOAD';

    /**
     * The create command creates a new configset based on a configset that has been previously uploaded.
     */
    const ACTION_CREATE = 'CREATE';

    /**
     * The delete command removes a configset.
     */
    const ACTION_DELETE = 'DELETE';

    /**
     * Action types.
     *
     * @var array
     */
    protected $actionTypes = [
        self::ACTION_LIST => ListConfigsets::class,
        self::ACTION_UPLOAD => Upload::class,
        self::ACTION_CREATE => Create::class,
        self::ACTION_DELETE => Delete::class,
    ];

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'admin/configs',
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_CONFIGSETS;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }

    /**
     * @param array $options
     *
     * @return ListConfigsets|ActionInterface
     */
    public function createList(array $options = []): ListConfigsets
    {
        return $this->createAction(self::ACTION_LIST, $options);
    }

    /**
     * @param array $options
     *
     * @return Upload|ActionInterface
     */
    public function createUpload(array $options = []): Upload
    {
        return $this->createAction(self::ACTION_UPLOAD, $options);
    }

    /**
     * @param array $options
     *
     * @return Create|ActionInterface
     */
    public function createCreate(array $options = []): Create
    {
        return $this->createAction(self::ACTION_CREATE, $options);
    }

    /**
     * @param array $options
     *
     * @return Delete|ActionInterface
     */
    public function createDelete(array $options = []): Delete
    {
        return $this->createAction(self::ACTION_DELETE, $options);
    }
}
