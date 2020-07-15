<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Event;

/**
 * Event definitions.
 *
 * @codeCoverageIgnore
 */
class Events
{
    /**
     * The preCreateRequest event is thrown just before a request is created based on a query object, using the
     * requestbuilder.
     *
     * The event listener receives a QueryInterface instance.
     *
     * @var string
     */
    public const PRE_CREATE_REQUEST = PreCreateRequest::class;

    /**
     * The postCreateRequest event is thrown just after a request has been created based on a query object, using the
     * requestbuilder.
     *
     * The event listener receives a QueryInterface instance and a Request instance.
     *
     * @var string
     */
    public const POST_CREATE_REQUEST = PostCreateRequest::class;

    /**
     * The preExecuteRequest event is thrown just before a request is sent to Solr.
     *
     * The event listener receives a Request instance.
     *
     * @var string
     */
    public const PRE_EXECUTE_REQUEST = PreExecuteRequest::class;

    /**
     * The postExecuteRequest event is thrown just after a request has been sent to Solr.
     *
     * The event listener receives a Request instance and a Response instance.
     *
     * @var string
     */
    public const POST_EXECUTE_REQUEST = PostExecuteRequest::class;

    /**
     * The preCreateResult event is before the Solr response data is parsed into a result object.
     *
     * The event listener receives a Query and a Response instance.
     *
     * @var string
     */
    public const PRE_CREATE_RESULT = PreCreateResult::class;

    /**
     * The postCreateResult event is thrown just after the Solr response data was parsed into a result object.
     *
     * The event listener receives a Query, Response and Result instance.
     *
     * @var string
     */
    public const POST_CREATE_RESULT = PostCreateResult::class;

    /**
     * The preExecute event is thrown as soon as the Solarium client execute method is called. This method
     * calls the createRequest, executeRequest and createResponse methods. Using this event you can override
     * the standard execution flow.
     *
     * The event listener receives a Query instance.
     *
     * @var string
     */
    public const PRE_EXECUTE = PreExecute::class;

    /**
     * The postExecute event is thrown just after a all execution is done.
     *
     * The event listener receives a Query instance and a Result instance.
     *
     * @var string
     */
    public const POST_EXECUTE = PostExecute::class;

    /**
     * The preCreateQuery event is thrown before the creation of a new query object. Using this event you can
     * for instance customize the returned query.
     *
     * The event listener receives a QueryType string and an Options array.
     *
     * @var string
     */
    public const PRE_CREATE_QUERY = PreCreateQuery::class;

    /**
     * The postCreateQuery event is thrown after the creation of a new query object. Using this event you can
     * for instance customize the returned query.
     *
     * The event listener receives a querytype string, an Options array and the resulting QueryType instance.
     *
     * @var string
     */
    public const POST_CREATE_QUERY = PostCreateQuery::class;

    /**
     * Not instantiable.
     */
    private function __construct()
    {
    }
}
