Luke query
==========

Luke queries can be used to [access information on the schema data](https://solr.apache.org/guide/luke-request-handler.html).

Building a Luke query
---------------------

**Available options:**

| Name                   | Type   | Default value                                 | Description                                                                                                                              |
|------------------------|--------|-----------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------|
| show                   | string | null                                          | The data about the index to include in the response. Use one of the `SHOW_*` class constants as value.                                   |
| id                     | string | null                                          | Get a document using the `uniqueKeyField` specified in the schema.                                                                       |
| docId                  | int    | null                                          | Get a document using a Lucene documentID.                                                                                                |
| fields                 | string | null                                          | Fields to get details for. Separate multiple fields with commas. Use `'*'` to include all fields.                                        |
| numTerms               | int    | null                                          | The number of top terms for each field.                                                                                                  |
| includeIndexFieldFlags | bool   | null                                          | Whether to return index flags for each field. Fetching index flags can slow down Luke requests.                                          |
| documentclass          | string | Solarium\\QueryType\\Select\\Result\\Document | Classname for a document in the result. If you set a custom classname make sure the class is readily available (or through autoloading). |
||

The `show` option determines which information will be queried from the Luke handler.
The Luke query defines constants for the possible values.

- `$lukeQuery::SHOW_INDEX`: high-level details about the [index](#index)
- `$lukeQuery::SHOW_ALL`: information about [all fields](#all-fields)
- `$lukeQuery::SHOW_SCHEMA`: details about the [schema](#schema)
- `$lukeQuery::SHOW_DOC`: details about a specific [document](#document)

If the `fields` option is provided to get [field details](#field-details), or `id` or `docId`
to get [document](#document) details, `show` can be omitted.

### Index

Return the high-level details about the index.

There are no additional options for this show style.

This information is included in all Luke results and can be retrieved with `$result->getIndex()`.

```php
$lukeQuery = $client->createLuke();
$lukeQuery->setShow($lukeQuery::SHOW_INDEX);

$result = $client->luke($lukeQuery);

$index = $result->getIndex();
```

### All fields

Return information about all fields.

`includeIndexFieldFlags` can be set to `false` to disable fetching and returning index flags.

This information can be retrieved with `$result->getFields()`.

```php
$lukeQuery = $client->createLuke();
$lukeQuery->setShow($lukeQuery::SHOW_ALL);

// omitting index flags for each field can speed up Luke requests
//$lukeQuery->setIncludeIndexFieldFlags(false);

$result = $client->luke($lukeQuery);

$fields = $result->getFields();
```

#### Field details

Return detailed information about specified fields. Use with caution especially on large indexes!

`fields` must be set to a field list or `'*'` to enable this. `'*'` fetches the details for all fields.

`numTerms` can be set to the number of top terms to return for each field.

`includeIndexFieldFlags` can be set to `false` to disable fetching and returning index flags.

This information can be retrieved with `$result->getFields()`.

```php
$lukeQuery = $client->createLuke();

// set the fields you want detailed information for
$lukeQuery->setFields('text,cat,price_c');

// you can also get detailed information for all fields
//$lukeQuery->setFields('*');

// omitting index flags for each field can speed up Luke requests
//$lukeQuery->setIncludeIndexFieldFlags(false);

// set the number of top terms for each field (Solr's default is 10)
$lukeQuery->setNumTerms(5);

$result = $client->luke($lukeQuery);

$fields = $result->getFields();
```

### Schema

Return details about the schema.

There are no additional options for this show style.

This information can be retrieved with `$result->getSchema()`.

```php
$lukeQuery = $client->createLuke();
$lukeQuery->setShow($lukeQuery::SHOW_SCHEMA);

$result = $client->luke($lukeQuery);

$schema = $result->getSchema();
```

### Document

Return details about a specific document. This includes the actual document from the index.

`id` can be set to an ID in the `uniqueKeyField` specified in the schema.

`docId` can be set to a Lucene documentID.

`documentclass` can be set to a custom classname for the actual document in the result.

This information can be retrieved with `$result->getDoc()`.

```php
$lukeQuery = $client->createLuke();
$lukeQuery->setShow($lukeQuery::SHOW_DOC);

// get a document using the uniqueKeyField specified in the schema
$lukeQuery->setId('9885A004');

// alternatively, you can use a Lucene documentID
//$lukeQuery->setDocId(27);

$result = $client->luke($lukeQuery);

$docInfo = $result->getDoc();
```

Result of a Luke query
----------------------

### Example usage

A full example is available for each show style.

- [Index](result-of-a-luke-query/index-details.md)
- [All fields](result-of-a-luke-query/all-fields.md)
- [Field details](result-of-a-luke-query/field-details.md)
- [Schema](result-of-a-luke-query/schema.md)
- [Document](result-of-a-luke-query/document.md)

### Property getters

A Luke result consists of an object or collection of objects with getters for every
possible property in a response. The getters are named after the corresponding
properties and follow the camelCase convention of the Solarium codebase.

#### Examples

- `version` → `getVersion()`
- `numDocs` → `getNumDocs()`
- `NOTE` → `getNote()`

### Omittable properties

There are properties that will always be present in a response and properties that can
be omitted. The getter for an omittable property will return `null` if it isn't present
in the response.

### Boolean properties

Some boolean properties will always be present in a response, while others may be omitted
if they aren't `true`. Result objects have isser or hasser convenience methods
— depending on what makes sense grammatically — for all boolean properties. They'll
always offer a boolean answer to the question "*is this …?*" or "*does this have …?*"

If the distinction between an explicit `false` and an omitted property matters, you can
use the getter. As for all omittable properties, it will return `null` instead.

#### Examples

- `current` → `isCurrent()` & `getCurrent()`
- `hasDeletions` → `hasDeletions()` & `getHasDeletions()`

### Flag lists

Fields have properties that are represented by Solr as a string containing a list of
flags. A separate key of the flags is included in the [info](#generally-helpful-information)
section of the response. Solarium augments each flag list by turning it into a
`Solarium\QueryType\Luke\Result\FlagList` object. This object provides the original
string representation, a traversable list of the flags that are set (incorporating
the definition from the key), and issers for every flag.

#### Example

```php
echo $field->getFlags();
// I-S-UM----OF-----l

foreach ($field->getFlags() as $f => $flag) {
    echo $f, ': ', $flag;
}
// I: Indexed
// S: Stored
// U: UnInvertible
// M: Multivalued
// O: Omit Norms
// F: Omit Term Frequencies & Positions
// l: Sort Missing Last

$field->getFlags()->isIndexed(); // true
$field->getFlags()->isTokenized(); // false
$field->getFlags()->isStored(); // true
$field->getFlags()->isDocValues(); // false
$field->getFlags()->isUninvertible(); // true
$field->getFlags()->isMultiValued(); // true
$field->getFlags()->isTermVectors(); // false
$field->getFlags()->isTermOffsets(); // false
$field->getFlags()->isTermPositions(); // false
$field->getFlags()->isTermPayloads(); // false
$field->getFlags()->isOmitNorms(); // true
$field->getFlags()->isOmitTermFreqAndPositions(); // true
$field->getFlags()->isOmitPositions(); // false
$field->getFlags()->isStoreOffsetsWithPositions(); // false
$field->getFlags()->isLazy(); // false
$field->getFlags()->isBinary(); // false
$field->getFlags()->isSortMissingFirst(); // false
$field->getFlags()->isSortMissingLast(); // true
```

### Generally helpful information

Some generally helpful information is available in the result for every show style
that includes more than just the high-level index details. This information can be
retrieved with `$result->getInfo()`.

#### Example usage

A full [Info](result-of-a-luke-query/info.md) example is available.
