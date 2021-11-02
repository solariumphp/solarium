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
     * @var \Solarium\Component\Result\Debug\Detail[]
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
            if ($subDetail instanceof Detail) {
                $this->subDetails[] = $subDetail;
            } else {
                $this->subDetails[] = new Detail($subDetail['match'], $subDetail['value'], $subDetail['description']);
            }
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

    public function offsetExists($offset): bool
    {
        return \in_array($offset, ['match', 'value', 'description']);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function offsetSet($offset, $value): void
    {
        // Details are immutable.
    }

    public function offsetUnset($offset): void
    {
        // Details are immutable.
    }

    public function debugDump(int $depth = 0): string
    {
        $string = '';
        if ($this->match) {
            $string .= str_repeat('... ', $depth).sprintf('%f', $this->value).' <= '.$this->description.PHP_EOL;
            foreach ($this->getSubDetails() ?? [] as $subDetail) {
                if ($subDetail->getMatch()) {
                    $string .= $subDetail->debugDump($depth + 1);
                }
            }
        }

        return $string;
    }

    public function __toString()
    {
        return $this->debugDump();
    }
}
