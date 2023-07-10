<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Configsets\Query\Action;

use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\Configsets\Result\ConfigsetsResult;
use Solarium\QueryType\Server\Query\Action\AbstractAction;
use Solarium\QueryType\Server\Query\Action\NameParameterTrait;

/**
 * Class Upload.
 *
 * @see https://solr.apache.org/guide/configsets-api.html#configsets-upload
 */
class Upload extends AbstractAction
{
    use NameParameterTrait;

    /**
     * The File to be uploaded, a zipped configset or a non-zipped single file of a configset.
     *
     * @var string
     */
    protected $file;

    /**
     * Returns the action type of the Configsets API action.
     *
     * @return string
     */
    public function getType(): string
    {
        return ConfigsetsQuery::ACTION_UPLOAD;
    }

    /**
     * The file to be uploaded, either a configset, which is sent as a zipped file, ora single, non-zipped file.
     *
     * @param string $file
     *
     * @return self Provides fluent interface
     *
     * @throws InvalidArgumentException
     */
    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Returns the file.
     *
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * If set to true, Solr will overwrite an existing configset with the same name (if false, the request will fail).
     * If filePath is provided, then this option specifies whether the specified file within the configset should be
     * overwritten if it already exists. Default is false when using the v1 API, but true when using the v2 API.
     *
     * @param bool $overwrite
     *
     * @return self Provides fluent interface
     */
    public function setOverwrite(bool $overwrite): self
    {
        $this->setOption('overwrite', $overwrite);

        return $this;
    }

    /**
     * Returns the overwrite setting.
     *
     * @return bool|null
     */
    public function getOverwrite(): ?bool
    {
        return $this->getOption('overwrite');
    }

    /**
     * When overwriting an existing configset (overwrite=true), this parameter tells Solr to delete the files in
     * ZooKeeper that existed in the old configset but not in the one being uploaded. Default is false.
     * This parameter cannot be set to true when filePath is used.
     *
     * @param bool $cleanup
     *
     * @return self Provides fluent interface
     */
    public function setCleanup(bool $cleanup): self
    {
        $this->setOption('cleanup', $cleanup);

        return $this;
    }

    /**
     * Returns the cleanup setting.
     *
     * @return bool|null
     */
    public function getCleanup(): ?bool
    {
        return $this->getOption('cleanup');
    }

    /**
     * This parameter allows the uploading of a single, non-zipped file to the given path under the configset in
     * ZooKeeper. This functionality respects the overwrite parameter, so a request will fail if the given file path
     * already exists in the configset and overwrite is set to false. The cleanup parameter cannot be set to true when
     * filePath is used.
     *
     * @param string $filePath
     *
     * @return self Provides fluent interface
     */
    public function setFilePath(string $filePath): self
    {
        $this->setOption('filePath', $filePath);

        return $this;
    }

    /**
     * Returns the filePath.
     *
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return $this->getOption('filePath');
    }

    /**
     * Returns the namespace and class of the result class for the action.
     *
     * @return string
     */
    public function getResultClass(): string
    {
        return ConfigsetsResult::class;
    }
}
