<?php

namespace Solarium\Core\Query;

use Solarium\Core\Client\Request;
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
        $request->addParam('timeAllowed', $query->getTimeAllowed());
        $request->addParam('NOW', $query->getNow());
        $request->addParam('TZ', $query->getTimeZone());
        $request->addParams($query->getParams());

        $request->addParam('wt', $query->getResponseWriter());
        if ($query::WT_JSON === $query->getResponseWriter()) {
            // Only flat JSON format is supported. Other JSON formats are easier to handle but might loose information.
            $request->addParam('json.nl', 'flat');
        }

        $isServerQuery = ($query instanceof AbstractServerQuery);
        $request->setIsServerRequest($isServerQuery);

        return $request;
    }

    /**
     * Render a param with localParams.
     *
     * LocalParams can be use in various Solr GET params.
     *
     * @see http://wiki.apache.org/solr/LocalParams
     *
     * @param string $value
     * @param array  $localParams in key => value format
     *
     * @return string with Solr localparams syntax
     */
    public function renderLocalParams(string $value, array $localParams = []): string
    {
        $params = '';
        foreach ($localParams as $paramName => $paramValue) {
            if (empty($paramValue)) {
                continue;
            }

            if (is_array($paramValue)) {
                $paramValue = implode($paramValue, ',');
            }

            $params .= $paramName.'='.$paramValue.' ';
        }

        if ('' !== $params) {
            $value = '{!'.trim($params).'}'.$value;
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
     * Uses lazy loading: the helper is instantiated on first use
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
