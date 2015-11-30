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

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Analysis\ResponseParser\Field;
use Solarium\QueryType\Schema\Query\Command\AddCopyField;
use Solarium\QueryType\Schema\Query\Command\AddDynamicField;
use Solarium\QueryType\Schema\Query\Command\AddField;
use Solarium\QueryType\Schema\Query\Command\AddFieldType;
use Solarium\QueryType\Schema\Query\Command\Command;
use Solarium\QueryType\Schema\Query\Command\DeleteCopyField;
use Solarium\QueryType\Schema\Query\Command\DeleteDynamicField;
use Solarium\QueryType\Schema\Query\Command\DeleteField;
use Solarium\QueryType\Schema\Query\Command\DeleteFieldType;
use Solarium\QueryType\Schema\Query\Command\ReplaceDynamicField;
use Solarium\QueryType\Schema\Query\Command\ReplaceField;
use Solarium\QueryType\Schema\Query\Command\ReplaceFieldType;
use Solarium\QueryType\Schema\Query\Field\CopyField;
use Solarium\QueryType\Schema\Query\Field\FieldInterface;
use Solarium\QueryType\Schema\Query\FieldType\FieldType;
use Solarium\QueryType\Schema\Query\FieldType\FieldTypeInterface;
use Solarium\QueryType\Schema\RequestBuilder;
use Solarium\QueryType\Schema\ResponseParser;

/**
 * Class Query
 * @author Beno!t POLASZEK
 */
class Query extends BaseQuery
{
    /**
     * Schema command add field
     */
    const   COMMAND_ADD_FIELD               =   'add-field';
    const   COMMAND_DELETE_FIELD            =   'delete-field';
    const   COMMAND_REPLACE_FIELD           =   'replace-field';
    const   COMMAND_ADD_DYNAMIC_FIELD       =   'add-dynamic-field';
    const   COMMAND_DELETE_DYNAMIC_FIELD    =   'delete-dynamic-field';
    const   COMMAND_REPLACE_DYNAMIC_FIELD   =   'replace-dynamic-field';
    const   COMMAND_ADD_COPY_FIELD          =   'add-copy-field';
    const   COMMAND_DELETE_COPY_FIELD       =   'delete-copy-field';
    const   COMMAND_ADD_FIELD_TYPE          =   'add-field-type';
    const   COMMAND_DELETE_FIELD_TYPE       =   'delete-field-type';
    const   COMMAND_REPLACE_FIELD_TYPE      =   'replace-field-type';

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
        self::COMMAND_ADD_FIELD             => 'Solarium\QueryType\Schema\Query\Command\AddField',
        self::COMMAND_DELETE_FIELD          => 'Solarium\QueryType\Schema\Query\Command\DeleteField',
        self::COMMAND_REPLACE_FIELD         => 'Solarium\QueryType\Schema\Query\Command\ReplaceField',
        self::COMMAND_ADD_DYNAMIC_FIELD     => 'Solarium\QueryType\Schema\Query\Command\AddDynamicField',
        self::COMMAND_DELETE_DYNAMIC_FIELD  => 'Solarium\QueryType\Schema\Query\Command\DeleteDynamicField',
        self::COMMAND_REPLACE_DYNAMIC_FIELD => 'Solarium\QueryType\Schema\Query\Command\ReplaceDynamicField',
        self::COMMAND_ADD_COPY_FIELD        => 'Solarium\QueryType\Schema\Query\Command\AddCopyField',
        self::COMMAND_DELETE_COPY_FIELD     => 'Solarium\QueryType\Schema\Query\Command\DeleteCopyField',
        self::COMMAND_ADD_FIELD_TYPE        => 'Solarium\QueryType\Schema\Query\Command\AddFieldType',
        self::COMMAND_DELETE_FIELD_TYPE     => 'Solarium\QueryType\Schema\Query\Command\DeleteFieldType',
        self::COMMAND_REPLACE_FIELD_TYPE    => 'Solarium\QueryType\Schema\Query\Command\ReplaceFieldType',
    );

    /**
     * @return array
     */
    public function getCommands() {
        return $this->commands;
    }

    /**
     * Add a command to this schema query
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
     * @param FieldInterface[] $fields
     * @return $this
     */
    public function addFields(array $fields)
    {
        if (array_key_exists(static::COMMAND_ADD_FIELD, $this->commands)) {
            $command = $this->commands[static::COMMAND_ADD_FIELD];
        } else {
            $command = new AddField();
        }
        $command->addFields($fields);

        return $this->add(static::COMMAND_ADD_FIELD, $command);
    }

    /**
     * @param FieldInterface $field
     * @return $this
     */
    public function addField(FieldInterface $field)
    {
        return $this->addFields(array($field));
    }

    /**
     * @param FieldInterface[] $fields
     * @return $this
     */
    public function replaceFields(array $fields)
    {
        if (array_key_exists(static::COMMAND_REPLACE_FIELD, $this->commands)) {
            $command = $this->commands[static::COMMAND_REPLACE_FIELD];
        } else {
            $command = new ReplaceField();
        }
        $command->addFields($fields);

        return $this->add(static::COMMAND_REPLACE_FIELD, $command);
    }

    /**
     * @param FieldInterface $field
     * @return $this
     */
    public function replaceField(FieldInterface $field)
    {
        return $this->replaceFields(array($field));
    }

    /**
     * @param FieldInterface[] $fields
     * @return $this
     */
    public function deleteFields(array $fields)
    {
        if (array_key_exists(static::COMMAND_DELETE_FIELD, $this->commands)) {
            $command = $this->commands[static::COMMAND_DELETE_FIELD];
        } else {
            $command = new DeleteField();
        }
        $command->addFields($fields);

        return $this->add(static::COMMAND_DELETE_FIELD, $command);
    }

    /**
     * @param FieldInterface $field
     * @return $this
     */
    public function deleteField(FieldInterface $field)
    {
        return $this->deleteFields(array($field));
    }

    /**
     * @param FieldInterface[] $fields
     * @return $this
     */
    public function addDynamicFields(array $fields)
    {
        if (array_key_exists(static::COMMAND_ADD_DYNAMIC_FIELD, $this->commands)) {
            $command = $this->commands[static::COMMAND_ADD_DYNAMIC_FIELD];
        } else {
            $command = new AddDynamicField();
        }

        $command->addFields($fields);

        return $this->add(static::COMMAND_ADD_DYNAMIC_FIELD, $command);
    }

    /**
     * @param FieldInterface $field
     * @return $this
     */
    public function addDynamicField(FieldInterface $field)
    {
        return $this->addDynamicFields(array($field));
    }

    /**
     * @param FieldInterface[] $fields
     * @return $this
     */
    public function replaceDynamicFields(array $fields)
    {

        if (array_key_exists(static::COMMAND_REPLACE_DYNAMIC_FIELD, $this->commands)) {
            $command = $this->commands[static::COMMAND_REPLACE_DYNAMIC_FIELD];
        } else {
            $command = new ReplaceDynamicField();
        }

        $command->addFields($fields);

        return $this->add(static::COMMAND_REPLACE_DYNAMIC_FIELD, $command);
    }

    /**
     * @param FieldInterface $field
     * @return $this
     */
    public function replaceDynamicField(FieldInterface $field)
    {
        return $this->replaceDynamicFields(array($field));
    }

    /**
     * @param FieldInterface[] $fields
     * @return $this
     */
    public function deleteDynamicFields(array $fields)
    {
        if (array_key_exists(static::COMMAND_DELETE_DYNAMIC_FIELD, $this->commands)) {
            $command = $this->commands[static::COMMAND_DELETE_DYNAMIC_FIELD];
        } else {
            $command = new DeleteDynamicField();
        }
        $command->addFields($fields);

        return $this->add(static::COMMAND_DELETE_DYNAMIC_FIELD, $command);
    }

    /**
     * @param FieldInterface $field
     * @return $this
     */
    public function deleteDynamicField(FieldInterface $field)
    {
        return $this->deleteDynamicFields(array($field));
    }

    /**
     * @param CopyField[] $fields
     * @return $this
     */
    public function addCopyFields(array $fields)
    {
        if (array_key_exists(static::COMMAND_ADD_COPY_FIELD, $this->commands)) {
            $command = $this->commands[static::COMMAND_ADD_COPY_FIELD];
        } else {
            $command = new AddCopyField();
        }

        $command->addFields($fields);

        return $this->add(static::COMMAND_ADD_COPY_FIELD, $command);
    }

    /**
     * @param CopyField $field
     * @return $this
     */
    public function addCopyField(CopyField $field)
    {
        return $this->addCopyFields(array($field));
    }

    /**
     * @param CopyField[] $fields
     * @return $this
     */
    public function deleteCopyFields(array $fields)
    {
        if (array_key_exists(static::COMMAND_DELETE_COPY_FIELD, $this->commands)) {
            $command = $this->commands[static::COMMAND_DELETE_COPY_FIELD];
        } else {
            $command = new DeleteCopyField;
        }
        $command->addFields($fields);

        return $this->add(static::COMMAND_DELETE_COPY_FIELD, $command);
    }

    /**
     * @param CopyField $field
     * @return $this
     */
    public function deleteCopyField(CopyField $field)
    {
        return $this->deleteCopyFields(array($field));
    }

    /**
     * @param FieldTypeInterface[] $fieldTypes
     * @return Query
     */
    public function addFieldTypes(array $fieldTypes)
    {
        if (array_key_exists(static::COMMAND_ADD_FIELD_TYPE, $this->commands)) {
            $command = $this->commands[static::COMMAND_ADD_FIELD_TYPE];
        } else {
            $command = new AddFieldType();
        }
        $command->addFieldTypes($fieldTypes);

        return $this->add(static::COMMAND_ADD_FIELD_TYPE, $command);
    }

    /**
     * @param FieldTypeInterface $fieldType
     * @return Query
     */
    public function addFieldType(FieldTypeInterface $fieldType)
    {
        return $this->addFieldTypes(array($fieldType));
    }

    /**
     * @param FieldTypeInterface[] $fieldTypes
     * @return Query
     */
    public function replaceFieldTypes(array $fieldTypes)
    {
        if (array_key_exists(static::COMMAND_REPLACE_FIELD_TYPE, $this->commands)) {
            $command = $this->commands[static::COMMAND_REPLACE_FIELD_TYPE];
        } else {
            $command = new ReplaceFieldType();
        }
        $command->addFieldTypes($fieldTypes);

        return $this->add(static::COMMAND_REPLACE_FIELD_TYPE, $command);
    }

    /**
     * @param FieldTypeInterface $fieldType
     * @return Query
     */
    public function replaceFieldType(FieldTypeInterface $fieldType)
    {
        return $this->replaceFieldTypes(array($fieldType));
    }

    /**
     * @param FieldTypeInterface[] $fieldTypes
     * @return Query
     */
    public function deleteFieldTypes(array $fieldTypes)
    {
        if (array_key_exists(static::COMMAND_DELETE_FIELD_TYPE, $this->commands)) {
            $command = $this->commands[static::COMMAND_DELETE_FIELD_TYPE];
        } else {
            $command = new DeleteFieldType();
        }
        $command->addFieldTypes($fieldTypes);

        return $this->add(static::COMMAND_DELETE_FIELD_TYPE, $command);
    }

    /**
     * @param FieldTypeInterface $fieldType
     * @return Query
     */
    public function deleteFieldType(FieldTypeInterface $fieldType)
    {
        return $this->deleteFieldTypes(array($fieldType));
    }

    /**
     * Get type for this query
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_SCHEMA;
    }

    /**
     * Get the requestbuilder class for this query
     *
     * @return RequestBuilderInterface
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder;
    }

    /**
     * Get the response parser class for this query
     *
     * @return ResponseParserInterface
     */
    public function getResponseParser()
    {
        return new ResponseParser;
    }


    /**
     * @return array
     */
    public function castAsArray()
    {
        return array_map(
            function (Command $command) {
                return $command->castAsArray();
            },
            $this->getCommands()
        );
    }
}
