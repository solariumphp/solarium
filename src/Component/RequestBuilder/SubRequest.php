<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;

/**
 * Class for describing a sub request.
 */
class SubRequest extends BaseRequestBuilder implements RequestParamsInterface
{
    use RequestParamsTrait;

    /**
     * Query parser.
     *
     * @var string
     */
    protected $queryParser = 'rerank';

    /**
     * Get query parser.
     *
     * @return string
     */
    public function getQueryParser(): string
    {
        return $this->queryParser;
    }

    /**
     * Set query parser.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setQueryParser(string $value): self
    {
        $this->queryParser = $value;

        return $this;
    }

    /**
     * returns the complete sub request as string.
     *
     * @return string
     */
    public function getSubQuery(): string
    {
        $queryString = '';
        $params = $this->getParams();

        if (0 !== \count($params)) {
            $queryString = $this->getHelper()->qparser(
                $this->getQueryParser(),
                $params
            );
        }

        return $queryString;
    }
}
