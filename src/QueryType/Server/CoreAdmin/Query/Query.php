<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query;

use Solarium\Core\Client\Client;
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
 * Can be used to perform an action on the core admin endpoint
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
     * Update command types.
     *
     * @var array
     */
    protected $actionTypes = [
        self::ACTION_CREATE => 'Solarium\QueryType\Server\CoreAdmin\Query\Action\Create',
        self::ACTION_MERGE_INDEXES => 'Solarium\QueryType\Server\CoreAdmin\Query\Action\MergeIndexes',
        self::ACTION_RELOAD => 'Solarium\QueryType\Server\CoreAdmin\Query\Action\Reload',
        self::ACTION_RENAME => 'Solarium\QueryType\Server\CoreAdmin\Query\Action\Rename',
        self::ACTION_REQUEST_RECOVERY => 'Solarium\QueryType\Server\CoreAdmin\Query\Action\RequestRecovery',
        self::ACTION_REQUEST_STATUS => 'Solarium\QueryType\Server\CoreAdmin\Query\Action\RequestStatus',
        self::ACTION_SPLIT => 'Solarium\QueryType\Server\CoreAdmin\Query\Action\Split',
        self::ACTION_STATUS => 'Solarium\QueryType\Server\CoreAdmin\Query\Action\Status',
        self::ACTION_SWAP => 'Solarium\QueryType\Server\CoreAdmin\Query\Action\Swap',
        self::ACTION_UNLOAD => 'Solarium\QueryType\Server\CoreAdmin\Query\Action\Unload',
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
     * Action that should be performed on the core admin api.
     *
     * @var ActionInterface
     */
    protected $action = null;

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
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser();
    }

    /**
     * @param array $options
     *
     * @return Create|ActionInterface
     */
    public function createCreate($options = [])
    {
        return $this->createAction(self::ACTION_CREATE, $options);
    }

    /**
     * @param array $options
     *
     * @return MergeIndexes|ActionInterface
     */
    public function createMergeIndexes($options = [])
    {
        return $this->createAction(self::ACTION_MERGE_INDEXES, $options);
    }

    /**
     * @param array $options
     *
     * @return Reload|ActionInterface
     */
    public function createReload($options = [])
    {
        return $this->createAction(self::ACTION_RELOAD, $options);
    }

    /**
     * @param array $options
     *
     * @return Rename|ActionInterface
     */
    public function createRename($options = [])
    {
        return $this->createAction(self::ACTION_RENAME, $options);
    }

    /**
     * @param array $options
     *
     * @return RequestRecovery|ActionInterface
     */
    public function createRequestRecovery($options = [])
    {
        return $this->createAction(self::ACTION_REQUEST_RECOVERY, $options);
    }

    /**
     * @param array $options
     *
     * @return RequestStatus|ActionInterface
     */
    public function createRequestStatus($options = [])
    {
        return $this->createAction(self::ACTION_REQUEST_STATUS, $options);
    }

    /**
     * @param array $options
     *
     * @return Split|ActionInterface
     */
    public function createSplit($options = [])
    {
        return $this->createAction(self::ACTION_SPLIT, $options);
    }

    /**
     * @param array $options
     *
     * @return Status|ActionInterface
     */
    public function createStatus($options = [])
    {
        return $this->createAction(self::ACTION_STATUS, $options);
    }

    /**
     * @param array $options
     *
     * @return Swap|ActionInterface
     */
    public function createSwap($options = [])
    {
        return $this->createAction(self::ACTION_SWAP, $options);
    }

    /**
     * @param array $options
     *
     * @return Unload|ActionInterface
     */
    public function createUnload($options = [])
    {
        return $this->createAction(self::ACTION_UNLOAD, $options);
    }
}
