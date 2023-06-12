<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result;

use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\QueryType\Luke\Result\Doc\DocInfo;
use Solarium\QueryType\Luke\Result\Fields\FieldInfo;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Info\Info;
use Solarium\QueryType\Luke\Result\Schema\Schema;

/**
 * Luke query result.
 */
class Result extends BaseResult
{
    /**
     * @var array
     */
    protected $index;

    /**
     * @var array
     */
    protected $schema;

    /**
     * @var array
     */
    protected $doc;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $info;

    /**
     * @var Index
     */
    protected $indexResult;

    /**
     * @var Schema|null
     */
    protected $schemaResult;

    /**
     * @var DocInfo|null;
     */
    protected $docResult;

    /**
     * @var FieldInfo[]|null;
     */
    protected $fieldsResult;

    /**
     * @var Info|null
     */
    protected $infoResult;

    /**
     * @return Index
     */
    public function getIndex(): Index
    {
        $this->parseResponse();

        return $this->indexResult;
    }

    /**
     * @return Schema|null
     */
    public function getSchema(): ?Schema
    {
        $this->parseResponse();

        return $this->schemaResult;
    }

    /**
     * @return DocInfo|null
     */
    public function getDoc(): ?DocInfo
    {
        $this->parseResponse();

        return $this->docResult;
    }

    /**
     * @return FieldInfo[]|null
     */
    public function getFields(): ?array
    {
        $this->parseResponse();

        return $this->fieldsResult;
    }

    /**
     * @return Info|null
     */
    public function getInfo(): ?Info
    {
        $this->parseResponse();

        return $this->infoResult;
    }
}
