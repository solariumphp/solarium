<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Analytics\Facet;

/**
 * Configurable Init Trait.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
trait ConfigurableInitTrait
{
    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        foreach ($this->options as $name => $option) {
            $setter = sprintf('set%s', ucfirst($name));

            if (true === \is_callable([$this, $setter])) {
                $this->$setter($option);
            }
        }
    }
}
