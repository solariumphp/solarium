<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Status4xxNoExceptionInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resource as RequestBuilder;
use Solarium\QueryType\ManagedResources\Result\Command as CommandResult;
use Solarium\QueryType\ManagedResources\Result\Stopwords\WordSet;
use Solarium\QueryType\ManagedResources\Result\Synonyms\SynonymMappings;

/**
 * Query base class.
 */
abstract class AbstractQuery extends BaseQuery implements Status4xxNoExceptionInterface
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
     * @var array<self::COMMAND_*, class-string<AbstractCommand>>
     */
    protected array $commandTypes;

    /**
     * Name of the managed resource to query.
     */
    protected string $name = '';

    /**
     * Name of the child resource to query.
     */
    protected ?string $term = null;

    /**
     * Default result class if no command is set.
     *
     * @var class-string<WordSet|SynonymMappings>
     */
    protected string $defaultResultClass;

    /**
     * Command.
     */
    protected ?AbstractCommand $command = null;

    /**
     * Get query type.
     *
     * @return Client::QUERY_MANAGED_*
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
     * @return ResponseParserInterface
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
     * @return self Provides fluent interface
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the name of the child resource to query.
     *
     * @return string|null
     */
    public function getTerm(): ?string
    {
        return $this->term;
    }

    /**
     * Set the name of the child resource to query.
     *
     * @param string $term
     *
     * @return self Provides fluent interface
     */
    public function setTerm(string $term): self
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Remove the name of the child resource. This reverts to querying the entire managed resource.
     *
     * @return self Provides fluent interface
     */
    public function removeTerm(): self
    {
        $this->term = null;

        return $this;
    }

    /**
     * Create a command instance.
     *
     * @param string     $type
     * @param array|null $options
     *
     * @throws InvalidArgumentException
     *
     * @return AbstractCommand
     */
    public function createCommand(string $type, ?array $options = null): AbstractCommand
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
        $this->options['resultclass'] = CommandResult::class;

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
        $this->options['resultclass'] = $this->defaultResultClass;

        return $this;
    }

    /**
     * Create an init args instance.
     *
     * @param array|null $initArgs
     *
     * @return InitArgsInterface
     */
    abstract public function createInitArgs(?array $initArgs = null): InitArgsInterface;

    /**
     * Percent-encode names and terms twice as a workaround for SOLR-6853?
     *
     * @return bool
     */
    public function getUseDoubleEncoding(): bool
    {
        return $this->getOption('useDoubleEncoding') ?? false;
    }

    /**
     * Percent-encode names and terms twice as a workaround for SOLR-6853?
     *
     * Solr versions prior to 10 required reserved characters to be doubly
     * percent-encoded. Set this to true if your Solr version is affected by
     * {@link https://issues.apache.org/jira/browse/SOLR-6853 SOLR-6853}.
     *
     * @param bool $useDoubleEncoding
     *
     * @return self Provides fluent interface
     */
    public function setUseDoubleEncoding(bool $useDoubleEncoding): self
    {
        $this->setOption('useDoubleEncoding', $useDoubleEncoding);

        return $this;
    }
}
