# CHANGELOG
All notable changes to the solarium library will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [4.2.0]
### Fixed
- If a term contains a space, the space needs to be quoted by Helper::escapeTerm()
- Typos


## [4.2.0-rc.1]
### Added
- Support for managed resources
- Support for add-distinct and removeregex modifiers

## [4.2.0-beta.1]
### Added
- Basic support for PUT requests in the HttpAdapter layer
- Support for managed resources
- Core Admin Queries
- Endpoint::getServerUri
- Endpoint::getCoreBaseUri
- Expression::indent
- BufferedAdd::setCommitWithin
- BufferedAdd::setOverwrite
- Set erroneous expression on StreamException
- Managed resources, stopwords and synonyms query types

### Deprecated
- Endpoint::getBaseUri is deprecated. Please use getServerUri or getCoreBaseUri now.

### Fixed
- Allow multiple Field Facets for the same field by dynamically using local facet params if required


## [4.1.0]
### Added
- Method AbstractQuery::removeParam() to remove a custom parameter or to reset a required but modified parameter
- Basic support for DELETE requests in the HttpAdapter layer
- Introduced an AdapterHelper class to start unifying implementations across all HTTP adapters

### Changed
- To unify the file extraction across all HTTP Adapters, the filename is now always reduced to its basepath


### Fixed
- Guzzle Integration tests
- Don't modify the time zone of DateTime objects passed by reference
- Extract request rejected because of missing multipart boundary


## [4.1.0-rc.1]
### Added
- Every component that has a 'query' option is now able to bind parameters to a query string via its setQuery() function
- Tests for cursormark
- Support for ReRankQuery

### Changed
- Renamed option 'q' to 'query' in Solarium\Component\Facet\JsonQuery for consistency

### Fixed
- Random test failures caused by different timestamps


## [4.1.0-beta.1]
### Added
- Query Elevation Component
- Option 'min' for JsonAggregation
- Support for NOW and TZ parameters

### Changed
- Test coverage and docs for cursor functionality
- Test coverage for JSON facets
- Branch aliases for composer

### Fixed
- Filter empty buckets from JSON facets during result parsing
- Cover 'contains' and 'containsignorecase' in FacetSet docs


## [4.1.0-alpha.1]
### Added
- Support for JSON Facet API

### Changed
- Constants FacetSet::FACET_* became FacetSetInterface::FACET_*


## [4.0.0]
### Added
- Support "sow" parameter (Split On Whitespace) in select queries


## [4.0.0-rc.1]
### Added
- Basic support for Solr Cloud streaming expressions


## [4.0.0-beta.1]
### Fixed
- Return type of Solarium\Component\QueryTraits\SuggesterTrait::getSuggester()
- Type hints in Solarium\Component\AbstractComponent


## [4.0.0-alpha.2]
### Added
- getSuggester() convenience method on Solarium\QueryType\Select\Query\Query
- More integration tests

### Removed
- Outdated symfony versions on test environment

### Fixed
- Don't escape the '*' in range queries
- Return type of getHighlighting() on Solarium\QueryType\Select\Result\Result
- Return type of getFacetSet() on Solarium\QueryType\Select\Result\Result


## [4.0.0-alpha.1] 
### Added
- Terms component
- Spellcheck component
- Spellcheck query type
- Added missing parameters to the Spellcheck query type and the component (compared to the 3.x Suggester)
- Support for deep paging with a cursor
- Symfony 4 support
- Nightly builds / tests
- Basic Integration tests running real Solr queries against Solr's techproducts example

### Changed
- Renamed folder library to src
- Use PSR-4 class loading
- Updated PHPUnit to v6.5
- Updated required PHP version to >= v7.0
- Isolated search components from the select query type and made them re-usable
- BC break: Suggester component is now compatible to Solr v6/7 (the existing one was renamed to Spellcheck)
- BC break: Suggester query type is now compatible to Solr v6/7 (the existing one was renamed to Spellcheck)
- Lots of source code re-structuring and clean-up

### Removed
- Phar support
- Exclude test suite from distribution
- Dropped support for Solr versions before 6
- Obsolete Autoloader.php
- Deprecated Solarium\Core\Plugin\Plugin in favor of Solarium\Core\Plugin\AbstractPlugin
- Deprecated Solarium\Core\Query\Query in favor of Solarium\Core\Query\AbstractQuery
- Deprecated Solarium\Core\Query\RequestBuilder in favor of Solarium\Core\Query\AbstractRequestBuilder
- Deprecated Solarium\Core\Query\ResponseParser in favor of Solarium\Core\Query\AbstractResponseParser
- Deprecated Solarium\QueryType\Analysis\Query\Query in favor of Solarium\QueryType\Analysis\Query\AbstractQuery

### Security
- Prevented query injection inside range queries


## [3.8.1]
### Fixed
- Restore PHP 5.3 compatibility (remove short array syntax)


## [3.8.0]
### Added
- Support for simple group format in response parser
- Helper for fetching ValueGroup from a Grouped result
- Guzzle 3 and Guzzle 6 client adapters

### Changed
- Various fixes in documentation

### Fixed
- Use GET request for extracting remote files
- Prevent ParallelExecution Curl spinloop


## [3.7.0]
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


## [3.6.0]
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


## [3.5.1]
### Fixed
- backwards incompatible change in classnames


## [3.5.0]
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


## [3.4.0]
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


## [3.3.0]
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
