<?php

require_once(__DIR__.'/init.php');

use Solarium\Core\Client\Adapter\TimeoutAwareInterface;
use Solarium\Core\Client\Request;

set_time_limit(0);
ini_set('memory_limit', ini_get('suhosin.memory_limit') ?: '-1');
ob_implicit_flush(true);
@ob_end_flush();

htmlHeader();

if (!isset($weight) || !isset($requestFormat)) {
    echo <<<'EOT'
        <h1>Usage</h1>

        <p>This file is intended to be included by a script that sets two variables:</p>

        <dl>
            <dt><code>$weight</code></dt>
            <dd>Either <code>''</code> for the regular plugins or <code>'lite'</code> for the lite versions.</dd>
            <dt><code>$requestFormat</code></dt>
            <dd>Any of the <code>Solarium\QueryType\Update\Query\Query::REQUEST_FORMAT_*</code> constants.</dd>
        </dl>

        <h2>Example</h2>

        <pre>
        &lt;?php

        require_once(__DIR__.'/init.php');

        use Solarium\QueryType\Update\Query\Query;

        $weight = '';
        $requestFormat = Query::REQUEST_FORMAT_JSON;

        require(__DIR__.'/7.5.3-plugin-bufferedupdate-benchmarks.php');
        </pre>
        EOT;

    htmlFooter();

    exit;
}

echo '<h2>Note: These benchmarks can take some time to run!</h2>';

// create a client instance and don't let the adapter timeout
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
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

// autoload the buffered add and delete plugins
$addBuffer = $client->getPlugin($addPlugin = 'bufferedadd'.$weight);
$delBuffer = $client->getPlugin($delPlugin = 'buffereddelete'.$weight);

$addBuffer->setRequestFormat($requestFormat);
$delBuffer->setRequestFormat($requestFormat);

echo '<h3>'.$addPlugin.' / '.$delPlugin.' ('.strtoupper($requestFormat).')</h3>';
echo '<table><thead>';
echo '<tr><th>add buffer size</th><th>add time</th>';
echo '<th>delete buffer size</th><th>delete time</th>';
echo '<th>mem peak usage</th></tr>';
echo '</thead><tbody style="text-align:right">';

$docs = 1200000;
foreach ([2000, 200, 20, 2] as $flushes) {
    $addBufferSize = $docs / $flushes;
    $delBufferSize = min(($docs / $flushes) * 4, $docs);

    $addBuffer->setBufferSize($addBufferSize);
    $delBuffer->setBufferSize($delBufferSize);

    echo '<tr><td>'.$addBufferSize.'</td>';

    $start = hrtime(true);

    for ($i = 0; $i < $docs; ++$i) {
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

    echo '<td>'.$delBufferSize.'</td>';

    $start = hrtime(true);

    for ($i = 0; $i < $docs; ++$i) {
        $delBuffer->addDeleteById(sprintf('test-%08d', $i));
    }

    $delTime = (hrtime(true) - $start) / 1000000;
    echo '<td>'.(int) $delTime.' ms</td>';

    $delBuffer->commit(null, true);

    $memoryPeakUsage = memory_get_peak_usage() / 1024;
    echo '<td>'.(int) $memoryPeakUsage.' KiB</td></tr>';
}

echo '</tbody></table>';

htmlFooter();
