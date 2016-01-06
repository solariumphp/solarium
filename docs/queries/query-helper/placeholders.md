The query helper has an 'assemble' method that can be used for building query strings in a way that is somewhat similar to SQL prepared statements. The placeholder syntax will be used via the built-in support in various (query) classes in most cases, but they all map to the same 'assemble' method of the query helper.

This method supports the following placeholder syntax:

| Placeholder | Description                                                                                                                                                                                   |
|-------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| %1%         | This means: replace with the literal contents of the first entry of the supplied array of bind vars                                                                                           |
| %L3%        | Replace with the literal contents of the third entry of the supplied array of bind vars. This is the default, so if you don't supply a 'mode' like in the first example literal will be used. |
| %P2%        | Replace with the contents of the second bind var entry, escaping as a phrase                                                                                                                  |
| %T1%        | Replace with the contents of the second bind var entry, escaping as a term                                                                                                                    |
||

Some notes:

-   The bind vars are supplied as an array. The placeholder number refers to the position of the entry in the array, not the array key! Position 1 is actually array key 0.
-   You can use the same bind var multiple times, even with different modes (literal, phrase or term)
-   Placeholder and bind var order don't need to match, just refer to the right positions.
-   The letters for the modes L (Literal), P (Phrase) and T (Term) are not case-sensitive.

Support in classes
------------------

Currently the following classes have built-in support for the placeholder syntax, making it even easier to use because you don't even need to call the query helper yourself:

-   Select query method setQuery($query, $bind = null)
-   Facet Query method setQuery($query, $bind = null)
-   FilterQuery method setQuery($query, $bind = null)
-   Spellcheck component methode setQuery($query, $bind = null)

In all other cases you can always use the 'assemble' method of the query helper and use the result as a normal query string.

Example
-------

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

// the placeholder syntax applies phrase escaping to the first term
// see the manual for all supported formats
$query->setQuery('features: %p1% AND inStock:%2%', array($input, 1));

// show the result after replacing the placeholders with values
echo $query->getQuery() . '<br/>';

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
