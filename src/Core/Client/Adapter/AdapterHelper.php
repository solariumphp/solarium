<?php

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;

/**
 * Helper class for shared adapter functionality.
 */
class AdapterHelper
{
    public static function buildUri(Request $request, Endpoint $endpoint): string
    {
        if (Request::API_V2 == $request->getApi()) {
            $baseUri = $endpoint->getV2BaseUri();
        } elseif ($request->getIsServerRequest()) {
            $baseUri = $endpoint->getV1BaseUri();
        } else {
            $baseUri = $endpoint->getBaseUri();
        }
        return $baseUri.$request->getUri();
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
        $baseName = basename($request->getFileUpload());
        $body = "--{$request->getHash()}\r\n";
        $body .= 'Content-Disposition: form-data; name="file"; filename="'.$baseName.'"';
        $body .= "\r\nContent-Type: application/octet-stream\r\n\r\n";
        $body .= file_get_contents($request->getFileUpload(), 'r');
        $body .= "\r\n--{$request->getHash()}--\r\n";

        return $body;
    }
}
