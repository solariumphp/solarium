<?php
require_once(__DIR__.'/init.php');

use Solarium\Core\Client\Adapter\TimeoutAwareInterface;
use Solarium\Core\Client\Request;

set_time_limit(0);
ini_set('memory_limit', ini_get('suhosin.memory_limit') ?: '-1');
ob_implicit_flush(true);
@ob_end_flush();

htmlHeader();

echo '<h2>Note: These benchmarks can take some time to run!</h2>';

// create a client instance and autoload the buffered add and delete plugins
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$addBuffer = $client->getPlugin('bufferedadd');
$delBuffer = $client->getPlugin('buffereddelete');

// don't timeout
$adapter = $client->getAdapter();
if ($adapter instanceof TimeoutAwareInterface) {
    $adapter->setTimeout(0);
}

// disable automatic commits for the benchmarks
$query = $client->createApi([
    'version' => Request::API_V1,
    'handler' => $config['endpoint']['localhost']['core'].'/config',
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

echo '<table><thead>';
echo '<tr><th>add buffer size</th><th>add time</th><th>delete buffer size</th><th>delete time</th></tr>';
echo '</thead><tbody style="text-align:right">';

foreach ([60, 600, 6000, 60000, 600000] as $bufferSize) {
    echo '<tr><td>'.$bufferSize.'</td>';

    $addBuffer->setBufferSize($bufferSize);
    $delBuffer->setBufferSize($bufferSize * 4);

    $start = hrtime(true);

    for ($i = 0; $i < 1200000; ++$i) {
        $data = [
            'id' => sprintf('test-%08d', $i),
            'name' => 'test for buffered add',
            'cat' => ['solarium-test', 'solarium-test-bufferedadd'],
        ];
        $addBuffer->createDocument($data);
    }

    $addTime = (hrtime(true) - $start) / 1000000;
    echo '<td>'.(int) $addTime.' ms</td>';

    $addBuffer->commit(null, true);

    echo '<td>'.($bufferSize * 4).'</td>';

    $start = hrtime(true);

    for ($i = 0; $i < 1200000; ++$i) {
        $delBuffer->addDeleteById(sprintf('test-%08d', $i));
    }

    $delTime = (hrtime(true) - $start) / 1000000;
    echo '<td>'.(int) $delTime.' ms</td></tr>';

    $delBuffer->commit(null, true);
}

echo '</tbody></table>';

htmlFooter();
