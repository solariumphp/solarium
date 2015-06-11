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
namespace Solarium\QueryType\Schema\Query;

use Solarium\Core\ArrayableInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\Query as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Schema\Query\Command\AddField;
use Solarium\QueryType\Schema\Query\Command\Command;
use Solarium\QueryType\Schema\RequestBuilder;
use Solarium\QueryType\Schema\ResponseParser;


class Query extends BaseQuery implements ArrayableInterface {

    /**
     * Schema command add field
     */
    const   COMMAND_ADD_FIELD   =   'add-field';

    /**
     * Default options
     *
     * @var array
     */
    protected $options = array(
        'handler'       => 'schema',
        'resultclass'   => 'Solarium\QueryType\Schema\Result',
        'omitheader'    => false,
    );

    protected $commands = array();

    /**
     * Schema command types
     *
     * @var array
     */
    protected $commandTypes = array(
        self::COMMAND_ADD_FIELD => 'Solarium\QueryType\Schema\Query\Command\AddField',
    );

    /**
     * @return array
     */
    public function getCommands() {
        return $this->commands;
    }

    /**
     * Add a command to this update query
     *
     * The command must be an instance of one of the Solarium\QueryType\Schema_*
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
     * @param  string|\Solarium\QueryType\Schema\Query\Command\Command $command
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
            throw new InvalidArgumentException("Schema commandtype unknown: " . $type);
        }

        $class = $this->commandTypes[$type];

        return new $class($options);
    }

    /**
     * @return bool
     */
    public function hasCommands() {
        return (bool) $this->commands;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function addFields(array $fields) {

        if (array_key_exists(static::COMMAND_ADD_FIELD, $this->commands))
            $add    =   $this->commands[static::COMMAND_ADD_FIELD];
        else
            $add    =   new AddField;

        $add->addFields($fields);
        return $this->add(static::COMMAND_ADD_FIELD, $add);
    }

    /**
     * @param $field
     * @return $this
     */
    public function addField($field) {
        return $this->addFields(array($field));
    }

    /**
     * Get type for this query
     *
     * @return string
     */
    public function getType() {
        return Client::QUERY_SCHEMA;
    }

    /**
     * Get the requestbuilder class for this query
     *
     * @return RequestBuilderInterface
     */
    public function getRequestBuilder() {
        return new RequestBuilder;
    }

    /**
     * Get the response parser class for this query
     *
     * @return ResponseParserInterface
     */
    public function getResponseParser() {
        return new ResponseParser;
    }


    /**
     * @return array
     */
    public function castAsArray() {
        return array_map(function (Command $command) {
            return $command->castAsArray();
        }, $this->getCommands());
    }

}