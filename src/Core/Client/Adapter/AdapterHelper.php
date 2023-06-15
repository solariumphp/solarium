<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Exception\HttpException;
use Solarium\Exception\UnexpectedValueException;

/**
 * Helper class for shared adapter functionality.
 */
class AdapterHelper
{
    /**
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @throws HttpException
     *
     * @return string
     */
    public static function buildUri(Request $request, Endpoint $endpoint): string
    {
        try {
            if (Request::API_V2 === $request->getApi()) {
                $baseUri = $endpoint->getV2BaseUri();
            } elseif ($request->getIsServerRequest()) {
                $baseUri = $endpoint->getV1BaseUri();
            } else {
                $baseUri = $endpoint->getBaseUri();
            }

            return $baseUri.$request->getUri();
        } catch (UnexpectedValueException $e) {
            // Backward compatibility: getBaseUri() now throws an UnexpectedValueException and we don't send a request.
            // In previous version we sent the request which resulted in HttpException.
            throw new HttpException($e->getMessage(), 404, $e->getTraceAsString());
        }
    }

    /**
     * This method is used to build the upload body for a file upload with the boundary markers.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function buildUploadBodyFromRequest(Request $request): string
    {
        $file = $request->getFileUpload();

        if (\is_resource($file)) {
            $baseName = basename(stream_get_meta_data($file)['uri']);
        } else {
            $baseName = basename($file);
        }

        $body = "--{$request->getHash()}\r\n";
        $body .= 'Content-Disposition: form-data; name="file"; filename="'.$baseName.'"';
        $body .= "\r\nContent-Type: ".Request::CONTENT_TYPE_APPLICATION_OCTET_STREAM."\r\n\r\n";

        if (\is_resource($file)) {
            rewind($file);
            $body .= stream_get_contents($file);
        } else {
            $body .= file_get_contents($file);
        }

        $body .= "\r\n--{$request->getHash()}--\r\n";

        return $body;
    }
}
