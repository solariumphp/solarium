<?php

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
    public function __construct($analysis)
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
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get raw text value.
     *
     * This values is not available in all cases
     *
     * @return string
     */
    public function getRawText()
    {
        return $this->rawText;
    }

    /**
     * Get start value.
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get end value.
     *
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Get postion value.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Get position history value.
     *
     * @return array
     */
    public function getPositionHistory()
    {
        if (is_array($this->positionHistory)) {
            return $this->positionHistory;
        }

        return [];
    }

    /**
     * Get type value.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get match value.
     *
     * @return bool
     */
    public function getMatch()
    {
        return $this->match;
    }
}
