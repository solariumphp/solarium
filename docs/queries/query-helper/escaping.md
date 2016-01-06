The helper offers two types of escaping: term and phrase. Term escaping will escape more characters than phrase escaping. Use phrase escaping when you want to escape a whole phrase and treat is a search string. It will be enclosed in quotes so any special characters lose their meaning and also the search will be done on the whole string as a single phrase. In all other cases use term escaping.

An example of term escaping in use for a query that would fail without escaping:

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// search input string, this value fails without escaping because of the double-quote
$input = 'ATA "133';

// in this case phrase escaping is used (most common) but you can also do term escaping, see the manual
// also note that the same can be done using the placeholder syntax, see example 6.3
$helper = $query->getHelper();
$query->setQuery('features:' . $helper->escapePhrase($input));

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';

    // the documents are also iterable, to get all fields
    foreach ($document as $field => $value) {
        // this converts multivalue fields to a comma-separated string
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table>';
}

htmlFooter();

```
