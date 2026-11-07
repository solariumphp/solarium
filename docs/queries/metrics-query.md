Metrics query
=============

A Metrics query can be used to access Solr's raw metrics data.

Building a Metrics query
------------------------

See the example code below.

**Available options:**

| Name           | Type   | Default value                                | Description                                                                                                                 |
|----------------|--------|----------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------|
| handler        | string | admin/metrics                                | Path to the metrics handler as configured in Solr                                                                           |
| responsewriter | string | prometheus                                   | Response writer format. Use one of the `WT_*` class constants as value.                                                     |
| resultclass    | string | Solarium\\QueryType\\Server\\Metrics\\Result | Classname for result. If you set a custom classname make sure the class is readily available (or through autoloading).      |
| name           | string |                                              | Metric name to filter on. Separate multiple names with commas.                                                              |
| category       | string |                                              | Category label to filter on. Separate multiple labels with commas.                                                          |
| core           | string |                                              | Core name to filter on. Separate multiple names with commas.                                                                |
| collection     | string |                                              | Collection name to filter on. Separate multiple names with commas.                                                          |
| shard          | string |                                              | Shard name to filter on. Separate multiple names with commas.                                                               |
| replica_type   | string |                                              | Replica type to filter on. Separate multiple types with commas. You can use the `REPLICA_TYPE_*` class constants as values. |
||

Executing a Metrics query
-------------------------

Use the `metrics` method of the client to execute the query object. See the example code below.

Result of a Metrics query
-------------------------

The result of a Metrics query is in the Prometheus format by default. This format is available since Solr 9.
Starting with Solr 10 this can be changed to OpenMetrics format.

```php
$query->setResponseWriter($query::WT_OPENMETRICS);
```

To parse either of these formats, you need to install the `butschster/prometheus-parser` library separately.

```sh
composer require butschster/prometheus-parser
```

Without this package, Solarium will throw a `RuntimeException` if you try to use `$result->getMetricSet()`.
You can still get the raw response data as a single large string.

```php
/** @var string $data */
$data = $result->getData()['metrics'];
```

Solr 9 and older versions could return metrics in JSON format. Solr 10 no longer supports this.
Unlike for other query types, Solarium requires you to set the JSON response writer explicitly in this case.
The JSON response is decoded as an array but not parsed into gettable properties on the Result object.

```php
$query->setResponseWriter($query::WT_JSON);
$result = $client->metrics($query);

/** @var array<string, array> $data */
$data = $result->getData()['metrics'];
```

Example
-------

### Prometheus response format (Solr 9 or higher)

```php
<?php

use Composer\InstalledVersions;

require_once __DIR__.'/init.php';

htmlHeader();

echo '<h2>Note: This example assumes a Prometheus response format from Solr 9 or higher</h2>';
echo '<p>Refer to <a href="2.12.2-metrics-query-json.php">Metrics query with JSON response</a> for older Solr versions.</p>';

if (!InstalledVersions::isInstalled('butschster/prometheus-parser')) {
    echo '<h2>Note: Parsing the Prometheus response format requires butschster/prometheus-parser</h2>';
}

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a metrics query instance
$query = $client->createMetrics();

// you can filter specific metrics
$query->setCategory('UPDATE');

// this executes the query and returns the result
$result = $client->metrics($query);

// without butschster/prometheus-parser installed, you can only get the raw output
echo '<h2>Raw output</h2>';
echo '<textarea rows="20" cols="200">'.htmlspecialchars($result->getData()['metrics']).'</textarea>';

// with butschster/prometheus-parser installed, you can get a result object
echo '<h2>Structured output</h2>';
echo '<p>Check the documentation at <a href="https://github.com/butschster/prometheus-parser">';
echo 'https://github.com/butschster/prometheus-parser</a> for more information on the structure ';
echo 'of the object returned by <code>$result->getMetricSet()</code>.</p>';

foreach ($result->getMetricSet()->getMetrics() as $metricFamily) {
    echo '<h3>'.$metricFamily->name.'</h3>';
    echo '<b>Description: '.$metricFamily->description.'</b><br/>';
    echo '<b>Type: '.$metricFamily->type.'</b><br/>';

    if (null !== $metricFamily->unit) {
        echo '<b>Unit: '.$metricFamily->unit.'</b><br/>';
    }

    foreach ($metricFamily->metrics as $metric) {
        echo '<hr/>';
        echo 'Labels: ';
        foreach ($metric->labels as $label) {
            echo '['.$label->name.'="'.$label->value.'"] ';
        }
        echo '<br/>';
        echo 'Value: '.$metric->value.'<br/>';
    }

    echo '<hr/>';
}

htmlFooter();

```

### JSON response format (Solr 9 or lower)

```php
<?php

require_once __DIR__.'/init.php';

htmlHeader();

echo '<h2>Note: This example assumes a JSON response format from Solr 9 or lower</h2>';
echo '<p>Refer to <a href="2.12.1-metrics-query.php">Metrics query with Prometheus response</a> for newer Solr versions.</p>';

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a metrics query instance
$query = $client->createMetrics();

// JSON response writer has to be set explicitly on a Metrics query
$query->setResponseWriter($query::WT_JSON);

// this executes the query and returns the result
$result = $client->metrics($query);

// result data for a JSON response is only accessible as an array
foreach ($result->getData()['metrics'] as $registry => $metrics) {
    echo '<h3>'.$registry.'</h3>';

    foreach ($metrics as $name => $properties) {
        echo '<h3>'.$name.'</h3>';

        if (is_scalar($properties)) {
            echo $properties.'<br/>';
        } else {
            echo '<table>';

            foreach ($properties as $property => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }

                echo '<tr><th>'.$property.'</th><td>'.$value.'</td></tr>';
            }

            echo '<table>';
        }
    }

    echo '<hr/>';
}

htmlFooter();
```
