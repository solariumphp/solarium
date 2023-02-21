Result of a Luke query — Info
=============================

```php
$info = $result->getInfo();
```

Some generally helpful information is available in the result for every show style
that includes more than just the high-level index details. It contains the key to
[flag lists](../luke-query.md#flag-lists) and a note.

Example usage
-------------

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a Luke query
$lukeQuery = $client->createLuke();

$result = $client->luke($lukeQuery);

$info = $result->getInfo();

echo '<h1>info</h1>';

echo '<table>';
echo '<tr><th>key</th><td>';
foreach ($info->getKey() as $abbreviation => $flag) {
    echo $abbreviation . ': ' . $flag . '<br/>';
}
echo '</td></tr>';
echo '<tr><th>NOTE</th><td>' . $info->getNote() . '</td></tr>';
echo '</table>';

htmlFooter();

```
