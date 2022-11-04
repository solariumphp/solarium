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
use Solarium\Exception\DomainException;
use Solarium\Exception\InvalidArgumentException;
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
     * Set the request format for the updates.
     *
     * Use one of the UpdateQuery::REQUEST_FORMAT_* constants as value.
     *
     * @param string $requestFormat
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function setRequestFormat(string $requestFormat): self
    {
        $this->updateQuery->setRequestFormat($requestFormat);

        return $this;
    }

    /**
     * Get the request format for the updates.
     *
     * @return string|null
     */
    public function getRequestFormat(): ?string
    {
        return $this->updateQuery->getRequestFormat();
    }

    /**
     * Set buffer size option.
     *
     * @param int $size
     *
     * @throws DomainException if trying to set a buffer size less than 1
     *
     * @return self Provides fluent interface
     */
    public function setBufferSize(int $size): self
    {
        if (\count($this->buffer) >= $size) {
            $this->flush();
        }

        if (1 > $size) {
            throw new DomainException('Buffer size must be at least 1.');
        }

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
        // keep request format
        $requestFormat = $this->updateQuery->getRequestFormat();
        $this->updateQuery = $this->client->createUpdate();
        $this->updateQuery->setRequestFormat($requestFormat);

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
     * Initialize options.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'buffersize':
                    $this->setBufferSize($value);
                    break;
            }
        }
    }

    /**
     * Plugin init function.
     *
     * This is an extension point for plugin implementations.
     * Will be called as soon as $this->client and options have been set.
     */
    protected function initPluginType(): void
    {
        $this->updateQuery = $this->client->createUpdate();

        if (null !== $requestFormat = $this->getOption('requestformat')) {
            $this->updateQuery->setRequestFormat($requestFormat);
        }
    }
}
