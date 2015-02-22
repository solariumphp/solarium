# CHANGELOG

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
