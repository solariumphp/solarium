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

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query delete command.
 *
 * @link http://wiki.apache.org/solr/UpdateXmlMessages#A.22delete.22_by_ID_and_by_Query
 */
class Delete extends AbstractCommand
{
    /**
     * Ids to delete.
     *
     * @var array
     */
    protected $ids = array();

    /**
     * Delete queries.
     *
     * @var array
     */
    protected $queries = array();

    /**
     * Get command type.
     *
     * @return string
     */
    public function getType()
    {
        return UpdateQuery::COMMAND_DELETE;
    }

    /**
     * Add a single ID to the delete command.
     *
     * @param int|string $id
     *
     * @return self Provides fluent interface
     */
    public function addId($id)
    {
        $this->ids[] = $id;

        return $this;
    }

    /**
     * Add multiple IDs to the delete command.
     *
     * @param array $ids
     *
     * @return self Provides fluent interface
     */
    public function addIds($ids)
    {
        $this->ids = array_merge($this->ids, $ids);

        return $this;
    }

    /**
     * Add a single query to the delete command.
     *
     * @param string $query
     *
     * @return self Provides fluent interface
     */
    public function addQuery($query)
    {
        $this->queries[] = $query;

        return $this;
    }

    /**
     * Add multiple queries to the delete command.
     *
     * @param array $queries
     *
     * @return self Provides fluent interface
     */
    public function addQueries($queries)
    {
        $this->queries = array_merge($this->queries, $queries);

        return $this;
    }

    /**
     * Get all queries of this delete command.
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * Get all qids of this delete command.
     *
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * Build ids/queries based on options.
     */
    protected function init()
    {
        $id = $this->getOption('id');
        if (null !== $id) {
            if (is_array($id)) {
                $this->addIds($id);
            } else {
                $this->addId($id);
            }
        }

        $queries = $this->getOption('query');
        if (null !== $queries) {
            if (is_array($queries)) {
                $this->addQueries($queries);
            } else {
                $this->addQuery($queries);
            }
        }
    }
}
