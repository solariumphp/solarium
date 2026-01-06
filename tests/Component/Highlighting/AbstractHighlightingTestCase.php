<?php

namespace Solarium\Tests\Component\Highlighting;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Highlighting\HighlightingInterface;

abstract class AbstractHighlightingTestCase extends TestCase
{
    protected HighlightingInterface $hlt;

    abstract public function testConfigMode();

    /**
     * @deprecated Use {@link HighlightingInterface::setMethod()} for Solr 6.4 and higher
     */
    public function testSetAndGetUseFastVectorHighlighter(): void
    {
        $this->hlt->setUseFastVectorHighlighter(true);
        $this->assertTrue($this->hlt->getUseFastVectorHighlighter());
    }

    public function testSetAndGetMethod(): void
    {
        $value = HighlightingInterface::METHOD_UNIFIED;
        $this->hlt->setMethod($value);

        $this->assertSame(
            $value,
            $this->hlt->getMethod()
        );
    }

    public function testSetAndGetUsePhraseHighlighter(): void
    {
        $this->hlt->setUsePhraseHighlighter(true);
        $this->assertTrue($this->hlt->getUsePhraseHighlighter());
    }

    public function testSetAndGetHighlightMultiTerm(): void
    {
        $this->hlt->setHighlightMultiTerm(true);
        $this->assertTrue($this->hlt->getHighlightMultiTerm());
    }

    public function testSetAndGetSnippets(): void
    {
        $value = 2;
        $this->hlt->setSnippets($value);

        $this->assertSame(
            $value,
            $this->hlt->getSnippets()
        );
    }

    public function testSetAndGetFragSize(): void
    {
        $value = 20;
        $this->hlt->setFragSize($value);

        $this->assertSame(
            $value,
            $this->hlt->getFragSize()
        );
    }

    public function testSetAndGetTagPrefix(): void
    {
        $value = '<i>';
        $this->hlt->setTagPrefix($value);

        $this->assertSame(
            $value,
            $this->hlt->getTagPrefix()
        );
    }

    public function testSetAndGetTagPostfix(): void
    {
        $value = '</i>';
        $this->hlt->setTagPostfix($value);

        $this->assertSame(
            $value,
            $this->hlt->getTagPostfix()
        );
    }

    public function testSetAndGetEncoder(): void
    {
        $value = HighlightingInterface::ENCODER_HTML;
        $this->hlt->setEncoder($value);

        $this->assertSame(
            $value,
            $this->hlt->getEncoder()
        );
    }

    public function testSetAndGetMaxAnalyzedChars(): void
    {
        $value = 200;
        $this->hlt->setMaxAnalyzedChars($value);

        $this->assertSame(
            $value,
            $this->hlt->getMaxAnalyzedChars()
        );
    }

    public function testSetAndGetOffsetSource(): void
    {
        $value = HighlightingInterface::OFFSETSOURCE_ANALYSIS;
        $this->hlt->setOffsetSource($value);

        $this->assertSame(
            $value,
            $this->hlt->getOffsetSource()
        );
    }

    public function testSetAndGetFragAlignRatio(): void
    {
        $value = .5;
        $this->hlt->setFragAlignRatio($value);

        $this->assertSame(
            $value,
            $this->hlt->getFragAlignRatio()
        );
    }

    public function testSetAndGetFragsizeIsMinimum(): void
    {
        $this->hlt->setFragsizeIsMinimum(true);
        $this->assertTrue($this->hlt->getFragsizeIsMinimum());
    }

    public function testSetAndGetTagEllipsis(): void
    {
        $value = '...';
        $this->hlt->setTagEllipsis($value);

        $this->assertSame(
            $value,
            $this->hlt->getTagEllipsis()
        );
    }

    public function testSetAndGetDefaultSummary(): void
    {
        $this->hlt->setDefaultSummary(true);
        $this->assertTrue($this->hlt->getDefaultSummary());
    }

    public function testSetAndGetScoreK1(): void
    {
        $value = 1.85;
        $this->hlt->setScoreK1($value);

        $this->assertSame(
            $value,
            $this->hlt->getScoreK1()
        );
    }

    public function testSetAndGetScoreB(): void
    {
        $value = .25;
        $this->hlt->setScoreB($value);

        $this->assertSame(
            $value,
            $this->hlt->getScoreB()
        );
    }

    public function testSetAndGetScorePivot(): void
    {
        $value = 42;
        $this->hlt->setScorePivot($value);

        $this->assertSame(
            $value,
            $this->hlt->getScorePivot()
        );
    }

    public function testSetAndGetBoundaryScannerLanguage(): void
    {
        $value = 'fr';
        $this->hlt->setBoundaryScannerLanguage($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerLanguage()
        );
    }

    public function testSetAndGetBoundaryScannerCountry(): void
    {
        $value = 'DE';
        $this->hlt->setBoundaryScannerCountry($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerCountry()
        );
    }

    public function testSetAndGetBoundaryScannerVariant(): void
    {
        $value = 'ao1990';
        $this->hlt->setBoundaryScannerVariant($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerVariant()
        );
    }

    public function testSetAndGetBoundaryScannerType(): void
    {
        $value = HighlightingInterface::BOUNDARYSCANNER_TYPE_SENTENCE;
        $this->hlt->setBoundaryScannerType($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerType()
        );
    }

    public function testSetAndGetBoundaryScannerSeparator(): void
    {
        $value = '¶';
        $this->hlt->setBoundaryScannerSeparator($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerSeparator()
        );
    }

    public function testSetAndGetWeightMatches(): void
    {
        $this->hlt->setWeightMatches(false);
        $this->assertFalse($this->hlt->getWeightMatches());
    }

    public function testSetAndGetMergeContiguous(): void
    {
        $this->hlt->setMergeContiguous(true);
        $this->assertTrue($this->hlt->getMergeContiguous());
    }

    public function testSetAndGetMaxMultiValuedToExamine(): void
    {
        $value = 20000;
        $this->hlt->setMaxMultiValuedToExamine($value);

        $this->assertSame(
            $value,
            $this->hlt->getMaxMultiValuedToExamine()
        );
    }

    public function testSetAndGetMaxMultiValuedToMatch(): void
    {
        $value = 10000;
        $this->hlt->setMaxMultiValuedToMatch($value);

        $this->assertSame(
            $value,
            $this->hlt->getMaxMultiValuedToMatch()
        );
    }

    public function testSetAndGetAlternateField(): void
    {
        $value = 'description';
        $this->hlt->setAlternateField($value);

        $this->assertSame(
            $value,
            $this->hlt->getAlternateField()
        );
    }

    public function testSetAndGetMaxAlternateFieldLength(): void
    {
        $value = 150;
        $this->hlt->setMaxAlternateFieldLength($value);

        $this->assertSame(
            $value,
            $this->hlt->getMaxAlternateFieldLength()
        );
    }

    public function testSetAndGetHighlightAlternate(): void
    {
        $this->hlt->setHighlightAlternate(false);
        $this->assertFalse($this->hlt->getHighlightAlternate());
    }

    public function testSetAndGetFormatter(): void
    {
        $value = 'myformatter';
        $this->hlt->setFormatter($value);

        $this->assertSame(
            $value,
            $this->hlt->getFormatter()
        );
    }

    public function testSetAndGetFormatterDefaultValue(): void
    {
        $this->hlt->setFormatter();

        $this->assertSame(
            HighlightingInterface::FORMATTER_SIMPLE,
            $this->hlt->getFormatter()
        );
    }

    public function testSetAndGetSimplePrefix(): void
    {
        $value = '<em>';
        $this->hlt->setSimplePrefix($value);

        $this->assertSame(
            $value,
            $this->hlt->getSimplePrefix()
        );
    }

    public function testSetAndGetSimplePostfix(): void
    {
        $value = '</em>';
        $this->hlt->setSimplePostfix($value);

        $this->assertSame(
            $value,
            $this->hlt->getSimplePostfix()
        );
    }

    public function testSetAndGetFragmenter(): void
    {
        $value = HighlightingInterface::FRAGMENTER_REGEX;
        $this->hlt->setFragmenter($value);

        $this->assertSame(
            $value,
            $this->hlt->getFragmenter()
        );
    }

    public function testSetAndGetRegexSlop(): void
    {
        $value = .8;
        $this->hlt->setRegexSlop($value);

        $this->assertSame(
            $value,
            $this->hlt->getRegexSlop()
        );
    }

    public function testSetAndGetRegexPattern(): void
    {
        $value = 'myPattern';
        $this->hlt->setRegexPattern($value);

        $this->assertSame(
            $value,
            $this->hlt->getRegexPattern()
        );
    }

    public function testSetAndGetRegexMaxAnalyzedChars(): void
    {
        $value = 500;
        $this->hlt->setRegexMaxAnalyzedChars($value);

        $this->assertSame(
            $value,
            $this->hlt->getRegexMaxAnalyzedChars()
        );
    }

    public function testSetAndGetPreserveMulti(): void
    {
        $this->hlt->setPreserveMulti(true);
        $this->assertTrue($this->hlt->getPreserveMulti());
    }

    public function testSetAndGetPayloads(): void
    {
        $this->hlt->setPayloads(false);
        $this->assertFalse($this->hlt->getPayloads());
    }

    public function testSetAndGetFragListBuilder(): void
    {
        $value = HighlightingInterface::FRAGLISTBUILDER_SINGLE;
        $this->hlt->setFragListBuilder($value);

        $this->assertSame(
            $value,
            $this->hlt->getFragListBuilder()
        );
    }

    public function testSetAndGetFragmentsBuilder(): void
    {
        $value = HighlightingInterface::FRAGMENTSBUILDER_DEFAULT;
        $this->hlt->setFragmentsBuilder($value);

        $this->assertSame(
            $value,
            $this->hlt->getFragmentsBuilder()
        );
    }

    public function testSetAndGetBoundaryScanner(): void
    {
        $value = HighlightingInterface::BOUNDARYSCANNER_SIMPLE;
        $this->hlt->setBoundaryScanner($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScanner()
        );
    }

    public function testSetAndGetBoundaryScannerMaxScan(): void
    {
        $value = 15;
        $this->hlt->setBoundaryScannerMaxScan($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerMaxScan()
        );
    }

    public function testSetAndGetBoundaryScannerChars(): void
    {
        $value = "\n";
        $this->hlt->setBoundaryScannerChars($value);

        $this->assertSame(
            $value,
            $this->hlt->getBoundaryScannerChars()
        );
    }

    public function testSetAndGetPhraseLimit(): void
    {
        $value = 20;
        $this->hlt->setPhraseLimit($value);

        $this->assertSame(
            $value,
            $this->hlt->getPhraseLimit()
        );
    }

    public function testSetAndGetMultiValuedSeparatorChar(): void
    {
        $value = '|';
        $this->hlt->setMultiValuedSeparatorChar($value);

        $this->assertSame(
            $value,
            $this->hlt->getMultiValuedSeparatorChar()
        );
    }
}
