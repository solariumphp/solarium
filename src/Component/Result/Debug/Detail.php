<?php

namespace Solarium\Component\Result\Debug;

/**
 * Select component debug detail result.
 */
class Detail
{
    /**
     * Value.
     *
     * @var float
     */
    protected $value;

    /**
     * Match.
     *
     * @var bool
     */
    protected $match;

    /**
     * Description.
     *
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $subDetails;

    /**
     * Constructor.
     *
     * @param bool   $match
     * @param float  $value
     * @param string $description
     */
    public function __construct($match, $value, $description)
    {
        $this->match = $match;
        $this->value = $value;
        $this->description = $description;
    }

    /**
     * Get match status.
     *
     * @return bool
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Get match value (score).
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param array $subDetails
     */
    public function setSubDetails($subDetails)
    {
        $this->subDetails = $subDetails;
    }

    /**
     * @return array
     */
    public function getSubDetails()
    {
        return $this->subDetails;
    }
}
