<?php

require(__DIR__.'/init.php');
htmlHeader();

if ($_POST) {
    // if data is posted add it to solr

    // create a client instance
    $client = new Solarium\Client($config);

    // get an update query instance
    $update = $client->createUpdate();

    // create a new document for the data
    // please note that any type of validation is missing in this example to keep it simple!
    $doc = $update->createDocument();
    $doc->id = $_POST['id'];
    $doc->name = $_POST['name'];
    $doc->price = $_POST['price'];

    // add the document and a commit command to the update query
    $update->addDocument($doc);
    $update->addCommit();

    // this executes the query and returns the result
    $result = $client->update($update);

    echo '<b>Update query executed</b><br/>';
    echo 'Query status: ' . $result->getStatus(). '<br/>';
    echo 'Query time: ' . $result->getQueryTime();

} else {
    // if no data is posted show a form
    ?>

    <form method="POST">
        Id: <input type="text" name="id"/> <br/>
        Name: <input type="text" name="name"/> <br/>
        Price: <input type="text" name="price"/> <br/>
        <input type="submit" value="Add"/>
    </form>

    <?php
}

htmlFooter();
