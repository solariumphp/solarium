<?php

namespace Solarium\Tests\QueryType\Analysis\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Query\Field as FieldQuery;
use Solarium\QueryType\Analysis\RequestBuilder\Field as FieldBuilder;

class FieldTest extends TestCase
{
    protected FieldQuery $query;

    protected FieldBuilder $builder;

    public function setUp(): void
    {
        $this->query = new FieldQuery();
        $this->builder = new FieldBuilder();
    }

    public function testBuild(): void
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
