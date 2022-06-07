<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Highlighting;

/**
 * Highlighting trait.
 */
trait HighlightingTrait
{
    /**
     * Set useFastVectorHighlighter option.
     *
     * @param bool $use
     *
     * @return self Provides fluent interface
     *
     * @deprecated Use {@link setMethod()} for Solr 6.4 and higher
     */
    public function setUseFastVectorHighlighter(bool $use): HighlightingInterface
    {
        $this->setOption('usefastvectorhighlighter', $use);

        return $this;
    }

    /**
     * Get useFastVectorHighlighter option.
     *
     * @return bool|null
     */
    public function getUseFastVectorHighlighter(): ?bool
    {
        return $this->getOption('usefastvectorhighlighter');
    }

    /**
     * Set highlighter method.
     *
     * Use one of the METHOD_* constants as value.
     *
     * @param string $method
     *
     * @return self Provides fluent interface
     */
    public function setMethod(string $method): HighlightingInterface
    {
        $this->setOption('method', $method);

        return $this;
    }

    /**
     * Get highlighter method.
     *
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->getOption('method');
    }

    /**
     * Set usePhraseHighlighter option.
     *
     * @param bool $use
     *
     * @return self Provides fluent interface
     */
    public function setUsePhraseHighlighter(bool $use): HighlightingInterface
    {
        $this->setOption('usephrasehighlighter', $use);

        return $this;
    }

    /**
     * Get usePhraseHighlighter option.
     *
     * @return bool|null
     */
    public function getUsePhraseHighlighter(): ?bool
    {
        return $this->getOption('usephrasehighlighter');
    }

    /**
     * Set highlightMultiTerm option.
     *
     * @param bool $highlight
     *
     * @return self Provides fluent interface
     */
    public function setHighlightMultiTerm(bool $highlight): HighlightingInterface
    {
        $this->setOption('highlightmultiterm', $highlight);

        return $this;
    }

    /**
     * Get highlightMultiTerm option.
     *
     * @return bool|null
     */
    public function getHighlightMultiTerm(): ?bool
    {
        return $this->getOption('highlightmultiterm');
    }

    /**
     * Set snippets option.
     *
     * Maximum number of snippets per field
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setSnippets(int $maximum): HighlightingInterface
    {
        $this->setOption('snippets', $maximum);

        return $this;
    }

    /**
     * Get snippets option.
     *
     * @return int|null
     */
    public function getSnippets(): ?int
    {
        return $this->getOption('snippets');
    }

    /**
     * Set fragsize option.
     *
     * The size, in characters, of fragments to consider for highlighting
     *
     * @param int $size
     *
     * @return self Provides fluent interface
     */
    public function setFragSize(int $size): HighlightingInterface
    {
        $this->setOption('fragsize', $size);

        return $this;
    }

    /**
     * Get fragsize option.
     *
     * @return int|null
     */
    public function getFragSize(): ?int
    {
        return $this->getOption('fragsize');
    }

    /**
     * Set tag prefix option.
     *
     * Solr option hl.tag.post
     *
     * @param string $prefix
     *
     * @return self Provides fluent interface
     */
    public function setTagPrefix(string $prefix): HighlightingInterface
    {
        $this->setOption('tagprefix', $prefix);

        return $this;
    }

    /**
     * Get tag prefix option.
     *
     * Solr option hl.tag.pre
     *
     * @return string|null
     */
    public function getTagPrefix(): ?string
    {
        return $this->getOption('tagprefix');
    }

    /**
     * Set tag postfix option.
     *
     * Solr option hl.tag.post
     *
     * @param string $postfix
     *
     * @return self Provides fluent interface
     */
    public function setTagPostfix(string $postfix): HighlightingInterface
    {
        $this->setOption('tagpostfix', $postfix);

        return $this;
    }

    /**
     * Get tag postfix option.
     *
     * Solr option hl.tag.post
     *
     * @return string|null
     */
    public function getTagPostfix(): ?string
    {
        return $this->getOption('tagpostfix');
    }

    /**
     * Set encoder option.
     *
     * Use one of the ENCODER_* constants as value.
     *
     * @param string $encoder
     *
     * @return self Provides fluent interface
     */
    public function setEncoder(string $encoder): HighlightingInterface
    {
        $this->setOption('encoder', $encoder);

        return $this;
    }

    /**
     * Get encoder option.
     *
     * @return string|null
     */
    public function getEncoder(): ?string
    {
        return $this->getOption('encoder');
    }

    /**
     * Set maxAnalyzedChars option.
     *
     * How many characters into a document to look for suitable snippets
     *
     * @param int $chars
     *
     * @return self Provides fluent interface
     */
    public function setMaxAnalyzedChars(int $chars): HighlightingInterface
    {
        $this->setOption('maxanalyzedchars', $chars);

        return $this;
    }

    /**
     * Get maxAnalyzedChars option.
     *
     * @return int|null
     */
    public function getMaxAnalyzedChars(): ?int
    {
        return $this->getOption('maxanalyzedchars');
    }

    /**
     * Set offsetSource option.
     *
     * Use one of the OFFSETSOURCE_* constants as value.
     *
     * @param string $source
     *
     * @return self Provides fluent interface
     */
    public function setOffsetSource(string $source): HighlightingInterface
    {
        $this->setOption('offsetsource', $source);

        return $this;
    }

    /**
     * Get offsetSource option.
     *
     * @return string|null
     */
    public function getOffsetSource(): ?string
    {
        return $this->getOption('offsetsource');
    }

    /**
     * Set fragAlignRatio option.
     *
     * Influences where the first highlighted text in a passage is positioned.
     *
     * @param float $ratio
     *
     * @return self Provides fluent interface
     */
    public function setFragAlignRatio(float $ratio): HighlightingInterface
    {
        $this->setOption('fragalignratio', $ratio);

        return $this;
    }

    /**
     * Get fragAlignRatio option.
     *
     * @return float|null
     */
    public function getFragAlignRatio(): ?float
    {
        return $this->getOption('fragalignratio');
    }

    /**
     * Set fragsizeIsMinimum option.
     *
     * @param bool $isMinimum
     *
     * @return self Provides fluent interface
     */
    public function setFragsizeIsMinimum(bool $isMinimum): HighlightingInterface
    {
        $this->setOption('fragsizeisminimum', $isMinimum);

        return $this;
    }

    /**
     * Get fragsizeIsMinimum option.
     *
     * @return bool|null
     */
    public function getFragsizeIsMinimum(): ?bool
    {
        return $this->getOption('fragsizeisminimum');
    }

    /**
     * Set tag.ellipsis option.
     *
     * @param string $ellipsis
     *
     * @return self Provides fluent interface
     */
    public function setTagEllipsis(string $ellipsis): HighlightingInterface
    {
        $this->setOption('tagellipsis', $ellipsis);

        return $this;
    }

    /**
     * Get tag.ellipsis option.
     *
     * @return string|null
     */
    public function getTagEllipsis(): ?string
    {
        return $this->getOption('tagellipsis');
    }

    /**
     * Set defaultSummary option.
     *
     * @param bool $defaultSummary
     *
     * @return self Provides fluent interface
     */
    public function setDefaultSummary(bool $defaultSummary): HighlightingInterface
    {
        $this->setOption('defaultsummary', $defaultSummary);

        return $this;
    }

    /**
     * Get defaultSummary option.
     *
     * @return bool|null
     */
    public function getDefaultSummary(): ?bool
    {
        return $this->getOption('defaultsummary');
    }

    /**
     * Set score.k1 option.
     *
     * BM25 term frequency normalization parameter 'k1'.
     *
     * @param float $k1
     *
     * @return self Provides fluent interface
     */
    public function setScoreK1(float $k1): HighlightingInterface
    {
        $this->setOption('scorek1', $k1);

        return $this;
    }

    /**
     * Get score.k1 option.
     *
     * @return float|null
     */
    public function getScoreK1(): ?float
    {
        return $this->getOption('scorek1');
    }

    /**
     * Set score.b option.
     *
     * BM25 length normalization parameter 'b'.
     *
     * @param float $b
     *
     * @return self Provides fluent interface
     */
    public function setScoreB(float $b): HighlightingInterface
    {
        $this->setOption('scoreb', $b);

        return $this;
    }

    /**
     * Get score.b option.
     *
     * @return float|null
     */
    public function getScoreB(): ?float
    {
        return $this->getOption('scoreb');
    }

    /**
     * Set score.pivot option.
     *
     * BM25 average passage length in characters.
     *
     * @param int $pivot
     *
     * @return self Provides fluent interface
     */
    public function setScorePivot(int $pivot): HighlightingInterface
    {
        $this->setOption('scorepivot', $pivot);

        return $this;
    }

    /**
     * Get score.pivot option.
     *
     * @return int|null
     */
    public function getScorePivot(): ?int
    {
        return $this->getOption('scorepivot');
    }

    /**
     * Set breakIterator boundary scanner language option.
     *
     * @param string $language
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerLanguage(string $language): HighlightingInterface
    {
        $this->setOption('boundaryscannerlanguage', $language);

        return $this;
    }

    /**
     * Get breakIterator boundary scanner language option.
     *
     * @return string|null
     */
    public function getBoundaryScannerLanguage(): ?string
    {
        return $this->getOption('boundaryscannerlanguage');
    }

    /**
     * Set breakIterator boundary scanner country option.
     *
     * @param string $country
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerCountry(string $country): HighlightingInterface
    {
        $this->setOption('boundaryscannercountry', $country);

        return $this;
    }

    /**
     * Get breakIterator boundary scanner country option.
     *
     * @return string|null
     */
    public function getBoundaryScannerCountry(): ?string
    {
        return $this->getOption('boundaryscannercountry');
    }

    /**
     * Set breakIterator boundary scanner variant option.
     *
     * @param string $variant
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerVariant(string $variant): HighlightingInterface
    {
        $this->setOption('boundaryscannervariant', $variant);

        return $this;
    }

    /**
     * Get breakIterator boundary scanner variant option.
     *
     * @return string|null
     */
    public function getBoundaryScannerVariant(): ?string
    {
        return $this->getOption('boundaryscannervariant');
    }

    /**
     * Set breakIterator boundary scanner type option.
     *
     * Use one of the BOUNDARYSCANNER_TYPE_* constants as value.
     *
     * @param string $type
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerType(string $type): HighlightingInterface
    {
        $this->setOption('boundaryscannertype', $type);

        return $this;
    }

    /**
     * Get breakIterator boundary scanner type option.
     *
     * @return string|null
     */
    public function getBoundaryScannerType(): ?string
    {
        return $this->getOption('boundaryscannertype');
    }

    /**
     * Set breakIterator boundary scanner separator option.
     *
     * Indicates which character to break the text on with BOUNDARYSCANNER_TYPE_SEPARATOR.
     *
     * @param string $separator
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerSeparator(string $separator): HighlightingInterface
    {
        $this->setOption('boundaryscannerseparator', $separator);

        return $this;
    }

    /**
     * Get breakIterator boundary scanner separator option.
     *
     * @return string|null
     */
    public function getBoundaryScannerSeparator(): ?string
    {
        return $this->getOption('boundaryscannerseparator');
    }

    /**
     * Set weightMatches option.
     *
     * Use Lucene's "Weight Matches" API instead of doing SpanQuery conversion.
     *
     * @param bool $weightMatches
     *
     * @return self Provides fluent interface
     */
    public function setWeightMatches(bool $weightMatches): HighlightingInterface
    {
        $this->setOption('weightmatches', $weightMatches);

        return $this;
    }

    /**
     * Get weightMatches option.
     *
     * @return bool|null
     */
    public function getWeightMatches(): ?bool
    {
        return $this->getOption('weightmatches');
    }

    /**
     * Set mergeContiguous option.
     *
     * Collapse contiguous fragments into a single fragment.
     *
     * @param bool $merge
     *
     * @return self Provides fluent interface
     */
    public function setMergeContiguous(bool $merge): HighlightingInterface
    {
        $this->setOption('mergecontiguous', $merge);

        return $this;
    }

    /**
     * Get mergeContiguous option.
     *
     * @return bool|null
     */
    public function getMergeContiguous(): ?bool
    {
        return $this->getOption('mergecontiguous');
    }

    /**
     * Set maxMultiValuedToExamine option.
     *
     * Maximum number of entries in a multi-valued field to examine before stopping.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaxMultiValuedToExamine(int $maximum): HighlightingInterface
    {
        $this->setOption('maxmultivaluedtoexamine', $maximum);

        return $this;
    }

    /**
     * Get maxMultiValuedToExamine option.
     *
     * @return int|null
     */
    public function getMaxMultiValuedToExamine(): ?int
    {
        return $this->getOption('maxmultivaluedtoexamine');
    }

    /**
     * Set maxMultiValuedToMatch option.
     *
     * Maximum number of matches in a multi-valued field that are found before stopping.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaxMultiValuedToMatch(int $maximum): HighlightingInterface
    {
        $this->setOption('maxmultivaluedtomatch', $maximum);

        return $this;
    }

    /**
     * Get maxMultiValuedToMatch option.
     *
     * @return int|null
     */
    public function getMaxMultiValuedToMatch(): ?int
    {
        return $this->getOption('maxmultivaluedtomatch');
    }

    /**
     * Set alternateField option.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function setAlternateField(string $field): HighlightingInterface
    {
        $this->setOption('alternatefield', $field);

        return $this;
    }

    /**
     * Get alternateField option.
     *
     * @return string|null
     */
    public function getAlternateField(): ?string
    {
        return $this->getOption('alternatefield');
    }

    /**
     * Set maxAlternateFieldLength option.
     *
     * @param int $length
     *
     * @return self Provides fluent interface
     */
    public function setMaxAlternateFieldLength(int $length): HighlightingInterface
    {
        $this->setOption('maxalternatefieldlength', $length);

        return $this;
    }

    /**
     * Get maxAlternateFieldLength option.
     *
     * @return int|null
     */
    public function getMaxAlternateFieldLength(): ?int
    {
        return $this->getOption('maxalternatefieldlength');
    }

    /**
     * Set highlightAlternate option.
     *
     * @param bool $highlight
     *
     * @return self Provides fluent interface
     */
    public function setHighlightAlternate(bool $highlight): HighlightingInterface
    {
        $this->setOption('highlightalternate', $highlight);

        return $this;
    }

    /**
     * Get highlightAlternate option.
     *
     * @return bool|null
     */
    public function getHighlightAlternate(): ?bool
    {
        return $this->getOption('highlightalternate');
    }

    /**
     * Set formatter option.
     *
     * Use one of the FORMATTER_* constants as value.
     *
     * @param string $formatter
     *
     * @return self Provides fluent interface
     */
    public function setFormatter(string $formatter = HighlightingInterface::FORMATTER_SIMPLE): HighlightingInterface
    {
        $this->setOption('formatter', $formatter);

        return $this;
    }

    /**
     * Get formatter option.
     *
     * @return string|null
     */
    public function getFormatter(): ?string
    {
        return $this->getOption('formatter');
    }

    /**
     * Set simple prefix option.
     *
     * Solr option hl.simple.pre
     *
     * @param string $prefix
     *
     * @return self Provides fluent interface
     */
    public function setSimplePrefix(string $prefix): HighlightingInterface
    {
        $this->setOption('simpleprefix', $prefix);

        return $this;
    }

    /**
     * Get simple prefix option.
     *
     * Solr option hl.simple.pre
     *
     * @return string|null
     */
    public function getSimplePrefix(): ?string
    {
        return $this->getOption('simpleprefix');
    }

    /**
     * Set simple postfix option.
     *
     * Solr option hl.simple.post
     *
     * @param string $postfix
     *
     * @return self Provides fluent interface
     */
    public function setSimplePostfix(string $postfix): HighlightingInterface
    {
        $this->setOption('simplepostfix', $postfix);

        return $this;
    }

    /**
     * Get simple postfix option.
     *
     * Solr option hl.simple.post
     *
     * @return string|null
     */
    public function getSimplePostfix(): ?string
    {
        return $this->getOption('simplepostfix');
    }

    /**
     * Set fragmenter option.
     *
     * Use one of the FRAGMENTER_* constants as value.
     *
     * @param string $fragmenter
     *
     * @return self Provides fluent interface
     */
    public function setFragmenter(string $fragmenter): HighlightingInterface
    {
        $this->setOption('fragmenter', $fragmenter);

        return $this;
    }

    /**
     * Get fragmenter option.
     *
     * @return string|null
     */
    public function getFragmenter(): ?string
    {
        return $this->getOption('fragmenter');
    }

    /**
     * Set regex.slop option.
     *
     * @param float $slop
     *
     * @return self Provides fluent interface
     */
    public function setRegexSlop(float $slop): HighlightingInterface
    {
        $this->setOption('regexslop', $slop);

        return $this;
    }

    /**
     * Get regex.slop option.
     *
     * @return float|null
     */
    public function getRegexSlop(): ?float
    {
        return $this->getOption('regexslop');
    }

    /**
     * Set regex.pattern option.
     *
     * @param string $pattern
     *
     * @return self Provides fluent interface
     */
    public function setRegexPattern(string $pattern): HighlightingInterface
    {
        $this->setOption('regexpattern', $pattern);

        return $this;
    }

    /**
     * Get regex.pattern option.
     *
     * @return string|null
     */
    public function getRegexPattern(): ?string
    {
        return $this->getOption('regexpattern');
    }

    /**
     * Set regex.maxAnalyzedChars option.
     *
     * @param int $chars
     *
     * @return self Provides fluent interface
     */
    public function setRegexMaxAnalyzedChars(int $chars): HighlightingInterface
    {
        $this->setOption('regexmaxanalyzedchars', $chars);

        return $this;
    }

    /**
     * Get regex.maxAnalyzedChars option.
     *
     * @return int|null
     */
    public function getRegexMaxAnalyzedChars(): ?int
    {
        return $this->getOption('regexmaxanalyzedchars');
    }

    /**
     * Set preserveMulti option.
     *
     * @param bool $preservemulti
     *
     * @return self Provides fluent interface
     */
    public function setPreserveMulti(bool $preservemulti): HighlightingInterface
    {
        $this->setOption('preservemulti', $preservemulti);

        return $this;
    }

    /**
     * Get preserveMulti option.
     *
     * @return bool|null
     */
    public function getPreserveMulti(): ?bool
    {
        return $this->getOption('preservemulti');
    }

    /**
     * Set payloads option.
     *
     * @param bool $payloads
     *
     * @return self Provides fluent interface
     */
    public function setPayloads(bool $payloads): HighlightingInterface
    {
        $this->setOption('payloads', $payloads);

        return $this;
    }

    /**
     * Get payloads option.
     *
     * @return bool|null
     */
    public function getPayloads(): ?bool
    {
        return $this->getOption('payloads');
    }

    /**
     * Set fragListBuilder option.
     *
     * Use one of the FRAGLISTBUILDER_* constants as value.
     *
     * @param string $builder
     *
     * @return self Provides fluent interface
     */
    public function setFragListBuilder(string $builder): HighlightingInterface
    {
        $this->setOption('fraglistbuilder', $builder);

        return $this;
    }

    /**
     * Get fragListBuilder option.
     *
     * @return string|null
     */
    public function getFragListBuilder(): ?string
    {
        return $this->getOption('fraglistbuilder');
    }

    /**
     * Set fragmentsBuilder option.
     *
     * Use one of the FRAGMENTSBUILDER_* constants or the name of your own fragments builder as value.
     *
     * @param string $builder
     *
     * @return self Provides fluent interface
     */
    public function setFragmentsBuilder(string $builder): HighlightingInterface
    {
        $this->setOption('fragmentsbuilder', $builder);

        return $this;
    }

    /**
     * Get fragmentsBuilder option.
     *
     * @return string|null
     */
    public function getFragmentsBuilder(): ?string
    {
        return $this->getOption('fragmentsbuilder');
    }

    /**
     * Set boundaryScanner option.
     *
     * Use one of the BOUNDARYSCANNER_* constants as value.
     *
     * @param string $scanner
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScanner(string $scanner): HighlightingInterface
    {
        $this->setOption('boundaryscanner', $scanner);

        return $this;
    }

    /**
     * Get boundaryScanner option.
     *
     * @return string|null
     */
    public function getBoundaryScanner(): ?string
    {
        return $this->getOption('boundaryscanner');
    }

    /**
     * Set simple boundary scanner maxScan option.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerMaxScan(int $maximum): HighlightingInterface
    {
        $this->setOption('boundaryscannermaxscan', $maximum);

        return $this;
    }

    /**
     * Get simple boundary scanner maxScan option.
     *
     * @return int|null
     */
    public function getBoundaryScannerMaxScan(): ?int
    {
        return $this->getOption('boundaryscannermaxscan');
    }

    /**
     * Set simple boundary scanner cgars option.
     *
     * @param string $chars
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerChars(string $chars): HighlightingInterface
    {
        $this->setOption('boundaryscannerchars', $chars);

        return $this;
    }

    /**
     * Get simple boundary scanner cgars option.
     *
     * @return string|null
     */
    public function getBoundaryScannerChars(): ?string
    {
        return $this->getOption('boundaryscannerchars');
    }

    /**
     * Set phraseLimit option.
     *
     * Maximum number of phrases to analyze when searching for the highest-scoring phrase.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setPhraseLimit(int $maximum): HighlightingInterface
    {
        $this->setOption('phraselimit', $maximum);

        return $this;
    }

    /**
     * Get phraseLimit option.
     *
     * @return int|null
     */
    public function getPhraseLimit(): ?int
    {
        return $this->getOption('phraselimit');
    }

    /**
     * Set multiValuedSeparatorChar option.
     *
     * Text to use to separate one value from the next for a multi-valued field.
     *
     * @param string $separator
     *
     * @return self Provides fluent interface
     */
    public function setMultiValuedSeparatorChar(string $separator): HighlightingInterface
    {
        $this->setOption('multivaluedseparatorchar', $separator);

        return $this;
    }

    /**
     * Get multiValuedSeparatorChar option.
     *
     * @return string|null
     */
    public function getMultiValuedSeparatorChar(): ?string
    {
        return $this->getOption('multivaluedseparatorchar');
    }
}
