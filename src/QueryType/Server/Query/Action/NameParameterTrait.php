<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Query\Action;

/**
 * Handling the name parameter which is essential for various actions.
 */
trait NameParameterTrait
{
    /**
     * Set the name of the collection. This parameter is required.
     *
     * @param string $collection
     *
     * @return self
     */
    public function setName(string $collection): self
    {
        $this->setOption('name', $collection);

        return $this;
    }

    /**
     * Get the name of the collection.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getOption('name');
    }
}
