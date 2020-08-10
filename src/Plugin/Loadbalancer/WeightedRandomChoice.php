<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\Loadbalancer;

use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\RuntimeException;

/**
 * Weighted random choice class.
 *
 * For use in the loadbalancer plugin
 */
class WeightedRandomChoice
{
    /**
     * Total weight of all choices.
     *
     * @var int
     */
    protected $totalWeight = 0;

    /**
     * Choices total lookup array.
     *
     * @var array
     */
    protected $lookup = [];

    /**
     * Values lookup array.
     *
     * @var array
     */
    protected $values = [];

    /**
     * Constructor.
     *
     * @param array $choices
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $choices)
    {
        $i = 0;
        foreach ($choices as $key => $weight) {
            if ($weight <= 0) {
                throw new InvalidArgumentException('Weight must be greater than zero');
            }

            $this->totalWeight += $weight;
            $this->lookup[$i] = $this->totalWeight;
            $this->values[$i] = $key;

            ++$i;
        }
    }

    /**
     * Get a (weighted) random entry.
     *
     * @param array $excludes Keys to exclude
     *
     * @throws RuntimeException
     *
     * @return string
     */
    public function getRandom(array $excludes = []): string
    {
        if (\count($excludes) === \count($this->values)) {
            throw new RuntimeException('No more server entries available');
        }

        // continue until a non-excluded value is found
        $result = null;
        while (1) {
            $result = $this->values[$this->getKey()];
            if (!\in_array($result, $excludes, true)) {
                break;
            }
        }

        return $result;
    }

    /**
     * Get a (weighted) random entry key.
     *
     * @return int
     */
    protected function getKey(): int
    {
        // We don't need cryptographically secure values, therefore mt_rand is the better choice over random_int().
        /** @noinspection RandomApiMigrationInspection */
        $random = mt_rand(1, $this->totalWeight);
        $high = \count($this->lookup) - 1;
        $low = 0;

        while ($low < $high) {
            $probe = (int) (($high + $low) / 2);
            if ($this->lookup[$probe] < $random) {
                $low = $probe + 1;
            } elseif ($this->lookup[$probe] > $random) {
                $high = $probe - 1;
            } else {
                return $probe;
            }
        }

        if ($this->lookup[$low] >= $random) {
            return $low;
        }

        return $low + 1;
    }
}
