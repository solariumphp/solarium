<?php

namespace Solarium\Tests\Component\Highlighting;

use Solarium\Component\Highlighting\Field;
use Solarium\Component\Highlighting\Highlighting;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Select\Query\Query;

class HighlightingTest extends AbstractHighlightingTestCase
{
    /**
     * @var Highlighting
     */
    protected $hlt;

    public function setUp(): void
    {
        $this->hlt = new Highlighting();
    }

    public function testConfigMode()
    {
        $options = [
            'usefastvectorhighlighter' => true,
            'method' => 'unified',
            'field' => [
                'fieldA' => [
                    'snippets' => 3,
                    'fragsize' => 25,
                ],
                'fieldB',
            ],
            'query' => 'text:myvalue',
            'queryparser' => 'myparser',
            'requirefieldmatch' => false,
            'queryfieldpattern' => 'fieldC,fieldD',
            'usephrasehighlighter' => false,
            'highlightmultiterm' => true,
            'snippets' => 2,
            'fragsize' => 20,
            'tagprefix' => '<i>',
            'tagpostfix' => '<\i>',
            'encoder' => 'html',
            'maxanalyzedchars' => 40,
            'offsetsource' => 'TERM_VECTORS',
            'fragalignratio' => .42,
            'fragsizeisminimum' => false,
            'tagellipsis' => '~~~',
            'defaultsummary' => true,
            'scorek1' => 1.6,
            'scoreb' => .45,
            'scorepivot' => 75,
            'boundaryscannerlanguage' => 'nl',
            'boundaryscannercountry' => 'NL',
            'boundaryscannervariant' => '1995',
            'boundaryscannertype' => 'LINE',
            'boundaryscannerseparator' => '¦',
            'weightmatches' => false,
            'mergecontiguous' => true,
            'maxmultivaluedtoexamine' => 4000,
            'maxmultivaluedtomatch' => 2000,
            'alternatefield' => 'text',
            'maxalternatefieldlength' => 50,
            'highlightalternate' => false,
            'formatter' => 'myFormatter',
            'simpleprefix' => '<b>',
            'simplepostfix' => '</b>',
            'fragmenter' => 'myFragmenter',
            'regexslop' => .8,
            'regexpattern' => 'myPattern',
            'regexmaxanalyzedchars' => 500,
            'preservemulti' => true,
            'payloads' => false,
            'fraglistbuilder' => 'simple',
            'fragmentsbuilder' => 'myBuilder',
            'boundaryscanner' => 'breakIterator',
            'boundaryscannermaxscan' => 12,
            'boundaryscannerchars' => "\t\n",
            'phraselimit' => 35,
            'multivaluedseparatorchar' => '|',
        ];

        $this->hlt->setOptions($options);

        $this->assertTrue($this->hlt->getUseFastVectorHighlighter());
        $this->assertSame($options['method'], $this->hlt->getMethod());
        $this->assertSame(['fieldA', 'fieldB'], array_keys($this->hlt->getFields()));
        $this->assertSame($options['field']['fieldA']['snippets'], $this->hlt->getField('fieldA')->getSnippets());
        $this->assertSame($options['field']['fieldA']['fragsize'], $this->hlt->getField('fieldA')->getFragSize());
        $this->assertNull($this->hlt->getField('FieldB')->getSnippets());
        $this->assertSame($options['query'], $this->hlt->getQuery());
        $this->assertSame($options['queryparser'], $this->hlt->getQueryParser());
        $this->assertFalse($this->hlt->getRequireFieldMatch());
        $this->assertSame(['fieldC', 'fieldD'], $this->hlt->getQueryFieldPattern());
        $this->assertFalse($this->hlt->getUsePhraseHighlighter());
        $this->assertTrue($this->hlt->getHighlightMultiTerm());
        $this->assertSame($options['snippets'], $this->hlt->getSnippets());
        $this->assertSame($options['fragsize'], $this->hlt->getFragSize());
        $this->assertSame($options['tagprefix'], $this->hlt->getTagPrefix());
        $this->assertSame($options['tagpostfix'], $this->hlt->getTagPostfix());
        $this->assertSame($options['encoder'], $this->hlt->getEncoder());
        $this->assertSame($options['maxanalyzedchars'], $this->hlt->getMaxAnalyzedChars());
        $this->assertSame($options['offsetsource'], $this->hlt->getOffsetSource());
        $this->assertSame($options['fragalignratio'], $this->hlt->getFragAlignRatio());
        $this->assertFalse($this->hlt->getFragsizeIsMinimum());
        $this->assertSame($options['tagellipsis'], $this->hlt->getTagEllipsis());
        $this->assertTrue($this->hlt->getDefaultSummary());
        $this->assertSame($options['scorek1'], $this->hlt->getScoreK1());
        $this->assertSame($options['scoreb'], $this->hlt->getScoreB());
        $this->assertSame($options['scorepivot'], $this->hlt->getScorePivot());
        $this->assertSame($options['boundaryscannerlanguage'], $this->hlt->getBoundaryScannerLanguage());
        $this->assertSame($options['boundaryscannercountry'], $this->hlt->getBoundaryScannerCountry());
        $this->assertSame($options['boundaryscannervariant'], $this->hlt->getBoundaryScannerVariant());
        $this->assertSame($options['boundaryscannertype'], $this->hlt->getBoundaryScannerType());
        $this->assertSame($options['boundaryscannerseparator'], $this->hlt->getBoundaryScannerSeparator());
        $this->assertFalse($this->hlt->getWeightMatches());
        $this->assertTrue($this->hlt->getMergeContiguous());
        $this->assertSame($options['maxmultivaluedtoexamine'], $this->hlt->getMaxMultiValuedToExamine());
        $this->assertSame($options['maxmultivaluedtomatch'], $this->hlt->getMaxMultiValuedToMatch());
        $this->assertSame($options['alternatefield'], $this->hlt->getAlternateField());
        $this->assertSame($options['maxalternatefieldlength'], $this->hlt->getMaxAlternateFieldLength());
        $this->assertFalse($this->hlt->getHighlightAlternate());
        $this->assertSame($options['formatter'], $this->hlt->getFormatter());
        $this->assertSame($options['simpleprefix'], $this->hlt->getSimplePrefix());
        $this->assertSame($options['simplepostfix'], $this->hlt->getSimplePostfix());
        $this->assertSame($options['fragmenter'], $this->hlt->getFragmenter());
        $this->assertSame($options['regexslop'], $this->hlt->getRegexSlop());
        $this->assertSame($options['regexpattern'], $this->hlt->getRegexPattern());
        $this->assertSame($options['regexmaxanalyzedchars'], $this->hlt->getRegexMaxAnalyzedChars());
        $this->assertTrue($this->hlt->getPreserveMulti());
        $this->assertFalse($this->hlt->getPayloads());
        $this->assertSame($options['fraglistbuilder'], $this->hlt->getFragListBuilder());
        $this->assertSame($options['fragmentsbuilder'], $this->hlt->getFragmentsBuilder());
        $this->assertSame($options['boundaryscannertype'], $this->hlt->getBoundaryScannerType());
        $this->assertSame($options['boundaryscannermaxscan'], $this->hlt->getBoundaryScannerMaxScan());
        $this->assertSame($options['boundaryscannerchars'], $this->hlt->getBoundaryScannerChars());
        $this->assertSame($options['phraselimit'], $this->hlt->getPhraseLimit());
        $this->assertSame($options['multivaluedseparatorchar'], $this->hlt->getMultiValuedSeparatorChar());
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
        $this->expectException(InvalidArgumentException::class);
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

    public function testSetAndGetQuery()
    {
        $value = 'text:myvalue';
        $this->hlt->setQuery($value);

        $this->assertSame(
            $value,
            $this->hlt->getQuery()
        );
    }

    public function testSetAndGetQueryParser()
    {
        $value = 'myparser';
        $this->hlt->setQueryParser($value);

        $this->assertSame(
            $value,
            $this->hlt->getQueryParser()
        );
    }

    public function testSetAndGetRequireFieldMatch()
    {
        $this->hlt->setRequireFieldMatch(true);
        $this->assertTrue($this->hlt->getRequireFieldMatch());
    }

    public function testSetAndGetQueryFieldPatternWithString()
    {
        $this->hlt->setQueryFieldPattern('fieldA,fieldB');
        $this->assertSame(['fieldA', 'fieldB'], $this->hlt->getQueryFieldPattern());
    }

    public function testSetAndGetQueryFieldPatternWithArray()
    {
        $this->hlt->setQueryFieldPattern(['fieldA', 'fieldB']);
        $this->assertSame(['fieldA', 'fieldB'], $this->hlt->getQueryFieldPattern());
    }
}
