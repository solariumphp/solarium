<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Analytics;

use Solarium\Component\AbstractComponent;
use Solarium\Component\Analytics\Facet\ConfigurableInitTrait;
use Solarium\Component\Analytics\Facet\ObjectTrait;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\RequestBuilder\Analytics as RequestBuilder;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\ResponseParser\Analytics as ResponseParser;
use Solarium\Component\ResponseParser\ComponentParserInterface;

/**
 * Analytics Component.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://solr.apache.org/guide/analytics.html
 */
class Analytics extends AbstractComponent implements \JsonSerializable
{
    use ConfigurableInitTrait;
    use ObjectTrait;

    /**
     * An array of functions.
     *
     * @var array
     */
    private $functions = [];

    /**
     * An array of expressions.
     *
     * @var array
     */
    private $expressions = [];

    /**
     * An array of groupings.
     *
     * @var \Solarium\Component\Analytics\Grouping[]
     */
    private $groupings = [];

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_ANALYTICS;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseParser(): ?ComponentParserInterface
    {
        return new ResponseParser();
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * @param array $functions
     *
     * @return $this
     */
    public function setFunctions(array $functions): self
    {
        foreach ($functions as $key => $function) {
            $this->addFunction($key, $function);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string $function
     *
     * @return $this
     */
    public function addFunction(string $key, string $function): self
    {
        $this->functions[$key] = $function;

        return $this;
    }

    /**
     * @return array
     */
    public function getExpressions(): array
    {
        return $this->expressions;
    }

    /**
     * @param array $expressions
     *
     * @return $this
     */
    public function setExpressions(array $expressions): self
    {
        foreach ($expressions as $key => $expression) {
            $this->addExpression($key, $expression);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string $expression
     *
     * @return $this
     */
    public function addExpression(string $key, string $expression): self
    {
        $this->expressions[$key] = $expression;

        return $this;
    }

    /**
     * @return \Solarium\Component\Analytics\Grouping[]
     */
    public function getGroupings(): array
    {
        return $this->groupings;
    }

    /**
     * @param \Solarium\Component\Analytics\Grouping[] $groupings
     *
     * @return $this
     */
    public function setGroupings(array $groupings): self
    {
        foreach ($groupings as $grouping) {
            $this->addGrouping($this->ensureObject(Grouping::class, $grouping));
        }

        return $this;
    }

    /**
     * @param \Solarium\Component\Analytics\Grouping $grouping
     *
     * @return $this
     */
    public function addGrouping(Grouping $grouping): self
    {
        $this->groupings[$grouping->getKey()] = $grouping;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter([
            'functions' => $this->functions,
            'expressions' => $this->expressions,
            'groupings' => $this->groupings,
        ]);
    }
}
