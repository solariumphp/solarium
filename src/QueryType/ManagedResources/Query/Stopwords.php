<?php

namespace Solarium\QueryType\ManagedResources\Query;

use InvalidArgumentException;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Add;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Delete;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Exists;
use Solarium\QueryType\ManagedResources\RequestBuilder\Stopwords as RequestBuilder;
use Solarium\QueryType\ManagedResources\ResponseParser\Stopwords as ResponseParser;
use Solarium\QueryType\ManagedResources\Result\Stopwords\WordSet;

class Stopwords extends BaseQuery
{
    /**
     * Stopwords command add.
     */
    const COMMAND_ADD = 'add';

    /**
     * Stopwords command delete.
     */
    const COMMAND_DELETE = 'delete';

    /**
     * Stopwords command delete.
     */
    const COMMAND_EXISTS = 'exists';

    /**
     * Name of the stopwords resource.
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
     * Stopwords command types.
     *
     * @var array
     */
    protected $commandTypes = [
        self::COMMAND_ADD => Add::class,
        self::COMMAND_DELETE => Delete::class,
        self::COMMAND_EXISTS => Exists::class,
    ];

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'schema/analysis/stopwords/',
        'resultclass' => WordSet::class,
        'omitheader' => true,
    ];

    /**
     * Get query type.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_MANAGED_STOPWORDS;
    }

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
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Get the name of the stopwords resource.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name of the stopwords resource.
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
            throw new InvalidArgumentException('Stopwords commandtype unknown: '.$type);
        }

        $class = $this->commandTypes[$type];

        return new $class($options);
    }

    /**
     * Get command for this stopwords query.
     *
     * @return AbstractCommand|null
     */
    public function getCommand(): ?AbstractCommand
    {
        return $this->command;
    }

    /**
     * Set a command to the stopwords query.
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
     * Remove command.
     *
     * @return self Provides fluent interface
     */
    public function removeCommand(): self
    {
        $this->command = null;

        return $this;
    }
}
