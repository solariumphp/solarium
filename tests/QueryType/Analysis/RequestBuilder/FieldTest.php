<?php

namespace Solarium\Tests\QueryType\Analysis\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Query\Field as FieldQuery;
use Solarium\QueryType\Analysis\RequestBuilder\Field as FieldBuilder;

class FieldTest extends TestCase
{
    /**
     * @var FieldQuery
     */
    protected $query;

    /**
     * @var FieldBuilder
     */
    protected $builder;

    public function setUp(): void
    {
        $this->query = new FieldQuery();
        $this->builder = new FieldBuilder();
    }

    public function testBuild()
    {
        $fieldValue = 'myvalue';
        $fieldName = 'myfield';
        $fieldType = 'text';

        $this->query->setFieldValue($fieldValue)
                     ->setFieldName($fieldName)
                     ->setFieldType($fieldType);

        $request = $this->builder->build($this->query);

        $this->assertSame($fieldValue, $request->getParam('analysis.fieldvalue'));
        $this->assertSame($fieldName, $request->getParam('analysis.fieldname'));
        $this->assertSame($fieldType, $request->getParam('analysis.fieldtype'));
    }
}
