<?php

namespace Solarium\Tests\QueryType\Schema\Query\FieldType\Analyzer\Tokenizer;

use Solarium\QueryType\Schema\Query\FieldType\Analyzer\Tokenizer\Tokenizer;

class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    protected function setUp()
    {
        $this->class = 'this-is-a-class';
        $this->delimiter = ',';
        $this->tokenizer = new Tokenizer($this->class, $this->delimiter);
    }

    public function testIsTokenizer()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\FieldType\Analyzer\Tokenizer\TokenizerInterface',
            $this->tokenizer
        );
    }

    public function testSetAndGetClass()
    {
        $this->assertEquals($this->class, $this->tokenizer->getClass());
        $class2 = 'class2';
        $this->tokenizer->setClass($class2);
        $this->assertEquals($class2, $this->tokenizer->getClass());
    }

    public function testSetAndGetDelimiter()
    {
        $this->assertEquals($this->delimiter, $this->tokenizer->getDelimiter());
        $delimiter2 = ';';
        $this->tokenizer->setDelimiter($delimiter2);
        $this->assertEquals($delimiter2, $this->tokenizer->getDelimiter());
    }

    public function testCastAsArray()
    {
        $this->assertEquals(
            array(
                'class' => $this->class,
                'delimiter' => $this->delimiter,
            ),
            $this->tokenizer->castAsArray()
        );
    }
}
