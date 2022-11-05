The highlighting component can be used to highlight matches in content. For more info see <https://solr.apache.org/guide/highlighting.html>.

Options
-------

### Common options

| Name                     | Type    | Default value | Description                                                                                     |
|--------------------------|---------|---------------|-------------------------------------------------------------------------------------------------|
| usefastvectorhighlighter | boolean | null          | Use the FastVector Highlighter. You should set `method` to 'fastVector' instead since Solr 6.4. |
| method                   | string  | null          | The highlighting implementation to use: 'unified', 'original' or 'fastVector'.                  |
| fields                   | string  | null          | Fields to generate highlighted snippets for. Separate multiple fields with commas.              |
| query                    | string  | null          | Overrides the q parameter for highlighting                                                      |
| queryparser              | string  | null          | The query parser to use if the query option is set                                              |
| requirefieldmatch        | boolean | null          | requireFieldMatch option                                                                        |
| queryfieldpattern        | string  | null          | queryFieldPattern option. Separate multiple fields with commas.                                 |
| usephrasehighlighter     | boolean | null          |                                                                                                 |
| highlightmultiterm       | boolean | null          |                                                                                                 |
| snippets                 | int     | null          | Maximum number of snippets per field                                                            |
| fragsize                 | int     | null          | The size, in characters, of fragments to consider for highlighting                              |
| tagprefix                | string  | null          | Solr option hl.tag.pre                                                                          |
| tagpostfix               | string  | null          | Solr option hl.tag.post                                                                         |
| encoder                  | string  | null          |                                                                                                 |
| maxanalyzedchars         | int     | null          | How many characters into a document to look for suitable snippets                               |
||

### Unified Highlighter options

| Name                     | Type    | Default value | Description                                                                              |
|--------------------------|---------|---------------|------------------------------------------------------------------------------------------|
| offsetsource             | string  | null          | Explicitly configure the offset source                                                   |
| fragalignratio           | float   | null          | Influences where the first highlighted text in a passage is positioned                   |
| fragsizeisminimum        | boolean | null          | Treat `fragsize` as a (soft) minimum fragment size                                       |
| tagellipsis              | string  | null          | Return one string with this text as the delimiter                                        |
| defaultsummary           | boolean | null          | Use the leading portion of the text if a proper highlighted snippet can't be generated   |
| scorek1                  | float   | null          | BM25 term frequency normalization parameter 'k1'                                         |
| scoreb                   | float   | null          | BM25 length normalization parameter 'b'                                                  |
| scorepivot               | int     | null          | BM25 average passage length in characters                                                |
| boundaryscannerlanguage  | string  | null          | Boundary scanner language for dividing the document into passages                        |
| boundaryscannercountry   | string  | null          | Boundary scanner country for dividing the document into passages                         |
| boundaryscannervariant   | string  | null          | Boundary scanner variant for dividing the document into passages                         |
| boundaryscannertype      | string  | null          | Boundary scanner type for dividing the document into passages                            |
| boundaryscannerseparator | string  | null          | Which character to break the text on. Use only `boundaryscannertype` set to 'SEPARATOR'. |
| weightmatches            | boolean | null          | Use Lucene's "Weight Matches" API instead of doing `SpanQuery` conversion                |
||

### Original Highlighter options

| Name                    | Type    | Default value | Description                                                                      |
|-------------------------|---------|---------------|----------------------------------------------------------------------------------|
| mergecontiguous         | boolean | null          | Collapse contiguous fragments into a single fragment                             |
| maxmultivaluedtoexamine | int     | null          | Maximum number of entries in a multi-valued field to examine before stopping     |
| maxmultivaluedtomatch   | int     | null          | maximum number of matches in a multi-valued field that are found before stopping |
| alternatefield          | string  | null          | alternateField option                                                            |
| maxalternatefieldlength | int     | null          | maxAlternateFieldLength option                                                   |
| highlightalternate      | boolean | null          | highlightAlternate option                                                        |
| formatter               | string  | null          | formatter option                                                                 |
| simpleprefix            | string  | null          | Solr option hl.simple.pre                                                        |
| simplepostfix           | string  | null          | Solr option hl.simple.post                                                       |
| fragmenter              | string  | null          |                                                                                  |
| regexslop               | float   | null          |                                                                                  |
| regexpattern            | string  | null          |                                                                                  |
| regexmaxanalyzedchars   | int     | null          |                                                                                  |
| preservemulti           | boolean | null          |                                                                                  |
| payloads                | boolean | null          |                                                                                  |
||

### FastVector Highlighter options

| Name                     | Type    | Default value | Description                                                                        |
|--------------------------|---------|---------------|------------------------------------------------------------------------------------|
| alternatefield           | string  | null          | alternateField option                                                              |
| maxalternatefieldlength  | int     | null          | maxAlternateFieldLength option                                                     |
| highlightalternate       | boolean | null          | highlightAlternate option                                                          |
| fraglistbuilder          | string  | null          |                                                                                    |
| fragmentsbuilder         | string  | null          |                                                                                    |
| boundaryscanner          | string  | null          | 'breakIterator' or 'simple'                                                        |
| boundaryscannertype      | string  | null          | 'breakIterator' boundary scanner type for dividing the document into passages      |
| boundaryscannerlanguage  | string  | null          | 'breakIterator' boundary scanner language for dividing the document into passages  |
| boundaryscannercountry   | string  | null          | 'breakIterator' boundary scanner country for dividing the document into passages   |
| boundaryscannermaxscan   | int     | null          | 'simple' boundary scanner maximum characters to scan                               |
| boundaryscannerchars     | string  | null          | 'simple' boundary scanner delimiters                                               |
| phraselimit              | int     | null          | Maximum number of phrases to analyze when searching for the highest-scoring phrase |
| multivaluedseparatorchar | string  | null          | Text to use to separate one value from the next for a multi-valued field           |
||

Per-field settings
------------------

Several options can be overridden on a per-field basis. You can use the `getField()` method to get a field options instance. See the example below.

Example
-------

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

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
// display the total number of documents found by Solr
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

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

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
// display the total number of documents found by Solr
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
