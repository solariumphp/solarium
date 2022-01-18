<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\AbstractBufferedUpdate;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\QueryType\Update\Result as UpdateResult;

/**
 * Buffered update plugin base class.
 */
abstract class AbstractBufferedUpdate extends AbstractPlugin
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'buffersize' => 100,
    ];

    /**
     * Update query instance.
     *
     * @var UpdateQuery
     */
    protected $updateQuery;

    /**
     * Buffer.
     *
     * @var array
     */
    protected $buffer = [];

    /**
     * Set the endpoint for the updates.
     *
     * @param Endpoint $endpoint The endpoint to set
     *
     * @return self Provides fluent interface
     */
    public function setEndpoint(Endpoint $endpoint): self
    {
        $this->setOption('endpoint', $endpoint);

        return $this;
    }

    /**
     * Return the endpoint.
     *
     * @return Endpoint|null
     */
    public function getEndpoint(): ?Endpoint
    {
        return $this->getOption('endpoint');
    }

    /**
     * Set buffer size option.
     *
     * @param int $size
     *
     * @return self Provides fluent interface
     */
    public function setBufferSize(int $size): self
    {
        $this->setOption('buffersize', $size);

        return $this;
    }

    /**
     * Get buffer size option value.
     *
     * @return int|null
     */
    public function getBufferSize(): ?int
    {
        return $this->getOption('buffersize');
    }

    /**
     * Get the current buffer.
     *
     * Any previously flushed contents will not be included!
     *
     * @return array
     */
    public function getBuffer(): array
    {
        return $this->buffer;
    }

    /**
     * Clear the buffer.
     *
     * Any remaining contents in the buffer will not be flushed to Solr!
     *
     * @return self Provides fluent interface
     */
    public function clear(): self
    {
        $this->updateQuery = $this->client->createUpdate();
        $this->buffer = [];

        return $this;
    }

    /**
     * Flush the buffer to Solr.
     *
     * @return UpdateResult|false
     */
    abstract public function flush();

    /**
     * Commit changes.
     *
     * Any remaining contents in the buffer will also be flushed to Solr.
     *
     * @return UpdateResult
     */
    abstract public function commit(): UpdateResult;

    /**
     * Plugin init function.
     *
     * This is an extension point for plugin implementations.
     * Will be called as soon as $this->client and options have been set.
     */
    protected function initPluginType(): void
    {
        $this->updateQuery = $this->client->createUpdate();
    }
}
