<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get an update query instance
$update = $client->createUpdate();

// create an XML string with a valid update command
$xml = '
<add>
    <doc>
        <field name="id">125</field>
        <field name="name">testdoc-3</field>
        <field name="price">325</field>
    </doc>
    <doc>
        <field name="id">126</field>
        <field name="name">testdoc-4</field>
        <field name="price">375</field>
    </doc>
</add>
';

// add the XML string and a commit command to the update query
$update->addRawXmlCommand($xml);
$update->addCommit();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();
