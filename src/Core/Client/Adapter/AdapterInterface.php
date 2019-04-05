<?php

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\ConfigurableInterface;

/**
 * Interface for client adapters.
 *
 * The goal of an adapter is to accept a query, execute it and return the right
 * result object. This is actually quite a complex task as it involves the
 * handling of all Solr communication.
 *
 * The adapter structure allows for varying implementations of this task.
 *
 * Most adapters will use some sort of HTTP client. In that case the
 * query request builders and query response parsers can be used to simplify
 * HTTP communication.
 *
 * However an adapter may also implement all logic by itself if needed.
 */
interface AdapterInterface extends ConfigurableInterface
{
    /**
     * Execute a request.
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return Response
     */
    public function execute(Request $request, Endpoint $endpoint): Response;
}
