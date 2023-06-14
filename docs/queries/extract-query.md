An extract query can be used to index files in Solr. For more info see <https://solr.apache.org/guide/uploading-data-with-solr-cell-using-apache-tika.html>.

Building an extract query
-------------------------

See the example code below.

**Available options:**

| Name          | Type    | Default value                 | Description                                                                                                                                   |
|---------------|---------|-------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------|
| handler       | string  | select                        | Name of the Solr request handler to use, without leading or trailing slashes                                                                  |
| resultclass   | string  | Solarium\_Result\_Select      | Classname for result. If you set a custom classname make sure the class is readily available (or through autoloading)                         |
| documentclass | string  | Solarium\_Document\_ReadWrite | Classname for documents in the resultset. If you set a custom classname make sure the class is readily available (or through autoloading)     |
| omitheader    | boolean | true                          | Disable Solr headers (saves some overhead, as the values aren't actually used in most cases)                                                  |
| extractonly   | boolean | false                         | If true, returns the extracted content from Tika without indexing the document                                                                |
| extractformat | string  | null                          | Controls the serialization format of the extracted content. By default 'xml', the other option is 'text'. Only valid if 'extractonly' is true |
||

Executing an extract query
--------------------------

First of all create an Extract query instance and set the options, a file and document. Use the `extract` method of the client to execute the query object.

See the example code below.

### Extracting from other sources

Extract queries can also take a stream URL or a file pointer resource instead of the name of a local file.

Remote streaming is disabled by default. Consult the reference guide on
[Content Streams](https://solr.apache.org/guide/solr/latest/indexing-guide/content-streams.html) for more info.

```php
$query->setFile('http://example.org/resource');
```

You can even index files that aren't stored on the filesystem: generated in memory, retrieved as a BLOB from a database …
Don't forget to close your file pointer afterwards!

```php
$contents = '...';
$file = tmpfile();
fwrite($file, $contents);
$query->setFile($file);

// ...

$client->extract($query);
fclose($file);
```

Result of an extract query
--------------------------

The result of an indexing extract query is similar to an update query.

With `extractonly` set to `true`, the extracted data is available in the result instead.

```php
$contents = $result->getFile();
$metadata = $result->getFileMetadata();
```

Examples
--------

### Extract from a file

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an extract query instance and add settings
$query = $client->createExtract();
$query->addFieldMapping('content', 'text');
$query->setUprefix('attr_');
$query->setFile(__DIR__.'/index.html');
$query->setCommit(true);
$query->setOmitHeader(false);

// add document
$doc = $query->createDocument();
$doc->id = 'extract-test';
$doc->some = 'more fields';
$query->setDocument($doc);

// this executes the query and returns the result
$result = $client->extract($query);

echo '<b>Extract query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();

```

### Extracting without indexing

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an extract query instance and add settings
$query = $client->createExtract();
$query->setFile(__DIR__.'/index.html');
$query->setExtractOnly(true);
$query->setExtractFormat($query::EXTRACT_FORMAT_TEXT);

// this executes the query and returns the result
$result = $client->extract($query);

echo '<b>Extract query executed</b><br/>';

echo '<textarea readonly="readonly" style="width:100%;height:400px">';
echo htmlspecialchars(trim($result->getFile()));
echo '</textarea>';

echo '<table>';

foreach ($result->getFileMetadata() as $field => $value) {
    if (is_array($value)) {
        $value = implode('<br/>', $value);
    }

    echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
}

echo '</table>';

htmlFooter();

```

### Extract from a resource

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an extract query instance and add settings
$query = $client->createExtract();
$query->addFieldMapping('content', 'text');
$query->setUprefix('attr_');
$query->setCommit(true);
$query->setOmitHeader(false);

// add content through a file pointer resource
$content = 'File contents that were generated, retrieved as a BLOB from a database …';
$file = tmpfile();
fwrite($file, $content);
$query->setFile($file);

// add document
$doc = $query->createDocument();
$doc->id = 'extract-test';
$doc->some = 'more fields';
$query->setDocument($doc);

// this executes the query and returns the result
$result = $client->extract($query);

echo '<b>Extract query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

// don't forget to close your file pointer!
fclose($file);

htmlFooter();

```
