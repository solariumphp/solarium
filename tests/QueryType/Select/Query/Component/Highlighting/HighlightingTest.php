<?php

namespace Solarium\Tests\QueryType\Select\Query\Component\Highlighting;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Highlighting\Field;
use Solarium\Component\Highlighting\Highlighting;
use Solarium\QueryType\Select\Query\Query;

class HighlightingTest extends TestCase
{
    /**
     * @var Highlighting
     */
    protected $hlt;

    public function setUp()
    {
        $this->hlt = new Highlighting();
    }

    public function testConfigMode()
    {
        $options = [
            'field' => [
                'fieldA' => [
                    'snippets' => 3,
                    'fragsize' => 25,
                ],
                'fieldB',
            ],
            'snippets' => 2,
            'fragsize' => 20,
            'mergecontiguous' => true,
            'requirefieldmatch' => false,
            'maxanalyzedchars' => 40,
            'alternatefield' => 'text',
            'maxalternatefieldlength' => 50,
            'preservemulti' => true,
            'formatter' => 'myFormatter',
            'simpleprefix' => '<b>',
            'simplepostfix' => '</b>',
            'tagprefix' => '<i>',
            'tagpostfix' => '<\i>',
            'fragmenter' => 'myFragmenter',
            'fraglistbuilder' => 'regex',
            'fragmentsbuilder' => 'myBuilder',
            'usefastvectorhighlighter' => true,
            'usephrasehighlighter' => false,
            'highlightmultiterm' => true,
            'regexslop' => .8,
            'regexpattern' => 'myPattern',
            'regexmaxanalyzedchars' => 500,
            'query' => 'text:myvalue',
            'phraselimit' => 35,
            'multivaluedseparatorchar' => '|',
            'boundaryscannermaxscan' => 12,
            'boundaryscannerchars' => "\t\n",
            'boundaryscannertype' => 'LINE',
            'boundaryscannerlanguage' => 'nl',
            'boundaryscannercountry' => 'NL',
        ];

        $this->hlt->setOptions($options);

        $this->assertSame(['fieldA', 'fieldB'], array_keys($this->hlt->getFields()));
        $this->assertSame($options['field']['fieldA']['snippets'], $this->hlt->getField('fieldA')->getSnippets());
        $this->assertSame($options['field']['fieldA']['fragsize'], $this->hlt->getField('fieldA')->getFragSize());
        $this->assertNull($this->hlt->getField('FieldB')->getSnippets());
        $this->assertSame($options['snippets'], $this->hlt->getSnippets());
        $this->assertSame($options['fragsize'], $this->hlt->getFragSize());
        $this->assertSame($options['mergecontiguous'], $this->hlt->getMergeContiguous());
        $this->assertSame($options['maxanalyzedchars'], $this->hlt->getMaxAnalyzedChars());
        $this->assertSame($options['alternatefield'], $this->hlt->getAlternateField());
        $this->assertSame($options['maxalternatefieldlength'], $this->hlt->getMaxAlternateFieldLength());
        $this->assertSame($options['preservemulti'], $this->hlt->getPreserveMulti());
        $this->assertSame($options['formatter'], $this->hlt->getFormatter());
        $this->assertSame($options['simpleprefix'], $this->hlt->getSimplePrefix());
        $this->assertSame($options['simplepostfix'], $this->hlt->getSimplePostfix());
        $this->assertSame($options['tagprefix'], $this->hlt->getTagPrefix());
        $this->assertSame($options['tagpostfix'], $this->hlt->getTagPostfix());
        $this->assertSame($options['fragmenter'], $this->hlt->getFragmenter());
        $this->assertSame($options['fraglistbuilder'], $this->hlt->getFragListBuilder());
        $this->assertSame($options['fragmentsbuilder'], $this->hlt->getFragmentsBuilder());
        $this->assertSame($options['usefastvectorhighlighter'], $this->hlt->getUseFastVectorHighlighter());
        $this->assertSame($options['usephrasehighlighter'], $this->hlt->getUsePhraseHighlighter());
        $this->assertSame($options['highlightmultiterm'], $this->hlt->getHighlightMultiTerm());
        $this->assertSame($options['regexslop'], $this->hlt->getRegexSlop());
        $this->assertSame($options['regexpattern'], $this->hlt->getRegexPattern());
        $this->assertSame($options['regexmaxanalyzedchars'], $this->hlt->getRegexMaxAnalyzedChars());
        $this->assertSame($options['query'], $this->hlt->getQuery());
        $this->assertSame($options['phraselimit'], $this->hlt->getPhraseLimit());
        $this->assertSame($options['multivaluedseparatorchar'], $this->hlt->getMultiValuedSeparatorChar());
        $this->assertSame($options['boundaryscannermaxscan'], $this->hlt->getBoundaryScannerMaxScan());
        $this->assertSame($options['boundaryscannerchars'], $this->hlt->getBoundaryScannerChars());
        $this->assertSame($options['boundaryscannertype'], $this->hlt->getBoundaryScannerType());
        $this->assertSame($options['boundaryscannerlanguage'], $this->hlt->getBoundaryScannerLanguage());
        $this->assertSame($options['boundaryscannercountry'], $this->hlt->getBoundaryScannerCountry());
    }

    public function testGetType()
    {
        $this->assertSame(Query::COMPONENT_HIGHLIGHTING, $this->hlt->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\Highlighting',
            $this->hlt->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Highlighting',
            $this->hlt->getRequestBuilder()
        );
    }

    public function testGetFieldAutocreate()
    {
        $name = 'test';
        $field = $this->hlt->getField($name);

        $this->assertSame($name, $field->getName());
    }

    public function testGetFieldNoAutocreate()
    {
        $name = 'test';
        $field = $this->hlt->getField($name, false);

        $this->assertNull($field);
    }

    public function testAddFieldWithObject()
    {
        $field = new Field();
        $field->setName('test');

        $this->hlt->addField($field);

        $this->assertSame($field, $this->hlt->getField('test'));
    }

    public function testAddFieldWithString()
    {
        $name = 'test';
        $this->hlt->addField($name);

        $this->assertSame([$name], array_keys($this->hlt->getFields()));
    }

    public function testAddFieldWithArray()
    {
        $config = [
            'name' => 'fieldA',
            'snippets' => 6,
        ];
        $this->hlt->addField($config);

        $this->assertSame(6, $this->hlt->getField('fieldA')->getSnippets());
    }

    public function testAddFieldWithObjectWithoutName()
    {
        $field = new Field();
        $this->expectException('Solarium\Exception\InvalidArgumentException');
        $this->hlt->addField($field);
    }

    public function testAddsFieldsWithString()
    {
        $fields = 'test1,test2';
        $this->hlt->addFields($fields);

        $this->assertSame(['test1', 'test2'], array_keys($this->hlt->getFields()));
    }

    public function testAddsFieldsWithArray()
    {
        $fields = [
            'test1' => ['snippets' => 2],
            'test2' => ['snippets' => 5],
        ];
        $this->hlt->addFields($fields);

        $this->assertSame(2, $this->hlt->getField('test1')->getSnippets());
        $this->assertSame(5, $this->hlt->getField('test2')->getSnippets());
    }

    public function testRemoveField()
    {
        $fields = [
            'test1' => ['snippets' => 2],
            'test2' => ['snippets' => 5],
        ];

        $this->hlt->addFields($fields);
        $this->assertSame(['test1', 'test2'], array_keys($this->hlt->getFields()));

        $this->hlt->removeField('test1');
        $this->assertSame(['test2'], array_keys($this->hlt->getFields()));
    }

    public function testRemoveFieldWithInvalidName()
    {
        $fields = [
            'test1' => ['snippets' => 2],
            'test2' => ['snippets' => 5],
        ];

        $this->hlt->addFields($fields);
        $this->assertSame(['test1', 'test2'], array_keys($this->hlt->getFields()));

        $this->hlt->removeField('test1=3'); // should fail silently and do nothing
        $this->assertSame(['test1', 'test2'], array_keys($this->hlt->getFields()));
    }

    public function testClearFields()
    {
        $fields = [
            'test1' => ['snippets' => 2],
            'test2' => ['snippets' => 5],
        ];

        $this->hlt->addFields($fields);
        $this->assertSame(['test1', 'test2'], array_keys($this->hlt->getFields()));

        $this->hlt->clearFields();
        $this->assertSame([], array_keys($this->hlt->getFields()));
    }

    public function testSetFields()
    {
        $fields = [
            'test1' => ['snippets' => 2],
            'test2' => ['snippets' => 5],
        ];

        $this->hlt->addFields($fields);
        $this->assertSame(['test1', 'test2'], array_keys($this->hlt->getFields()));

        $newFields = [
            'test3' => ['snippets' => 4],
            'test4' => ['snippets' => 6],
        ];

        $this->hlt->setFields($newFields);
        $this->assertSame(['test3', 'test4'], array_keys($this->hlt->getFields()));
    }

    public function testSetAndGetSnippets()
    {
        $value = 2;
        $this->hlt->setSnippets($value);

        $this->assertSame(
            $value,
            $this->hlt->getSnippets()
        );
    }

    public function testSetAndGetFragSize()
    {
        $value = 20;
        $this->hlt->setFragSize($value);

        $this->assertSame(
            $value,
            $this->hlt->getFragSize()
        );
    }

    public function testSetAndGetMergeContiguous()
    {
        $value = true;
        $this->hlt->setMergeContiguous($value);

        $this->assertSame(
            $value,
            $this->hlt->getMergeContiguous()
        );
    }

    public function testSetAndGetRequireFieldMatch()
    {
        $value = true;
        $this->hlt->setRequireFieldMatch($value);

        $this->assertSame(
            $value,
            $this->hlt->getRequireFieldMatch()
        );
    }

    public function testSetAndGetMaxAnalyzedChars()
    {
        $value = 200;
        $this->hlt->setMaxAnalyzedChars($value);

        $this->assertSame(
            $value,
            $this->hlt->getMaxAnalyzedChars()
        );
    }

    public function testSetAndGetAlternateField()
    {
        $value = 'description';
        $this->hlt->setAlternateField($value);

        $this->assertSame(
            $value,
            $this->hlt->getAlternateField()
        );
    }

    public function testSetAndGetMaxAlternateFieldLength()
    {
        $value = 150;
        $this->hlt->setMaxAlternateFieldLength($value);

        $this->assertSame(
            $value,
            $this->hlt->getMaxAlternateFieldLength()
        );
    }

    public function testSetAndGetPreserveMulti()
    {
        $value = true;
        $this->hlt->setPreserveMulti($value);

        $this->assertSame(
            $value,
            $this->hlt->getPreserveMulti()
        );
    }

    public function testSetAndGetFormatter()
    {
        $this->hlt->setFormatter();

        $this->assertSame(
            'simple',
            $this->hlt->getFormatter()
        );
    }

    public function testSetAndGetSimplePrefix()
    {
        $value = '<em>';
        $this->hlt->setSimplePrefix($value);

        $this->assertSame(
            $value,
            $this->hlt->getSimplePrefix()
        );
    }

    public function testSetAndGetSimplePostfix()
    {
        $value = '</em>';
        $this->hlt->setSimplePostfix($value);

        $this->assertSame(
            $value,
            $this->hlt->getSimplePostfix()
        );
    }

    public function testSetAndGetFragmenter()
    {
        $value = Highlighting::FRAGMENTER_REGEX;
        $this->hlt->setFragmenter($value);

        $this->assertSame(
            $value,
            $this->hlt->getFragmenter()
        );
    }

    public function testSetAndGetFragListBuilder()
    {
        $value = 'myBuilder';
        $this->hlt->setFragListBuilder($value);

        $this->assertSame(
            $value,
            $this->hlt->getFragListBuilder()
        );
    }

    public function testSetAndGetFragmentsBuilder()
    {
        $value = 'myBuilder';
        $this->hlt->setFragmentsBuilder($value);

        $this->assertSame(
            $value,
            $this->hlt->getFragmentsBuilder()
        );
    }

    public function testSetAndGetUseFastVectorHighlighter()
    {
        $value = true;
        $this->hlt->setUseFastVectorHighlighter($value);

        $this->assertSame(
            $value,
            $this->hlt->getUseFastVectorHighlighter()
        );
    }

    public function testSetAndGetUsePhraseHighlighter()
    {
        $value = true;
        $this->hlt->setUsePhraseHighlighter($value);

        $this->assertSame(
            $value,
            $this->hlt->getUsePhraseHighlighter()
        );
    }

    public function testSetAndGetHighlightMultiTerm()
    {
        $value = true;
        $this->hlt->setHighlightMultiTerm($value);

        $this->assertSame(
            $value,
            $this->hlt->getHighlightMultiTerm()
        );
    }

    public function testSetAndGetRegexSlop()
    {
        $value = .8;
        $this->hlt->setRegexSlop($value);

        $this->assertSame(
            $value,
            $this->hlt->getRegexSlop()
        );
    }

    public function testSetAndGetRegexPattern()
    {
        $value = 'myPattern';
        $this->hlt->setRegexPattern($value);

        $this->assertSame(
            $value,
            $this->hlt->getRegexPattern()
        );
    }

    public function testSetAndGetRegexMaxAnalyzedChars()
    {
        $value = 500;
        $this->hlt->setRegexMaxAnalyzedChars($value);

        $this->assertSame(
            $value,
            $this->hlt->getRegexMaxAnalyzedChars()
        );
    }

    public function testSetAndGetQuery()
    {
        $value = 'text:myvalue';
        $this->hlt->setQuery($value);

        $this->assertSame(
            $value,
            $this->hlt->getQuery()
        );
    }

    public function testSetAndGetPhraseLimit()
    {
        $value = 20;
        $this->hlt->setPhraseLimit($value);

        $this->assertSame(
            $value,
            $this->hlt->getPhraseLimit()
        );
    }

    public function testSetAndGetTagPrefix()
    {
        $value = '<i>';
        $this->hlt->setTagPrefix($value);

        $this->assertSame(
            $value,
            $this->hlt->getTagPrefix()
        );
    }

    public function testSetAndGetTagPostfix()
    {
        $value = '</i>';
        $this->hlt->setTagPostfix($value);

        $this->assertSame(
            $value,
            $this->hlt->getTagPostfix()
        );
    }

    public function testSetAndGetMultiValuedSeparatorChar()
    {
        $value = '|';
        $this->hlt->setMultiValuedSeparatorChar($value);

        $this->assertSame(
            $value,
            $this->hlt->getMultiValuedSeparatorChar()
        );
    }

    public function testSetAndGetBoundaryScannerChars()
    {
        $value = "\n";
        $this->hlt->setBoundaryScannerChars($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerChars()
        );
    }

    public function testSetAndGetBoundaryScannerMaxScan()
    {
        $value = 15;
        $this->hlt->setBoundaryScannerMaxScan($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerMaxScan()
        );
    }

    public function testSetAndGetBoundaryScannerType()
    {
        $value = 'SENTENCE';
        $this->hlt->setBoundaryScannerType($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerType()
        );
    }

    public function testSetAndGetBoundaryScannerCountry()
    {
        $value = 'DE';
        $this->hlt->setBoundaryScannerCountry($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerCountry()
        );
    }

    public function testSetAndGetBoundaryScannerLanguage()
    {
        $value = 'fr';
        $this->hlt->setBoundaryScannerLanguage($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerLanguage()
        );
    }
}
