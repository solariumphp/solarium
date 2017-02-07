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
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Schema;

use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\QueryType\Schema\Query\Field\CopyField;
use Solarium\QueryType\Schema\Query\Field\Field;
use Solarium\QueryType\Schema\Query\FieldType\FieldType;

/**
 * Schema result
 * @author Beno!t POLASZEK
 */
class Result extends BaseResult
{
    /**
     * Status code returned by Solr
     *
     * @var int
     */
    protected $status;

    /**
     * Solr index queryTime
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @var int
     */
    protected $queryTime;

    protected $errors = array();

    protected $name;

    protected $version;

    protected $uniqueKey;

    protected $defaultSearchField;

    /**
     * @var FieldType[]
     */
    protected $fieldTypes = array();

    /**
     * @var Field[]
     */
    protected $fields = array();

    /**
     * @var Field[]
     */
    protected $dynamicFields = array();

    /**
     * @var CopyField[]
     */
    protected $copyFields = array();

    /**
     * Get Solr status code
     *
     * This is not the HTTP status code! The normal value for success is 0.
     *
     * @return int
     */
    public function getStatus()
    {
        $this->parseResponse();

        return $this->status;
    }

    /**
     * Get Solr query time
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @return int
     */
    public function getQueryTime()
    {
        $this->parseResponse();

        return $this->queryTime;
    }

    /**
     * Get Solr query time
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @return int
     */
    public function getName()
    {
        $this->parseResponse();

        return $this->name;
    }

    /**
     * @return Query\FieldType\FieldType[]
     */
    public function getFieldTypes()
    {
        $this->parseResponse();

        return $this->fieldTypes;
    }

    /**
     * @return Query\Field\Field[]
     */
    public function getFields()
    {
        $this->parseResponse();

        return $this->fields;
    }

    /**
     * @return Query\Field\Field[]
     */
    public function getDynamicFields()
    {
        $this->parseResponse();

        return $this->dynamicFields;
    }

    /**
     * @return Query\Field\CopyField[]
     */
    public function getCopyFields()
    {
        $this->parseResponse();

        return $this->copyFields;
    }

    /**
     * @param $fieldType
     * @return null|FieldType
     */
    public function getFieldType($fieldType) {
        return array_key_exists($fieldType, $this->getFieldTypes()) ? $this->fieldTypes[$fieldType] : null;
    }

    /**
     * @param $fieldName
     * @return null|Field
     */
    public function getField($fieldName) {
        return array_key_exists($fieldName, $this->getFields()) ? $this->fields[$fieldName] : null;
    }

    /**
     * @param $fieldName
     * @return null|Field
     */
    public function getDynamicField($fieldName) {
        return array_key_exists($fieldName, $this->getDynamicFields()) ? $this->dynamicFields[$fieldName] : null;
    }

    /**
     * @param $source
     * @return null|CopyField
     */
    public function getCopyField($source) {
        return array_key_exists($source, $this->getCopyFields()) ? $this->copyFields[$source] : null;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        $this->parseResponse();

        return $this->version;
    }

    /**
     * @return mixed
     */
    public function getUniqueKey()
    {
        $this->parseResponse();

        return $this->uniqueKey;
    }

    /**
     * @return mixed
     */
    public function getDefaultSearchField()
    {
        $this->parseResponse();

        return $this->defaultSearchField;
    }

    /**
     * Return errors (if any)
     * @return array
     */
    public function getErrors()
    {
        $this->parseResponse();

        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return (bool) $this->getErrors();
    }

}
