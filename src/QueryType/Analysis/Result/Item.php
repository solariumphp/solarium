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
     */
    protected string $text;

    /**
     * RawText string.
     */
    protected ?string $rawText = null;

    /**
     * Start.
     */
    protected ?int $start;

    /**
     * End.
     */
    protected ?int $end;

    /**
     * Position.
     */
    protected ?int $position;

    /**
     * Position history.
     */
    protected ?array $positionHistory;

    /**
     * Type.
     */
    protected ?string $type;

    /**
     * Match.
     */
    protected bool $match = false;

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
     * @return int|null
     */
    public function getStart(): ?int
    {
        return $this->start;
    }

    /**
     * Get end value.
     *
     * @return int|null
     */
    public function getEnd(): ?int
    {
        return $this->end;
    }

    /**
     * Get postion value.
     *
     * @return int|null
     */
    public function getPosition(): ?int
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
        return $this->positionHistory ?? [];
    }

    /**
     * Get type value.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Get match value.
     *
     * @return bool
     */
    public function getMatch(): bool
    {
        return $this->match;
    }
}
