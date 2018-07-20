<?php

namespace Solarium\Component\RequestBuilder;

/**
 * Class for describing a sub request.
 */
class SubRequest implements RequestParamsInterface
{
    use RequestParamsTrait;

    /**
     * Request params.
     *
     * Multivalue params are supported using a multidimensional array:
     * 'fq' => array('cat:1','published:1')
     *
     * @var array
     */
    protected $queryParser = 'rerank';

    /**
     * Get query parser.
     *
     * @return string
     */
    public function getQueryParser()
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
    public function setQueryParser(string $value)
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
    public function getSubQuery($separator = ' ')
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
