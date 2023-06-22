# CHANGELOG
All notable changes to the Solarium library will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [6.3.1]
### Added
- Loadbalancer plugin can failover on an optional list of HTTP status codes
- Solarium\QueryType\Extract\Query::setFile() now supports file pointer resources
- Solarium\QueryType\Extract\Result::getFile() and getFileMetadata() to access the retrieved data for `extractOnly=true`

### Fixed
- Solarium\Core\Query\Helper::escapeTerm() has to quote reserved terms `AND`, `OR`, `TO`

### Changed
- Solarium\Core\Client\Endpoint::setAuthentication() marks $password as #[\SensitiveParameter] (PHP 8 >= 8.2.0)
- Solarium\Core\Client\Endpoint::setAuthorizationToken() marks $token as #[\SensitiveParameter] (PHP 8 >= 8.2.0)
- Solarium\Core\Client\Request::setAuthentication() marks $password as #[\SensitiveParameter] (PHP 8 >= 8.2.0)


## [6.3.0]
### Added
- Support for Luke queries
- Solarium\Component\QueryElevation::setExcludeTags()
- Solarium\Core\Query\Result\QueryType::getStatus() and getQueryTime(), inherited by all Solarium\QueryType Results
- Solarium\QueryType\CoreAdmin\Result\Result::getInitFailureResults()
- Solarium\QueryType\Ping\Result::getPingStatus() and getZkConnected()
- Fluent interface methods for adding/removing excludes in Solarium\Component\Facet\AbstractFacet
- Fluent interface methods for adding/removing terms in Solarium\Component\Facet\Field

### Fixed
- JSON serialization of arrays with non-consecutive indices in multivalue fields
- PHP 8.2 deprecations
- Handling of escaped literal commas in local parameters for faceting

### Changed
- Update queries use the JSON request format by default
- Ping queries set omitHeader=false by default

### Removed
- Removed deprecated class constant Client::Version. Use Client::getVersion() instead
- Removed Core/Query/AbstractResponseParser::addHeaderInfo()

### Deprecated
- Solarium\QueryType\Server\Collections\Result\CreateResult::getStatus(), use getCreateStatus() instead
- Solarium\QueryType\Server\Collections\Result\DeleteResult::getStatus(), use getDeleteStatus() instead
- Solarium\QueryType\Server\Collections\Result\ReloadResult::getStatus(), use getReloadStatus() instead
- LocalParameters::removeTerms(), use removeTerm() instead


## [6.2.8]
### Added
- PHP 8.2 support
- JSON formatted update requests
- Solarium\Component\Highlighting\Highlighting::setQueryFieldPattern()


## [6.2.7]
### Added
- Core\Client\Adapter\Curl::setProxy() to set proxy (instead of through options)
- Proxy support for Http adapter with Core\Client\Adapter\Http::setProxy()
- Authorization token support

### Fixed
- Plugins unregister event listeners when removed with Client::removePlugin()
- Workaround for opcache.preload issue in deprecated code unless 6.3.0 will be released

### Changed
- `RequestBuilder`s must set a Content-Type on the `Request` for POST and PUT requests. `Adapter`s no longer set a default.

### Deprecated
- Setting proxy on the Curl adapter through options, use setProxy() instead


## [6.2.6]
### Fixed
- An empty array for a multiValued field was wrongly interpreted as an empty child document by the Update request builder in 6.2.5


## [6.2.5]
### Added
- Results and Documents implement [JsonSerializable](https://www.php.net/manual/en/class.jsonserializable)
- ParallelExecution dispatches PreExecute, PreExecuteRequest, PostExecuteRequest, PostExecute events. It can be combined with plugins that hook into these events (e.g. PostBigRequest).
- ParallelExecution support for Server queries
- Solarium\Client::getVersion()

### Fixed
- Adding nested child documents through `Document::setField()` and `Document::addField()`

### Changed
- ParallelExecution doesn't replace an existing cURL adapter on the Client. Timeout and proxy settings are honoured on parallel requests.
- ParallelExecution sets the 'timeout' and 'connectiontimeout' options from (Connection)TimeoutAware adapters when switching to a cURL adapter

### Removed
- Solarium\QueryType\Update\Query\Document::setFilterControlCharacters(), extend Update\Query\Query to use a custom request builder & helper if you don't want control characters filtered

### Deprecated
- Solarium\Client::VERSION


## [6.2.4] 
### Added
- Symfony 6 support
- Solr 9 support
- Unified Highlighter support + improved support for other highlighters

### Fixed
- Solarium\QueryType\Server\Collections\Query\Action\ClusterStatus::getRoute() always returned NULL even if a route was set
- Solarium\Component\Highlighting\Highlighting::setMethod() didn't set the correct request parameter

### Changed
- Solarium\QueryType\Select\Query\Query::setCursormark() and getCursormark() are now setCursorMark() and getCursorMark() with uppercase M
- Managed resources execute GET requests for the Exists command by default to avoid SOLR-15116 and SOLR-16274. Set the 'useHeadRequest' option to `true` to execute HEAD requests instead.

### Removed
- Solarium\QueryType\Stream\Expression, use Solarium\QueryType\Stream\ExpressionBuilder instead


## [6.2.3]
### Added
- Plugin\BufferedAddLite (BufferedAdd without event dispatching)
- Plugin\BufferedDelete and Plugin\BufferedDeleteLite

### Fixed
- Local parameter values are now escaped automatically when necessary


## [6.2.2]
### Added
- PHP 8.1 support


## [6.2.1]
### Added
- Possibility to set the context on an endpoint for SolrCloud instances with a non-default `hostContext` or Solr instances behind a reverse proxy, defaults to `solr` if omitted


## [6.2.0]
### Added
- Component\FacetSet::setOffset()
- Component\FacetSet::setMethod() and Component\FacetSet::{METHOD_ENUM,METHOD_FC,METHOD_FCS,METHOD_UIF}
- Component\FacetSet::setEnumCacheMinimumDocumentFrequency()
- Component\FacetSet::setExists()
- Component\FacetSet::setOverrequestCount()
- Component\FacetSet::setOverrequestRatio()
- Component\FacetSet::setThreads()
- Component\FacetSet::setPivotMinCount() to set the global facet.pivot.mincount parameter
- Component\Facet\Pivot::setPivotMinCount() to set the facet.pivot.mincount parameter for a specific pivot's fields
- Component\Facet\Pivot::setOffset()
- Component\Facet\Pivot::setSort()
- Component\Facet\Pivot::setOverrequestCount()
- Component\Facet\Pivot::setOverrequestRatio()
- Component\Facet\Field::METHOD_FCS for per-segment field faceting for single-valued string fields
- Component\Facet\Field::METHOD_UIF for UnInvertedField faceting
- Component\Facet\Field::setEnumCacheMinimumDocumentFrequency()
- Component\Facet\Field::setExists()
- Component\Facet\Field::setOverrequestCount()
- Component\Facet\Field::setOverrequestRatio()
- Component\Facet\Field::setThreads()
- Component\Facet\JsonTerms::{SORT_COUNT_ASC,SORT_COUNT_DESC,SORT_INDEX_ASC,SORT_INDEX_DESC}
- Component\Facet\JsonTerms::setOverRefine()
- Component\Facet\JsonTerms::setPrelimSort()

### Fixed
- Component\Facet\Pivot::setLimit() now sets the correct query parameter
- Component\Facet\JsonTerms::setSort() PHPDoc

### Deprecated
- Component\Facet\Pivot::setMinCount(), use Component\FacetSet::setPivotMinCount() or Component\Facet\Pivot::setPivotMinCount() instead
- Component\Facet\JsonTerms::SORT_COUNT, use SORT_COUNT_ASC or SORT_COUNT_DESC instead
- Component\Facet\JsonTerms::SORT_INDEX, use SORT_INDEX_ASC or SORT_INDEX_DESC instead


## [6.1.6]
### Added
- PHP 8.1 support
- QueryType\Update\Query\Document::setFields() to set all fields on a Document

### Fixed
- Always respect automatic filtering of control characters in field values in QueryType\Update\Query\Document
- Remove the field modifier along with the value(s) and boost in QueryType\Update\Query\Document::removeField()
- Allow string to be returned for `min`, `max` and `mean` statistics in Component\Result\Stats\ResultTrait


## [6.1.5]
### Added
- Component\Result\Stats\Result::getDistinctValues()
- Component\Result\Stats\Result::getCountDistinct()
- Component\Result\Stats\Result::getCardinality()
- Component\Result\Stats\FacetValue::getPercentiles()
- Component\Result\Stats\FacetValue::getDistinctValues()
- Component\Result\Stats\FacetValue::getCountDistinct()
- Component\Result\Stats\FacetValue::getCardinality()
- Component\Result\Stats\FacetValue::getStatValue()
- Plugin PostBigExtractRequest
- Support for Configset API
- Set connection timeout on cURL adapter

### Fixed
- Component\Result\Stats\Result::getPercentiles() returns percentiles as an associative array

### Changed
- Component\Result\Stats\Result::getMean() returns `NAN` instead of `'NaN'` if mean is NaN
- Component\Result\Stats\FacetValue::getMean() returns `NAN` instead of `'NaN'` if mean is NaN
- Component\Result\Stats\Result::getValue() is renamed to getStatValue()

### Deprecated
- Component\Result\Stats\FacetValue::getFacets()
- Component\Result\Stats\Result::getValue()


## [6.1.4]
### Added
- Solarium\QueryType\ManagedResources\Result\Command::getWasSuccessful()
- Solarium\QueryType\ManagedResources\Result\Command::getStatusMessage()
- Query a single term in a Managed Resource

### Fixed
- Syntax error in request with facet queries that contain local parameters
- HEAD requests could lead to timeouts with cURL adapter
- Fix for reserved characters in managed resources (SOLR-6853)
- Parsing nested details in debug response

### Changed
- Solarium\Component\Result\Stats\Result::getValue() is now public


## [6.1.3]
### Fixed
- possible exception in Debug\Detail::__toString() when sub details are missing


## [6.1.2]
### Added
- MoreLikeThis::setMaximumDocumentFrequency()
- MoreLikeThis::setMaximumDocumentFrequencyPercentage()
- getInterestingTerms() of MoreLikeThis Component results

### Fixed
- Debug\Detail return value types
- Debug\Document return value types

### Deprecated
- Support for `mlt.match.include` and `mlt.match.offset` in MoreLikeThis Component (they only work in MLT queries)


## [6.1.1]

### Fixed
- Set Client::VERSION to '6.1.1'. Release 6.1.0 accidentally declared itself as 6.0.4.


## [6.1.0]
### Added
- Indexing labelled nested child documents through pseudo-fields
- Extract query now supports extractFormat
- Helper::rangeQuery() now supports left-inclusive only and right-inclusive only queries

### Fixed
- PrefetchIterator::key() should return 0 instead of NULL on a fresh PrefetchIterator
- PrefetchIterator::next() shouldn't skip fetched results after PrefetchIterator::count() on a fresh PrefetchIterator
- PrefetchIterator::rewind() no longer results in duplicate documents when invoked mid-set
- Fixed incorrect median function
- Fix for maxScore being returned as "NaN" when group.query doesn't match any docs (SOLR-13839)

### Changed
- Exception message for invalid/unavailable file in Extract query now contains filename
- Helper::rangeQuery() detects point values without parameter to turn off escaping

### Removed
- PHP 7.2 support


## [6.0.4]
### Added
- PHP 8 support

### Fixed
- Avoid Notice: Undefined variable: http_response_header


## [6.0.3]
### Fixed
- Tika based file extraction with Solr 8.6
- Avoid TypeError if ClusterState contains no collections

### Changed
- Require specific symfony/event-dispatcher-contracts package instead of the generic symfony/contracts


## [6.0.2]
### Added
- Support for the analytics component
- Function builder
- Solarium\Component\FacetSet::setMatches()
- Solarium\Component\FacetSet::setExcludeTerms()
- Solarium\Component\Facet\Field::setMatches()
- Solarium\Component\Facet\Field::setExcludeTerms()
- Solarium\Component\Highlighting\Highlighting::setMethod()

### Changed
- Refactored Managed Resources code: use `createCommand()` and `createInitArgs()` to issue commands


## [6.0.1]
### Added
- Solarium\Component\Result\Facet\JsonRange::getBefore()
- Solarium\Component\Result\Facet\JsonRange::getAfter()
- Solarium\Component\Result\Facet\JsonRange::getBetween()

### Changed
 - Json range facet result now returns Solarium\Component\Result\Facet\JsonRange
 

## [6.0.0]
### Added
- \Solarium\Component\Result\Facet\Buckets::getNumBuckets()

### Changed
- Thrown exceptions always implement Solarium\Exception\ExceptionInterface


## [6.0.0-rc.1]
### Added
- \Solarium\Support\Utility::getXmlEncoding()

### Fixed
- MoreLikeThis result parsing fails on SolrCloud
- MinimumScoreFilter plugin might fail on Solr 7 in cloud mode


## [6.0.0-beta.1]
### Changed
- PostBigRequest plugin now acts on PRE_EXECUTE_REQUEST event instead of POST_CREATE_REQUEST
- CustomizeRequest plugin now acts on POST_CREATE_REQUEST event instead of PRE_EXECUTE_REQUEST

### Removed
- PHP 7.1 support


## [6.0.0-alpha.1]
### Added
- Raw XML commands to update query
- Raw XML from file in update query
- Set input encoding for select and update queries
- Create and configure Managed Resources

### Changed
- More strict types and type hinting
- `AdapterInterface` does not extend `ConfigurableInterface` anymore
- `Http` Adapter does not implement `ConfigurableInterface` anymore
- `Psr18Adapter` does not implement `ConfigurableInterface` anymore
- Solarium Client now accepts any PSR-14 compatible event dispatcher (previously it had to be the Symfony EventDispatcher)

### Removed
- Zend2HttpAdapter
- GuzzleAdapter
- Guzzle3Adapter
- Endpoint::setTimeout and Endpoint::getTimeout
- Passing local parameter options (e.g. ``key``, ``tag``, ``exclude``) without the ``local_`` prefix 
- Support for Solr versions before 7.7


## [5.2.0]
### Added
- PSR-18 http adapter

### Fixed
- PUT requests against Solr 8.5.0 using the Zend2Http and Http adapters

### Deprecated
- Zend2HttpAdapter, use PSR-18 http adapter instead
- GuzzleAdapter, use PSR-18 http adapter instead
- Guzzle3Adapter, use PSR-18 http adapter instead
- Endpoint::setTimeout and Endpoint::getTimeout, configure the timeout on the http adapter instead


## [5.1.6]
### Added
- Range facet pivot support
- Support for useConfiguredElevatedOrder
- FilterQuery::setCache and FilterQuery::setCost()

### Fixed
- Setting limit for pivot facets

### Changed
- Internal handling of Solr local parameters

### Deprecated
- Helper::cacheControl(). Use FilterQuery::setCache() and FilterQuery::setCost() instead


## [5.1.5]
### Security
- Remove explicit requirements for symfony/cache because of CVE-2019-18889

### Added
- Symfony 5 support

### Fixed
- PHP 7.4 compatibility issue: deprecated parameter order of implode()
- PHP 7.4 test coverage
- Solarium\Component\Result\Stats\Result getters might return null


## [5.1.4]
### Added
- Solarium\Component\Facet\Pivot::setLimit()
- Solarium\Component\Facet\Pivot::getLimit()

### Fixed
-  Client::checkExact() checks against wrong version number


## [5.1.3]
### Fixed
- Solarium\Component\ResponseParser\Debug fails on SolrCloud 6.x during extracting timing phases


## [5.1.2]
### Fixed
- BufferedAdd does not support Symfony event dispatcher
- An empty array as value in combination with the `set` modifier should remove a field when performing Atomic Updates


## [5.1.1]
### Fixed
- PHP 7.1 compatibility issue: date constants are not available as part of DateTimeInterface before PHP 7.2.0
- Use Symfony\Contracts\EventDispatcher\Event instead of deprecated Symfony\Component\EventDispatcher\Event


## [5.1.0]
### Fixed
- BufferedAdd::commit() type hints
- Symfony >=4.3 event dispatcher deprecation warnings


## [5.1.0-rc.1]
### Added
- Solarium\Core\Query\Helper::formatDate() now handles DateTimeImmutable

### Changed
- Try to capture complete response body as error message when using guzzle instead of using guzzle's truncated message
- Adapted to Symfony >=4.3 event dispatching, backward compatible to >=3.4, <=4.2

### Fixed
- Complex ReRank queries should not cause Solr parse errors
- Update request builders format \DateTimeImmutable correctly
- Symfony >=4.3 event dispatcher deprecation warnings

### Removed
- Symfony <3.4 support


## [5.0.3]
### Fixed
- Solarium\QueryType\MoreLikeThis\Query::setBoost()

### Changed
- Solarium\Core\Query\AbstractQuery::setTimeZone() now accepts \DateTimeZone objects as parameter


## [5.0.2]
### Fixed
- Spellchecker result isn't NULL in case of no suggestions and correctly spelled
- RangeFacet Result
- Solarium\QueryType\Select\Result and Component return types
- Solarium\Component\Highlighting::setFields() should accept comma separated string, too
- Solarium\Component\Result\Grouping\ValueGroup various return types
- Solarium\Component\RequestBuilder\RequestParamsTrait::addParam should not add empty arrays
- MinimumScoreFilterPlugin
- Running the examples

### Added
- Solarium\Component\MoreLikeThis::setInterestingTerms()
- Solarium\Component\MoreLikeThis::setMatchInclude()
- Solarium\Component\MoreLikeThis::setMatchOffset()


## [5.0.1]
### Fixed
- Getting started documentation


## [5.0.0]
### Added
- Component\Result\Facet\Bucket::getFacetSet()


## [5.0.0-rc.1]
### Added
- Spellcheck\Suggestion::getOriginalTerm()
- QueryType\Stream\ExpressionBuilder

### Changed
- Usage of composer and autoloader in examples.

### Fixed
- Query::setFields() should accept comma separated string, too.
- Readthedocs theme

### Deprecated
- QueryType\Stream\Expression is deprecated. Use QueryType\Stream\ExpressionBuilder instead.


## [5.0.0-beta.1]
### Added
- Support multiple spellcheck dictionaries

### Fixed
- Helper::rangeQuery() must not escape point values. Added a new parameter to turn off escaping.


## [5.0.0-alpha.2]
### Added
- Introduced FacetResultInterface

### Fixed
- TypeError: Return value of Solarium\Component\Result\FacetSet::getFacet()


## [5.0.0-alpha.1]
### Added
- Solr 8 support

### Changed
- Updated dev and test environments to newer package versions, for example PHPUnit 8.0
- Use PHP 7.1 style argument and return type declarations
- PHP 7.1 or higher required
- Refactored the two variants of DocumentInterface to become one to reduce confusion

### Removed
- PHP 7.0 support

### Fixed
- Status codes of the HTTPAdapter


## [4.3.0-alpha.2]
### Added
- Basic V2 API support
- Endpoint::getV2BaseUri

### Changed
- AdapterHelper functions are static

### Fixed
- In the past, the V1 API endpoint `solr` was not added automatically, so most users set it as path on the endpoint. This bug was discovered with the addition of V2 API support. In almost every setup, the path has to be set to `/` instead of `/solr` with this release!


## [4.3.0-alpha.1]
### Added
- Experimental support for collection API
- Parameter 'distrib' for queries

### Changed
- Deprecation of Endpoint::getBaseUri is revoked! It transparently forwards to getCollectionBaseUri or getCoreBaseUri now
- Endpoint::getBaseUri, ::getBaseCoreUri and ::getBaseCollectionUri throw UnexpectedValueException if no core or collection has been set

### Removed
- Symfony 2.x support
- Zend 1.x support
- PECL::Http adapter
- PHP 7.0 support
- Solr 1.4 result parser

### Fixed
- Support for add-distinct and removeregex modifiers in Document::setFieldModifier
- Zend2Http adapter caused duplicate request parameters


## [4.2.0]
### Fixed
- If a term contains a space, the space needs to be quoted by Helper::escapeTerm()
- Typos


## [4.2.0-rc.1]
### Added
- Support for managed resources
- Support for add-distinct and removeregex modifiers
- Basic support for Collections API (create, delete, reload, clusterstatus)

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
- Endpoint::getBaseUri is deprecated. Please use getServerUri or getCoreBaseUri now

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
- Basic support for SolrCloud streaming expressions


## [4.0.0-beta.1]
### Fixed
- Return type of Solarium\Component\QueryTraits\SuggesterTrait::getSuggester()
- Type hints in Solarium\Component\AbstractComponent


## [4.0.0-alpha.2]
### Added
- getSuggester() convenience method on Solarium\QueryType\Select\Query\Query
- More integration tests

### Removed
- Outdated Symfony versions on test environment

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
