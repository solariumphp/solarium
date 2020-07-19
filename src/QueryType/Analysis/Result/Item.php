<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Analysis\Result;

/**
 * Analysis item.
 */
class Item
{
    /**
     * Text string.
     *
     * @var string
     */
    protected $text;

    /**
     * RawText string.
     *
     * @var string
     */
    protected $rawText;

    /**
     * Start.
     *
     * @var int
     */
    protected $start;

    /**
     * End.
     *
     * @var int
     */
    protected $end;

    /**
     * Position.
     *
     * @var int
     */
    protected $position;

    /**
     * Position history.
     *
     * @var array
     */
    protected $positionHistory;

    /**
     * Type.
     *
     * @var string
     */
    protected $type;

    /**
     * Match.
     *
     * @var bool
     */
    protected $match = false;

    /**
     * Constructor.
     *
     * @param array $analysis
     */
    public function __construct(array $analysis)
    {
        $this->text = $analysis['text'];
        $this->start = $analysis['start'];
        $this->end = $analysis['end'];
        $this->position = $analysis['position'];
        $this->positionHistory = $analysis['positionHistory'];
        $this->type = $analysis['type'];

        if (isset($analysis['raw_text'])) {
            $this->rawText = $analysis['raw_text'];
        }

        if (isset($analysis['match'])) {
            $this->match = $analysis['match'];
        }
    }

    /**
     * Get text value.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Get raw text value.
     *
     * This values is not available in all cases
     *
     * @return string|null
     */
    public function getRawText(): ?string
    {
        return $this->rawText;
    }

    /**
     * Get start value.
     *
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * Get end value.
     *
     * @return int
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * Get postion value.
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Get position history value.
     *
     * @return array
     */
    public function getPositionHistory(): array
    {
        if (\is_array($this->positionHistory)) {
            return $this->positionHistory;
        }

        return [];
    }

    /**
     * Get type value.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get match value.
     *
     * @return bool|null
     */
    public function getMatch(): ?bool
    {
        return $this->match;
    }
}
