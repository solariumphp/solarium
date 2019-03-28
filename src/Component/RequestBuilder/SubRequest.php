<?php

namespace Solarium\Component\RequestBuilder;

/**
 * Class for describing a sub request.
 */
class SubRequest implements RequestParamsInterface
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
     * @param string $separator
     *
     * @return string
     */
    public function getSubQuery(string $separator = ' '): string
    {
        $queryString = '';
        foreach ($this->getParams() as $key => $value) {
            $queryString .= $separator.$key.'='.$value;
        }
        if ($queryString) {
            $queryString = '{!'.$this->getQueryParser().$queryString.'}';
        }

        return $queryString;
    }
}
