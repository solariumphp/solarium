<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Update\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\DocumentInterface;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Command\AbstractCommand;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Query\Command\Commit as CommitCommand;
use Solarium\QueryType\Update\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\Update\Query\Command\Optimize as OptimizeCommand;
use Solarium\QueryType\Update\Query\Command\RawXml as RawXmlCommand;
use Solarium\QueryType\Update\Query\Command\Rollback as RollbackCommand;
use Solarium\QueryType\Update\RequestBuilder\Json as JsonRequestBuilder;
use Solarium\QueryType\Update\RequestBuilder\Xml as XmlRequestBuilder;
use Solarium\QueryType\Update\ResponseParser;
use Solarium\QueryType\Update\Result;

/**
 * Update query.
 *
 * Can be used to send multiple update commands to Solr, e.g. add, delete,
 * rollback, commit, optimize.
 * Multiple commands of any type can be combined into a single update query.
 */
class Query extends BaseQuery
{
    /**
     * Update command add.
     */
    const COMMAND_ADD = 'add';

    /**
     * Update command commit.
     */
    const COMMAND_COMMIT = 'commit';

    /**
     * Update command delete.
     */
    const COMMAND_DELETE = 'delete';

    /**
     * Update command optimize.
     */
    const COMMAND_OPTIMIZE = 'optimize';

    /**
     * Update command raw XML.
     */
    const COMMAND_RAWXML = 'rawxml';

    /**
     * Update command rollback.
     */
    const COMMAND_ROLLBACK = 'rollback';

    /**
     * JSON request format.
     */
    const REQUEST_FORMAT_JSON = 'json';

    /**
     * XML request format.
     */
    const REQUEST_FORMAT_XML = 'xml';

    /**
     * Update command types.
     *
     * @var array
     */
    protected $commandTypes = [
        self::COMMAND_ADD => AddCommand::class,
        self::COMMAND_COMMIT => CommitCommand::class,
        self::COMMAND_DELETE => DeleteCommand::class,
        self::COMMAND_OPTIMIZE => OptimizeCommand::class,
        self::COMMAND_RAWXML => RawXmlCommand::class,
        self::COMMAND_ROLLBACK => RollbackCommand::class,
    ];

    /**
     * Request formats.
     *
     * @var array
     */
    protected $requestFormats = [
        self::REQUEST_FORMAT_JSON => JsonRequestBuilder::class,
        self::REQUEST_FORMAT_XML => XmlRequestBuilder::class,
    ];

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'update',
        'requestformat' => self::REQUEST_FORMAT_JSON,
        'resultclass' => Result::class,
        'documentclass' => Document::class,
        'omitheader' => false,
    ];

    /**
     * Array of commands.
     *
     * The commands will be executed in the order of this array, this can be
     * important in some cases. For instance a rollback.
     *
     * @var AbstractCommand[]
     */
    protected $commands = [];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_UPDATE;
    }

    /**
     * Set the request format for this query.
     *
     * Use one of the REQUEST_FORMAT_* constants as value.
     *
     * @param string $requestFormat
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function setRequestFormat(string $requestFormat): self
    {
        $requestFormat = strtolower($requestFormat);

        if (!isset($this->requestFormats[$requestFormat])) {
            throw new InvalidArgumentException(sprintf('Unsupported request format: %s', $requestFormat));
        }

        $this->setOption('requestformat', $requestFormat);

        return $this;
    }

    /**
     * Get the request format for this query.
     *
     * @return string
     */
    public function getRequestFormat(): string
    {
        return $this->getOption('requestformat');
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilderInterface
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        $class = $this->requestFormats[$this->options['requestformat']];

        return new $class();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Create a command instance.
     *
     * @param string $type
     * @param array  $options
     *
     * @throws InvalidArgumentException
     *
     * @return AbstractCommand
     */
    public function createCommand(string $type, array $options = null): AbstractCommand
    {
        $type = strtolower($type);

        if (!isset($this->commandTypes[$type])) {
            throw new InvalidArgumentException(sprintf('Update commandtype unknown: %s', $type));
        }

        $class = $this->commandTypes[$type];

        return new $class($options);
    }

    /**
     * Get all commands for this update query.
     *
     * @return AbstractCommand[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Add a command to this update query.
     *
     * The command must be an instance of one of the
     * Solarium\QueryType\Update\Query\Command\* classes.
     *
     * @param string|null     $key
     * @param AbstractCommand $command
     *
     * @return self Provides fluent interface
     */
    public function add(?string $key, AbstractCommand $command): self
    {
        if ($key) {
            $this->commands[$key] = $command;
        } else {
            $this->commands[] = $command;
        }

        return $this;
    }

    /**
     * Remove a command.
     *
     * You can remove a command by passing its key or by passing the command instance.
     *
     * @param string|AbstractCommand $keyOrCommand
     *
     * @return self Provides fluent interface
     */
    public function remove($keyOrCommand): self
    {
        if (\is_object($keyOrCommand)) {
            foreach ($this->commands as $key => $instance) {
                if ($instance === $keyOrCommand) {
                    unset($this->commands[$key]);
                    break;
                }
            }
        } else {
            if (isset($this->commands[$keyOrCommand])) {
                unset($this->commands[$keyOrCommand]);
            }
        }

        return $this;
    }

    /**
     * Convenience method to add a rollback command.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @return self Provides fluent interface
     */
    public function addRollback(): self
    {
        return $this->add(null, new RollbackCommand());
    }

    /**
     * Convenience method to add a delete query command.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param string $query
     * @param array  $bind  Bind values for placeholders in the query string
     *
     * @return self Provides fluent interface
     */
    public function addDeleteQuery(string $query, array $bind = null): self
    {
        if (null !== $bind) {
            $query = $this->getHelper()->assemble($query, $bind);
        }

        $delete = new DeleteCommand();
        $delete->addQuery($query);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a multi delete query command.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param array $queries
     *
     * @return self Provides fluent interface
     */
    public function addDeleteQueries(array $queries): self
    {
        $delete = new DeleteCommand();
        $delete->addQueries($queries);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a delete by ID command.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param int|string $id
     *
     * @return self Provides fluent interface
     */
    public function addDeleteById($id): self
    {
        $delete = new DeleteCommand();
        $delete->addId($id);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a delete by IDs command.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param array $ids
     *
     * @return self Provides fluent interface
     */
    public function addDeleteByIds(array $ids): self
    {
        $delete = new DeleteCommand();
        $delete->addIds($ids);

        return $this->add(null, $delete);
    }

    /**
     * Convenience method to add a 'add document' command.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param DocumentInterface $document
     * @param bool              $overwrite
     * @param int               $commitWithin
     *
     * @return self Provides fluent interface
     */
    public function addDocument(DocumentInterface $document, bool $overwrite = null, int $commitWithin = null): self
    {
        return $this->addDocuments([$document], $overwrite, $commitWithin);
    }

    /**
     * Convenience method to add a 'add documents' command.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param array $documents
     * @param bool  $overwrite
     * @param int   $commitWithin
     *
     * @return self Provides fluent interface
     */
    public function addDocuments(array $documents, bool $overwrite = null, int $commitWithin = null): self
    {
        $add = new AddCommand();

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
     * Convenience method to add a commit command.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param bool $softCommit
     * @param bool $waitSearcher
     * @param bool $expungeDeletes
     *
     * @return self Provides fluent interface
     */
    public function addCommit(bool $softCommit = null, bool $waitSearcher = null, bool $expungeDeletes = null): self
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
     * Convenience method to add an optimize command.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param bool $softCommit
     * @param bool $waitSearcher
     * @param int  $maxSegments
     *
     * @return self Provides fluent interface
     */
    public function addOptimize(bool $softCommit = null, bool $waitSearcher = null, int $maxSegments = null): self
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
     * Convenience method for adding a raw XML command.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param string $command
     *
     * @return self Provides fluent interface
     */
    public function addRawXmlCommand(string $command): self
    {
        $raw = new RawXmlCommand();

        $raw->addCommand($command);

        return $this->add(null, $raw);
    }

    /**
     * Convenience method for adding raw XML commands.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param array $commands
     *
     * @return self Provides fluent interface
     */
    public function addRawXmlCommands(array $commands): self
    {
        $raw = new RawXmlCommand();

        $raw->addCommands($commands);

        return $this->add(null, $raw);
    }

    /**
     * Convenience method for adding a raw XML command from a file.
     *
     * If you need more control, like choosing a key for the command you need to
     * create you own command instance and use the add method.
     *
     * @param string $filename
     *
     * @return self Provides fluent interface
     */
    public function addRawXmlFile(string $filename): self
    {
        $raw = new RawXmlCommand();

        $raw->addCommandFromFile($filename);

        return $this->add(null, $raw);
    }

    /**
     * Set a custom document class for use in the createDocument method.
     *
     * This class should implement the document interface
     *
     * @param string $value classname
     *
     * @return self Provides fluent interface
     */
    public function setDocumentClass(string $value): self
    {
        $this->setOption('documentclass', $value);

        return $this;
    }

    /**
     * Get the current documentclass option.
     *
     * The value is a classname, not an instance.
     *
     * @return string
     */
    public function getDocumentClass(): string
    {
        return $this->getOption('documentclass');
    }

    /**
     * Create a document object instance.
     *
     * You can optionally directly supply the fields and boosts
     * to get a ready-made document instance for direct use in an add command.
     *
     * @since 2.1.0
     *
     * @param array $fields
     * @param array $boosts
     * @param array $modifiers
     *
     * @return DocumentInterface
     */
    public function createDocument(array $fields = [], array $boosts = [], array $modifiers = []): DocumentInterface
    {
        $class = $this->getDocumentClass();

        return new $class($fields, $boosts, $modifiers);
    }

    /**
     * Initialize options.
     *
     * {@internal Several options need some extra checks or setup work,
     *            for these options the setters are called.}
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function init(): void
    {
        if (isset($this->options['requestformat'])) {
            $this->setRequestFormat($this->options['requestformat']);
        }

        if (isset($this->options['command'])) {
            foreach ($this->options['command'] as $key => $value) {
                $type = $value['type'];

                if (self::COMMAND_ADD === $type) {
                    throw new RuntimeException('Adding documents is not supported in configuration, use the API for this');
                }

                $this->add($key, $this->createCommand($type, $value));
            }
        }
    }
}
