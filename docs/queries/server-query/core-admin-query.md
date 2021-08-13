CoreAdmin queries can be used to [administrate cores on your Solr server](https://solr.apache.org/guide/coreadmin-api.html).

The CoreAdmin API on the Apache Solr server has several "actions" available and every action can have a set of arguments.

Building a CoreAdmin query
--------------------------

The following example shows how your can build a CoreAdmin query that executes the status action:

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a CoreAdmin query
$coreAdminQuery = $client->createCoreAdmin();

// use the CoreAdmin query to build a Status action
$statusAction = $coreAdminQuery->createStatus();
$statusAction->setCore('techproducts');
$coreAdminQuery->setAction($statusAction);

$response = $client->coreAdmin($coreAdminQuery);
$statusResult = $response->getStatusResult();

echo '<b>CoreAdmin status action execution:</b><br/>';
echo 'Uptime of the core ( ' .$statusResult->getCoreName(). ' ): ' . $statusResult->getUptime();

htmlFooter();
```

Beside the **status** action there are several actions available that can be created with the create***ActionName()*** method.


Available actions
-----------------

The api implements the following actions

Create
======

Use to create a new core.

**Available action methods**:

| Name              | Arguments                     | Description                                                   |
|-------------------|-------------------------------|---------------------------------------------------------------|
| setAsync          | string $async                 | Identifier for async execution to request the status later    |
| setCore           | string $core                  | Name of the core                                              |
| setInstanceDir    | string $instanceDir           | Instance dir that should be used                              |
| setConfig         | string $config                | Name of the config file relative to the instanceDir           |
| setSchema         | string $schema                | Name of the schema file                                       |
| setDataDir        | string $dataDir               | Name of the dataDir relative to the instance dir              |
| setConfigSet      | string $configSet             | Name of the configSet that should be used                     |
| setCollection     | string $collection            | Name of the collection where this core belongs to             |
| setShard          | string $shard                 | ShardId that this core represents                             |
| setCoreProperty   | string $name, string $value   | Entry for the core.properties file, can be used n times       |


MergeIndexes
============

Use to merge cores.

**Available action methods**:

| Name              | Arguments                     | Description                                                   |
|-------------------|-------------------------------|---------------------------------------------------------------|
| setAsync          | string $async                 | Identifier for async execution to request the status later    |
| setCore           | string $core                  | Name of the core where the data should be merged into         |
| setIndexDir       | array $indexDir               | Array of index directories that should be merged              |
| setSrcCore        | array $srcCore                | Array of source cores that should be merged                   |


Reload
======

Use to reload a core.

**Available action methods**:

| Name              | Arguments                     | Description                                                   |
|-------------------|-------------------------------|---------------------------------------------------------------|
| setCore           | string $core                  | Name of the core that should be reloaded                      |

Rename
======

Use to rename a core.

**Available action methods**:

| Name              | Arguments                     | Description                                                   |
|-------------------|-------------------------------|---------------------------------------------------------------|
| setAsync          | string $async                 | Identifier for async execution to request the status later    |
| setCore           | string $core                  | Name of the core that should be renamed                       |
| setOther          | string $otherCoreName         | New name of the core                                          |


RequestRecovery
===============

Use to recover a core.

**Note**: Only relevant for SolrCloud where cores are shards and a cover can be recovered from the leader (a copy of that core on another node).

**Available action methods**:

| Name              | Arguments                     | Description                                                   |
|-------------------|-------------------------------|---------------------------------------------------------------|
| setCore           | string $core                  | Name of the core that should be recovered                     |


RequestStatus
=============

Use to get the status from an asynchronous request. When you have previously passed an async identifier for another action,
RequestStatus can be used later to retrieve the state for that asynchronous action.

**Available action methods**:

| Name              | Arguments                     | Description                                                   |
|-------------------|-------------------------------|---------------------------------------------------------------|
| setRequestId      | string $requestId             | Id of the asynchronous request that was previously executed.  |

Split
=====

Use to split a core.

See also: <https://solr.apache.org/guide/coreadmin-api.html#coreadmin-split>.

**Available action methods**:

| Name              | Arguments                     | Description                                                      |
|-------------------|-------------------------------|------------------------------------------------------------------|
| setAsync          | string $async                 | Identifier for async execution to request the status later       |
| setCore           | string $core                  | Name of the core that should be renamed                          |
| setPath           | array $path                   | Array of pathes where the parts of the splitted index is written |
| setTargetCore     | array $targetCore             | Array of target core names that should be used for splitting     |
| setRanges         | string $ranges                | Comma separated list of hash ranges in hexadecimal format        |
| setSplitKey       | string $splitKey              | Key to be used for splitting the index                           |


Status
======

Use to get the status of one or all cores.

**Note**: When no name is passed the status for all cores will be retrieved.

**Available action methods**:

| Name              | Arguments                     | Description                                                      |
|-------------------|-------------------------------|------------------------------------------------------------------|
| setCore           | string $core                  | Optional name of the core where the status should be retrieved   |

Swap
====

Use to swap a core.

**Available action methods**:

| Name              | Arguments                     | Description                                                   |
|-------------------|-------------------------------|---------------------------------------------------------------|
| setAsync          | string $async                 | Identifier for async execution to request the status later    |
| setCore           | string $core                  | Name of the core that should be swap                          |
| setOther          | string $otherCoreName         | Target core to swap with                                      |

Unload
======

Use to unload a core.

**Available action methods**:

| Name                 | Arguments                     | Description                                                   |
|----------------------|-------------------------------|---------------------------------------------------------------|
| setAsync             | string $async                 | Identifier for async execution to request the status later    |
| setCore              | string $core                  | Name of the core that should be swap                          |
| setDeleteIndex       | bool $boolean                 | Deletes the index directory                                   |
| setDeleteDataDir     | bool $boolean                 | Deletes the data directory                                    |
| setDeleteInstanceDir | bool $boolean                 | Deletes the instance directory                                |


