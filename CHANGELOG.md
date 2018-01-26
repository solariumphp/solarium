# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
### Changed
### Fixed

## [3.9.0-alpha] - 2018-01-19
## Added
- Provide fluent interface (PR #483)

### Changed
- Performance updates for formatting values (PR #485)
- Updated PHP annotations and docblock (PR #526)

### Fixed
- (backport) Fixes bugs from PR #484: fix Http adapter for extraction requests (PR #519)

## [3.8.1] - 2017-02-02
### Fixed
- Restore PHP 5.3 compatibility (remove short array syntax)

## [3.8.0] - 2017-01-31
### Added
- Support for simple group format in response parser
- Helper for fetching ValueGroup from a Grouped result
- Guzzle 3 and Guzzle 6 client adapters

### Changed
- Various fixes in documentation

### Fixed
- Use GET request for extracting remote files
- Prevent ParallelExecution Curl spinloop

## [3.7.0] - 2016-10-28
### Added
- Support for nested documents in update query
- Spatial component for select query
- Support for keys and excludes in interval facet
- Support for grouping using a function (group.func)
- Included suggestion in composer file for a query builder library

### Changed
- Lots of fixes in documentation markup

### Fixed
- Spellcheck collation parsing for Solr 5+

## [3.6.0] - 2016-05-03
### Added
- Support for replicas in distributed search
- Support for multiple boost queries in dismax
- Support for additional stats values like percentiles
- Added Symfony 3.x components to CI tests for PHP 5.5+

### Changed
- No longer allow failures for HHVM in continuous integration
- ClientInterface now also used for standard Client class

### Fixed
- Several typo / markup fixes in documentation
- Several docblock fixes


## [3.5.1] - 2015-12-15
### Fixed
- backwards incompatible change in classnames

## [3.5.0] - 2015-12-14
### Added
- Make it possible to bypass (system-wide) proxy setting in Curl adapter
- ClientInterface
- Support for facet.contains settings
- Docs in repository (markdown format)
- SensioLabs Insight (including lots of fixed in the code based on report)

### Changed
- Lots of code style fixes
- Refactored 'base' plugin class to AbstractPlugin
- Removed old PHP environments for Travis, added PHP7
- Set license to a valid SPDX license identifier
- Set hard paths in .gitignore to prevent tree lookups
- Updated Symfony event dispatcher dependency to a maintained version

### Fixed
- PHAR generator updated to support namespacing
- Collations broken for Solr 5 data format

## [3.4.0] - 2015-06-14
### Added
- Facet interval support
- ZF2 http adapter
- Stats for pivot facet
- Support for calling empty() and isset() on result document properties
- Composer test script
- 'contributing' file

### Changed
- Lots of code style fixes, using the SF2 code style

### Fixed
- Docblock fixes in grouping component facets
- Only check type for added documents to add query if provided as an array
- Curl file upload handling
- Spellcheck 'collations' and 'correctlyspelled' updated to support Solr 5 format
- Curl adapter now uses Solr 5 compatible headers for a GET request


## [3.3.0] - 2014-11-16
### Added
- Support for data fixtures
- MinimumScoreFilter plugin, also for grouping
- Support for nested debug-info in the debug query

### Changed
- Filter control characters in update documents by default
- Facet range now supports the mincount parameter
- Spellcheck response parser can now handle multiple suggestions
- Prefetch iterator now resets if prefetch or query settings are changed
- Added matchoffset setting to MLT querytype
- PreFetchIterator plugin now supports setting an endpoint
- BufferedAdd plugin now supports an endpoint supplied as a config
- Updated curl adapter file handling to prevent warnings in php >=5.5.0
- Added remote file streaming support to extract request handler
- Query result now also supports maxscore
- MoreLikeThis now allows for individual boosts on query fields
- Select query component MoreLikeThis now supports boosting multiple fields
- Added PHP 5.5, 5.6 and HHVM to Travis config
- Solarium now uses Coveralls for test coverage reports
- If a config object does not supply a toArray method, the object is converted by Solarium
- Highlighting now supports hl.preserveMulti param
- Stats component now supports exludes
- Range query helper now supports wildcards
- Support HTTPS scheme for endpoints
- CURL and PECL_HTTP adapters now set connection timeout and dns cache timeout
- Extract query now supports ExtractOnly
- The event dispatcher can now be injected
- PSR-0 and PSR-2 code fixes

### Fixed
- Fixes in build.xml (use phpunit in vendor directory)
- Pivot facet does not accept extra parameters
- Pivot facet uses the wrong key in result parsing
- Wrong handling of boolean values in update documents
- Highlight query should only set hl.fl param if there are any fields set
- Curl crash when open_basedir is set
- Fix suggester parser with duplicates
- Removed constructor from interface Solarium/Core/ConfigurableInterface.php
