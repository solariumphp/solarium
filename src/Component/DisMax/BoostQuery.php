<?php

namespace Solarium\Component\DisMax;

use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Core\Configurable;
use Solarium\Core\Query\Helper;

/**
 * Boost query.
 */
class BoostQuery extends Configurable implements QueryInterface
{
    use QueryTrait;

    /**
     * Get key value.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getOption('key');
    }

    /**
     * Set key value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setKey($value)
    {
        return $this->setOption('key', $value);
    }

    /**
     * Returns a query helper.
     *
     * @return \Solarium\Core\Query\Helper
     */
    public function getHelper()
    {
        return new Helper();
    }

    /**
     * Initialize options.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'key':
                    $this->setKey($value);
                    break;
                case 'query':
                    $this->setQuery($value);
                    break;
            }
        }
    }
}
