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
 * @licence http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @package Solarium
 * @subpackage Query
 */

/**
 * Update Query
 */
class Solarium_Query_Update extends Solarium_Query
{

    /**
     * Default options
     * 
     * @var array
     */
    protected $_options = array(
        'path'          => '/update',
        'resultclass' => 'Solarium_Result_Update',
        'override' => null,
        'commitwithin' => null,
    );

    /**
     * Array of commands that define this update query
     *
     * @var array
     */
    protected $_commands = array();

    /**
     * Get all commands for this update query
     * 
     * @return array
     */
    public function getCommands()
    {
        return $this->_commands;
    }

    /**
     * Add a command to this update query. This must be an instance of one of
     * the Solarium_Query_Update_* classes.
     *
     * @param string $key
     * @param object $command
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function add($key, $command)
    {
        if (0 !== strlen($key)) {
            $this->_commands[$key] = $command;
        } else {
            $this->_commands[] = $command;
        }

        return $this;
    }

    /**
     * Remove a command, by key
     *
     * @param string $key
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function remove($key)
    {
        if (isset($this->_commands[$key])) {
            unset($this->_commands[$key]); 
        }
        return $this;
    }

    /**
     * Convenience method for adding a rollback.
     * If you need more control, like choosing a key for the command you need to
     * create you own command instances and you the add method.
     *
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addRollback()
    {
        return $this->add(null, new Solarium_Query_Update_Command_Rollback);
    }

    /**
     * Convenience method for adding a delete query.
     * If you need more control, like choosing a key for the command you need to
     * create you own command instances and you the add method.
     *
     * @param string $query
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addDeleteQuery($query)
    {
        $delete = new Solarium_Query_Update_Command_Delete;
        $delete->addQuery($query);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add multiple delete queries to the update
     * If you need more control, like choosing a key for the command you need to
     * create you own command instances and you the add method.
     *
     * @param string $key
     * @param array $queries
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addDeleteQueries($queries)
    {
        $delete = new Solarium_Query_Update_Command_Delete;
        $delete->addQueries($queries);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a delete by ID to the update
     * If you need more control, like choosing a key for the command you need to
     * create you own command instances and you the add method.
     *
     * @param int|string $id
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addDeleteById($id)
    {
        $delete = new Solarium_Query_Update_Command_Delete;
        $delete->addId($id);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add delete by multiple IDs to the update
     * If you need more control, like choosing a key for the command you need to
     * create you own command instances and you the add method.
     *
     * @param array $id
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addDeleteByIds($ids)
    {
        $delete = new Solarium_Query_Update_Command_Delete;
        $delete->addIds($ids);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a document to the update.
     * If you need more control, like choosing a key for the command you need to
     * create you own command instances and you the add method.
     *
     * @param Solarium_Document_ReadWrite $document
     * @param boolean $overwrite
     * @param int $commitWithin
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addDocument($document, $override = null,
                                $commitWithin = null)
    {
        return $this->addDocuments(array($document), $override, $commitWithin);
    }

    /**
     * Convenience method to add multiple documents to the update.
     * If you need more control, like choosing a key for the command you need to
     * create you own command instances and you the add method.
     *
     * @param array $documents
     * @param boolean $overwrite
     * @param int $commitWithin
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addDocuments($documents, $overwrite = null,
                                 $commitWithin = null)
    {
        $add = new Solarium_Query_Update_Command_Add;
        if (null !== $overwrite) $add->setOverwrite($overwrite);
        if (null !== $commitWithin) $add->setCommitWithin($commitWithin);

        $add->addDocuments($documents);
        return $this->add(null, $add);
    }

    /**
     * Convenience method to add a commit command to the update
     * If you need more control, like choosing a key for the command you need to
     * create you own command instances and you the add method.
     *
     * @param boolean $waitFlush
     * @param boolean $waitSearcher
     * @param boolean $expungeDeletes
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addCommit($waitFlush = null, $waitSearcher = null,
                              $expungeDeletes = null)
    {
        $commit = new Solarium_Query_Update_Command_Commit();
        if (null !== $waitFlush) $commit->setWaitFlush($waitFlush);
        if (null !== $waitSearcher) $commit->setWaitSearcher($waitSearcher);
        if (null !== $expungeDeletes)
            $commit->setExpungeDeletes($expungeDeletes);

        return $this->add(null, $commit);
    }

    /**
     * Convenience method to add an optimize command to the update
     * If you need more control, like choosing a key for the command you need to
     * create you own command instances and you the add method.
     *
     * @param boolean $waitFlush
     * @param boolean $waitSearcher
     * @param int $maxSegments
     * @return Solarium_Query_Update Provides fluent interface
     */
   public function addOptimize($waitFlush = null, $waitSearcher = null,
                               $maxSegments = null)
   {
       $optimize = new Solarium_Query_Update_Command_Optimize();
       if (null !== $waitFlush) $optimize->setWaitFlush($waitFlush);
       if (null !== $waitSearcher) $optimize->setWaitSearcher($waitSearcher);
       if (null !== $maxSegments) $optimize->setMaxSegments($maxSegments);

       return $this->add(null, $optimize);
   }

}