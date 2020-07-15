<?php

namespace Solarium\QueryType\ManagedResources\Query;

use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resource as RequestBuilder;

abstract class AbstractQuery extends BaseQuery
{
    /**
     * Command add.
     */
    const COMMAND_ADD = 'add';

    /**
     * Command config.
     */
    const COMMAND_CONFIG = 'config';

    /**
     * Command create.
     */
    const COMMAND_CREATE = 'create';

    /**
     * Command delete.
     */
    const COMMAND_DELETE = 'delete';

    /**
     * Command exists.
     */
    const COMMAND_EXISTS = 'exists';

    /**
     * Command remove.
     */
    const COMMAND_REMOVE = 'remove';

    /**
     * Command types.
     *
     * @var array
     */
    protected $commandTypes;

    /**
     * Name of the managed resource.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Command.
     *
     * @var AbstractCommand
     */
    protected $command;

    /**
     * Get query type.
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Get the request builder class for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get the response parser class for this query.
     *
     * @return ResponseParser
     */
    abstract public function getResponseParser(): ResponseParserInterface;

    /**
     * Get the name of the managed resource.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name of the managed resource.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Create a command instance.
     *
     * @param string $type
     * @param mixed  $options
     *
     * @throws InvalidArgumentException
     *
     * @return AbstractCommand
     */
    public function createCommand($type, $options = null): AbstractCommand
    {
        $type = strtolower($type);

        if (!isset($this->commandTypes[$type])) {
            throw new InvalidArgumentException(sprintf('Managed resource command type unknown: %s', $type));
        }

        $class = $this->commandTypes[$type];

        return new $class($options);
    }

    /**
     * Get command for this query.
     *
     * @return AbstractCommand|null
     */
    public function getCommand(): ?AbstractCommand
    {
        return $this->command;
    }

    /**
     * Set a command to the query.
     *
     * @param AbstractCommand $command
     *
     * @return self Provides fluent interface
     */
    public function setCommand(AbstractCommand $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Remove command from the query.
     *
     * @return self Provides fluent interface
     */
    public function removeCommand(): self
    {
        $this->command = null;

        return $this;
    }

    /**
     * Create an init args instance.
     *
     * @return InitArgsInterface
     */
    abstract public function createInitArgs(): InitArgsInterface;
}
