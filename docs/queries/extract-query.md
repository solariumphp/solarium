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

Extract queries can also take a stream URL or a stream resource instead of the name of a local file.

Remote streaming is disabled by default. Consult the reference guide on
[Content Streams](https://solr.apache.org/guide/solr/latest/indexing-guide/content-streams.html) for more info.

```php
$query->setFile('http://example.org/resource');
```

You can even index files that aren't stored on the filesystem: generated in memory, retrieved as a textual or binary large object from a database …

If your content is generated in memory, you can create a temporary file with [`tmpfile()`](https://www.php.net/manual/en/function.tmpfile.php)
or write it to a [`php://memory` or `php://temp`](https://www.php.net/manual/en/wrappers.php.php#wrappers.php.memory) stream
that can be passed to an Extract query. Don't forget to close your file pointer afterwards!

```php
$contents = '...';
$file = tmpfile();
fwrite($file, $contents);
$query->setFile($file);

// ...

$client->extract($query);
fclose($file);
```

```php
$contents = '...';
$file = fopen('php://memory', 'w+');
fwrite($file, $contents);
$query->setFile($file);

// ...

$client->extract($query);
fclose($file);
```

If your content is stored in a database, you can fetch it as [PDO Large Objects (LOBs)](https://www.php.net/manual/en/pdo.lobs.php).

```php
$db = new PDO(...);

$select = $db->prepare("SELECT content FROM table WHERE id = ?");
$select->execute($id);
$select->bindColumn(1, $content, PDO::PARAM_LOB);
$select->fetch(PDO::FETCH_BOUND);

$query->setFile($content);

// ...

$client->extract($query);
```

**Note:** Using a LOB as a stream doesn't work in PHP < 8.1.0 because of [PHP Bug #40913](https://bugs.php.net/bug.php?id=40913).

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

### Extract from generated content

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

// open a file pointer resource and add it to the query
$file = tmpfile();
$query->setFile($file);

// write generated content to the file pointer
ob_start();
phpcredits();
fwrite($file, ob_get_contents());
ob_end_clean();

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

### Extract from PDO Large Objects (LOBs)

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

echo "<h2>Note: This example doesn't work in PHP &lt; 8.1.0!</h2>";
echo "<h2>Note: This example requires the PDO_SQLITE PDO driver (enabled by default in PHP)</h2>";

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an extract query instance and add settings
$query = $client->createExtract();
$query->addFieldMapping('content', 'text');
$query->setUprefix('attr_');
$query->setCommit(true);
$query->setOmitHeader(false);

// create a database & store content as an example
$db = new PDO('sqlite::memory:');
$db->exec("CREATE TABLE test (id INT, content TEXT)");
$insert = $db->prepare("INSERT INTO test (id, content) VALUES (:id, :content)");
$insert->execute(['id' => 1, 'content' => file_get_contents(__DIR__.'/index.html')]);

// get content from the database and map it as a stream
$select = $db->prepare("SELECT content FROM test WHERE id = :id");
$select->execute(['id' => 1]);
$select->bindColumn(1, $content, PDO::PARAM_LOB);
$select->fetch(PDO::FETCH_BOUND);

// add content as a stream resource
$query->setFile($content);

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
