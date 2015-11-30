Documents
=========

The Solarium document classes represent the documents in Solr indexes. Solarium has two built-in document classes, one included with the select query for reading data and one for updating data in the update query.

The document fieldnames are related to your Solr index schema. When reading documents from Solr all stored fields will be returned automatically. When updating data you need to add the correct fields to the document, the document object has no knowledge of the schema.

In the following sections the usage of both document types and the use of custom documents is described.


Read-only document
==================

This is the default document type for a select query result. This is an immutable object that allows access to the field values by name or by iterating over the document. This object implements the `Iterator`, `Countable` and `ArrayAccess` interfaces. You can use the document in multiple ways:

-   access fields as object vars (fieldname as varname)
-   access fields as array entries (fieldname as key)
-   iterate over all fields (returning fieldnames as 'key' and the fieldvalue as 'value')
-   count it (returns the nr. of fields in the document)

The example belows shows all these options.

To enforce the immutable state of this document type an exception will be thrown if you try to alter a field value. For an updateable document you should use this class: Solarium\\QueryType\\Update\\Query\\Document

Solarium uses this document type as default for select queries for two reasons:

-   in most cases no update functionality is needed, so it will only be overhead
-   to discourage the use of Solr as a DB, as in reading - altering - saving. Almost all schemas have index-only fields. There is no way to read the value of there fields, so this data will be lost when re-saving the document! Updates should normally be done based on your origin data (i.e. the database). If you are *really sure* you want to update Solr data, you can set a read-write document class as the document type for your select query, alter the documents and use them in an update query.

Example usage
-------------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createQuery($client::QUERY_SELECT);

// this executes the query and returns the result
$resultset = $client->execute($query);

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


Read-write document
===================

This document type can be used for update queries. It extends the Read-Only document and adds the ability to add, set or remove field values and boosts.

Any fields you set must exactly match the field names in your Solr schema, or you will get an exception when you try to add them.

You can set field values in multiple ways:

-   as object property
-   as array entry
-   by using the `setField` and `addField` methods

See the API docs for details and the example code below for examples.

Multivalue fields
-----------------

If you set field values by property, array entry or by using the `setField` method you need to supply an array of values for a multivalue field. Any existing field values will be overwritten.

If you want to add an extra value to an existing field, without overwriting, you should use the `addField` method. If you use this method on a field with a single value it will automatically be converted into a multivalue field, preserving the current value. You will need to call this method once for each value you want to add, it doesn't support arrays. You can also use this method for creating a new field, so you don't need to use a special method for the first field value.

Dates
-----

If you have a date in your Solr schema you can set this in the document as a string in the Solr date format. However, you can also set a PHP DateTime object as the field value in your document. In that case Solarium will automatically convert it to a datetime string in the correct format.

Atomic updates
--------------

You can create atomic updates by using the setFieldModifier method. Set a modifier on the field you want to update. The supported modifiers are:

-   MODIFIER\_SET
-   MODIFIER\_ADD
-   MODIFIER\_INC

The addField and setField methods also support modifiers as an optional argument. Any document that uses modifiers MUST have a key, you can set the key using the setKey method.

A document with atomic updates can be added to an update query just like any other document.

For more info on Solr atomic updates please read the manual: <http://wiki.apache.org/solr/Atomic_Updates>

Versioning
----------

The document has getVersion() and setVersion methods. By default no version is used, but you can set a version manually. There is a set of predefined values:

-   VERSION\_DONT\_CARE
-   VERSION\_MUST\_EXIST
-   VERSION\_MUST\_NOT\_EXIST

But you can also set a custom version (specific ID).

For more info on versioning please see this blogpost: <http://yonik.com/solr/optimistic-concurrency/>

Boosts
------

There are two types of boosts: a document boost and a per-field boost. See Solr documentation for the details about index-time boosts. *Do not confuse these with query-time boosts (term^2)*

You can set the document boost with the `setBoost` method.

Field boosts can be set with the `setFieldBoost` method, or with optional parameters of the `setField` and `addField` methods. See the API docs for details.

Example usage
-------------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get an update query instance
$update = $client->createUpdate();

// create a new document for the data
$doc1 = $update->createDocument();
$doc1->id = 123;
$doc1->name = 'testdoc-1';
$doc1->price = 364;

// and a second one
$doc2 = $update->createDocument();
$doc2->id = 124;
$doc2->name = 'testdoc-2';
$doc2->price = 340;

// add the documents and a commit command to the update query
$update->addDocuments(array($doc1, $doc2));
$update->addCommit();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();

```


Custom document
===============

You can easily use your own 'document' types, for instance to directly map Solr results to entity models. You need to do the following:

-   make sure the class is available (already loaded or can be autoloaded)
-   set the 'documentclass' option of your query to your own classname
-   the class must implement the same interface as the original document class.