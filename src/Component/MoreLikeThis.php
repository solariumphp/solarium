<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\ComponentTraits\MoreLikeThisTrait;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\MoreLikeThis as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\MoreLikeThis as ResponseParser;

/**
 * MoreLikeThis component.
 *
 * @see https://solr.apache.org/guide/morelikethis.html
 */
class MoreLikeThis extends AbstractComponent implements MoreLikeThisInterface
{
    use MoreLikeThisTrait;

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS;
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
     * Set fields option.
     *
     * The fields to use for similarity. NOTE: if possible, these should have a
     * stored TermVector.
     *
     * When using string input you can separate multiple fields with commas.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param string|array $fields
     *
     * @return self Provides fluent interface
     */
    public function setFields($fields): self
    {
        if (\is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        $this->setOption('fields', $fields);

        return $this;
    }

    /**
     * Get fields option.
     *
     * @return array
     */
    public function getFields(): array
    {
        $fields = $this->getOption('fields');
        if (null === $fields) {
            $fields = [];
        }

        return $fields;
    }

    /**
     * Set count option.
     *
     * The number of similar documents to return for each result.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#search-component-parameters
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setCount(int $count): self
    {
        $this->setOption('count', $count);

        return $this;
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
     * Set the match.include parameter, which is either 'true' or 'false'.
     *
     * This doesn't actually do anything for the MoreLikeThisComponent as
     * this parameter is only for the MoreLikeThisHandler.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#request-handler-parameters
     *
     * @param bool $include
     *
     * @return self Provides fluent interface
     *
     * @deprecated Will be removed in Solarium 8. This parameter is only accessible through the MoreLikeThisHandler.
     */
    public function setMatchInclude(bool $include): self
    {
        return $this;
    }

    /**
     * Get the match.include parameter.
     *
     * This always returns null for the MoreLikeThisComponent as
     * this parameter is only for the MoreLikeThisHandler.
     *
     * @return bool|null
     *
     * @deprecated Will be removed in Solarium 8. This parameter is only accessible through the MoreLikeThisHandler.
     */
    public function getMatchInclude(): ?bool
    {
        return null;
    }

    /**
     * Set the mlt.match.offset parameter.
     *
     * This doesn't actually do anything for the MoreLikeThisComponent as
     * this parameter is only for the MoreLikeThisHandler.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#request-handler-parameters
     *
     * @param int $offset
     *
     * @return self Provides fluent interface
     *
     * @deprecated Will be removed in Solarium 8. This parameter is only accessible through the MoreLikeThisHandler.
     */
    public function setMatchOffset(int $offset): self
    {
        return $this;
    }

    /**
     * Get the mlt.match.offset parameter.
     *
     * This always returns null for the MoreLikeThisComponent as
     * this parameter is only for the MoreLikeThisHandler.
     *
     * @return int|null
     *
     * @deprecated Will be removed in Solarium 8. This parameter is only accessible through the MoreLikeThisHandler.
     */
    public function getMatchOffset(): ?int
    {
        return null;
    }

    /**
     * Initialize options.
     *
     * {@internal Options that set a list of fields need additional setup work
     *            because they can be an array or a comma separated string.}
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'fields':
                    $this->setFields($value);
                    break;
                case 'queryfields':
                    $this->setQueryFields($value);
                    break;
            }
        }
    }
}
