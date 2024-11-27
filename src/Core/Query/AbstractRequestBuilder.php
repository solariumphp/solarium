<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\LocalParameters\LocalParameter;
use Solarium\QueryType\Server\AbstractServerQuery;

/**
 * Class for building Solarium client requests.
 */
abstract class AbstractRequestBuilder implements RequestBuilderInterface
{
    /**
     * Helper instance.
     *
     * @var Helper
     */
    protected $helper;

    /**
     * Build request for a select query.
     *
     * @param AbstractQuery|QueryInterface $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = new Request();
        $request->setHandler($query->getHandler());
        $request->addParam('distrib', $query->getDistrib());
        $request->addParam('omitHeader', $query->getOmitHeader());
        $request->addParam('NOW', $query->getNow());
        $request->addParam('TZ', $query->getTimeZone());
        $request->addParam('ie', $query->getInputEncoding());
        $request->addParams($query->getParams());

        $request->addParam('wt', $query->getResponseWriter());
        if ($query::WT_JSON === $query->getResponseWriter()) {
            // Only flat JSON format is supported. Other JSON formats are easier to handle but might lose information.
            $request->addParam('json.nl', 'flat');
        }

        $isServerQuery = $query instanceof AbstractServerQuery;
        $request->setIsServerRequest($isServerQuery);

        return $request;
    }

    /**
     * Render a param with localParams.
     *
     * LocalParams can be use in various Solr GET params.
     *
     * @see https://solr.apache.org/guide/local-parameters-in-queries.html
     *
     * @param string $value
     * @param array  $localParams in key => value format
     *
     * @return string with Solr localparams syntax
     */
    public function renderLocalParams(string $value, array $localParams = []): string
    {
        $params = '';
        $helper = $this->getHelper();

        if (str_starts_with($value, '{!')) {
            $params = substr($value, 2, strpos($value, '}') - 2).' ';
            $value = substr($value, strpos($value, '}') + 1);
        }

        foreach ($localParams as $paramName => $paramValue) {
            if (null === $paramValue || '' === $paramValue || [] === $paramValue) {
                continue;
            }

            if (\is_array($paramValue)) {
                $paramValue = implode(',', $paramValue);
            } elseif (\is_bool($paramValue)) {
                $paramValue = $paramValue ? 'true' : 'false';
            }

            if (LocalParameter::isSplitSmart($paramName)) {
                $paramValue = $helper->escapeLocalParamValue($paramValue, ',');
            } else {
                $paramValue = $helper->escapeLocalParamValue($paramValue);
            }

            $params .= $paramName.'='.$paramValue.' ';
        }

        if ('' !== $params = trim($params)) {
            $value = sprintf('{!%s}%s', $params, $value);
        }

        return $value;
    }

    /**
     * Render a boolean attribute.
     *
     * For use in building XML messages
     *
     * @param string    $name
     * @param bool|null $value
     *
     * @return string
     */
    public function boolAttrib(string $name, ?bool $value): string
    {
        if (null !== $value) {
            $stringValue = (true === (bool) $value) ? 'true' : 'false';

            return $this->attrib($name, $stringValue);
        }

        return '';
    }

    /**
     * Render an attribute.
     *
     * For use in building XML messages
     *
     * @param string      $name
     * @param string|null $value
     *
     * @return string
     */
    public function attrib(string $name, ?string $value): string
    {
        if (null !== $value) {
            return ' '.$name.'="'.$value.'"';
        }

        return '';
    }

    /**
     * Get a helper instance.
     *
     * Uses lazy loading: the helper is instantiated on first use.
     *
     * @return Helper
     */
    public function getHelper(): Helper
    {
        if (null === $this->helper) {
            $this->helper = new Helper();
        }

        return $this->helper;
    }
}
