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

namespace Solarium\QueryType\Select\Query\Component;

use Solarium\Core\Configurable;
use Solarium\Core\Query\AbstractQuery;
use Solarium\QueryType\Select\ResponseParser\Component\ComponentParserInterface;
use Solarium\QueryType\Select\RequestBuilder\Component\ComponentRequestBuilderInterface;

/**
 * Query component base class.
 */
abstract class AbstractComponent extends Configurable
{
    /**
     * @var AbstractQuery
     */
    protected $queryInstance;

    /**
     * Get component type.
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Get the requestbuilder class for this query.
     *
     * @return ComponentRequestBuilderInterface
     */
    abstract public function getRequestBuilder();

    /**
     * Get the response parser class for this query.
     *
     * @return ComponentParserInterface
     */
    abstract public function getResponseParser();

    /**
     * Set parent query instance.
     *
     * @param AbstractQuery $instance
     *
     * @return self Provides fluent interface
     */
    public function setQueryInstance(AbstractQuery $instance)
    {
        $this->queryInstance = $instance;

        return $this;
    }

    /**
     * Get parent query instance.
     *
     * @return AbstractQuery
     */
    public function getQueryInstance()
    {
        return $this->queryInstance;
    }
}
