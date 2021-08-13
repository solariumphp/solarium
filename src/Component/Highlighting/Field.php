<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Highlighting;

use Solarium\Core\Configurable;

/**
 * Highlighting per-field settings.
 *
 * @see https://solr.apache.org/guide/highlighting.html
 */
class Field extends Configurable
{
    /**
     * Value for fragmenter option gap.
     */
    const FRAGMENTER_GAP = 'gap';

    /**
     * Value for fragmenter option regex.
     */
    const FRAGMENTER_REGEX = 'regex';

    /**
     * Get name option.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getOption('name');
    }

    /**
     * Set name option.
     *
     * @param string $name
     *
     * @return self Provides fluent interface
     */
    public function setName(string $name): self
    {
        $this->setOption('name', $name);

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
}
