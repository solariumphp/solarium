<?php

declare(strict_types=1);

namespace Solarium\Core\Query\LocalParameters;

use Solarium\QueryType\Select\Query\FilterQuery;

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
     * Convenience method for backwards compatibility.
     *
     * @param string $exclude
     *
     * @throws \Solarium\Exception\OutOfBoundsException
     *
     * @return $this
     */
    public function addExclude(string $exclude): self
    {
        @trigger_error('The "addExclude" method is deprecated in Solarium 5 and will be removed in Solarium 6. Use "->getLocalParameters()->setExclude($exclude)" instead', E_USER_DEPRECATED);

        $this->getLocalParameters()->setExclude($exclude);

        return $this;
    }

    /**
     * Convenience method for backwards compatibility.
     *
     * @param array $excludes
     *
     * @throws \Solarium\Exception\OutOfBoundsException
     *
     * @return $this
     */
    public function setExcludes(array $excludes): self
    {
        @trigger_error('The "setExcludes" method is deprecated in Solarium 5 and will be removed in Solarium 6. Use "->getLocalParameters()->clearExcludes()->addExcludes($excludes)" instead', E_USER_DEPRECATED);

        $this->getLocalParameters()->clearExcludes()->addExcludes($excludes);

        return $this;
    }

    /**
     * Convenience method for backwards compatibility.
     *
     * @param array $excludes
     *
     * @throws \Solarium\Exception\OutOfBoundsException
     *
     * @return $this
     */
    public function addExcludes(array $excludes): self
    {
        @trigger_error('The "addExcludes" method is deprecated in Solarium 5 and will be removed in Solarium 6. Use "->getLocalParameters()->addExcludes($excludes)" instead', E_USER_DEPRECATED);

        $this->getLocalParameters()->addExcludes($excludes);

        return $this;
    }

    /**
     * Convenience method for backwards compatibility.
     *
     * @param string $exclude
     *
     * @throws \Solarium\Exception\OutOfBoundsException
     *
     * @return $this
     */
    public function removeExclude(string $exclude): self
    {
        @trigger_error('The "removeExclude" method is deprecated in Solarium 5 and will be removed in Solarium 6. Use "->getLocalParameters()->removeExclude($exclude)" instead', E_USER_DEPRECATED);

        $this->getLocalParameters()->removeExclude($exclude);

        return $this;
    }

    /**
     * Convenience method for backwards compatibility.
     *
     * @throws \Solarium\Exception\OutOfBoundsException
     *
     * @return $this
     */
    public function clearExcludes(): self
    {
        @trigger_error('The "clearExcludes" method is deprecated in Solarium 5 and will be removed in Solarium 6. Use "->getLocalParameters()->clearExcludes()" instead', E_USER_DEPRECATED);

        $this->getLocalParameters()->clearExcludes();

        return $this;
    }

    /**
     * Convenience method for backwards compatibility.
     *
     * @throws \Solarium\Exception\OutOfBoundsException
     *
     * @return array
     */
    public function getExcludes(): array
    {
        @trigger_error('The "getExcludes" method is deprecated in Solarium 5 and will be removed in Solarium 6. Use "->getLocalParameters()->getExcludes()" instead', E_USER_DEPRECATED);

        return $this->getLocalParameters()->getExcludes();
    }

    /**
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    protected function initLocalParameters(): void
    {
        $this->localParameters = new LocalParameters();

        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'exclude':
                    @trigger_error('setting local parameter using the "exclude" option is deprecated in Solarium 5 and will be removed in Solarium 6. Use "local_exclude" instead', E_USER_DEPRECATED);
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_EXCLUDE]:
                    if (!\is_array($value)) {
                        $value = explode(',', $value);
                    }

                    $this->getLocalParameters()->addExcludes($value);
                    unset($this->options[$name]);
                    break;

                case 'key':
                    if ($this instanceof FilterQuery) {
                        break;
                    }
                    @trigger_error('setting local parameter using the "key" option is deprecated in Solarium 5 and will be removed in Solarium 6. Use "local_key" instead', E_USER_DEPRECATED);
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_KEY]:
                    $this->getLocalParameters()->setKey($value);
                    unset($this->options[$name]);
                    break;

                case 'tag':
                    @trigger_error('setting local parameter using the "tag" option is deprecated in Solarium 5 and will be removed in Solarium 6. Use "local_tag" instead', E_USER_DEPRECATED);
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_TAG]:
                    if (!\is_array($value)) {
                        $value = explode(',', $value);
                    }

                    $this->getLocalParameters()->addTags($value);
                    unset($this->options[$name]);
                    break;

                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_RANGE]:
                    if (!\is_array($value)) {
                        $value = explode(',', $value);
                    }

                    $this->getLocalParameters()->addRanges($value);
                    unset($this->options[$name]);
                    break;

                case 'stats':
                    @trigger_error('setting local parameter using the "stats" option is deprecated in Solarium 5 and will be removed in Solarium 6. Use "local_stats" instead', E_USER_DEPRECATED);
                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_STAT]:
                    if (!\is_array($value)) {
                        $value = explode(',', $value);
                    }

                    $this->getLocalParameters()->addStats($value);
                    unset($this->options[$name]);
                    break;

                case LocalParameter::PARAMETER_MAP[LocalParameter::TYPE_TERM]:
                    if (!\is_array($value)) {
                        $value = explode(',', $value);
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
