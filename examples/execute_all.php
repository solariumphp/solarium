<?php

use Solarium\Core\Client\Request;
use Solarium\Exception\HttpException;
use Solarium\Support\Utility;

try {

    require(__DIR__.'/init.php');

    $collection_or_core_name = $config['endpoint']['localhost']['core'] = uniqid();

    // only necessary for solrcloud mode, but needs to be set to retrieve the mode from admin/info/system
    $config['endpoint']['localhost']['username'] = 'solr';
    $config['endpoint']['localhost']['password'] = 'SolrRocks';

    // create a client instance
    $client = new Solarium\Client($adapter, $eventDispatcher, $config);

    $query = $client->createApi([
        'handler' => 'admin/info/system',
        'version' => Request::API_V1,
    ]);

    $data = $client->execute($query)->getData();
    $solr_mode = $data['mode'] ?? 'server';
    $solrSpecVersion = $data['lucene']['solr-spec-version'];
    $solrVersion = (int) strstr($solrSpecVersion, '.', true);

    if ('solrcloud' === $data['mode']) {
        $config['endpoint']['localhost']['collection'] = $config['endpoint']['localhost']['core'];
        unset($config['endpoint']['localhost']['core']);
        // adjust the client instance
        $client = new Solarium\Client($adapter, $eventDispatcher, $config);

        // upload the techproducts configset
        $configsetsQuery = $client->createConfigsets();
        $UploadAction = $configsetsQuery->createUpload();
        $UploadAction
            ->setFile(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'Integration'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'techproducts.zip')
            ->setName('techproducts')
            ->setOverwrite(true);
        $configsetsQuery->setAction($UploadAction);
        $client->configsets($configsetsQuery);

        // create core with unique name using the techproducts configset
        $collectionsQuery = $client->createCollections();
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

    // @todo Figure out why this fails on Solr 9.
    if (9 !== $solrVersion) {
        // check if /mlt handler exists (it will in the github worklow, but not when running this script on its own)
        $query = $client->createApi([
            'version' => Request::API_V1,
            'handler' => $collection_or_core_name.'/config/requestHandler',
        ]);
        $query->addParam('componentName', '/mlt');
        $response = $client->execute($query);
        $mltHandler = $response->getData()['config']['requestHandler']['/mlt'];

        if (null === $mltHandler) {
            // set up /mlt handler for MoreLikeThis query examples
            $query = $client->createApi([
                'version' => Request::API_V1,
                'handler' => $collection_or_core_name.'/config',
                'method' => Request::METHOD_POST,
                'rawdata' => json_encode([
                    'add-requesthandler' => [
                        'name' => '/mlt',
                        'class' => 'solr.MoreLikeThisHandler',
                    ],
                ]),
            ]);
            $client->execute($query);
        }
    }

    // check if attr_* dynamic field definition exists (it was removed from the techproducts configset in Solr 9.1)
    try {
        $query = $client->createApi([
            'version' => Request::API_V1,
            'handler' => $collection_or_core_name.'/schema/dynamicfields/'.rawurlencode('attr_*'),
        ]);
        $response = $client->execute($query);
    } catch (HttpException $e) {
        $query = $client->createApi([
            'version' => Request::API_V1,
            'handler' => $collection_or_core_name.'/schema',
            'method' => Request::METHOD_POST,
            'rawdata' => json_encode([
                'add-dynamic-field' => [
                    'name' => 'attr_*',
                    'type' => 'text_general',
                    'indexed' => true,
                    'stored' => true,
                    'multiValued' => true,
                ],
            ]),
        ]);
        $client->execute($query);
    }

    // disable automatic commits for update examples
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
    $dataDir = __DIR__.
        DIRECTORY_SEPARATOR.'..'.
        DIRECTORY_SEPARATOR.'lucene-solr'.
        DIRECTORY_SEPARATOR.'solr'.
        DIRECTORY_SEPARATOR.'example'.
        DIRECTORY_SEPARATOR.'exampledocs';
    foreach (glob($dataDir.DIRECTORY_SEPARATOR.'*.xml') as $file) {
        $update = $client->createUpdate();
        $update->setRequestFormat($update::REQUEST_FORMAT_XML);

        if (null !== $encoding = Utility::getXmlEncoding($file)) {
            $update->setInputEncoding($encoding);
        }

        $update->addRawXmlFile($file);
        $client->update($update);
    }

    $update = $client->createUpdate();
    $update->addCommit(true, true);
    $client->update($update);

    // examples that can't be run against techproducts
    $skipAltogether = [
        '2.1.5.8-distributed-search.php',
        '7.5.3-plugin-bufferedupdate-benchmarks.php', // intended to be included, not to be run standalone
        '7.5.3.1-plugin-bufferedupdate-benchmarks-xml.php', // takes too long for a workflow, can be run manually
        '7.5.3.2-plugin-bufferedupdate-lite-benchmarks-xml.php', // takes too long for a workflow, can be run manually
        '7.5.3.3-plugin-bufferedupdate-benchmarks-json.php', // takes too long for a workflow, can be run manually
        '7.5.3.4-plugin-bufferedupdate-lite-benchmarks-json.php', // takes too long for a workflow, can be run manually
    ];

    // examples that can't be run against this Solr version
    $skipForSolrVersion = [];

    if (9 === $solrVersion) {
        $skipForSolrVersion[] = '2.3.1-mlt-query.php';
        $skipForSolrVersion[] = '2.3.2-mlt-stream.php';
    }

    // examples that can't be run in cloud mode
    $skipForCloud = [
        '2.1.5.7-grouping-by-query.php',
        '2.2.5-rollback.php',
        '2.10.2-managedresources-stopwords.php',
        '2.10.3-managedresources-synonyms.php',
        '2.11.4-luke-query-doc.php',
        '7.1-plugin-loadbalancer.php',
    ];

    // examples that can't be run in this PHP version
    $skipForPHPVersion = [];

    if (version_compare(PHP_VERSION, '8.1.0', '<')) {
        // @see https://bugs.php.net/bug.php?id=40913
        $skipForPHPVersion[] = '2.7.3.2-extract-query-pdo-lob.php';
    }

    foreach (scandir(__DIR__) as $example) {
        if (preg_match('/^\d.*\.php/', $example)) {
            print "\n".$example.' ';
            if (in_array($example, $skipAltogether)) {
                print 'Could not be run against the techproducts example.';
            } elseif (in_array($example, $skipForSolrVersion)) {
                printf('Could not be run against Solr %d.', $solrVersion);
            } elseif ('solrcloud' === $solr_mode && in_array($example, $skipForCloud)) {
                print 'Could not be run in cloud mode.';
            } elseif (in_array($example, $skipForPHPVersion)) {
                printf('Could not be run in PHP %s.', PHP_VERSION);
            } else {
                ob_start();
                require($example);
                ob_end_clean();
            }
        }
    }

    if ('solrcloud' === $solr_mode) {
        $collectionsQuery = $client->createCollections();
        $deleteAction = $collectionsQuery->createDelete();
        $deleteAction->setName($collection_or_core_name);
        $collectionsQuery->setAction($deleteAction);
        $client->collections($collectionsQuery);

        $configsetsQuery = $client->createConfigsets();
        $action = $configsetsQuery->createDelete();
        $action->setName('techproducts');
        $configsetsQuery->setAction($action);
        $client->configsets($configsetsQuery);
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
