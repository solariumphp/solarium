<?php

namespace Solarium\Support\DataFixtures;

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
        $this->fixtures = array();
    }

    /**
     * @param FixtureInterface $fixture
     */
    public function addFixture(FixtureInterface $fixture)
    {
        $this->fixtures[] = $fixture;
    }

    /**
     * @return FixtureInterface[]
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }

    /**
     * @param string $dir
     *
     * @throws \InvalidArgumentException
     */
    public function loadFromDirectory($dir)
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not exist', $dir));
        }

        $includedFiles = array();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        /** @var $file \DirectoryIterator */
        foreach ($iterator as $file) {
            if (($fileName = $file->getBasename($this->fileExtension)) == $file->getBasename()) {
                continue;
            }
            $sourceFile = realpath($file->getPathName());
            /** @noinspection PhpIncludeInspection */
            require_once $sourceFile;
            $includedFiles[] = $sourceFile;
        }
        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $reflClass = new \ReflectionClass($className);
            $sourceFile = $reflClass->getFileName();

            if (in_array($sourceFile, $includedFiles)) {
                $fixture = new $className();

                $this->addFixture($fixture);
            }
        }
    }
}
