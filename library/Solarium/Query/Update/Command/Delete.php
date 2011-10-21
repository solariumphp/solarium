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
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Query
 */

/**
 * Update query delete command
 *
 * For details about the Solr options see:
 * @link http://wiki.apache.org/solr/UpdateXmlMessages#A.22delete.22_by_ID_and_by_Query
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Update_Command_Delete extends Solarium_Query_Update_Command
{

    /**
     * Ids to delete
     *
     * @var array
     */
    protected $_ids = array();

    /**
     * Delete queries
     * 
     * @var array
     */
    protected $_queries = array();

    /**
     * Get command type
     * 
     * @return string
     */
    public function getType()
    {
        return Solarium_Query_Update::COMMAND_DELETE;
    }

    /**
     * Build ids/queries based on options
     * 
     * @return void
     */
    protected function _init()
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

    /**
     * Add a single ID to the delete command
     *
     * @param int|string $id
     * @return Solarium_Query_Update_Command_Delete Provides fluent interface
     */
    public function addId($id)
    {
        $this->_ids[] = $id;

        return $this;
    }

    /**
     * Add multiple IDs to the delete command
     *
     * @param array $ids
     * @return Solarium_Query_Update_Command_Delete Provides fluent interface
     */
    public function addIds($ids)
    {
        $this->_ids = array_merge($this->_ids, $ids);

        return $this;
    }

    /**
     * Add a single query to the delete command
     *
     * @param string $query
     * @return Solarium_Query_Update_Command_Delete Provides fluent interface
     */
    public function addQuery($query)
    {
        $this->_queries[] = $query;

        return $this;
    }

    /**
     * Add multiple queries to the delete command
     *
     * @param array $queries
     * @return Solarium_Query_Update_Command_Delete Provides fluent interface
     */
    public function addQueries($queries)
    {
        $this->_queries = array_merge($this->_queries, $queries);

        return $this;
    }

    /**
     * Get all queries of this delete command
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->_queries;
    }

    /**
     * Get all qids of this delete command
     *
     * @return array
     */
    public function getIds()
    {
        return $this->_ids;
    }

}