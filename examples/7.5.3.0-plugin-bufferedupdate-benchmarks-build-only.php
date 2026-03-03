<?php

use Composer\InstalledVersions;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest;
use Solarium\QueryType\Update\Query\Query;

require_once __DIR__.'/init.php';

set_time_limit(0);
ini_set('memory_limit', -1);
ob_implicit_flush(true);
@ob_end_flush();

htmlHeader();

echo '<h2>Note: These benchmarks build the requests but don\'t execute them</h2>';

$requestFormats = [
    Query::REQUEST_FORMAT_XML,
    Query::REQUEST_FORMAT_JSON,
];

if (InstalledVersions::isInstalled('spomky-labs/cbor-php')) {
    $requestFormats[] = Query::REQUEST_FORMAT_CBOR;
} else {
    echo '<h2>Note: The CBOR benchmark requires spomky-labs/cbor-php</h2>';
}

// memory usage is only useful with PHP 8.2+, earlier version don't allow resetting between benchmarks
$withMemoryUsage = function_exists('memory_reset_peak_usage');

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// autoload the buffered add plugin
$addBuffer = $client->getPlugin('bufferedaddlite');

// return a dummy response instead of executing the query
$response = new Response('', ['HTTP/1.0 200 OK']);

// keep measures for individual build times
$start = 0;
$buildTimes = [];

$client->getEventDispatcher()->addListener(
    Events::PRE_EXECUTE_REQUEST,
    function (PreExecuteRequest $event) use ($response, &$start, &$buildTimes): void {
        $buildTimes[] = (hrtime(true) - $start) / 1000000;
        $event->setResponse($response);
        $start = hrtime(true);
    }
);

$docs = 1200000;

foreach ($requestFormats as $requestFormat) {
    $addBuffer->setRequestFormat($requestFormat);

    echo '<h3>'.strtoupper($requestFormat).'</h3>';
    echo '<table><thead>';
    echo '<tr><th rowspan="2">buffer size</th><th colspan="5">build time</th>';
    if ($withMemoryUsage) {
        echo '<th rowspan="2">mem peak usage</th>';
    }
    echo '</tr>';
    echo '<tr><th>min</th><th>max</th><th>mean</th><th>median</th><th>total</th></tr>';
    echo '</thead><tbody style="text-align:right">';

    foreach ([2000, 200, 20, 2] as $flushes) {
        $bufferSize = $docs / $flushes;

        $addBuffer->setBufferSize($bufferSize);

        echo '<tr><td>'.$bufferSize.'</td>';

        $buildTimes = [];

        if ($withMemoryUsage) {
            memory_reset_peak_usage();
        }

        $start = hrtime(true);

        for ($i = 0; $i < $docs; ++$i) {
            $data = [
                'id' => sprintf('test-%08d', $i),
                'name' => 'test for buffered add',
                'cat' => ['solarium-test', 'solarium-test-bufferedadd'],
            ];
            $addBuffer->createDocument($data);
        }

        sort($buildTimes);
        $halfway = $flushes / 2;
        $total = array_sum($buildTimes);
        $min = reset($buildTimes);
        $max = end($buildTimes);
        $mean = $total / $flushes;
        $median = ($buildTimes[$halfway - 1] + $buildTimes[$halfway]) / 2;
        echo '<td>'.(int) $min.' ms</td>';
        echo '<td>'.(int) $max.' ms</td>';
        echo '<td>'.(int) $mean.' ms</td>';
        echo '<td>'.(int) $median.' ms</td>';
        echo '<td>'.(int) $total.' ms</td>';

        if ($withMemoryUsage) {
            $memoryPeakUsage = memory_get_peak_usage() / 1024;
            echo '<td>'.(int) $memoryPeakUsage.' KiB</td>';
        }

        echo '</tr>';
    }

    echo '</tbody></table>';
}

htmlFooter();
