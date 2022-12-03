You can use this command to add XML formatted update commands to an update query.

Make sure the XML is valid as Solarium will not check this. If you are constructing these strings in your own code, you should probably be using the other commands Solarium provides to build your update query.

This command can only be used with the XML request format.

Options
-------

This command has no options.

Example
-------

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an update query instance
$update = $client->createUpdate();

// set XML request format
$update->setRequestFormat($update::REQUEST_FORMAT_XML);

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

// or use an XML file containing a valid update command
$xmlfile = 'example.xml';

// add the XML string, the XML file and a commit command to the update query
$update->addRawXmlCommand($xml);
$update->addRawXmlFile($xmlfile);
$update->addCommit();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();

```
