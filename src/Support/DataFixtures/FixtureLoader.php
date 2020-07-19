<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Support\DataFixtures;

use ReflectionException;

/**
 * This class is just a convenience wrapper around the fixture loading process.
 *
 * @author Baldur Rensch <brensch@gmail.com>
 */
class FixtureLoader
{
    /**
     * @var Loader
     */
    private $loader;

    /**
     * @var Purger
     */
    private $purger;

    /**
     * @var Executor
     */
    private $executor;

    /**
     * @param Loader   $loader
     * @param Purger   $purger
     * @param Executor $executor
     */
    public function __construct(Loader $loader, Purger $purger, Executor $executor)
    {
        $this->loader = $loader;
        $this->purger = $purger;
        $this->executor = $executor;
    }

    /**
     * @param string $dir
     * @param bool   $append
     *
     * @throws ReflectionException
     *
     * @return self
     */
    public function loadFixturesFromDir(string $dir, bool $append = true): self
    {
        if (!$append) {
            $this->purger->purge();
        }

        $this->loader->loadFromDirectory($dir);

        $this->executor->execute($this->loader->getFixtures());

        return $this;
    }
}
