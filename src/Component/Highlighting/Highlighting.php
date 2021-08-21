<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Highlighting;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\Highlighting as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\Highlighting as ResponseParser;
use Solarium\Exception\InvalidArgumentException;

/**
 * Highlighting component.
 *
 * @see https://solr.apache.org/guide/highlighting.html
 */
class Highlighting extends AbstractComponent implements QueryInterface
{
    use QueryTrait;

    /**
     * Value for fragmenter option gap.
     */
    const FRAGMENTER_GAP = 'gap';

    /**
     * Value for fragmenter option regex.
     */
    const FRAGMENTER_REGEX = 'regex';

    /**
     * Value for BoundaryScanner type.
     */
    const BOUNDARYSCANNER_TYPE_CHARACTER = 'CHARACTER';

    /**
     * Value for BoundaryScanner type.
     */
    const BOUNDARYSCANNER_TYPE_WORD = 'WORD';

    /**
     * Value for BoundaryScanner type.
     */
    const BOUNDARYSCANNER_TYPE_SENTENCE = 'SENTENCE';

    /**
     * Value for BoundaryScanner type.
     */
    const BOUNDARYSCANNER_TYPE_LINE = 'LINE';

    /**
     * Array of fields for highlighting.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_HIGHLIGHTING;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ?ComponentParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Get a field options object.
     *
     * @param string $name
     * @param bool   $autocreate
     *
     * @return Field|null
     */
    public function getField($name, $autocreate = true): ?Field
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        if ($autocreate) {
            $this->addField($name);

            return $this->fields[$name];
        }

        return null;
    }

    /**
     * Add a field for highlighting.
     *
     * @param string|array|Field $field
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addField($field): self
    {
        // autocreate object for string input
        if (\is_string($field)) {
            $field = new Field(['name' => $field]);
        } elseif (\is_array($field)) {
            $field = new Field($field);
        }

        // validate field
        if (null === $field->getName()) {
            throw new InvalidArgumentException('To add a highlighting field it needs to have at least a "name" setting');
        }

        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * Add multiple fields for highlighting.
     *
     * @param string|array $fields can be an array of object instances or a string with comma
     *                             separated fieldnames
     *
     * @return self Provides fluent interface
     */
    public function addFields($fields): self
    {
        if (\is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        foreach ($fields as $key => $field) {
            // in case of a config array without key: add key to config
            if (\is_array($field) && !isset($field['name'])) {
                $field['name'] = $key;
            }

            $this->addField($field);
        }

        return $this;
    }

    /**
     * Remove a highlighting field.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function removeField(string $field): self
    {
        if (isset($this->fields[$field])) {
            unset($this->fields[$field]);
        }

        return $this;
    }

    /**
     * Remove all fields.
     *
     * @return self Provides fluent interface
     */
    public function clearFields(): self
    {
        $this->fields = [];

        return $this;
    }

    /**
     * Get the list of fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Set multiple fields.
     *
     * This overwrites any existing fields
     *
     * @param string|array $fields can be an array of object instances or a string with comma
     *                             separated fieldnames
     *
     * @return self Provides fluent interface
     */
    public function setFields($fields): self
    {
        $this->clearFields();
        $this->addFields($fields);

        return $this;
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
    public function setSnippets(int $maximum): self
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
    public function setFragSize(int $size): self
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
     * Set mergeContiguous option.
     *
     * Collapse contiguous fragments into a single fragment
     *
     * @param bool $merge
     *
     * @return self Provides fluent interface
     */
    public function setMergeContiguous(bool $merge): self
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
     * Set requireFieldMatch option.
     *
     * @param bool $require
     *
     * @return self Provides fluent interface
     */
    public function setRequireFieldMatch(bool $require): self
    {
        $this->setOption('requirefieldmatch', $require);

        return $this;
    }

    /**
     * Get requireFieldMatch option.
     *
     * @return bool|null
     */
    public function getRequireFieldMatch(): ?bool
    {
        return $this->getOption('requirefieldmatch');
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
    public function setMaxAnalyzedChars(int $chars): self
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
     * Set alternatefield option.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function setAlternateField(string $field): self
    {
        $this->setOption('alternatefield', $field);

        return $this;
    }

    /**
     * Get alternatefield option.
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
    public function setMaxAlternateFieldLength(int $length): self
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
     * Set preserveMulti option.
     *
     * @param bool $preservemulti
     *
     * @return self Provides fluent interface
     */
    public function setPreserveMulti(bool $preservemulti): self
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
     * Set formatter option.
     *
     * @param string $formatter
     *
     * @return self Provides fluent interface
     */
    public function setFormatter(string $formatter = 'simple'): self
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
     * Solr option h1.simple.pre
     *
     * @param string $prefix
     *
     * @return self Provides fluent interface
     */
    public function setSimplePrefix(string $prefix): self
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
     * Solr option h1.simple.post
     *
     * @param string $postfix
     *
     * @return self Provides fluent interface
     */
    public function setSimplePostfix(string $postfix): self
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
     * Set tag prefix option.
     *
     * Solr option h1.tag.post
     *
     * @param string $prefix
     *
     * @return self Provides fluent interface
     */
    public function setTagPrefix(string $prefix): self
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
     * Solr option h1.tag.post
     *
     * @param string $postfix
     *
     * @return self Provides fluent interface
     */
    public function setTagPostfix(string $postfix): self
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
     * Set fragmenter option.
     *
     * Use one of the constants as value.
     *
     * @param string $fragmenter
     *
     * @return self Provides fluent interface
     */
    public function setFragmenter(string $fragmenter): self
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
     * Set fraglistbuilder option.
     *
     * @param string $builder
     *
     * @return self Provides fluent interface
     */
    public function setFragListBuilder(string $builder): self
    {
        $this->setOption('fraglistbuilder', $builder);

        return $this;
    }

    /**
     * Get fraglistbuilder option.
     *
     * @return string|null
     */
    public function getFragListBuilder(): ?string
    {
        return $this->getOption('fraglistbuilder');
    }

    /**
     * Set fragmentsbuilder option.
     *
     * @param string $builder
     *
     * @return self Provides fluent interface
     */
    public function setFragmentsBuilder(string $builder): self
    {
        $this->setOption('fragmentsbuilder', $builder);

        return $this;
    }

    /**
     * Get fragmentsbuilder option.
     *
     * @return string|null
     */
    public function getFragmentsBuilder(): ?string
    {
        return $this->getOption('fragmentsbuilder');
    }

    /**
     * Set useFastVectorHighlighter option.
     *
     * @param bool $use
     *
     * @return self Provides fluent interface
     */
    public function setUseFastVectorHighlighter(bool $use): self
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
     * Set usePhraseHighlighter option.
     *
     * @param bool $use
     *
     * @return self Provides fluent interface
     */
    public function setUsePhraseHighlighter(bool $use): self
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
     * Set HighlightMultiTerm option.
     *
     * @param bool $highlight
     *
     * @return self Provides fluent interface
     */
    public function setHighlightMultiTerm(bool $highlight): self
    {
        $this->setOption('highlightmultiterm', $highlight);

        return $this;
    }

    /**
     * Get HighlightMultiTerm option.
     *
     * @return bool|null
     */
    public function getHighlightMultiTerm(): ?bool
    {
        return $this->getOption('highlightmultiterm');
    }

    /**
     * Set RegexSlop option.
     *
     * @param float $slop
     *
     * @return self Provides fluent interface
     */
    public function setRegexSlop(float $slop): self
    {
        $this->setOption('regexslop', $slop);

        return $this;
    }

    /**
     * Get RegexSlop option.
     *
     * @return float|null
     */
    public function getRegexSlop(): ?float
    {
        return $this->getOption('regexslop');
    }

    /**
     * Set RegexPattern option.
     *
     * @param string $pattern
     *
     * @return self Provides fluent interface
     */
    public function setRegexPattern(string $pattern): self
    {
        $this->setOption('regexpattern', $pattern);

        return $this;
    }

    /**
     * Get RegexPattern option.
     *
     * @return string|null
     */
    public function getRegexPattern(): ?string
    {
        return $this->getOption('regexpattern');
    }

    /**
     * Set RegexMaxAnalyzedChars option.
     *
     * @param int $chars
     *
     * @return self Provides fluent interface
     */
    public function setRegexMaxAnalyzedChars(int $chars): self
    {
        $this->setOption('regexmaxanalyzedchars', $chars);

        return $this;
    }

    /**
     * Get RegexMaxAnalyzedChars option.
     *
     * @return int|null
     */
    public function getRegexMaxAnalyzedChars(): ?int
    {
        return $this->getOption('regexmaxanalyzedchars');
    }

    /**
     * Set phraselimit option.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setPhraseLimit(int $maximum): self
    {
        $this->setOption('phraselimit', $maximum);

        return $this;
    }

    /**
     * Get phraselimit option.
     *
     * @return int|null
     */
    public function getPhraseLimit(): ?int
    {
        return $this->getOption('phraselimit');
    }

    /**
     * Set MultiValuedSeparatorChar option.
     *
     * @param string $separator
     *
     * @return self Provides fluent interface
     */
    public function setMultiValuedSeparatorChar(string $separator): self
    {
        $this->setOption('multivaluedseparatorchar', $separator);

        return $this;
    }

    /**
     * Get MultiValuedSeparatorChar option.
     *
     * @return string|null
     */
    public function getMultiValuedSeparatorChar(): ?string
    {
        return $this->getOption('multivaluedseparatorchar');
    }

    /**
     * Set boundaryscannermaxscan option.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerMaxScan(int $maximum): self
    {
        $this->setOption('boundaryscannermaxscan', $maximum);

        return $this;
    }

    /**
     * Get boundaryscannermaxscan option.
     *
     * @return int|null
     */
    public function getBoundaryScannerMaxScan(): ?int
    {
        return $this->getOption('boundaryscannermaxscan');
    }

    /**
     * Set boundaryscannerchars option.
     *
     * @param string $chars
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerChars(string $chars): self
    {
        $this->setOption('boundaryscannerchars', $chars);

        return $this;
    }

    /**
     * Get boundaryscannerchars option.
     *
     * @return string|null
     */
    public function getBoundaryScannerChars(): ?string
    {
        return $this->getOption('boundaryscannerchars');
    }

    /**
     * Set boundaryscannertype option.
     *
     * @param string $type
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerType(string $type): self
    {
        $this->setOption('boundaryscannertype', $type);

        return $this;
    }

    /**
     * Get boundaryscannertype option.
     *
     * @return string|null
     */
    public function getBoundaryScannerType(): ?string
    {
        return $this->getOption('boundaryscannertype');
    }

    /**
     * Set boundaryscannerlanguage option.
     *
     * @param string $language
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerLanguage(string $language): self
    {
        $this->setOption('boundaryscannerlanguage', $language);

        return $this;
    }

    /**
     * Get boundaryscannerlanguage option.
     *
     * @return string|null
     */
    public function getBoundaryScannerLanguage(): ?string
    {
        return $this->getOption('boundaryscannerlanguage');
    }

    /**
     * Set boundaryscannercountry option.
     *
     * @param string $country
     *
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerCountry(string $country): self
    {
        $this->setOption('boundaryscannercountry', $country);

        return $this;
    }

    /**
     * Get boundaryscannercountry option.
     *
     * @return string|null
     */
    public function getBoundaryScannerCountry(): ?string
    {
        return $this->getOption('boundaryscannercountry');
    }

    /**
     * Set method option.
     *
     * @param string $method
     *
     * @return self Provides fluent interface
     */
    public function setMethod(string $method): self
    {
        $this->setOption('method', $method);

        return $this;
    }

    /**
     * Get method option.
     *
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->getOption('method');
    }

    /**
     * Initialize options.
     *
     * The field option needs setup work
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'field':
                    $this->addFields($value);
                    break;
            }
        }
    }
}
