eDisMax is a Solr component that adds features to the 'standard' DisMax component. As the params are not compatible between the two Solarium also has a dedicated eDisMax component. It has many similarities to the DisMax component, in the library it event extends the Dismax class.

Options
-------

The eDismax component supports the options of the dismax component and adds the following options:

| Name                | Type   | Default value | Description                                                                                                                                                                        |
|---------------------|--------|---------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| boostfunctionsmult  | string | null          | Functions (with optional boosts) that will be included in the user's query to influence the score by multiplying its value. Format is: "funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2" |
| phrasebigramfields  | string | null          | As with 'pf' but chops the input into bi-grams, e.g. "the brown fox jumped" is queried as "the brown" "brown fox" "fox jumped". Format is: "fieldA^1.0 fieldB^2.2 fieldC^3.5"      |
| phrasebigramslop    | string | null          | As with 'ps' but sets default slop factor for 'pf2'. If not specified, 'ps' will be used.                                                                                          |
| phrasetrigramfields | string | null          | As with 'pf' but chops the input into tri-grams, e.g. "the brown fox jumped" is queried as "the brown fox" "brown fox jumped". Format is: "fieldA^1.0 fieldB^2.2 fieldC^3.5"       |
| phrasetrigramslop   | string | null          | As with 'ps' but sets default slop factor for 'pf3'. If not specified, 'ps' will be used.                                                                                          |
| userfields          | string | null          | Specifies which schema fields the end user shall be allowed to query for explicitly. This parameter supports wildcards.                                                            |
||

### Selecting all documents

The normal Solr syntax \*:\* doesn't work for Dismax. To select all documents you need to do two things:

-   make sure the 'query' value of the select query is set to an empty string. **The default query value in a Solarium select query is \*:\* so you need manually set it to an empty string!**
-   set the dismax query alternative to \*:\*. While the DisMax 'main' query doesn't support the normal query syntax, the query alternative does.

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

// get the dismax component and set a boost query
$edismax = $query->getEDisMax();

// this query is now a dismax query
$query->setQuery('memory -printer');

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
