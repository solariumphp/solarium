<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\CoreAdmin\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Server\AbstractServerQuery;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Create;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\MergeIndexes;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Reload;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Rename;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\RequestRecovery;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\RequestStatus;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Split;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Status;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Swap;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Unload;
use Solarium\QueryType\Server\CoreAdmin\ResponseParser;
use Solarium\QueryType\Server\Query\Action\ActionInterface;
use Solarium\QueryType\Server\Query\RequestBuilder;

/**
 * CoreAdmin query.
 *
 * Can be used to perform an action on the admin/cores endpoint
 */
class Query extends AbstractServerQuery
{
    /**
     * Create core action.
     */
    const ACTION_CREATE = 'CREATE';

    /**
     * Merge indexes action.
     */
    const ACTION_MERGE_INDEXES = 'MERGEINDEXES';

    /**
     * Reload core action.
     */
    const ACTION_RELOAD = 'RELOAD';

    /**
     * Rename core action.
     */
    const ACTION_RENAME = 'RENAME';

    /**
     * Request the recovery of a core.
     */
    const ACTION_REQUEST_RECOVERY = 'REQUESTRECOVERY';

    /**
     * Request the status of a core.
     */
    const ACTION_REQUEST_STATUS = 'REQUESTSTATUS';

    /**
     * Request a split of a core.
     */
    const ACTION_SPLIT = 'SPLIT';

    /**
     * Request the status of a core.
     */
    const ACTION_STATUS = 'STATUS';

    /**
     * Request the swap of two cores.
     */
    const ACTION_SWAP = 'SWAP';

    /**
     * Request the unload of a core.
     */
    const ACTION_UNLOAD = 'UNLOAD';

    /**
     * Action types.
     *
     * @var array
     */
    protected $actionTypes = [
        self::ACTION_CREATE => Create::class,
        self::ACTION_MERGE_INDEXES => MergeIndexes::class,
        self::ACTION_RELOAD => Reload::class,
        self::ACTION_RENAME => Rename::class,
        self::ACTION_REQUEST_RECOVERY => RequestRecovery::class,
        self::ACTION_REQUEST_STATUS => RequestStatus::class,
        self::ACTION_SPLIT => Split::class,
        self::ACTION_STATUS => Status::class,
        self::ACTION_SWAP => Swap::class,
        self::ACTION_UNLOAD => Unload::class,
    ];

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'admin/cores',
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_CORE_ADMIN;
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
     * @return Create|ActionInterface
     */
    public function createCreate($options = []): Create
    {
        return $this->createAction(self::ACTION_CREATE, $options);
    }

    /**
     * @param array $options
     *
     * @return MergeIndexes|ActionInterface
     */
    public function createMergeIndexes($options = []): MergeIndexes
    {
        return $this->createAction(self::ACTION_MERGE_INDEXES, $options);
    }

    /**
     * @param array $options
     *
     * @return Reload|ActionInterface
     */
    public function createReload($options = []): Reload
    {
        return $this->createAction(self::ACTION_RELOAD, $options);
    }

    /**
     * @param array $options
     *
     * @return Rename|ActionInterface
     */
    public function createRename($options = []): Rename
    {
        return $this->createAction(self::ACTION_RENAME, $options);
    }

    /**
     * @param array $options
     *
     * @return RequestRecovery|ActionInterface
     */
    public function createRequestRecovery($options = []): RequestRecovery
    {
        return $this->createAction(self::ACTION_REQUEST_RECOVERY, $options);
    }

    /**
     * @param array $options
     *
     * @return RequestStatus|ActionInterface
     */
    public function createRequestStatus($options = []): RequestStatus
    {
        return $this->createAction(self::ACTION_REQUEST_STATUS, $options);
    }

    /**
     * @param array $options
     *
     * @return Split|ActionInterface
     */
    public function createSplit($options = []): Split
    {
        return $this->createAction(self::ACTION_SPLIT, $options);
    }

    /**
     * @param array $options
     *
     * @return Status|ActionInterface
     */
    public function createStatus($options = []): Status
    {
        return $this->createAction(self::ACTION_STATUS, $options);
    }

    /**
     * @param array $options
     *
     * @return Swap|ActionInterface
     */
    public function createSwap($options = []): Swap
    {
        return $this->createAction(self::ACTION_SWAP, $options);
    }

    /**
     * @param array $options
     *
     * @return Unload|ActionInterface
     */
    public function createUnload($options = []): Unload
    {
        return $this->createAction(self::ACTION_UNLOAD, $options);
    }
}
