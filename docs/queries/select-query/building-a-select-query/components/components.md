There are so many options available for use in a Solr select query that putting them all in a single model would result in a hard to maintain, hard to use API and decreased performance. Therefore the select query model itself only implements common query parameters. Additional functionality can be added by using components.

Components have multiple advantages:

-   they model specific Solr components/handlers in detail, in some cases with sub-models
-   components are only loaded when actually used
-   the component structure allows for easy addition of extra (custom) components

The Solarium concept of a 'component' is not exactly the same as a Solr component. For instance, the Solr FacetComponent is reflected in Solarium by a FacetSet component. And Solariums DisMax component actually uses the DisMaxQParserPlugin of Solr. Because there are so many types of components, parsers, handlers etcetera in Solr this is simplified into just 'components' in Solarium. For each Solarium component you can find what part of Solr it uses in the docs.
