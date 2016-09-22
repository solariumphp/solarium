The result format for the grouping format can differ quite a lot, based on the options you use. If you use the mainResult option the grouping results are returned as a normal resultset. In that case there is no grouping resultset. In all other cases the normal resultset will be empty and any documents found will be inside the groups. So instead of iterating the resultset like you normally would you need to use the grouping component result as your main resultset. Also mind that in this case the 'numFound' value of the main resultset will be NULL, as any results found are inside the grouping result.

On top of that there are two different group formats: field groups and query groups.

For query groups the query is used as the key for the group. Inside the group you can access some data like numFound and maximumScore, and a set of documents. You can see it in action in the example below.

The field group is a little more complicated. The field name is used as key for the group(s). Inside these 'fieldGroups' there is some data like 'matches' and 'numberOfGroups' (the last one only when activated in the query). And there is a collection of 'valueGroups'. These are subgroups for each unique value in the field. In each valueGroup there is a 'numFound' value and a set of documents. Again, see the example below to get the idea.

In most cases only one type of grouping is used, but you can mix any number of query groups with field groups. If you do so, be careful to handle each group in the right way based on the type of group as they are clearly not compatible.

Examples
--------

Grouped by field: 

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

Grouped by query: 

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
