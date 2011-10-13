<?php

require('init.php');
htmlHeader();

// In most cases using the API or config is advisable, however in some cases it can make sense to extend classes.
// This makes it possible to create 'query inheritance' like in this example
class ProductQuery extends Solarium_Query_Select{

    protected function _init()
    {
        parent::_init();

        // basic params
        $this->setQuery('*:*');
        $this->setStart(2)->setRows(20);
        $this->setFields(array('id','name','price'));
        $this->addSort('price', Solarium_Query_Select::SORT_ASC);

        // create a facet field instance and set options
        $facetSet = $this->getFacetSet();
        $facetSet->createFacetField('stock')->setField('inStock');
    }

}

// This query inherits all of the query params of it's parent (using parent::_init) and adds some more
// Ofcourse it could also alter or remove settings
class ProductPriceLimitedQuery extends ProductQuery{

    protected function _init()
    {
        parent::_init();

        // create a filterquery
        $this->createFilterQuery('maxprice')->setQuery('price:[1 TO 300]');
    }

}

// create a client instance
$client = new Solarium_Client($config);

// create a query instance
$query = new ProductPriceLimitedQuery;

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet counts for field "inStock":<br/>';
$facet = $resultset->getFacetSet()->getFacet('stock');
foreach($facet as $value => $count) {
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