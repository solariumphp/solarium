A select query has options and commands. These commands and options are instructions for the client classes to build and execute a request and return the correct result. In the following sections both the options and commands will be discussed in detail.

Options
-------

The options below can be set as query option values, but also by using the set/get methods. See the API docs for all available methods.

| Name                 | Type             | Default value                 | Description                                                                                                                                                         |
|----------------------|------------------|-------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| handler              | string           | select                        | Name of the Solr request handler to use, without leading or trailing slashes                                                                                        |
| resultclass          | string           | Solarium\_Result\_Select      | Classname for result. If you set a custom classname make sure the class is readily available (or through autoloading)                                               |
| documentclass        | string           | Solarium\_Document\_ReadWrite | Classname for documents in the resultset. If you set a custom classname make sure the class is readily available (or through autoloading)                           |
| query                | string           | \*:\*                         | Query to execute                                                                                                                                                    |
| start                | int              | 0                             | Start position (offset) in the complete Solr query resultset, to paginate big resultsets.                                                                           |
| rows                 | integer          | 10                            | Number of rows to fetch, starting from the 'start' (offset) position. It's a limit, you might get less.                                                             |
| fields               | string           | -   ,score                    | Comma separated list of fields to fetch from Solr. There are two special values: '\*' meaning 'all fields' and 'score' to also fetch the Solr document score value. |
| sort                 | array            | empty array                   | Array with sort field as key and sort order as values. Multiple entries possible, they are used in the order of the array. Example: array('price' =&gt; 'asc')      |
| querydefaultoperator | string           | null                          | With a null value the default of your Solr config will be used. If you want to override this supply 'AND' or 'OR' as the value.                                     |
| querydefaultfield    | string           | null                          | With a null value the default of your Solr config will be used. If you want to override this supply a field name as the value.                                      |
| responsewriter       | string           | json                          | You can set this to 'phps' for improved response parsing performance, at the cost of a (possible) security risk. Only use 'phps' for trusted Solr instances.        |
| tag                  | array of strings | null                          | You can supply one or multiple tags for the main query string, to allow for exclusion of the main query in facets                                                   |
||

Examples
--------

A simple select with some params, using the API mode:

```php
<?php

require(__DIR__.'/init.php');

htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// set a query (all prices starting from 12)
$query->setQuery('price:[12 TO *]');

// set start and rows param (comparable to SQL limit) using fluent interface
$query->setStart(2)->setRows(20);

// set fields to fetch (this overrides the default setting 'all fields')
$query->setFields(array('id','name','price', 'score'));

// sort the results by price ascending
$query->addSort('price', $query::SORT_ASC);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// display the max score
echo '<br>MaxScore: '.$resultset->getMaxScore();

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

An example using the select query in config mode: (filterqueries and components will be explained in following sections)

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();


// In this case an array is used for configuration to keep the example simple.
// For an easier to use config file you are probably better of with another format, like Zend_Config_Ini
// See the documentation for more info about this.
$select = array(
    'query'         => '*:*',
    'start'         => 2,
    'rows'          => 20,
    'fields'        => array('id','name','price'),
    'sort'          => array('price' => 'asc'),
    'filterquery' => array(
        'maxprice' => array(
            'query' => 'price:[1 TO 300]'
        ),
    ),
    'component' => array(
        'facetset' => array(
            'facet' => array(
                // notice this config uses an inline key value, instead of array key like the filterquery
                array('type' => 'field', 'key' => 'stock', 'field' => 'inStock'),
            )
        ),
    ),
);

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance based on the config
$query = $client->createSelect($select);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet counts for field "inStock":<br/>';
$facet = $resultset->getFacetSet()->getFacet('stock');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';
    echo '<tr><th>id</th><td>' . $document->id . '</td></tr>';
    echo '<tr><th>name</th><td>' . $document->name . '</td></tr>';
    echo '<tr><th>price</th><td>' . $document->price . '</td></tr>';
    echo '</table>';
}

htmlFooter();

```
