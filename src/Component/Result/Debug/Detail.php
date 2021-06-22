<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Debug;

/**
 * Select component debug detail result.
 */
class Detail implements \ArrayAccess
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
     * @param \Solarium\Component\Result\Debug\Detail[]|array $subDetails
     *
     * @return self
     */
    public function setSubDetails(array $subDetails): self
    {
        $this->subDetails = [];
        foreach ($subDetails as $subDetail) {
            $this->subDetails[] = new Detail($subDetail['match'], $subDetail['value'], $subDetail['description']);
        }

        return $this;
    }

    /**
     * @return \Solarium\Component\Result\Debug\Detail[]|null
     */
    public function getSubDetails(): ?array
    {
        return $this->subDetails;
    }

    public function offsetExists($offset)
    {
        return in_array($offset, ['match', 'value', 'description']);
    }

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function offsetSet($offset, $value)
    {
        // Details are immutable.
    }

    public function offsetUnset($offset)
    {
        // Details are immutable.
    }

    public function __toString()
    {
        $string = '';
        if ($this->match) {
            $string .= sprintf('%f', $this->value).' <= '.$this->description.PHP_EOL;
            foreach ($this->getSubDetails() as $subDetail) {
                if ($subDetail->getMatch()) {
                    $string .= '... '.$subDetail;
                }
            }
        }

       return $string;
    }
}
