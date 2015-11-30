The concept of a 'facetset' is doesn't exist in Solr. It was added to Solarium to have one central component for using facets of various type. You can use the facetset to create and manage facets, and also to set global facet options.

See the API docs for all methods. In the following sections facet types will be detailed. The examples used on those pages will also show the usage of the facetset component.

Global facet options
--------------------

| Name     | Type    | Default value | Description                              |
|----------|---------|---------------|------------------------------------------|
| prefix   | string  | null          | Limit the terms for faceting by a prefix |
| sort     | string  | null          | Set the facet sort order                 |
| limit    | int     | null          | Set the facet limit                      |
| mincount | int     | null          | Set the facet mincount                   |
| missing  | boolean | null          | Set the 'count missing' option           |
||

Standard facet options
----------------------

All facet types available in the facetset extend a base class that offers a standard set of options. The following options are available for ALL facet types:

| Name     | Type   | Default value | Description                                                 |
|----------|--------|---------------|-------------------------------------------------------------|
| key      | string | null          | Key to identify the facet (mandatory)                       |
| excludes | string | null          | Add one or multiple filterquery tags to exclude for a facet |
||


