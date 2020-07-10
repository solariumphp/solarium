<?php

declare(strict_types=1);

namespace Solarium\Manager\Command;

use ArrayIterator;

/**
 * Command Collection.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class CommandCollection implements \ArrayAccess, \IteratorAggregate, \JsonSerializable
{
    /**
     * @var array
     */
    private $commands;

    /**
     * @param array $commands
     */
    public function __construct(array $commands = [])
    {
        $this->commands = $commands;
    }

    /**
     * @param string $command
     *
     * @return \JsonSerializable[]|\JsonSerializable|null
     */
    public function get($command): ?array
    {
        return $this->commands[$command] ?? null;
    }

    /**
     * @param string                                $command
     * @param \JsonSerializable[]|\JsonSerializable $value
     */
    public function set(string $command, $value)
    {
        $this->commands[$command] = $value;
    }

    /**
     * @param string            $command
     * @param \JsonSerializable $value
     */
    public function add(string $command, \JsonSerializable $value)
    {
        $this->commands[$command][] = $value;
    }

    /**
     * @param mixed $command
     *
     * @return \JsonSerializable[]|\JsonSerializable|null
     */
    public function remove($command)
    {
        if (false === $this->containsCommand($command)) {
            return null;
        }

        $removed = $this->commands[$command];

        unset($this->commands[$command]);

        return $removed;
    }

    /**
     * @param mixed $command
     *
     * @return bool
     */
    public function containsCommand($command): bool
    {
        return isset($this->commands[$command]) || \array_key_exists($command, $this->commands);
    }

    /**
     * Clear.
     */
    public function clear(): void
    {
        $this->commands = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->commands);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsCommand($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter($this->commands);
    }
}
