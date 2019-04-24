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
    public function __construct(bool $match, float $value, string $description)
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
    public function getMatch(): bool
    {
        return $this->match;
    }

    /**
     * Get match value (score).
     *
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param array $subDetails
     *
     * @return self
     */
    public function setSubDetails(array $subDetails): self
    {
        $this->subDetails = $subDetails;
        return $this;
    }

    /**
     * @return array
     */
    public function getSubDetails(): array
    {
        return $this->subDetails;
    }
}
