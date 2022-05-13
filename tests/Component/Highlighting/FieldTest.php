<?php

namespace Solarium\Tests\Component\Highlighting;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Highlighting\Field;
use Solarium\Component\Highlighting\Highlighting;

class FieldTest extends TestCase
{
    /**
     * @var Field
     */
    protected $fld;

    public function setUp(): void
    {
        $this->fld = new Field();
    }

    public function testConfigMode()
    {
        $options = [
            'method' => 'unified',
            'snippets' => 3,
            'fragsize' => 25,
            'mergecontiguous' => true,
            'alternatefield' => 'text',
            'preservemulti' => true,
            'formatter' => 'myFormatter',
            'simpleprefix' => '<b>',
            'simplepostfix' => '</b>',
            'fragmenter' => 'myFragmenter',
            'usefastvectorhighlighter' => true,
        ];

        $this->fld->setOptions($options);

        $this->assertSame('unified', $this->fld->getMethod());
        $this->assertSame(3, $this->fld->getSnippets());
        $this->assertSame(25, $this->fld->getFragSize());
        $this->assertTrue($this->fld->getMergeContiguous());
        $this->assertSame('text', $this->fld->getAlternateField());
        $this->assertTrue($this->fld->getPreserveMulti());
        $this->assertSame('myFormatter', $this->fld->getFormatter());
        $this->assertSame('<b>', $this->fld->getSimplePrefix());
        $this->assertSame('</b>', $this->fld->getSimplePostfix());
        $this->assertSame('myFragmenter', $this->fld->getFragmenter());
        $this->assertTrue($this->fld->getUseFastVectorHighlighter());
    }

    public function testSetAndGetName()
    {
        $value = 'testname';
        $this->fld->setName($value);

        $this->assertSame(
            $value,
            $this->fld->getName()
        );
    }

    public function testSetAndGetMethod()
    {
        $value = 'unified';
        $this->fld->setMethod($value);

        $this->assertSame(
            $value,
            $this->fld->getMethod()
        );
    }

    public function testSetAndGetSnippets()
    {
        $value = 2;
        $this->fld->setSnippets($value);

        $this->assertSame(
            $value,
            $this->fld->getSnippets()
        );
    }

    public function testSetAndGetFragSize()
    {
        $value = 20;
        $this->fld->setFragSize($value);

        $this->assertSame(
            $value,
            $this->fld->getFragSize()
        );
    }

    public function testSetAndGetMergeContiguous()
    {
        $this->fld->setMergeContiguous(true);
        $this->assertTrue($this->fld->getMergeContiguous());
    }

    public function testSetAndGetAlternateField()
    {
        $value = 'description';
        $this->fld->setAlternateField($value);

        $this->assertSame(
            $value,
            $this->fld->getAlternateField()
        );
    }

    public function testSetAndGetPreserveMulti()
    {
        $this->fld->setPreserveMulti(true);
        $this->assertTrue($this->fld->getPreserveMulti());
    }

    public function testSetAndGetFormatter()
    {
        $this->fld->setFormatter();

        $this->assertSame(
            'simple',
            $this->fld->getFormatter()
        );
    }

    public function testSetAndGetSimplePrefix()
    {
        $value = '<em>';
        $this->fld->setSimplePrefix($value);

        $this->assertSame(
            $value,
            $this->fld->getSimplePrefix()
        );
    }

    public function testSetAndGetSimplePostfix()
    {
        $value = '</em>';
        $this->fld->setSimplePostfix($value);

        $this->assertSame(
            $value,
            $this->fld->getSimplePostfix()
        );
    }

    public function testSetAndGetFragmenter()
    {
        $value = Highlighting::FRAGMENTER_REGEX;
        $this->fld->setFragmenter($value);

        $this->assertSame(
            $value,
            $this->fld->getFragmenter()
        );
    }

    public function testSetAndGetUseFastVectorHighlighter()
    {
        $this->fld->setUseFastVectorHighlighter(true);
        $this->assertTrue($this->fld->getUseFastVectorHighlighter());
    }
}
