A suggester query is a fast way to create an autocomplete feature. For more info on the Solr suggester component see: <http://wiki.apache.org/solr/Suggester>

Building a suggester query
--------------------------

See the example code below.

**Available options:**

| Name            | Type    | Default value | Description                                                                            |
|-----------------|---------|---------------|----------------------------------------------------------------------------------------|
| query           | string  | null          | Query to spellcheck                                                                    |
| dictionary      | string  | null          | The name of the dictionary to use                                                      |
| onlymorepopular | boolean | null          | Only return suggestions that result in more hits for the query than the existing query |
| collate         | boolean | null          |                                                                                        |
||

Executing a terms query
-----------------------

Use the `suggester` method of the client to execute the query object. See the example code below.

Result of a terms query
-----------------------

The result of a terms query offers direct access to the resulting suggestions, and can also be iterated.

Example
-------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a suggester query instance
$query = $client->createSuggester();
$query->setQuery('ap ip v'); //multiple terms
$query->setDictionary('suggest');
$query->setOnlyMorePopular(true);
$query->setCount(10);
$query->setCollate(true);

// this executes the query and returns the result
$resultset = $client->suggester($query);

echo '<b>Query:</b> '.$query->getQuery().'<hr/>';

// display results for each term
foreach ($resultset as $term => $termResult) {
    echo '<h3>' . $term . '</h3>';
    echo 'NumFound: '.$termResult->getNumFound().'<br/>';
    echo 'StartOffset: '.$termResult->getStartOffset().'<br/>';
    echo 'EndOffset: '.$termResult->getEndOffset().'<br/>';
    echo 'Suggestions:<br/>';
    foreach ($termResult as $result) {
        echo '- '.$result.'<br/>';
    }

    echo '<hr/>';
}

// display collation
echo 'Collation: '.$resultset->getCollation();

htmlFooter();

```
