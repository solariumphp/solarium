The facet class supports the Solr pivot facet: <http://wiki.apache.org/solr/SimpleFacetParameters#Pivot_.28ie_Decision_Tree.29_Faceting>

Options
-------

The options below can be set as query option values, but also by using the set/get methods. See the API docs for all available methods.

Only the facet-type specific options are listed. See [Facetset component](V3:Facetset_component "wikilink") for the option shared by all facet types.

| Name     | Type   | Default value | Description                              |
|----------|--------|---------------|------------------------------------------|
| mincount | int    | null          | Facet results limited by a minimum count |
| fields   | string | null          | Field to pivot on, separated by commas   |
||

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

// get the facetset component
$facetSet = $query->getFacetSet();

// create two facet pivot instances
$facet = $facetSet->createFacetPivot('cat-popularity-instock');
$facet->addFields('cat,popularity,inStock');
$facet->setMinCount(0);

$facet = $facetSet->createFacetPivot('popularity-cat');
$facet->addFields('popularity,cat');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet results
$facetResult = $resultset->getFacetSet()->getFacet('cat-popularity-instock');
echo '<h3>cat &raquo; popularity &raquo; instock</h3>';
foreach ($facetResult as $pivot) {
    displayPivotFacet($pivot);
}

$facetResult = $resultset->getFacetSet()->getFacet('popularity-cat');
echo '<h3>popularity &raquo; cat</h3>';
foreach ($facetResult as $pivot) {
    displayPivotFacet($pivot);
}

htmlFooter();


/**
 * Recursively render pivot facets
 *
 * @param $pivot
 */
function displayPivotFacet($pivot)
{
    echo '<ul>';
    echo '<li>Field: '.$pivot->getField().'</li>';
    echo '<li>Value: '.$pivot->getValue().'</li>';
    echo '<li>Count: '.$pivot->getCount().'</li>';
    foreach ($pivot->getPivot() as $nextPivot) {
        displayPivotFacet($nextPivot);
    }
    echo '</ul>';
}

```
