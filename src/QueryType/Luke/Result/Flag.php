<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result;

/**
 * Immutable field flag.
 */
class Flag
{
    /**
     * @var string
     */
    protected $abbreviation;

    /**
     * @var string
     */
    protected $display;

    /**
     * Constructor.
     *
     * @param string $abbreviation
     * @param string $display
     */
    public function __construct(string $abbreviation, string $display)
    {
        $this->abbreviation = $abbreviation;
        $this->display = $display;
    }

    /**
     * @return string
     */
    public function getAbbreviation(): string
    {
        return $this->abbreviation;
    }

    /**
     * @return string
     */
    public function getDisplay(): string
    {
        return $this->display;
    }

    public function __toString(): string
    {
        return $this->display;
    }
}
