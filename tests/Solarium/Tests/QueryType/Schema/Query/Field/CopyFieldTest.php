<?php

namespace Solarium\Tests\QueryType\Schema\Field;

use Solarium\QueryType\Schema\Query\Field\CopyField;

class CopyFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAttributesFromConstructor()
    {
        $source = 'the_source';
        $dest = 'destination';
        $maxChars = 10;
        $field = new CopyField($source, $dest, $maxChars);
        $this->assertEquals($source, $field->getSource());
        $this->assertEquals(array($dest), $field->getDest());
        $this->assertEquals($maxChars, $field->getMaxChars());
    }

    public function testSetAndGetSource()
    {
        $source = 'different';
        $field = new CopyField();
        $field->setSource($source);
        $this->assertEquals($source, $field->getSource());
        $this->assertEquals($source, (string) $field);
    }

    public function testSetAddAndGetDest()
    {
        $field = new CopyField();
        $field->setDest(array('dest1'));
        $field->addDest('dest2');
        $this->assertEquals(array('dest1', 'dest2'), $field->getDest());
    }

    public function testSetAndGetMaxChars()
    {
        $field = new CopyField();
        $this->assertNull($field->getMaxChars());
        $field->setMaxChars('10');
        $this->assertSame(10, $field->getMaxChars());
    }

    public function testCastAsArray()
    {
        $field = new CopyField('source', 'destination');
        $field->setMaxChars('20');
        $this->assertEquals(
            array(
                'source' => 'source',
                'dest' => array('destination'),
                'maxChars' => 20,
            ),
            $field->castAsArray()
        );
    }
}
