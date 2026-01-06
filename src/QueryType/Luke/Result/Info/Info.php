<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Info;

/**
 * Retrieved info information.
 */
class Info
{
    protected array $key;

    protected string $note;

    /**
     * @return array
     */
    public function getKey(): array
    {
        return $this->key;
    }

    /**
     * @param array $key
     *
     * @return self Provides fluent interface
     */
    public function setKey(array $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     *
     * @return self Provides fluent interface
     */
    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
