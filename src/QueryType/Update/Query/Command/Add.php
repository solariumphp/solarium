<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @see http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Document\DocumentInterface;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query add command.
 *
 * @see http://wiki.apache.org/solr/UpdateXmlMessages#add.2BAC8-update
 */
class Add extends AbstractCommand
{
    /**
     * Documents to add.
     *
     * @var \Solarium\QueryType\Update\Query\Document\DocumentInterface[]
     */
    protected $documents = [];

    /**
     * Get command type.
     *
     * @return string
     */
    public function getType()
    {
        return UpdateQuery::COMMAND_ADD;
    }

    /**
     * Add a single document.
     *
     *
     * @param DocumentInterface $document
     *
     * @throws RuntimeException
     *
     * @return self Provides fluent interface
     */
    public function addDocument(DocumentInterface $document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Add multiple documents.
     *
     * @param array|\Traversable $documents
     *
     * @throws RuntimeException If any of the given documents does not implement DocumentInterface
     *
     * @return self Provides fluent interface
     */
    public function addDocuments($documents)
    {
        //only check documents for type if in an array (iterating a Traversable may do unnecessary work)
        if (is_array($documents)) {
            foreach ($documents as $document) {
                if (!($document instanceof DocumentInterface)) {
                    throw new RuntimeException('Documents must implement DocumentInterface.');
                }
            }
        }

        //if we don't have documents so far, accept arrays or Traversable objects as-is
        if (empty($this->documents)) {
            $this->documents = $documents;

            return $this;
        }

        //if something Traversable is passed in, and there are existing documents, convert all to arrays before merging
        if ($documents instanceof \Traversable) {
            $documents = iterator_to_array($documents);
        }
        if ($this->documents instanceof \Traversable) {
            $this->documents = array_merge(iterator_to_array($this->documents), $documents);
        } else {
            $this->documents = array_merge($this->documents, $documents);
        }

        return $this;
    }

    /**
     * Get all documents.
     *
     * @return DocumentInterface[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Set overwrite option.
     *
     * @param bool $overwrite
     *
     * @return self Provides fluent interface
     */
    public function setOverwrite($overwrite)
    {
        return $this->setOption('overwrite', $overwrite);
    }

    /**
     * Get overwrite option.
     *
     * @return bool
     */
    public function getOverwrite()
    {
        return $this->getOption('overwrite');
    }

    /**
     * Get commitWithin option.
     *
     * @param bool $commitWithin
     *
     * @return self Provides fluent interface
     */
    public function setCommitWithin($commitWithin)
    {
        return $this->setOption('commitwithin', $commitWithin);
    }

    /**
     * Set commitWithin option.
     *
     * @return bool
     */
    public function getCommitWithin()
    {
        return $this->getOption('commitwithin');
    }
}
