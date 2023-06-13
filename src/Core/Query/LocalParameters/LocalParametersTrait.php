<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query\LocalParameters;

use Solarium\Exception\OutOfBoundsException;

/**
 * Local Parameters Trait.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
trait LocalParametersTrait
{
    /**
     * @var \Solarium\Core\Query\LocalParameters\LocalParameters
     */
    private $localParameters;

    /**
     * @return \Solarium\Core\Query\LocalParameters\LocalParameters
     */
    public function getLocalParameters(): LocalParameters
    {
        if (null === $this->localParameters) {
            $this->localParameters = new LocalParameters();
        }

        return $this->localParameters;
    }

    /**
     * @throws OutOfBoundsException
     */
    protected function initLocalParameters(): void
    {
        $this->localParameters = new LocalParameters();

        foreach ($this->options as $name => $value) {
            switch ($name) {
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_EXCLUDE]:
                    if (!\is_array($value)) {
                        $value = preg_split('/(?<!\\\\),/', $value);
                    }

                    $this->getLocalParameters()->addExcludes($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_KEY]:
                    $this->getLocalParameters()->setKey((string) $value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_TAG]:
                    if (!\is_array($value)) {
                        $value = preg_split('/(?<!\\\\),/', $value);
                    }

                    $this->getLocalParameters()->addTags($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_RANGE]:
                    if (!\is_array($value)) {
                        $value = preg_split('/(?<!\\\\),/', $value);
                    }

                    $this->getLocalParameters()->addRanges($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_STAT]:
                    if (!\is_array($value)) {
                        $value = preg_split('/(?<!\\\\),/', $value);
                    }

                    $this->getLocalParameters()->addStats($value);
                    unset($this->options[$name]);
                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_TERM]:
                    if (!\is_array($value)) {
                        $value = preg_split('/(?<!\\\\),/', $value);
                    }

                    $this->getLocalParameters()->addTerms($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_TYPE]:
                    $this->getLocalParameters()->setType($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_QUERY]:
                    $this->getLocalParameters()->setQuery($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_QUERY_FIELD]:
                    $this->getLocalParameters()->setQueryField($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_DEFAULT_FIELD]:
                    $this->getLocalParameters()->setDefaultField($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_MAX]:
                    $this->getLocalParameters()->setMax($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_MEAN]:
                    $this->getLocalParameters()->setMean($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_MIN]:
                    $this->getLocalParameters()->setMin($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_VALUE]:
                    $this->getLocalParameters()->setLocalValue($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_CACHE]:
                    $this->getLocalParameters()->setCache($value);
                    unset($this->options[$name]);

                    break;
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_COST]:
                    $this->getLocalParameters()->setCost($value);
                    unset($this->options[$name]);

                    break;
            }
        }
    }
}
