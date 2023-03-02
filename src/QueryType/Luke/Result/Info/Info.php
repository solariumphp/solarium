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
    /**
     * @var array
     */
    protected $key;

    /**
     * @var string
     */
    protected $note;

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
     * @return self
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
     * @return self
     */
    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
