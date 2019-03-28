<?php

namespace Solarium\Component\ComponentTraits;

/**
 * Spellcheck Component Trait.
 */
trait SpellcheckTrait
{
    /**
     * Used to further customize collation parameters.
     *
     * @var array
     */
    protected $collateParams = [];

    /**
     * Set build option.
     *
     * Build the spellcheck?
     *
     * @param bool $build
     *
     * @return self Provides fluent interface
     */
    public function setBuild(bool $build): self
    {
        return $this->setOption('build', $build);
    }

    /**
     * Get build option.
     *
     * @return bool|null
     */
    public function getBuild()
    {
        return $this->getOption('build');
    }

    /**
     * Set reload option.
     *
     * Reload the dictionary?
     *
     * @param bool $reload
     *
     * @return self Provides fluent interface
     */
    public function setReload(bool $reload): self
    {
        return $this->setOption('reload', $reload);
    }

    /**
     * Get fragsize option.
     *
     * @return bool|null
     */
    public function getReload(): ?bool
    {
        return $this->getOption('reload');
    }

    /**
     * Set dictionary option.
     *
     * The name of the dictionary to use
     *
     * @param string $dictionary
     *
     * @return self Provides fluent interface
     */
    public function setDictionary(string $dictionary): self
    {
        return $this->setOption('dictionary', $dictionary);
    }

    /**
     * Get dictionary option.
     *
     * @return string|null
     */
    public function getDictionary(): ?string
    {
        return $this->getOption('dictionary');
    }

    /**
     * Set count option.
     *
     * The maximum number of suggestions to return
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setCount(int $count): self
    {
        return $this->setOption('count', $count);
    }

    /**
     * Get count option.
     *
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->getOption('count');
    }

    /**
     * Set onlyMorePopular option.
     *
     * Only return suggestions that result in more hits for the query than the existing query
     *
     * @param bool $onlyMorePopular
     *
     * @return self Provides fluent interface
     */
    public function setOnlyMorePopular(bool $onlyMorePopular): self
    {
        return $this->setOption('onlymorepopular', $onlyMorePopular);
    }

    /**
     * Get onlyMorePopular option.
     *
     * @return bool|null
     */
    public function getOnlyMorePopular(): ?bool
    {
        return $this->getOption('onlymorepopular');
    }

    /**
     * Set extendedResults option.
     *
     * @param bool $extendedResults
     *
     * @return self Provides fluent interface
     */
    public function setExtendedResults(bool $extendedResults): self
    {
        return $this->setOption('extendedresults', $extendedResults);
    }

    /**
     * Get extendedResults option.
     *
     * @return bool|null
     */
    public function getExtendedResults(): ?bool
    {
        return $this->getOption('extendedresults');
    }

    /**
     * Set collate option.
     *
     * @param bool $collate
     *
     * @return self Provides fluent interface
     */
    public function setCollate(bool $collate): self
    {
        return $this->setOption('collate', $collate);
    }

    /**
     * Get collate option.
     *
     * @return bool|null
     */
    public function getCollate(): ?bool
    {
        return $this->getOption('collate');
    }

    /**
     * Set maxCollations option.
     *
     * @param int $maxCollations
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollations(int $maxCollations): self
    {
        return $this->setOption('maxcollations', $maxCollations);
    }

    /**
     * Get maxCollations option.
     *
     * @return int|null
     */
    public function getMaxCollations(): ?int
    {
        return $this->getOption('maxcollations');
    }

    /**
     * Set maxCollationTries option.
     *
     * @param string $maxCollationTries
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollationTries(string $maxCollationTries): self
    {
        return $this->setOption('maxcollationtries', $maxCollationTries);
    }

    /**
     * Get maxCollationTries option.
     *
     * @return string|null
     */
    public function getMaxCollationTries(): ?string
    {
        return $this->getOption('maxcollationtries');
    }

    /**
     * Set maxCollationEvaluations option.
     *
     * @param int $maxCollationEvaluations
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollationEvaluations(int $maxCollationEvaluations): self
    {
        return $this->setOption('maxcollationevaluations', $maxCollationEvaluations);
    }

    /**
     * Get maxCollationEvaluations option.
     *
     * @return int|null
     */
    public function getMaxCollationEvaluations(): ?int
    {
        return $this->getOption('maxcollationevaluations');
    }

    /**
     * Set collateExtendedResults option.
     *
     * @param string $collateExtendedResults
     *
     * @return self Provides fluent interface
     */
    public function setCollateExtendedResults(string $collateExtendedResults): self
    {
        return $this->setOption('collateextendedresults', $collateExtendedResults);
    }

    /**
     * Get collateExtendedResults option.
     *
     * @return string|null
     */
    public function getCollateExtendedResults(): ?string
    {
        return $this->getOption('collateextendedresults');
    }

    /**
     * Set accuracy option.
     *
     * @param float $accuracy
     *
     * @return self Provides fluent interface
     */
    public function setAccuracy(float $accuracy): self
    {
        return $this->setOption('accuracy', $accuracy);
    }

    /**
     * Get accuracy option.
     *
     * @return float|null
     */
    public function getAccuracy(): ?float
    {
        return $this->getOption('accuracy');
    }

    /**
     * Set a collation param.
     *
     * @param string $param
     * @param mixed  $value
     *
     * @return self Provides fluent interface
     */
    public function setCollateParam(string $param, $value): self
    {
        $this->collateParams[$param] = $value;

        return $this;
    }

    /**
     * Returns the array of collate params.
     *
     * @return array
     */
    public function getCollateParams(): array
    {
        return $this->collateParams;
    }
}
