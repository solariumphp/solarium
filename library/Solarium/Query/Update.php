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
 * Update query
 *
 * Can be used to send multiple update commands to solr, e.g. add, delete,
 * rollback, commit, optimize.
 * Multiple commands of any type can be combined into a single update query.
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Update extends Solarium_Query
{

    /**
     * Update command add
     */
    const COMMAND_ADD = 'add';

    /**
     * Update command delete
     */
    const COMMAND_DELETE = 'delete';

    /**
     * Update command commit
     */
    const COMMAND_COMMIT = 'commit';

    /**
     * Update command rollback
     */
    const COMMAND_ROLLBACK = 'rollback';

    /**
     * Update command optimize
     */
    const COMMAND_OPTIMIZE = 'optimize';

    /**
     * Update command types
     *
     * @var array
     */
    protected $_commandTypes = array(
        self::COMMAND_ADD => 'Solarium_Query_Update_Command_Add',
        self::COMMAND_DELETE => 'Solarium_Query_Update_Command_Delete',
        self::COMMAND_COMMIT => 'Solarium_Query_Update_Command_Commit',
        self::COMMAND_OPTIMIZE => 'Solarium_Query_Update_Command_Optimize',
        self::COMMAND_ROLLBACK => 'Solarium_Query_Update_Command_Rollback',
    );

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'handler'       => 'update',
        'resultclass'   => 'Solarium_Result_Update',
        'documentclass' => 'Solarium_Document_ReadWrite',
    );

    /**
     * Array of commands
     *
     * The commands will be executed in the order of this array, this can be
     * important in some cases. For instance a rollback.
     *
     * @var array
     */
    protected $_commands = array();

    /**
     * Get type for this query
     *
     * @return string
     */
    public function getType()
    {
        return Solarium_Client::QUERYTYPE_UPDATE;
    }

    /**
     * Initialize options
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     *
     * @return void
     */
    protected function _init()
    {
        if (isset($this->_options['command'])) {
            foreach ($this->_options['command'] as $key => $value) {

                $type = $value['type'];

                if ($type == self::COMMAND_ADD) {
                    throw new Solarium_Exception(
                        "Adding documents is not supported in configuration, use the API for this"
                    );
                }

                $this->add($key, $this->createCommand($type, $value));
            }
        }

    }

    /**
     * Create a command instance
     *
     * @throws Solarium_Exception
     * @param string $type
     * @param mixed $options
     * @return Solarium_Query_Update_Command
     */
    public function createCommand($type, $options = null)
    {
        $type = strtolower($type);

        if (!isset($this->_commandTypes[$type])) {
            throw new Solarium_Exception("Update commandtype unknown: " . $type);
        }

        $class = $this->_commandTypes[$type];
        return new $class($options);
    }

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
     * Add a command to this update query
     *
     * The command must be an instance of one of the Solarium_Query_Update_*
     * classes.
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
     * Remove a command
     *
     * You can remove a command by passing it's key or by passing the command instance
     *
     * @param string|Solarium_Query_Update_Command $command
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function remove($command)
    {
        if (is_object($command)) {
            foreach ($this->_commands as $key => $instance) {
                if ($instance === $command) {
                    unset($this->_commands[$key]);
                    break;
                }
            }
        } else {
            if (isset($this->_commands[$command])) {
                unset($this->_commands[$command]);
            }
        }

        return $this;
    }

    /**
     * Convenience method for adding a rollback command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addRollback()
    {
        return $this->add(null, new Solarium_Query_Update_Command_Rollback);
    }

    /**
     * Convenience method for adding a delete query command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
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
     * Convenience method to add a multi delete query command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
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
     * Convenience method to add a delete by ID command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
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
     * Convenience method to add a delete by IDs command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param array $ids
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addDeleteByIds($ids)
    {
        $delete = new Solarium_Query_Update_Command_Delete;
        $delete->addIds($ids);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a 'add document' command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param Solarium_Document_ReadWrite $document
     * @param boolean $overwrite
     * @param int $commitWithin
     * @return Solarium_Query_Update Provides fluent interface
     */
    public function addDocument($document, $overwrite = null,
                                $commitWithin = null)
    {
        return $this->addDocuments(array($document), $overwrite, $commitWithin);
    }

    /**
     * Convenience method to add a 'add documents' command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
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
     * Convenience method to add a commit command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
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
     * Convenience method to add an optimize command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
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

   /**
    * Set a custom document class for use in the createDocument method
    *
    * This class should extend Solarium_Document_ReadWrite or
    * at least be compatible with it's interface
    *
    * @param string $value classname
    * @return Solarium_Query
    */
    public function setDocumentClass($value)
    {
        return $this->_setOption('documentclass', $value);
    }

    /**
     * Get the current documentclass option
     *
     * The value is a classname, not an instance
     *
     * @return string
     */
    public function getDocumentClass()
    {
        return $this->getOption('documentclass');
    }

    /**
     * Create a document object instance
     *
     * You can optionally directly supply the fields and boosts
     * to get a ready-made document instance for direct use in an add command
     *
     * @since 2.1.0
     *
     * @param array $fields
     * @param array $boosts
     * @return Solarium_Document_ReadWrite
     */
    public function createDocument($fields = array(), $boosts = array())
    {
        $class = $this->getDocumentClass();

        return new $class($fields, $boosts);
    }

}