The DisMax component can be used if you want to search using the DisMaxRequestHandler. This also affects the handling of the query string of the select query object (the 'main' query).

Options
-------

| Name             | Type   | Default value | Description                                                                                                                                                                              |
|------------------|--------|---------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| queryalternative | string | null          | If specified, this query will be used (and parsed by default using standard query parsing syntax) when the main query string is not specified or blank.                                  |
| queryfields      | string | null          | List of fields and the "boosts" to associate with each of them when building DisjunctionMaxQueries from the user's query. The format supported is "fieldOne^2.3 fieldTwo fieldThree^0.4" |
| mimimummatch     | string | null          | This option makes it possible to say that a certain minimum number of clauses must match. See Solr manual for details.                                                                   |
| phrasefields     | string | null          | This param can be used to "boost" the score of documents in cases where all of the terms in the "q" param appear in close proximity. Format is: "fieldA^1.0 fieldB^2.2"                  |
| phraseslop       | string | null          | Amount of slop on phrase queries built for "pf" fields (affects boosting)                                                                                                                |
| queryphraseslop  | string | null          | Amount of slop on phrase queries explicitly included in the user's query string (in qf fields; affects matching)                                                                         |
| tie              | float  | null          | Float value to use as tiebreaker in DisjunctionMaxQueries                                                                                                                                |
| boostquery       | string | null          | A raw query string (in the SolrQuerySyntax) that will be included with the user's query to influence the score.                                                                          |
| boostfunctions   | string | null          | Functions (with optional boosts) that will be included in the user's query to influence the score. Format is: "funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2"                                |
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
$dismax = $query->getDisMax();
$dismax->setBoostQuery('cat:"graphics card"^2');

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

Edismax
-------

For eDismax you should use the separate eDisMax component.
