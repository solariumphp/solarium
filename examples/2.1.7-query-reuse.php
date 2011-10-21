<?php

require('init.php');
htmlHeader();

// create a client instance
$client = new Solarium_Client($config);


// first create a base query as a query class
class PriceQuery extends Solarium_Query_Select
{
    protected function _init()
    {
        // set a query (all prices starting from 12)
        $this->setQuery('price:[12 TO *]');

        // set start and rows param (comparable to SQL limit) using fluent interface
        $this->setStart(2)->setRows(20);

        // set fields to fetch (this overrides the default setting 'all fields')
        $this->setFields(array('id','name','price'));

        // sort the results by price ascending
        $this->addSort('price', self::SORT_ASC);
    }
}

// the query instance easily be altered based on user input
// try calling this page with "?start=10" added to the url.
$query = new PriceQuery();
if(isset($_GET['start']) && is_numeric($_GET['start'])){
    $query->setStart($_GET['start']);
}

// alternatively you can use class inheritance to create query inheritance
// in this example this class isn't actually used, but you can simple replace
// the var $query with an instance of this class...
class LowerPriceQuery extends PriceQuery{
    protected function _init()
    {
        // this call makes sure we get all the settings of the parent class
        parent::_init();

        $this->setQuery('price:[5 TO *]');
    }
}

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';

    // the documents are also iterable, to get all fields
    foreach($document AS $field => $value)
    {
        // this converts multivalue fields to a comma-separated string
        if(is_array($value)) $value = implode(', ', $value);

        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table>';
}

htmlFooter();