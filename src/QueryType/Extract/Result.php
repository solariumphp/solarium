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

        // Solr versions before 8.6 used the file name as key within the response. Solr 8.6 uses the general key 'file'.
        // To be compatible to any version we re-add the specific or the general key.
        $filename = basename($this->query->getOption('file'));
        if (!isset($this->data[$filename]) && isset($this->data['file'])) {
            $this->data[$filename] = $this->data['file'];
        } elseif (!isset($this->data['file']) && isset($this->data[$filename])) {
            $this->data['file'] = $this->data[$filename];
        }

        return $this->data;
    }
}
