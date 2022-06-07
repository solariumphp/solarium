<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Highlighting;

/**
 * Highlighting interface.
 */
interface HighlightingInterface
{
    /**
     * Unified Highlighter.
     */
    const METHOD_UNIFIED = 'unified';

    /**
     * Original Highlighter.
     */
    const METHOD_ORIGINAL = 'original';

    /**
     * FastVector Highlighter.
     */
    const METHOD_FASTVECTOR = 'fastVector';

    /**
     * HTML/XML encoder.
     */
    const ENCODER_HTML = 'html';

    /**
     * Run the stored text through the analysis chain for detecting offsets.
     */
    const OFFSETSOURCE_ANALYSIS = 'ANALYSIS';

    /**
     * Look up offsets from postings.
     */
    const OFFSETSOURCE_POSTINGS = 'POSTINGS';

    /**
     * Look up offsets from postings with "light" term vectors.
     */
    const OFFSETSOURCE_POSTINGS_WITH_TERM_VECTORS = 'POSTINGS_WITH_TERM_VECTORS';

    /**
     * Look up offsets from "full" term vectors.
     */
    const OFFSETSOURCE_TERM_VECTORS = 'TERM_VECTORS';

    /**
     * Value for BoundaryScanner type.
     */
    const BOUNDARYSCANNER_TYPE_SEPARATOR = 'SEPARATOR';

    /**
     * Value for BoundaryScanner type.
     */
    const BOUNDARYSCANNER_TYPE_SENTENCE = 'SENTENCE';

    /**
     * Value for BoundaryScanner type.
     */
    const BOUNDARYSCANNER_TYPE_WORD = 'WORD';

    /**
     * Value for BoundaryScanner type.
     */
    const BOUNDARYSCANNER_TYPE_CHARACTER = 'CHARACTER';

    /**
     * Value for BoundaryScanner type.
     */
    const BOUNDARYSCANNER_TYPE_LINE = 'LINE';

    /**
     * Value for BoundaryScanner type.
     */
    const BOUNDARYSCANNER_TYPE_WHOLE = 'WHOLE';

    /**
     * Simple formatter.
     */
    const FORMATTER_SIMPLE = 'simple';

    /**
     * Gap fragmenter.
     */
    const FRAGMENTER_GAP = 'gap';

    /**
     * Regex fragmenter.
     */
    const FRAGMENTER_REGEX = 'regex';

    /**
     * Use a weighted snippet fragmenting algorithm.
     */
    const FRAGLISTBUILDER_WEIGHTED = 'weighted';

    /**
     * Return the entire field contents as a one snippet.
     */
    const FRAGLISTBUILDER_SINGLE = 'single';

    /**
     * Use a simple snippet fragmenting algorithm.
     */
    const FRAGLISTBUILDER_SIMPLE = 'simple';

    /**
     * Use the default fragments builder.
     */
    const FRAGMENTSBUILDER_DEFAULT = 'default';

    /**
     * Use the pre-configured colored fragments builder.
     */
    const FRAGMENTSBUILDER_COLORED = 'colored';

    /**
     * Use the breakIterator boundary scanner.
     */
    const BOUNDARYSCANNER_BREAKITERATOR = 'breakIterator';

    /**
     * Use the simple boundary scanner.
     */
    const BOUNDARYSCANNER_SIMPLE = 'simple';

    /**
     * Set useFastVectorHighlighter option.
     *
     * @param bool $use
     *
     * @return self Provides fluent interface
     *
     * @deprecated Use {@link setMethod()} for Solr 6.4 and higher
     */
    public function setUseFastVectorHighlighter(bool $use): HighlightingInterface;

    /**
     * Get useFastVectorHighlighter option.
     *
     * @return bool|null
     */
    public function getUseFastVectorHighlighter(): ?bool;

    /**
     * Set highlighter method.
     *
     * Use one of the METHOD_* constants as value.
     *
     * @param string $method
     *
     * @return self Provides fluent interface
     */
    public function setMethod(string $method): HighlightingInterface;

    /**
     * Get highlighter method.
     *
     * @return string|null
     */
    public function getMethod(): ?string;

    /**
     * Set usePhraseHighlighter option.
     *
     * @param bool $use
     *
     * @return self Provides fluent interface
     */
    public function setUsePhraseHighlighter(bool $use): HighlightingInterface;

    /**
     * Get usePhraseHighlighter option.
     *
     * @return bool|null
     */
    public function getUsePhraseHighlighter(): ?bool;

    /**
     * Set HighlightMultiTerm option.
     *
     * @param bool $highlight
     *
     * @return self Provides fluent interface
     */
    public function setHighlightMultiTerm(bool $highlight): HighlightingInterface;

    /**
     * Get HighlightMultiTerm option.
     *
     * @return bool|null
     */
    public function getHighlightMultiTerm(): ?bool;

    /**
     * Set snippets option.
     *
     * Maximum number of snippets per field
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setSnippets(int $maximum): HighlightingInterface;

    /**
     * Get snippets option.
     *
     * @return int|null
     */
    public function getSnippets(): ?int;

    /**
     * Set fragsize option.
     *
     * The size, in characters, of fragments to consider for highlighting
     *
     * @param int $size
     *
     * @return self Provides fluent interface
     */
    public function setFragSize(int $size): HighlightingInterface;

    /**
     * Get fragsize option.
     *
     * @return int|null
     */
    public function getFragSize(): ?int;

    /**
     * Set tag prefix option.
     *
     * Solr option hl.tag.post
     *
     * @param string $prefix
     *
     * @return self Provides fluent interface
     */
    public function setTagPrefix(string $prefix): HighlightingInterface;

    /**
     * Get tag prefix option.
     *
     * Solr option hl.tag.pre
     *
     * @return string|null
     */
    public function getTagPrefix(): ?string;

    /**
     * Set tag postfix option.
     *
     * Solr option hl.tag.post
     *
     * @param string $postfix
     *
     * @return self Provides fluent interface
     */
    public function setTagPostfix(string $postfix): HighlightingInterface;

    /**
     * Get tag postfix option.
     *
     * Solr option hl.tag.post
     *
     * @return string|null
     */
    public function getTagPostfix(): ?string;

    /**
     * Set encoder option.
     *
     * Use one of the ENCODER_* constants as value.
     *
     * @param string $encoder
     *
     * @return self Provides fluent interface
     */
    public function setEncoder(string $encoder): HighlightingInterface;

    /**
     * Get encoder option.
     *
     * @return string|null
     */
    public function getEncoder(): ?string;

    /**
     * Set maxAnalyzedChars option.
     *
     * How many characters into a document to look for suitable snippets
     *
     * @param int $chars
     *
     * @return self Provides fluent interface
     */
    public function setMaxAnalyzedChars(int $chars): HighlightingInterface;

    /**
     * Get maxAnalyzedChars option.
     *
     * @return int|null
     */
    public function getMaxAnalyzedChars(): ?int;

    /**
     * Set offsetSource option.
     *
     * Use one of the OFFSETSOURCE_* constants as value.
     *
     * @param string $offsetSource
     *
     * @return self Provides fluent interface
     */
    public function setOffsetSource(string $offsetSource): HighlightingInterface;

    /**
     * Get offsetSource option.
     *
     * @return string|null
     */
    public function getOffsetSource(): ?string;

    /**
     * Set fragAlignRatio option.
     *
     * Influences where the first highlighted text in a passage is positioned.
     *
     * @param float $fragAlignRatio
     *
     * @return self Provides fluent interface
     */
    public function setFragAlignRatio(float $fragAlignRatio): HighlightingInterface;

    /**
     * Get fragAlignRatio option.
     *
     * @return float|null
     */
    public function getFragAlignRatio(): ?float;

    /**
     * Set fragsizeIsMinimum option.
     *
     * @param bool $isMinimum
     *
     * @return self Provides fluent interface
     */
    public function setFragsizeIsMinimum(bool $isMinimum): HighlightingInterface;

    /**
     * Get fragsizeIsMinimum option.
     *
     * @return bool|null
     */
    public function getFragsizeIsMinimum(): ?bool;

    /**
     * Set tag.ellipsis option.
     *
     * @param string $ellipsis
     *
     * @return self Provides fluent interface
     */
    public function setTagEllipsis(string $ellipsis): HighlightingInterface;

    /**
     * Get tag.ellipsis option.
     *
     * @return string|null
     */
    public function getTagEllipsis(): ?string;

    /**
     * Set defaultSummary option.
     *
     * @param bool $defaultSummary
     *
     * @return self Provides fluent interface
     */
    public function setDefaultSummary(bool $defaultSummary): HighlightingInterface;

    /**
     * Get defaultSummary option.
     *
     * @return bool|null
     */
    public function getDefaultSummary(): ?bool;

    /**
     * Set score.k1 option.
     *
     * BM25 term frequency normalization parameter 'k1'.
     *
     * @param float $k1
     *
     * @return self Provides fluent interface
     */
    public function setScoreK1(float $k1): HighlightingInterface;

    /**
     * Get score.k1 option.
     *
     * @return float|null
     */
    public function getScoreK1(): ?float;

    /**
     * Set score.b option.
     *
     * BM25 length normalization parameter 'b'.
     *
     * @param float $b
     *
     * @return self Provides fluent interface
     */
    public function setScoreB(float $b): HighlightingInterface;

    /**
     * Get score.b option.
     *
     * @return float|null
     */
    public function getScoreB(): ?float;

    /**
     * Set score.pivot option.
     *
     * BM25 average passage length in characters.
     *
     * @param int $pivot
     *
     * @return self Provides fluent interface
     */
    public function setScorePivot(int $pivot): HighlightingInterface;

    /**
     * Get score.pivot option.
     *
     * @return int|null
     */
    public function getScorePivot(): ?int;

    /**
     * Set breakIterator boundary scanner language option.
     *
     * @param string $language
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerLanguage(string $language): HighlightingInterface;

    /**
     * Get breakIterator boundary scanner language option.
     *
     * @return string|null
     */
    public function getBoundaryScannerLanguage(): ?string;

    /**
     * Set breakIterator boundary scanner country option.
     *
     * @param string $country
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerCountry(string $country): HighlightingInterface;

    /**
     * Get breakIterator boundary scanner country option.
     *
     * @return string|null
     */
    public function getBoundaryScannerCountry(): ?string;

    /**
     * Set breakIterator boundary scanner variant option.
     *
     * @param string $variant
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerVariant(string $variant): HighlightingInterface;

    /**
     * Get breakIterator boundary scanner variant option.
     *
     * @return string|null
     */
    public function getBoundaryScannerVariant(): ?string;

    /**
     * Set breakIterator boundary scanner type option.
     *
     * Use one of the BOUNDARYSCANNER_TYPE_* constants as value.
     *
     * @param string $type
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerType(string $type): HighlightingInterface;

    /**
     * Get breakIterator boundary scanner type option.
     *
     * @return string|null
     */
    public function getBoundaryScannerType(): ?string;

    /**
     * Set breakIterator boundary scanner separator option.
     *
     * Indicates which character to break the text on with BOUNDARYSCANNER_TYPE_SEPARATOR.
     *
     * @param string $separator
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerSeparator(string $separator): HighlightingInterface;

    /**
     * Get breakIterator boundary scanner separator option.
     *
     * @return string|null
     */
    public function getBoundaryScannerSeparator(): ?string;

    /**
     * Set weightMatches option.
     *
     * Use Lucene's "Weight Matches" API instead of doing SpanQuery conversion.
     *
     * @param bool $weightMatches
     *
     * @return self Provides fluent interface
     */
    public function setWeightMatches(bool $weightMatches): HighlightingInterface;

    /**
     * Get weightMatches option.
     *
     * @return bool|null
     */
    public function getWeightMatches(): ?bool;

    /**
     * Set mergeContiguous option.
     *
     * Collapse contiguous fragments into a single fragment.
     *
     * @param bool $merge
     *
     * @return self Provides fluent interface
     */
    public function setMergeContiguous(bool $merge): HighlightingInterface;

    /**
     * Get mergeContiguous option.
     *
     * @return bool|null
     */
    public function getMergeContiguous(): ?bool;

    /**
     * Set maxMultiValuedToExamine option.
     *
     * Maximum number of entries in a multi-valued field to examine before stopping.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaxMultiValuedToExamine(int $maximum): HighlightingInterface;

    /**
     * Get maxMultiValuedToExamine option.
     *
     * @return int|null
     */
    public function getMaxMultiValuedToExamine(): ?int;

    /**
     * Set maxMultiValuedToMatch option.
     *
     * Maximum number of matches in a multi-valued field that are found before stopping.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaxMultiValuedToMatch(int $maximum): HighlightingInterface;

    /**
     * Get maxMultiValuedToMatch option.
     *
     * @return int|null
     */
    public function getMaxMultiValuedToMatch(): ?int;

    /**
     * Set alternateField option.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function setAlternateField(string $field): HighlightingInterface;

    /**
     * Get alternateField option.
     *
     * @return string|null
     */
    public function getAlternateField(): ?string;

    /**
     * Set maxAlternateFieldLength option.
     *
     * @param int $length
     *
     * @return self Provides fluent interface
     */
    public function setMaxAlternateFieldLength(int $length): HighlightingInterface;

    /**
     * Get maxAlternateFieldLength option.
     *
     * @return int|null
     */
    public function getMaxAlternateFieldLength(): ?int;

    /**
     * Set highlightAlternate option.
     *
     * @param bool $highlight
     *
     * @return self Provides fluent interface
     */
    public function setHighlightAlternate(bool $highlight): HighlightingInterface;

    /**
     * Get highlightAlternate option.
     *
     * @return bool|null
     */
    public function getHighlightAlternate(): ?bool;

    /**
     * Set formatter option.
     *
     * Use one of the FORMATTER_* constants as value.
     *
     * @param string $formatter
     *
     * @return self Provides fluent interface
     */
    public function setFormatter(string $formatter = HighlightingInterface::FORMATTER_SIMPLE): HighlightingInterface;

    /**
     * Get formatter option.
     *
     * @return string|null
     */
    public function getFormatter(): ?string;

    /**
     * Set simple prefix option.
     *
     * Solr option hl.simple.pre
     *
     * @param string $prefix
     *
     * @return self Provides fluent interface
     */
    public function setSimplePrefix(string $prefix): HighlightingInterface;

    /**
     * Get simple prefix option.
     *
     * Solr option hl.simple.pre
     *
     * @return string|null
     */
    public function getSimplePrefix(): ?string;

    /**
     * Set simple postfix option.
     *
     * Solr option hl.simple.post
     *
     * @param string $postfix
     *
     * @return self Provides fluent interface
     */
    public function setSimplePostfix(string $postfix): HighlightingInterface;

    /**
     * Get simple postfix option.
     *
     * Solr option hl.simple.post
     *
     * @return string|null
     */
    public function getSimplePostfix(): ?string;

    /**
     * Set fragmenter option.
     *
     * Use one of the FRAGMENTER_* constants as value.
     *
     * @param string $fragmenter
     *
     * @return self Provides fluent interface
     */
    public function setFragmenter(string $fragmenter): HighlightingInterface;

    /**
     * Get fragmenter option.
     *
     * @return string|null
     */
    public function getFragmenter(): ?string;

    /**
     * Set regex.slop option.
     *
     * @param float $slop
     *
     * @return self Provides fluent interface
     */
    public function setRegexSlop(float $slop): HighlightingInterface;

    /**
     * Get regex.slop option.
     *
     * @return float|null
     */
    public function getRegexSlop(): ?float;

    /**
     * Set regex.pattern option.
     *
     * @param string $pattern
     *
     * @return self Provides fluent interface
     */
    public function setRegexPattern(string $pattern): HighlightingInterface;

    /**
     * Get regex.pattern option.
     *
     * @return string|null
     */
    public function getRegexPattern(): ?string;

    /**
     * Set regex.maxAnalyzedChars option.
     *
     * @param int $chars
     *
     * @return self Provides fluent interface
     */
    public function setRegexMaxAnalyzedChars(int $chars): HighlightingInterface;

    /**
     * Get regex.maxAnalyzedChars option.
     *
     * @return int|null
     */
    public function getRegexMaxAnalyzedChars(): ?int;

    /**
     * Set preserveMulti option.
     *
     * @param bool $preservemulti
     *
     * @return self Provides fluent interface
     */
    public function setPreserveMulti(bool $preservemulti): HighlightingInterface;

    /**
     * Get preserveMulti option.
     *
     * @return bool|null
     */
    public function getPreserveMulti(): ?bool;

    /**
     * Set payloads option.
     *
     * @param bool $payloads
     *
     * @return self Provides fluent interface
     */
    public function setPayloads(bool $payloads): HighlightingInterface;

    /**
     * Get payloads option.
     *
     * @return bool|null
     */
    public function getPayloads(): ?bool;

    /**
     * Set fragListBuilder option.
     *
     * Use one of the FRAGLISTBUILDER_* constants as value.
     *
     * @param string $builder
     *
     * @return self Provides fluent interface
     */
    public function setFragListBuilder(string $builder): HighlightingInterface;

    /**
     * Get fragListBuilder option.
     *
     * @return string|null
     */
    public function getFragListBuilder(): ?string;

    /**
     * Set fragmentsBuilder option.
     *
     * Use one of the FRAGMENTSBUILDER_* constants or the name of your own fragments builder as value.
     *
     * @param string $builder
     *
     * @return self Provides fluent interface
     */
    public function setFragmentsBuilder(string $builder): HighlightingInterface;

    /**
     * Get fragmentsBuilder option.
     *
     * @return string|null
     */
    public function getFragmentsBuilder(): ?string;

    /**
     * Set boundaryScanner option.
     *
     * Use one of the BOUNDARYSCANNER_* constants as value.
     *
     * @param string $scanner
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScanner(string $scanner): HighlightingInterface;

    /**
     * Get boundaryScanner option.
     *
     * @return string|null
     */
    public function getBoundaryScanner(): ?string;

    /**
     * Set simple boundary scanner maxScan option.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerMaxScan(int $maximum): HighlightingInterface;

    /**
     * Get simple boundary scanner maxScan option.
     *
     * @return int|null
     */
    public function getBoundaryScannerMaxScan(): ?int;

    /**
     * Set simple boundary scanner cgars option.
     *
     * @param string $chars
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerChars(string $chars): HighlightingInterface;

    /**
     * Get simple boundary scanner cgars option.
     *
     * @return string|null
     */
    public function getBoundaryScannerChars(): ?string;

    /**
     * Set phraseLimit option.
     *
     * Maximum number of phrases to analyze when searching for the highest-scoring phrase.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setPhraseLimit(int $maximum): HighlightingInterface;

    /**
     * Get phraseLimit option.
     *
     * @return int|null
     */
    public function getPhraseLimit(): ?int;

    /**
     * Set multiValuedSeparatorChar option.
     *
     * Text to use to separate one value from the next for a multi-valued field.
     *
     * @param string $separator
     *
     * @return self Provides fluent interface
     */
    public function setMultiValuedSeparatorChar(string $separator): HighlightingInterface;

    /**
     * Get multiValuedSeparatorChar option.
     *
     * @return string|null
     */
    public function getMultiValuedSeparatorChar(): ?string;
}
