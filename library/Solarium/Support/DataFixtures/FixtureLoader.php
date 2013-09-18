<?php

namespace Solarium\Support\DataFixtures;

/**
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
     * @param Loader $loader
     * @param Purger $purger
     */
    public function __construct(Loader $loader, Purger $purger)
    {
        $this->loader = $loader;
        $this->purger = $purger;
    }

    /**
     * @param      $dir
     * @param bool $append
     */
    public function loadFixturesFromDir($dir, $append = true)
    {
        if (!$append) {
            $this->purger->purge();
        }

        $this->loader->loadFromDirectory($dir);
    }
}
