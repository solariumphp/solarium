<?php

namespace Solarium\Tests\Component\Highlighting;

use Solarium\Component\Highlighting\Field;

class FieldTest extends AbstractHighlightingTestCase
{
    /**
     * @var Field
     */
    protected $hlt;

    public function setUp(): void
    {
        $this->hlt = new Field();
    }

    public function testConfigMode()
    {
        $options = [
            'usefastvectorhighlighter' => true,
            'method' => 'unified',
            'usephrasehighlighter' => false,
            'highlightmultiterm' => true,
            'snippets' => 3,
            'fragsize' => 25,
            'tagprefix' => '<i>',
            'tagpostfix' => '<\i>',
            'encoder' => 'html',
            'maxanalyzedchars' => 40,
            'offsetsource' => 'POSTINGS',
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
            'boundaryscannertype' => 'WORD',
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
        $this->assertSame('unified', $this->hlt->getMethod());
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
        $this->assertSame($options['tagellipsis'], $this->hlt->getTagEllipsis());
        $this->assertFalse($this->hlt->getFragsizeIsMinimum());
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
        $this->assertSame($options['boundaryscanner'], $this->hlt->getBoundaryScanner());
        $this->assertSame($options['boundaryscannermaxscan'], $this->hlt->getBoundaryScannerMaxScan());
        $this->assertSame($options['boundaryscannerchars'], $this->hlt->getBoundaryScannerChars());
        $this->assertSame($options['phraselimit'], $this->hlt->getPhraseLimit());
        $this->assertSame($options['multivaluedseparatorchar'], $this->hlt->getMultiValuedSeparatorChar());
    }

    public function testSetAndGetName()
    {
        $value = 'testname';
        $this->hlt->setName($value);

        $this->assertSame(
            $value,
            $this->hlt->getName()
        );
    }
}
