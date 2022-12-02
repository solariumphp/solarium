<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Support\DataFixtures;

use ReflectionException;
use Solarium\Exception\InvalidArgumentException;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 */
class Loader
{
    /**
     * @var FixtureInterface[]
     */
    private $fixtures;

    /**
     * The file extension of fixture files.
     *
     * @var string
     */
    private $fileExtension = '.php';

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->fixtures = [];
    }

    /**
     * @param FixtureInterface $fixture
     *
     * @return self
     */
    public function addFixture(FixtureInterface $fixture): self
    {
        $this->fixtures[] = $fixture;

        return $this;
    }

    /**
     * @return FixtureInterface[]
     */
    public function getFixtures(): array
    {
        return $this->fixtures;
    }

    /**
     * @param string $dir
     *
     * @throws InvalidArgumentException
     * @throws ReflectionException
     *
     * @return self
     */
    public function loadFromDirectory(string $dir): self
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException(sprintf('"%s" does not exist', $dir));
        }

        $includedFiles = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        /** @var \DirectoryIterator $file */
        foreach ($iterator as $file) {
            if ($file->getBasename($this->fileExtension) === $file->getBasename()) {
                continue;
            }
            $sourceFile = realpath($file->getPathname());
            /** @noinspection PhpIncludeInspection */
            require_once $sourceFile;
            $includedFiles[] = $sourceFile;
        }
        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $reflClass = new \ReflectionClass($className);
            $sourceFile = $reflClass->getFileName();

            if (\in_array($sourceFile, $includedFiles, true)) {
                $fixture = new $className();

                $this->addFixture($fixture);
            }
        }

        return $this;
    }
}
