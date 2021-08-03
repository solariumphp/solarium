<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Collections\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Server\AbstractServerQuery;
use Solarium\QueryType\Server\Collections\Query\Action\ClusterStatus;
use Solarium\QueryType\Server\Collections\Query\Action\Create;
use Solarium\QueryType\Server\Collections\Query\Action\Delete;
use Solarium\QueryType\Server\Collections\Query\Action\Reload;
use Solarium\QueryType\Server\Query\Action\ActionInterface;
use Solarium\QueryType\Server\Query\RequestBuilder;
use Solarium\QueryType\Server\Query\ResponseParser;

/**
 * Collections query.
 *
 * Can be used to perform an action on the Collections API admin endpoint
 */
class Query extends AbstractServerQuery
{
    /**
     * Create a Collection.
     */
    const ACTION_CREATE = 'CREATE';

    /**
     * Modify Attributes of a Collection.
     */
    const ACTION_MODIFYCOLLECTION = 'MODIFYCOLLECTION';

    /**
     * Reload a Collection.
     */
    const ACTION_RELOAD = 'RELOAD';

    /**
     * Split a Shard.
     */
    const ACTION_SPLITSHARD = 'SPLITSHARD';

    /**
     * Create a Shard.
     */
    const ACTION_CREATESHARD = 'CREATESHARD';

    /**
     * Delete a Shard.
     */
    const ACTION_DELETESHARD = 'DELETESHARD';

    /**
     * Create or Modify an Alias for a Collection.
     */
    const ACTION_CREATEALIAS = 'CREATEALIAS';

    /**
     * List of all aliases in the cluster.
     */
    const ACTION_LISTALIASES = 'LISTALIASES';

    /**
     * Modify Alias Properties for a Collection.
     */
    const ACTION_ALIASPROP = 'ALIASPROP';

    /**
     * Delete a Collection Alias.
     */
    const ACTION_DELETEALIAS = 'DELETEALIAS';

    /**
     * Delete a Collection.
     */
    const ACTION_DELETE = 'DELETE';

    /**
     * Delete a Replica.
     */
    const ACTION_DELETEREPLICA = 'DELETEREPLICA';

    /**
     * Add Replica.
     */
    const ACTION_ADDREPLICA = 'ADDREPLICA';

    /**
     * Cluster Properties.
     */
    const ACTION_CLUSTERPROP = 'CLUSTERPROP';

    /**
     * Collection Properties.
     */
    const ACTION_COLLECTIONPROP = 'COLLECTIONPROP';

    /**
     * Migrate Documents to Another Collection.
     */
    const ACTION_MIGRATE = 'MIGRATE';

    /**
     * Add a Role.
     */
    const ACTION_ADDROLE = 'ADDROLE';

    /**
     * Remove Role.
     */
    const ACTION_REMOVEROLE = 'REMOVEROLE';

    /**
     * Overseer Status and Statistics.
     */
    const ACTION_OVERSEERSTATUS = 'OVERSEERSTATUS';

    /**
     * Cluster Status.
     */
    const ACTION_CLUSTERSTATUS = 'CLUSTERSTATUS';

    /**
     * Request Status of an Async Call.
     */
    const ACTION_REQUESTSTATUS = 'REQUESTSTATUS';

    /**
     * Delete Status.
     */
    const ACTION_DELETESTATUS = 'DELETESTATUS';

    /**
     * List Collections.
     */
    const ACTION_LIST = 'LIST';

    /**
     * Add Replica Property.
     */
    const ACTION_ADDREPLICAPROP = 'ADDREPLICAPROP';

    /**
     * Delete Replica Property.
     */
    const ACTION_DELETEREPLICAPROP = 'DELETEREPLICAPROP';

    /**
     * Balance a Property Across Nodes.
     */
    const ACTION_BALANCESHARDUNIQUE = 'BALANCESHARDUNIQUE';

    /**
     * Rebalance Leaders.
     */
    const ACTION_REBALANCELEADERS = 'REBALANCELEADERS';

    /**
     * Force Shard Leader.
     */
    const ACTION_FORCELEADER = 'FORCELEADER';

    /**
     * Migrate Cluster State.
     */
    const ACTION_MIGRATESTATEFORMAT = 'MIGRATESTATEFORMAT';

    /**
     * Backup Collection.
     */
    const ACTION_BACKUP = 'BACKUP';

    /**
     * Restore Collection.
     */
    const ACTION_RESTORE = 'RESTORE';

    /**
     * Delete Replicas in a Node.
     */
    const ACTION_DELETENODE = 'DELETENODE';

    /**
     * Move All Replicas in a Node to Another.
     */
    const ACTION_REPLACENODE = 'REPLACENODE';

    /**
     * Move a Replica to a New Node.
     */
    const ACTION_MOVEREPLICA = 'MOVEREPLICA';

    /**
     * Utilize a New Node.
     */
    const ACTION_UTILIZENODE = 'UTILIZENODE';

    /**
     * Action types.
     *
     * @var array
     */
    protected $actionTypes = [
        self::ACTION_CREATE => Create::class,
        /*
        self::ACTION_MODIFYCOLLECTION => 'Solarium\QueryType\Server\Collections\Query\Action\ModifyCollection',
        */
        self::ACTION_RELOAD => Reload::class,
        /*
        self::ACTION_SPLITSHARD => 'Solarium\QueryType\Server\Collections\Query\Action\SplitShard',
        self::ACTION_CREATESHARD => 'Solarium\QueryType\Server\Collections\Query\Action\SplitShard',
        self::ACTION_DELETESHARD => 'Solarium\QueryType\Server\Collections\Query\Action\DeleteShard',
        self::ACTION_CREATEALIAS => 'Solarium\QueryType\Server\Collections\Query\Action\CreateAlias',
        self::ACTION_LISTALIASES => 'Solarium\QueryType\Server\Collections\Query\Action\ListAliases',
        self::ACTION_ALIASPROP => 'Solarium\QueryType\Server\Collections\Query\Action\AliasProp',
        self::ACTION_DELETEALIAS => 'Solarium\QueryType\Server\Collections\Query\Action\DeleteAlias',
        */
        self::ACTION_DELETE => Delete::class,
        /*
        self::ACTION_DELETEREPLICA => 'Solarium\QueryType\Server\Collections\Query\Action\DeleteReplica',
        self::ACTION_ADDREPLICA => 'Solarium\QueryType\Server\Collections\Query\Action\AddReplica',
        self::ACTION_CLUSTERPROP => 'Solarium\QueryType\Server\Collections\Query\Action\ClusterProp',
        self::ACTION_COLLECTIONPROP => 'Solarium\QueryType\Server\Collections\Query\Action\CollectionProp',
        self::ACTION_MIGRATE => 'Solarium\QueryType\Server\Collections\Query\Action\Migrate',
        self::ACTION_ADDROLE => 'Solarium\QueryType\Server\Collections\Query\Action\AddRole',
        self::ACTION_REMOVEROLE => 'Solarium\QueryType\Server\Collections\Query\Action\RemoveRole',
        self::ACTION_OVERSEERSTATUS => 'Solarium\QueryType\Server\Collections\Query\Action\OverseerStatus',
        */
        self::ACTION_CLUSTERSTATUS => ClusterStatus::class,
        /*
        self::ACTION_REQUESTSTATUS => 'Solarium\QueryType\Server\Collections\Query\Action\RequestsStatus',
        self::ACTION_DELETESTATUS => 'Solarium\QueryType\Server\Collections\Query\Action\DeleteStatus',
        self::ACTION_LIST => 'Solarium\QueryType\Server\Collections\Query\Action\List',
        self::ACTION_ADDREPLICAPROP => 'Solarium\QueryType\Server\Collections\Query\Action\AddReplicaProp',
        self::ACTION_DELETEREPLICAPROP => 'Solarium\QueryType\Server\Collections\Query\Action\DeleteReplicaProp',
        self::ACTION_BALANCESHARDUNIQUE => 'Solarium\QueryType\Server\Collections\Query\Action\BalanceShardUnique',
        self::ACTION_REBALANCELEADERS => 'Solarium\QueryType\Server\Collections\Query\Action\RebalanceLeaders',
        self::ACTION_FORCELEADER => 'Solarium\QueryType\Server\Collections\Query\Action\ForceLeader',
        self::ACTION_MIGRATESTATEFORMAT => 'Solarium\QueryType\Server\Collections\Query\Action\MigrateStateFormat',
        self::ACTION_BACKUP => 'Solarium\QueryType\Server\Collections\Query\Action\Backup',
        self::ACTION_RESTORE => 'Solarium\QueryType\Server\Collections\Query\Action\Restore',
        self::ACTION_DELETENODE => 'Solarium\QueryType\Server\Collections\Query\Action\DeleteNode',
        self::ACTION_REPLACENODE => 'Solarium\QueryType\Server\Collections\Query\Action\ReplaceNode',
        self::ACTION_MOVEREPLICA => 'Solarium\QueryType\Server\Collections\Query\Action\MoveReplica',
        self::ACTION_UTILIZENODE => 'Solarium\QueryType\Server\Collections\Query\Action\UtilizeNode',
        */
    ];

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'admin/collections',
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_COLLECTIONS;
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
     * @return ActionInterface|Create
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

    /**
     * @param array $options
     *
     * @return Reload|ActionInterface
     */
    public function createReload(array $options = []): Reload
    {
        return $this->createAction(self::ACTION_RELOAD, $options);
    }

    /**
     * @param array $options
     *
     * @return ClusterStatus|ActionInterface
     */
    public function createClusterStatus(array $options = []): ClusterStatus
    {
        return $this->createAction(self::ACTION_CLUSTERSTATUS, $options);
    }
}
