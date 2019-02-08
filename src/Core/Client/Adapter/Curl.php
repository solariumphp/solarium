<?php

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Configurable;
use Solarium\Exception\HttpException;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\RuntimeException;

/**
 * cURL HTTP adapter.
 *
 * @author Intervals <info@myintervals.com>
 */
class Curl extends Configurable implements AdapterInterface
{
    /**
     * Execute a Solr request using the cURL Http.
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return Response
     */
    public function execute($request, $endpoint)
    {
        return $this->getData($request, $endpoint);
    }

    /**
     * Get the response for a curl handle.
     *
     * @param resource $handle
     * @param string   $httpResponse
     *
     * @return Response
     */
    public function getResponse($handle, $httpResponse)
    {
        if (false !== $httpResponse && null !== $httpResponse) {
            $data = $httpResponse;
            $info = curl_getinfo($handle);
            $headers = [];
            $headers[] = 'HTTP/1.1 '.$info['http_code'].' OK';
        } else {
            $headers = [];
            $data = '';
        }

        $this->check($data, $headers, $handle);
        curl_close($handle);

        return new Response($data, $headers);
    }

    /**
     * Create curl handle for a request.
     *
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @throws InvalidArgumentException
     *
     * @return resource
     */
    public function createHandle($request, $endpoint)
    {
        $uri = AdapterHelper::buildUri($request, $endpoint);

        $method = $request->getMethod();
        $options = $this->createOptions($request, $endpoint);

        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $uri);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        if (!(function_exists('ini_get') && ini_get('open_basedir'))) {
            curl_setopt($handler, CURLOPT_FOLLOWLOCATION, true);
        }
        curl_setopt($handler, CURLOPT_TIMEOUT, $options['timeout']);
        curl_setopt($handler, CURLOPT_CONNECTTIMEOUT, $options['timeout']);

        if (null !== ($proxy = $this->getOption('proxy'))) {
            curl_setopt($handler, CURLOPT_PROXY, $proxy);
        }

        if (!isset($options['headers']['Content-Type'])) {
            if (Request::METHOD_GET == $method) {
                $options['headers']['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
            } else {
                $options['headers']['Content-Type'] = 'application/xml; charset=utf-8';
            }
        }

        // Try endpoint authentication first, fallback to request for backwards compatibility
        $authData = $endpoint->getAuthentication();
        if (empty($authData['username'])) {
            $authData = $request->getAuthentication();
        }

        if (!empty($authData['username']) && !empty($authData['password'])) {
            curl_setopt($handler, CURLOPT_USERPWD, $authData['username'].':'.$authData['password']);
            curl_setopt($handler, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        if (count($options['headers'])) {
            $headers = [];
            foreach ($options['headers'] as $key => $value) {
                $headers[] = $key.': '.$value;
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
        }

        if (Request::METHOD_POST == $method) {
            curl_setopt($handler, CURLOPT_POST, true);

            if ($request->getFileUpload()) {
                $data = AdapterHelper::buildUploadBodyFromRequest($request);
                curl_setopt($handler, CURLOPT_POSTFIELDS, $data);
            } else {
                curl_setopt($handler, CURLOPT_POSTFIELDS, $request->getRawData());
            }
        } elseif (Request::METHOD_GET == $method) {
            curl_setopt($handler, CURLOPT_HTTPGET, true);
        } elseif (Request::METHOD_HEAD == $method) {
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'HEAD');
        } elseif (Request::METHOD_DELETE == $method) {
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } elseif (Request::METHOD_PUT == $method) {
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'PUT');

            if ($request->getFileUpload()) {
                $data = AdapterHelper::buildUploadBodyFromRequest($request);
                curl_setopt($handler, CURLOPT_POSTFIELDS, $data);
            } else {
                curl_setopt($handler, CURLOPT_POSTFIELDS, $request->getRawData());
            }
        } else {
            throw new InvalidArgumentException("unsupported method: $method");
        }

        return $handler;
    }

    /**
     * Check result of a request.
     *
     *
     * @param string   $data
     * @param array    $headers
     * @param resource $handle
     *
     * @throws HttpException
     */
    public function check($data, $headers, $handle)
    {
        // if there is no data and there are no headers it's a total failure,
        // a connection to the host was impossible.
        if (empty($data) && 0 == count($headers)) {
            throw new HttpException('HTTP request failed, '.curl_error($handle));
        }
    }

    /**
     * Execute request.
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return Response
     */
    protected function getData($request, $endpoint)
    {
        $handle = $this->createHandle($request, $endpoint);
        $httpResponse = curl_exec($handle);

        return $this->getResponse($handle, $httpResponse);
    }

    /**
     * Initialization hook.
     *
     * Checks the availability of Curl_http
     *
     * @throws RuntimeException
     */
    protected function init()
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('cURL is not available, install it to use the CurlHttp adapter');
        }

        parent::init();
    }

    /**
     * Create http request options from request.
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return array
     */
    protected function createOptions($request, $endpoint)
    {
        $options = [
            'timeout' => $endpoint->getTimeout(),
        ];
        foreach ($request->getHeaders() as $headerLine) {
            list($header, $value) = explode(':', $headerLine);
            if ($header = trim($header)) {
                $options['headers'][$header] = trim($value);
            }
        }

        return $options;
    }
}
