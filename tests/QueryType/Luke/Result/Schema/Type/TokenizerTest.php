<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Schema\Type\Tokenizer;

class TokenizerTest extends TestCase
{
    /**
     * @var Tokenizer
     */
    protected $tokenizer;

    public function setUp(): void
    {
        $this->tokenizer = new Tokenizer('org.example.TokenizerFactory');
    }

    public function testGetClassName()
    {
        $this->assertSame('org.example.TokenizerFactory', $this->tokenizer->getClassName());
    }

    public function testSetAndGetArgs()
    {
        $args = [
            'class' => 'TokenizerFactory',
            'luceneMatchVersion' => '1.2.3',
        ];
        $this->assertSame($this->tokenizer, $this->tokenizer->setArgs($args));
        $this->assertSame($args, $this->tokenizer->getArgs());
    }

    public function testToString()
    {
        $this->assertSame('org.example.TokenizerFactory', (string) $this->tokenizer);
    }
}
