<?php

require(__DIR__.'/init.php');
htmlHeader();

// This example shows how to manually execute the query flow.
// By doing this manually you can customize data in between any step (although a plugin might be better for this)
// And you can use only a part of the flow. You could for instance use the query object and request builder,
// but execute the request in your own code.


// create a client instance
$client = new Solarium\Client($config);

// create a select query instance
$query = $client->createSelect();

// manually create a request for the query
$request = $client->createRequest($query);

// you can now use the request object for getting an uri (ie. to use in you own code)
// or you could modify the request object
echo 'Request URI: ' . $request->getUri() . '<br/>';

// you can still execute the request using the client and get a 'raw' response object
$response = $client->executeRequest($request);

// and finally you can convert the response into a result
$result = $client->createResult($query, $response);

// display the total number of documents found by solr
echo 'NumFound: '.$result->getNumFound();

// show documents using the resultset iterator
foreach ($result as $document) {

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
