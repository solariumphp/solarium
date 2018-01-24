<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\Core\Event;

/**
 * Event definitions.
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
    const PRE_CREATE_REQUEST = 'solarium.core.preCreateRequest';

    /**
     * The postCreateRequest event is thrown just after a request has been created based on a query object, using the
     * requestbuilder.
     *
     * The event listener receives a QueryInterface instance and a Request instance.
     *
     * @var string
     */
    const POST_CREATE_REQUEST = 'solarium.core.postCreateRequest';

    /**
     * The preExecuteRequest event is thrown just before a request is sent to Solr.
     *
     * The event listener receives a Request instance.
     *
     * @var string
     */
    const PRE_EXECUTE_REQUEST = 'solarium.core.preExecuteRequest';

    /**
     * The postExecuteRequest event is thrown just after a request has been sent to Solr.
     *
     * The event listener receives a Request instance and a Response instance.
     *
     * @var string
     */
    const POST_EXECUTE_REQUEST = 'solarium.core.postExecuteRequest';

    /**
     * The preCreateResult event is before the Solr response data is parsed into a result object.
     *
     * The event listener receives a Query and a Response instance.
     *
     * @var string
     */
    const PRE_CREATE_RESULT = 'solarium.core.preCreateResult';

    /**
     * The postCreateResult event is thrown just after the Solr response data was parsed into a result object.
     *
     * The event listener receives a Query, Response and Result instance.
     *
     * @var string
     */
    const POST_CREATE_RESULT = 'solarium.core.postCreateResult';

    /**
     * The preExecute event is thrown as soon as the Solarium client execute method is called. This method
     * calls the createRequest, executeRequest and createResponse methods. Using this event you can override
     * the standard execution flow.
     *
     * The event listener receives a Query instance.
     *
     * @var string
     */
    const PRE_EXECUTE = 'solarium.core.preExecute';

    /**
     * The postExecute event is thrown just after a all execution is done.
     *
     * The event listener receives a Query instance and a Result instance.
     *
     * @var string
     */
    const POST_EXECUTE = 'solarium.core.postExecute';

    /**
     * The preCreateQuery event is thrown before the creation of a new query object. Using this event you can
     * for instance customize the returned query.
     *
     * The event listener receives a QueryType string and an Options array.
     *
     * @var string
     */
    const PRE_CREATE_QUERY = 'solarium.core.preCreateQuery';

    /**
     * The postCreateQuery event is thrown after the creation of a new query object. Using this event you can
     * for instance customize the returned query.
     *
     * The event listener receives a querytype string, an Options array and the resulting QueryType instance.
     *
     * @var string
     */
    const POST_CREATE_QUERY = 'solarium.core.postCreateQuery';
}
