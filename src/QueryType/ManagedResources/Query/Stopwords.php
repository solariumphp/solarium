<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\ManagedResources\Query\Command\Config;
use Solarium\QueryType\ManagedResources\Query\Command\Delete;
use Solarium\QueryType\ManagedResources\Query\Command\Exists;
use Solarium\QueryType\ManagedResources\Query\Command\Remove;
use Solarium\QueryType\ManagedResources\Query\Command\Stopwords\Add;
use Solarium\QueryType\ManagedResources\Query\Command\Stopwords\Create;
use Solarium\QueryType\ManagedResources\Query\Stopwords\InitArgs;
use Solarium\QueryType\ManagedResources\ResponseParser\Command as CommandResponseParser;
use Solarium\QueryType\ManagedResources\ResponseParser\Exists as ExistsResponseParser;
use Solarium\QueryType\ManagedResources\ResponseParser\Remove as RemoveResponseParser;
use Solarium\QueryType\ManagedResources\ResponseParser\Stopword as StopwordResponseParser;
use Solarium\QueryType\ManagedResources\ResponseParser\Stopwords as StopwordsResponseParser;
use Solarium\QueryType\ManagedResources\Result\Stopwords\WordSet;

/**
 * Stopwords.
 */
class Stopwords extends AbstractQuery
{
    /**
     * Default result class if no command is set.
     *
     * @var string
     */
    protected $defaultResultClass = WordSet::class;

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
     * Command types.
     *
     * @var array
     */
    protected $commandTypes = [
        self::COMMAND_ADD => Add::class,
        self::COMMAND_CONFIG => Config::class,
        self::COMMAND_CREATE => Create::class,
        self::COMMAND_DELETE => Delete::class,
        self::COMMAND_EXISTS => Exists::class,
        self::COMMAND_REMOVE => Remove::class,
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
     * Get the response parser class for this query.
     *
     * @return \Solarium\Core\Query\ResponseParserInterface
     */
    public function getResponseParser(): ResponseParserInterface
    {
        if (null === $this->command) {
            if (null === $this->term) {
                $parser = new StopwordsResponseParser();
            } else {
                $parser = new StopwordResponseParser();
            }
        } elseif (self::COMMAND_EXISTS === $this->command->getType()) {
            $parser = new ExistsResponseParser();
        } elseif (self::COMMAND_REMOVE === $this->command->getType()) {
            $parser = new RemoveResponseParser();
        } else {
            $parser = new CommandResponseParser();
        }

        return $parser;
    }

    /**
     * Create an init args instance.
     *
     * @param array|null $initArgs
     *
     * @return \Solarium\QueryType\ManagedResources\Query\Stopwords\InitArgs
     */
    public function createInitArgs(?array $initArgs = null): InitArgsInterface
    {
        return new InitArgs($initArgs);
    }
}
