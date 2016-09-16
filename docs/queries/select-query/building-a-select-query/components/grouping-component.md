For a description of Solr grouping (also referred to as 'result grouping' or 'field collapse') see the [http://wiki.apache.org/solr/FieldCollapsing Solr wiki page](http://wiki.apache.org/solr/FieldCollapsing_Solr_wiki_page "wikilink").

It's important to have a good understanding of the available options, as they can have have big effects on the result format.

Options
-------

| Name            | Type    | Default value | Description                                                                                                                                                                                       |
|-----------------|---------|---------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| queries         | string  | null          | Queries to generate grouped results for. Separate multiple fields with commas.                                                                                                                    |
| fields          | string  | null          | Fields to generate grouped results for. Separate multiple fields with commas.                                                                                                                     |
| limit           | int     | null          | The number of results (documents) to return for each group                                                                                                                                        |
| offset          | int     | null          | The offset into the document list of each group                                                                                                                                                   |
| sort            | string  | null          | How to sort documents within a single group                                                                                                                                                       |
| mainresult      | boolean | null          | If true, the result of the first field grouping command is used as the main resultset. This way you can handle the resultdata like a normal search result, instead of the grouping result format. |
| numberofgroups  | boolean | null          | If true, include a count of the number of groups that have matched the query                                                                                                                      |
| cachepercentage | int     | null          | If &gt; 0 enables grouping cache in Solr. See Solr docs for details.                                                                                                                              |
| truncate        | boolean | null          | If true, facet counts are based on the most relevant document of each group matching the query. This param is available since Solarium 3.3 and only works with Solr 3.4 and up.                   |
| function        | string  | null          | Group based on the unique values of a function query. Only available in Solr 4.0+                                                                                                                 |
| facet           | boolean | null          | Group based on the unique values of a function query. Only available in Solr 4.0+                                                                                                                 |
| format          | string  | null          | If 'simple', the grouped documents are presented in a single flat list.                                                                                                                           |
||

Examples
--------

Grouping by field: 

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();
$query->setRows(50);

// get grouping component and set a field to group by
$groupComponent = $query->getGrouping();
$groupComponent->addField('inStock');
// maximum number of items per group
$groupComponent->setLimit(3);
// get a group count
$groupComponent->setNumberOfGroups(true);

// this executes the query and returns the result
$resultset = $client->select($query);

$groups = $resultset->getGrouping();
foreach ($groups as $groupKey => $fieldGroup) {

    echo '<h1>'.$groupKey.'</h1>';
    echo 'Matches: '.$fieldGroup->getMatches().'<br/>';
    echo 'Number of groups: '.$fieldGroup->getNumberOfGroups();

    foreach ($fieldGroup as $valueGroup) {

        echo '<h2>'.(int)$valueGroup->getValue().'</h2>';

        foreach ($valueGroup as $document) {

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
    }
}

htmlFooter();

```

Grouping by query: 

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// get grouping component and create two query groups
$groupComponent = $query->getGrouping();
$groupComponent->addQuery('price:[0 TO 99.99]');
$groupComponent->addQuery('price:[100 TO *]');
// sorting inside groups
$groupComponent->setSort('price desc');
// maximum number of items per group
$groupComponent->setLimit(5);

// this executes the query and returns the result
$resultset = $client->select($query);

$groups = $resultset->getGrouping();
foreach ($groups as $groupKey => $group) {

    echo '<h1>'.$groupKey.'</h1>';

    foreach ($group as $document) {
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
}

htmlFooter();

```
