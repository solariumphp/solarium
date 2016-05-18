The highlighting component can be used to highlight matches in content. For more info see <http://wiki.apache.org/solr/HighlightingParameters>

Options
-------

| Name                     | Type    | Default value | Description                                                                        |
|--------------------------|---------|---------------|------------------------------------------------------------------------------------|
| fields                   | string  | null          | Fields to generate highlighted snippets for. Separate multiple fields with commas. |
| snippets                 | int     | null          | Maximum number of snippets per field                                               |
| fragsize                 | int     | null          | The size, in characters, of fragments to consider for highlighting                 |
| mergecontiguous          | boolean | null          | Collapse contiguous fragments into a single fragment                               |
| requirefieldmatch        | boolean | null          | requireFieldMatch option                                                           |
| maxanalyzedchars         | int     | null          | How many characters into a document to look for suitable snippets                  |
| alternatefield           | string  | null          | Alternatefield option                                                              |
| maxalternatefieldlength  | int     | null          | maxAlternateFieldLength option                                                     |
| formatter                | string  | null          | formatter option                                                                   |
| simpleprefix             | string  | null          | Solr option h1.simple.pre                                                          |
| simplepostfix            | string  | null          | Solr option h1.simple.post                                                         |
| fragmenter               | string  | null          |                                                                                    |
| fraglistbuilder          | string  | null          |                                                                                    |
| fragmentsbuilder         | string  | null          |                                                                                    |
| usefastvectorhighlighter | boolean | null          |                                                                                    |
| usephrasehighlighter     | boolean | null          |                                                                                    |
| highlightmultiterm       | boolean | null          |                                                                                    |
| regexslop                | float   | null          |                                                                                    |
| regexpattern             | string  | null          |                                                                                    |
| regexmaxanalyzedchars    | int     | null          |                                                                                    |
| query                    | string  | null          | Overrides the q parameter for highlighting                                         |
| phraselimit              | int     | null          |                                                                                    |
| multivaluedseparatorchar | string  | null          |                                                                                    |
| boundaryscannerchars     | string  | null          |                                                                                    |
| boundaryscannermaxscan   | int     | null          |                                                                                    |
| boundaryscannertype      | string  | null          |                                                                                    |
| boundaryscannercountry   | string  | null          |                                                                                    |
||

Per-field settings
------------------

Several options can be overridden on a per-field basis. You can use the `getField` method to get a field options instance. See the example below.

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
$query->setQuery('memory');

// get highlighting component and apply settings
$hl = $query->getHighlighting();
$hl->setFields('name, features');
$hl->setSimplePrefix('<b>');
$hl->setSimplePostfix('</b>');

// this executes the query and returns the result
$resultset = $client->select($query);
$highlighting = $resultset->getHighlighting();
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

    echo '</table><br/><b>Highlighting results:</b><br/>';

    // highlighting results can be fetched by document id (the field defined as uniquekey in this schema)
    $highlightedDoc = $highlighting->getResult($document->id);
    if ($highlightedDoc) {
        foreach ($highlightedDoc as $field => $highlight) {
            echo implode(' (...) ', $highlight) . '<br/>';
        }
    }

}

htmlFooter();

```

Per-field settings:

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();
$query->setQuery('memory');

// get highlighting component and apply settings
// highlights are applied to three fields with a different markup for each field
// much more per-field settings are available, see the manual for all options
$hl = $query->getHighlighting();
$hl->getField('name')->setSimplePrefix('<b>')->setSimplePostfix('</b>');
$hl->getField('cat')->setSimplePrefix('<u>')->setSimplePostfix('</u>');
$hl->getField('features')->setSimplePrefix('<i>')->setSimplePostfix('</i>');

// this executes the query and returns the result
$resultset = $client->select($query);
$highlighting = $resultset->getHighlighting();
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

    echo '</table><br/><b>Highlighting results:</b><br/>';

    // highlighting results can be fetched by document id (the field defined as uniquekey in this schema)
    $highlightedDoc = $highlighting->getResult($document->id);
    if ($highlightedDoc) {
        foreach ($highlightedDoc as $field => $highlight) {
            echo implode(' (...) ', $highlight) . '<br/>';
        }
    }

}

htmlFooter();

```
