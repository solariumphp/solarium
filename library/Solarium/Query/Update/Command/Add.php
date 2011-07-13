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
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Query
 */

/**
 * Update query add command
 *
 * For details about the Solr options see:
 * @link http://wiki.apache.org/solr/UpdateXmlMessages#add.2BAC8-update
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Update_Command_Add extends Solarium_Query_Update_Command
{

    /**
     * Documents to add
     *
     * @var array
     */
    protected $_documents = array();

    /**
     * Get command type
     * 
     * @return string
     */
    public function getType()
    {
        return Solarium_Query_Update::COMMAND_ADD;
    }

    /**
     * Add a single document
     *
     * @param object $document
     * @return Solarium_Query_Update_Command_Add Provides fluent interface
     */
    public function addDocument($document)
    {
        $this->_documents[] = $document;

        return $this;
    }

    /**
     * Add multiple documents
     *
     * @param array|Traversable $documents
     * @return Solarium_Query_Update_Command_Add Provides fluent interface
     */
    public function addDocuments($documents)
    {
        //if we don't have documents so far, accept arrays or Traversable objects as-is
        if (empty($this->_documents)) {
            $this->_documents = $documents;
            return $this;
        }

        //if something Traversable is passed in, and there are existing documents, convert all to arrays before merging
        if ($documents instanceof Traversable) {
            $documents = iterator_to_array($documents);
        }
        if ($this->_documents instanceof Traversable) {
            $this->_documents = array_merge(iterator_to_array($this->_documents), $documents);
        } else {
            $this->_documents = array_merge($this->_documents, $documents);
        }

        return $this;
    }

    /**
     * Get all documents
     * 
     * @return array
     */
    public function getDocuments()
    {
        return $this->_documents;
    }

    /**
     * Set overwrite option
     *
     * @param boolean $overwrite
     * @return Solarium_Query_Update_Command_Add Provides fluent interface
     */
    public function setOverwrite($overwrite)
    {
        return $this->_setOption('overwrite', $overwrite);
    }

    /**
     * Get overwrite option
     *
     * @return boolean
     */
    public function getOverwrite()
    {
        return $this->getOption('overwrite');
    }

    /**
     * Get commitWithin option
     *
     * @param boolean $commitWithin
     * @return Solarium_Query_Update_Command_Add Provides fluent interface
     */
    public function setCommitWithin($commitWithin)
    {
        return $this->_setOption('commitwithin', $commitWithin);
    }

    /**
     * Set commitWithin option
     * 
     * @return boolean
     */
    public function getCommitWithin()
    {
        return $this->getOption('commitwithin');
    }
}