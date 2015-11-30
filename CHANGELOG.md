# CHANGELOG

## 3.5.0 - 2015-11-30

- improvement: lots of code style fixes
- improvement: refactored 'base' plugin class to AbstractPlugin
- improvement: removed old PHP environments for Travis, added PHP7
- improvement: set license to a valid SPDX license identifier
- bugfix: PHAR generator updated to support namespacing
- bugfix: Collations broken for Solr 5 data format
- added: Make it possible to bypass (system-wide) proxy setting in Curl adapter
- improvement: Added SensioLabs Insight (including lots of fixed in the code based on report)
- added: ClientInterface
- improvement: Set hard paths in .gitignore to prevent tree lookups
- added: Support for facet.contains settings
- improvement: updated Symfony event dispatcher dependency to a maintained version

## 3.4.0 - 2015-06-14

- bugfix: only check type for added documents to add query if provided as an array
- improvement: added support for calling empty() and isset() on result document properties
- improvement: added composer test script
- bugfix: curl file upload handling
- improvement: added 'contributing' file
- improvement: docblock fixes in grouping component facets
- added: facet interval support
- added: ZF2 http adapter
- added: stats for pivot facet
- bugfix: spellcheck 'collations' and 'correctlyspelled' updated to support Solr 5 format
- bugfix: curl adapter now uses Solr 5 compatible headers for a GET request
- improvement: lots of code style fixes, using the SF2 code style


## 3.3.0 - 2014-11-16

- improvement: fixes in build.xml (use phpunit in vendor directory)
- improvement: added support for nested debug-info in the debug query
- new feature: added support for data fixtures
- improvement: filter control characters in update documents by default
- bugfix: pivot facet does not accept extra parameters
- improvement: Facet range now supports the mincount parameter
- improvement: Spellcheck response parser can now handle multiple suggestions
- bugfix: Pivot facet uses the wrong key in result parsing
- bugfix: Wrong handling of boolean values in update documents
- improvement: Removed constructor from interface Solarium/Core/ConfigurableInterface.php
- improvement: Prefetch iterator now resets if prefetch or query settings are changed
- improvement: Added matchoffset setting to MLT querytype
- bugfix: Highlight query should only set hl.fl param if there are any fields set
- bugfix: Curl crash when open_basedir is set
- improvement: PreFetchIterator plugin now supports setting an endpoint
- improvement: BufferedAdd plugin now supports an endpoint supplied as a config
- improvement: Updated curl adapter file handling to prevent warnings in php >=5.5.0
- improvement: Added remote file streaming support to extract request handler
- improvement: Query result now also supports maxscore
- new feature: Added MinimumScoreFilter plugin, also for grouping
- improvement: MoreLikeThis now allows for individual boosts on query fields
- bugfix: Fix suggester parser with duplicates
- improvement: Select query component MoreLikeThis now supports boosting multiple fields
- improvement: added PHP 5.5, 5.6 and HHVM to Travis config
- improvement: Solarium now uses Coveralls for test coverage reports
- improvement: if a config object does not supply a toArray method, the object is converted by Solarium
- improvement: Highlighting now supports hl.preserveMulti param
- improvement: Stats component now supports exludes
- improvement: Range query helper now supports wildcards
- improvement: Support HTTPS scheme for endpoints
- improvement: CURL and PECL_HTTP adapters now set connection timeout and dns cache timeout
- improvement: Extract query now supports ExtractOnly
- improvement: The event dispatcher can now be injected
- improvement: PSR-0 and PSR-2 code fixes
