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
     * Build request for a select query.
     *
     * @param QueryInterface|Query $query
     *
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        $request = new Request();
        $request->setHandler($query->getHandler());
        $request->addParam('omitHeader', $query->getOmitHeader());
        $request->addParam('timeAllowed', $query->getTimeAllowed());
        $request->addParam('NOW', $query->getNow());
        $request->addParam('TZ', $query->getTimeZone());
        $request->addParams($query->getParams());

        $request->addParam('wt', $query->getResponseWriter());
        if ($query->getResponseWriter() == $query::WT_JSON) {
            // only one JSON format is supported
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
    public function renderLocalParams($value, $localParams = [])
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
     * @param string $name
     * @param bool   $value
     *
     * @return string
     */
    public function boolAttrib($name, $value)
    {
        if (null !== $value) {
            $value = (true === (bool) $value) ? 'true' : 'false';

            return $this->attrib($name, $value);
        }

        return '';
    }

    /**
     * Render an attribute.
     *
     * For use in building XML messages
     *
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    public function attrib($name, $value)
    {
        if (null !== $value) {
            return ' '.$name.'="'.$value.'"';
        }

        return '';
    }
}
