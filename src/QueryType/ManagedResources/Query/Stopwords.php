<?php

namespace Solarium\QueryType\ManagedResources\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\ManagedResources\Query\Command\Config;
use Solarium\QueryType\ManagedResources\Query\Command\Delete;
use Solarium\QueryType\ManagedResources\Query\Command\Exists;
use Solarium\QueryType\ManagedResources\Query\Command\Remove;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Add;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Create;
use Solarium\QueryType\ManagedResources\ResponseParser\Stopwords as ResponseParser;
use Solarium\QueryType\ManagedResources\Result\Stopwords\WordSet;

class Stopwords extends AbstractQuery
{
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
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }
}
