<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Field;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\FlagList;
use Solarium\QueryType\Luke\Result\Schema\Field\AbstractField;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicBasedField;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicField;
use Solarium\QueryType\Luke\Result\Schema\Field\Field;
use Solarium\QueryType\Luke\Result\Schema\Field\WildcardField;
use Solarium\QueryType\Luke\Result\Schema\Type\Type;

abstract class AbstractFieldTestCase extends TestCase
{
    /**
     * @var AbstractField
     */
    protected $field;

    abstract public function testGetName();

    public function testSetAndGetType()
    {
        $type = new Type('my_type');
        $this->assertSame($this->field, $this->field->setType($type));
        $this->assertSame($type, $this->field->getType());
    }

    public function testSetAndGetFlags()
    {
        $flags = new FlagList('-', []);
        $this->assertSame($this->field, $this->field->setFlags($flags));
        $this->assertSame($flags, $this->field->getFlags());
    }

    public function testSetAndGetAndIsRequired()
    {
        $this->assertSame($this->field, $this->field->setRequired(true));
        $this->assertTrue($this->field->getRequired());
        $this->assertTrue($this->field->isRequired());

        $this->assertSame($this->field, $this->field->setRequired(false));
        $this->assertFalse($this->field->getRequired());
        $this->assertFalse($this->field->isRequired());

        $this->assertSame($this->field, $this->field->setRequired(null));
        $this->assertNull($this->field->getRequired());
        $this->assertFalse($this->field->isRequired());
    }

    public function testSetAndGetDefault()
    {
        $this->assertSame($this->field, $this->field->setDefault('0.0'));
        $this->assertSame('0.0', $this->field->getDefault());

        $this->assertSame($this->field, $this->field->setDefault(null));
        $this->assertNull($this->field->getDefault());
    }

    public function testSetAndGetAndIsUniqueKey()
    {
        $this->assertSame($this->field, $this->field->setUniqueKey(true));
        $this->assertTrue($this->field->getUniqueKey());
        $this->assertTrue($this->field->isUniqueKey());

        $this->assertSame($this->field, $this->field->setUniqueKey(false));
        $this->assertFalse($this->field->getUniqueKey());
        $this->assertFalse($this->field->isUniqueKey());

        $this->assertSame($this->field, $this->field->setUniqueKey(null));
        $this->assertNull($this->field->getUniqueKey());
        $this->assertFalse($this->field->isUniqueKey());
    }

    public function testSetAndGetPositionIncrementGap()
    {
        $this->assertSame($this->field, $this->field->setPositionIncrementGap(42));
        $this->assertSame(42, $this->field->getPositionIncrementGap());

        $this->assertSame($this->field, $this->field->setPositionIncrementGap(null));
        $this->assertNull($this->field->getPositionIncrementGap());
    }

    public function testAddAndGetCopyDests()
    {
        $copyDests = [
            $field = new Field('field_a'),
            $dynamicField = new DynamicField('*_b'),
            $dynamicBasedField = new DynamicBasedField('field_b'),
        ];
        $this->assertSame($this->field, $this->field->addCopyDest($field));
        $this->assertSame($this->field, $this->field->addCopyDest($dynamicField));
        $this->assertSame($this->field, $this->field->addCopyDest($dynamicBasedField));
        $this->assertSame($copyDests, $this->field->getCopyDests());
    }

    public function testAddAndGetCopySources()
    {
        $copyDests = [
            $field = new Field('field_a'),
            $dynamicField = new DynamicField('*_b'),
            $dynamicBasedField = new DynamicBasedField('field_b'),
            $wildcard = new WildcardField('field_*'),
        ];
        $this->assertSame($this->field, $this->field->addCopySource($field));
        $this->assertSame($this->field, $this->field->addCopySource($dynamicField));
        $this->assertSame($this->field, $this->field->addCopySource($dynamicBasedField));
        $this->assertSame($this->field, $this->field->addCopySource($wildcard));
        $this->assertSame($copyDests, $this->field->getCopySources());
    }

    abstract public function testToString();
}
