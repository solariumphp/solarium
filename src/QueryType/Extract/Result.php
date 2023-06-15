<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Extract;

use Solarium\Exception\RuntimeException;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Update\Result as UpdateResult;

/**
 * An extract result is similar to an update result, but we do want to return a query specific result class instead of
 * an update query result class.
 */
class Result extends UpdateResult
{
    /**
     * Retrieved file contents for extractOnly=true.
     *
     * @var string
     */
    protected $file;

    /**
     * Retrieved file metadata for extractOnly=true.
     *
     * @var array
     */
    protected $fileMetadata;

    /**
     * Returns the retrieved file contents.
     *
     * Will return null if extractOnly wasn't set to true.
     *
     * @return string|null
     */
    public function getFile(): ?string
    {
        $this->parseResponse();

        return $this->file;
    }

    /**
     * Returns the retrieved file metadata.
     *
     * Will return null if extractOnly wasn't set to true.
     *
     * @return array|null
     */
    public function getFileMetadata(): ?array
    {
        $this->parseResponse();

        return $this->fileMetadata;
    }

    /**
     * Get Solr response data.
     *
     * Includes a lazy loading mechanism: JSON body data is decoded on first use and then saved for reuse.
     *
     * @return array
     *
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function getData(): array
    {
        parent::getData();

        if (true === $this->query->getOption('extractonly')) {
            // Solr versions before 8.6 used the file name as key within the response. Solr 8.6 uses the general key 'file'.
            // To be compatible to any version we reference the file data by both the specific and the general key.
            $filename = $this->query->getResourceName();

            if (!isset($this->data[$filename]) && isset($this->data['file'])) {
                $this->data[$filename] = &$this->data['file'];
            } elseif (!isset($this->data['file']) && isset($this->data[$filename])) {
                $this->data['file'] = &$this->data[$filename];
            }

            if (!isset($this->data[$filename.'_metadata']) && isset($this->data['file_metadata'])) {
                $this->data[$filename.'_metadata'] = &$this->data['file_metadata'];
            } elseif (!isset($this->data['file_metadata']) && isset($this->data[$filename.'_metadata'])) {
                $this->data['file_metadata'] = &$this->data[$filename.'_metadata'];
            }
        }

        return $this->data;
    }
}
