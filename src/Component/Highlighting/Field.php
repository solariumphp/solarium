<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Highlighting;

use Solarium\Core\Configurable;

/**
 * Highlighting per-field settings.
 *
 * @see https://solr.apache.org/guide/highlighting.html
 */
class Field extends Configurable implements HighlightingInterface
{
    use HighlightingTrait;

    /**
     * Get name option.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getOption('name');
    }

    /**
     * Set name option.
     *
     * @param string $name
     *
     * @return self Provides fluent interface
     */
    public function setName(string $name): self
    {
        $this->setOption('name', $name);

        return $this;
    }
}
