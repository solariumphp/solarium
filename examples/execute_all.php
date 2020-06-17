<?php

use Solarium\Core\Client\Request;
use Solarium\Support\Utility;

try {

    require(__DIR__.'/init.php');

    $collection_or_core_name = $config['endpoint']['localhost']['core'] = uniqid();

    // create a client instance
    $client = new Solarium\Client($adapter, $eventDispatcher, $config);

    $query = $client->createApi([
        'handler' => 'admin/info/system',
        'version' => Request::API_V1,
    ]);

    $data = $client->execute($query)->getData();
    $solr_mode = $data['mode'] ?? 'server';

    if ('solrcloud' === $data['mode']) {
        $config['endpoint']['localhost']['collection'] = $config['endpoint']['localhost']['core'];
        unset($config['endpoint']['localhost']['core']);
        // adjust the client instance
        $client = new Solarium\Client($adapter, $eventDispatcher, $config);

        $collectionsQuery = $client->createCollections();

        // create core with unique name using the techproducts configset
        $createAction = $collectionsQuery->createCreate();
        $createAction->setName($collection_or_core_name)
            ->setCollectionConfigName('techproducts')
            ->setNumShards(2);
        $collectionsQuery->setAction($createAction);
        $client->collections($collectionsQuery);
    } else {
        $coreAdminQuery = $client->createCoreAdmin();

        // create core with unique name using the techproducts configset
        $createAction = $coreAdminQuery->createCreate();
        $createAction->setCore($collection_or_core_name)
            ->setConfigSet('sample_techproducts_configs');
        $coreAdminQuery->setAction($createAction);
        $response = $client->coreAdmin($coreAdminQuery);
    }

    // disable automatic commits for update tests
    $query = $client->createApi([
        'version' => Request::API_V1,
        'handler' => $collection_or_core_name.'/config',
        'method' => Request::METHOD_POST,
        'rawdata' => json_encode([
            'set-property' => [
                'updateHandler.autoCommit.maxDocs' => -1,
                'updateHandler.autoCommit.maxTime' => -1,
                'updateHandler.autoCommit.openSearcher' => true,
                'updateHandler.autoSoftCommit.maxDocs' => -1,
                'updateHandler.autoSoftCommit.maxTime' => -1,
            ],
        ]),
    ]);
    $client->execute($query);

    // index techproducts sample data
    foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'Integration'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'techproducts'.DIRECTORY_SEPARATOR.'*.xml') as $file) {
        $update = $client->createUpdate();

        if (null !== $encoding = Utility::getXmlEncoding($file)) {
            $update->setInputEncoding($encoding);
        }

        $update->addRawXmlFile($file);
        $client->update($update);
    }

    $update = $client->createUpdate();
    $update->addCommit(true, true);
    $client->update($update);

    foreach (scandir(__DIR__) as $example) {
        if (preg_match('/^\d.*\.php/', $example)) {
            print "\n".$example.' ';
            if (!in_array($example, ['2.1.5.8-distributed-search.php', '2.3.2-mlt-stream.php'])) {
                if ('solrcloud' !== $solr_mode || !in_array($example, ['2.2.5-rollback.php', '7.1-plugin-loadbalancer.php'])) {
                    ob_start();
                    require($example);
                    ob_end_clean();
                } else {
                    print 'Could not be run in cloud mode.';
                }

            } else {
                print 'Could not be run against the techproducts example.';
            }
        }
    }

    if ('solrcloud' === $solr_mode) {
        $collectionsQuery = $client->createCollections();
        $deleteAction = $collectionsQuery->createDelete();
        $deleteAction->setName($collection_or_core_name);
        $collectionsQuery->setAction($deleteAction);
        $client->collections($collectionsQuery);
    } else {
        $coreAdminQuery = $client->createCoreAdmin();
        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore($collection_or_core_name)
            ->setDeleteDataDir(true)
            ->setDeleteIndex(true)
            ->setDeleteInstanceDir(true);
        $coreAdminQuery->setAction($unloadAction);
        $client->coreAdmin($coreAdminQuery);
    }
} catch (\Exception $e) {
    print $e;
    exit(1);
}

print "\n\n";
exit(0);