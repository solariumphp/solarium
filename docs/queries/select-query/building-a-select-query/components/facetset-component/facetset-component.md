The concept of a 'facetset' doesn't exist in Solr. It was added to Solarium to have one central component for using facets of various type. You can use the facetset to create and manage facets, and also to set global facet options.

See the API docs for all methods. In the following sections facet types will be detailed. The examples used on those pages will also show the usage of the facetset component.

Global facet options
--------------------

See <https://solr.apache.org/guide/faceting.html#field-value-faceting-parameters> for details.

| Name               | Type    | Default value | Description                                                                                                                                                             |
|--------------------|---------|---------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| prefix             | string  | null          | Limit the terms on which to facet to those starting with the given prefix. This does not limit the query, only the facets.                                              |
| contains           | string  | null          | Limit the terms on which to facet to those containing the given substring. This does not limit the query, only the facets. Available since Solr 5.1.                    |
| containsignorecase | boolean | null          | If 'contains' is used, causes case to be ignored when matching the given substring against candidate facet terms.                                                       |
| matches            | string  | null          | Limit the terms on which to facet to those matching the given regular expression. This does not limit the query, only the facets.                                       |
| sort               | string  | null          | Sort order (sorted by count or index). Use one of the class constants.                                                                                                  |
| limit              | int     | null          | Limit the facet counts.                                                                                                                                                 |
| offset             | int     | null          | Show facet count starting from this offset.                                                                                                                             |
| mincount           | int     | null          | Minimal term count to be included in facet count results.                                                                                                               |
| missing            | boolean | null          | Also make a count of all document that have no value for the facet field.                                                                                               |
| method             | string  | null          | Use one of the class constants as value.                                                                                                                                |
| enum.cache.minDf   | int     | null          | If 'method' is set to `METHOD_ENUM`, set the minimum document frequency for which the filterCache should be used.                                                       |
| exists             | boolean | null          | Set to `true` to cap facet counts by 1.                                                                                                                                 |
| excludeTerms       | string  | null          | Exclude these terms from facet counts. Specify a comma separated list. Use `\,` for a literal comma.                                                                    |
| overrequest.count  | int     | null          | Change the amount of over-requesting Solr does.                                                                                                                         |
| overrequest.ratio  | float   | null          | Change the amount of over-requesting Solr does.                                                                                                                         |
| threads            | int     | null          | Maximum number of threads for parallel execution. Omitting or `0` uses only the main request thread. Negative number allows up to (Java's) `Integer.MAX_VALUE` threads. |
||

Standard facet options
----------------------

All facet types available in the facetset extend a base class that offers a standard set of options. The following options are available for ALL facet types:

| Name          | Type   | Default value | Description                                                  |
|---------------|--------|---------------|--------------------------------------------------------------|
| local_key     | string | null          | Key to identify the facet (mandatory).                       |
| local_exclude | string | null          | Add one or multiple filterquery tags to exclude for a facet. |
||

Pivot facet options
-------------------

There is one option that can be set on the facetset with a deviating name when working with [pivot facets](facet-pivot.md).

| Name           | Type | Default value | Description                                                                                      |
|----------------|------|---------------|--------------------------------------------------------------------------------------------------|
| pivot.mincount | int  | null          | Minimum number of documents that need to match in order for the facet to be included in results. |
||
