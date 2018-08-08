<?php

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Client\Request;

/**
 * Helper class for shared adapter functionality.
 */
class AdapterHelper
{
    /**
     * This method is used to build the upload body for a file upload with the boundary markers.
     *
     * @param Request $request
     *
     * @return string
     */
    public function buildUploadBodyFromRequest(Request $request)
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
