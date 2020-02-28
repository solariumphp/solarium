<?php

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query raw XML command.
 *
 * @see http://wiki.apache.org/solr/UpdateXmlMessages
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
     * Get all XML command strings of this command.
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Build XML command strings based on options.
     */
    protected function init()
    {
        $command = $this->getOption('command');
        if (null !== $command) {
            if (is_array($command)) {
                $this->addCommands($command);
            } else {
                $this->addCommand($command);
            }
        }
    }
}
