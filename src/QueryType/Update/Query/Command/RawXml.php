<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query raw XML command.
 *
 * @see https://solr.apache.org/guide/uploading-data-with-index-handlers.html#xml-formatted-index-updates
 */
class RawXml extends AbstractCommand
{
    /**
     * XML command strings to send.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Get command type.
     *
     * @return string
     */
    public function getType(): string
    {
        return UpdateQuery::COMMAND_RAWXML;
    }

    /**
     * Add a single XML command string to the command.
     *
     * @param string $command
     *
     * @return self Provides fluent interface
     */
    public function addCommand(string $command): self
    {
        $this->commands[] = $command;

        return $this;
    }

    /**
     * Add multiple XML command strings to the command.
     *
     * @param array $commands
     *
     * @return self Provides fluent interface
     */
    public function addCommands(array $commands): self
    {
        $this->commands = array_merge($this->commands, $commands);

        return $this;
    }

    /**
     * Add an XML command string from a file to the command.
     *
     * @param string $filename
     *
     * @throws RuntimeException
     *
     * @return self Provides fluent interface
     */
    public function addCommandFromFile(string $filename): self
    {
        $command = @file_get_contents($filename);

        if (false === $command) {
            throw new RuntimeException('Update query raw XML file path/url invalid or not available');
        }

        // discard UTF-8 Byte Order Mark
        if (0 === strpos($command, pack('CCC', 0xEF, 0xBB, 0xBF))) {
            $command = substr($command, 3);
        }

        // discard XML declaration
        if (0 === strpos($command, '<?xml')) {
            $command = substr($command, strpos($command, '?>') + 2);
        }

        $this->addCommand($command);

        return $this;
    }

    /**
     * Get all XML command strings of this command.
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Clear all XML command strings.
     *
     * @return self Provides fluent interface
     */
    public function clear(): self
    {
        $this->commands = [];

        return $this;
    }

    /**
     * Build XML command strings based on options.
     */
    protected function init(): void
    {
        $command = $this->getOption('command');
        if (null !== $command) {
            if (\is_array($command)) {
                $this->addCommands($command);
            } else {
                $this->addCommand($command);
            }
        }
    }
}
