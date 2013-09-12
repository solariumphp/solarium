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
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Update\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\Query as BaseQuery;
use Solarium\QueryType\Update\RequestBuilder;
use Solarium\QueryType\Update\ResponseParser;
use Solarium\Exception\RuntimeException;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Update\Query\Command\Command;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Query\Command\Commit as CommitCommand;
use Solarium\QueryType\Update\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\Update\Query\Command\Optimize as OptimizeCommand;
use Solarium\QueryType\Update\Query\Command\Rollback as RollbackCommand;
use Solarium\QueryType\Update\Query\Document\DocumentInterface;

/**
 * Update query
 *
 * Can be used to send multiple update commands to solr, e.g. add, delete,
 * rollback, commit, optimize.
 * Multiple commands of any type can be combined into a single update query.
 */
class Query extends BaseQuery
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
    protected $commandTypes = array(
        self::COMMAND_ADD => 'Solarium\QueryType\Update\Query\Command\Add',
        self::COMMAND_DELETE => 'Solarium\QueryType\Update\Query\Command\Delete',
        self::COMMAND_COMMIT => 'Solarium\QueryType\Update\Query\Command\Commit',
        self::COMMAND_OPTIMIZE => 'Solarium\QueryType\Update\Query\Command\Optimize',
        self::COMMAND_ROLLBACK => 'Solarium\QueryType\Update\Query\Command\Rollback',
    );

    /**
     * Default options
     *
     * @var array
     */
    protected $options = array(
        'handler'       => 'update',
        'resultclass'   => 'Solarium\QueryType\Update\Result',
        'documentclass' => 'Solarium\QueryType\Update\Query\Document\Document',
        'omitheader'    => false,
    );

    /**
     * Array of commands
     *
     * The commands will be executed in the order of this array, this can be
     * important in some cases. For instance a rollback.
     *
     * @var Command[]
     */
    protected $commands = array();

    /**
     * Get type for this query
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_UPDATE;
    }

    /**
     * Get a requestbuilder for this query
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder;
    }

    /**
     * Get a response parser for this query
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser;
    }

    /**
     * Initialize options
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     *
     * @throws RuntimeException
     * @return void
     */
    protected function init()
    {
        if (isset($this->options['command'])) {
            foreach ($this->options['command'] as $key => $value) {

                $type = $value['type'];

                if ($type == self::COMMAND_ADD) {
                    throw new RuntimeException(
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
     * @throws InvalidArgumentException
     * @param  string                   $type
     * @param  mixed                    $options
     * @return Command
     */
    public function createCommand($type, $options = null)
    {
        $type = strtolower($type);

        if (!isset($this->commandTypes[$type])) {
            throw new InvalidArgumentException("Update commandtype unknown: " . $type);
        }

        $class = $this->commandTypes[$type];

        return new $class($options);
    }

    /**
     * Get all commands for this update query
     *
     * @return Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Add a command to this update query
     *
     * The command must be an instance of one of the Solarium\QueryType\Update_*
     * classes.
     *
     * @param  string $key
     * @param  object $command
     * @return self   Provides fluent interface
     */
    public function add($key, $command)
    {
        if (0 !== strlen($key)) {
            $this->commands[$key] = $command;
        } else {
            $this->commands[] = $command;
        }

        return $this;
    }

    /**
     * Remove a command
     *
     * You can remove a command by passing its key or by passing the command instance.
     *
     * @param  string|\Solarium\QueryType\Update\Query\Command\Command $command
     * @return self                                                    Provides fluent interface
     */
    public function remove($command)
    {
        if (is_object($command)) {
            foreach ($this->commands as $key => $instance) {
                if ($instance === $command) {
                    unset($this->commands[$key]);
                    break;
                }
            }
        } else {
            if (isset($this->commands[$command])) {
                unset($this->commands[$command]);
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
     * @return self Provides fluent interface
     */
    public function addRollback()
    {
        return $this->add(null, new RollbackCommand);
    }

    /**
     * Convenience method for adding a delete query command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param  string $query
     * @param  array  $bind  Bind values for placeholders in the query string
     * @return self   Provides fluent interface
     */
    public function addDeleteQuery($query, $bind = null)
    {
        if (!is_null($bind)) {
            $query = $this->getHelper()->assemble($query, $bind);
        }

        $delete = new DeleteCommand;
        $delete->addQuery($query);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a multi delete query command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param  array $queries
     * @return self  Provides fluent interface
     */
    public function addDeleteQueries($queries)
    {
        $delete = new DeleteCommand;
        $delete->addQueries($queries);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a delete by ID command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param  int|string $id
     * @return self       Provides fluent interface
     */
    public function addDeleteById($id)
    {
        $delete = new DeleteCommand;
        $delete->addId($id);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a delete by IDs command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param  array $ids
     * @return self  Provides fluent interface
     */
    public function addDeleteByIds($ids)
    {
        $delete = new DeleteCommand;
        $delete->addIds($ids);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a 'add document' command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param  DocumentInterface $document
     * @param  boolean           $overwrite
     * @param  int               $commitWithin
     * @return self              Provides fluent interface
     */
    public function addDocument(DocumentInterface $document, $overwrite = null, $commitWithin = null)
    {
        return $this->addDocuments(array($document), $overwrite, $commitWithin);
    }

    /**
     * Convenience method to add a 'add documents' command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param  array   $documents
     * @param  boolean $overwrite
     * @param  int     $commitWithin
     * @return self    Provides fluent interface
     */
    public function addDocuments($documents, $overwrite = null, $commitWithin = null)
    {
        $add = new AddCommand;

        if (null !== $overwrite) {
            $add->setOverwrite($overwrite);
        }

        if (null !== $commitWithin) {
            $add->setCommitWithin($commitWithin);
        }

        $add->addDocuments($documents);

        return $this->add(null, $add);
    }

    /**
     * Convenience method to add a commit command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param  boolean $softCommit
     * @param  boolean $waitSearcher
     * @param  boolean $expungeDeletes
     * @return self    Provides fluent interface
     */
    public function addCommit($softCommit = null, $waitSearcher = null, $expungeDeletes = null)
    {
        $commit = new CommitCommand();

        if (null !== $softCommit) {
            $commit->setSoftCommit($softCommit);
        }

        if (null !== $waitSearcher) {
            $commit->setWaitSearcher($waitSearcher);
        }

        if (null !== $expungeDeletes) {
            $commit->setExpungeDeletes($expungeDeletes);
        }

        return $this->add(null, $commit);
    }

    /**
     * Convenience method to add an optimize command
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param  boolean $softCommit
     * @param  boolean $waitSearcher
     * @param  int     $maxSegments
     * @return self    Provides fluent interface
     */
    public function addOptimize($softCommit = null, $waitSearcher = null, $maxSegments = null)
    {
        $optimize = new OptimizeCommand();

        if (null !== $softCommit) {
            $optimize->setSoftCommit($softCommit);
        }

        if (null !== $waitSearcher) {
            $optimize->setWaitSearcher($waitSearcher);
        }

        if (null !== $maxSegments) {
            $optimize->setMaxSegments($maxSegments);
        }

        return $this->add(null, $optimize);
    }

    /**
     * Set a custom document class for use in the createDocument method
     *
     * This class should implement the document interface
     *
     * @param string $value classname
     * @return self Provides fluent interface
     */
    public function setDocumentClass($value)
    {
        return $this->setOption('documentclass', $value);
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
     * @param  array    $fields
     * @param  array    $boosts
     * @param  array    $modifiers
     * @return DocumentInterface
     */
    public function createDocument($fields = array(), $boosts = array(), $modifiers = array())
    {
        $class = $this->getDocumentClass();
        return new $class($fields, $boosts, $modifiers);
    }
}
