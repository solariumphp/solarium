<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\ParallelExecution;

use Solarium\Core\Client\Adapter\ConnectionTimeoutAwareInterface;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Adapter\TimeoutAwareInterface;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Event\PostExecute as PostExecuteEvent;
use Solarium\Core\Event\PostExecuteRequest as PostExecuteRequestEvent;
use Solarium\Core\Event\PreExecute as PreExecuteEvent;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\Result;
use Solarium\Exception\HttpException;
use Solarium\Exception\RuntimeException;
use Solarium\Plugin\ParallelExecution\Event\ExecuteEnd as ExecuteEndEvent;
use Solarium\Plugin\ParallelExecution\Event\ExecuteStart as ExecuteStartEvent;

/**
 * ParallelExecution plugin.
 *
 * You can use this plugin to run multiple queries parallel. This functionality depends on the cURL adapter so you
 * do need to have cURL available in your PHP environment.
 *
 * While query execution is parallel, the results only become available as soon as all requests have finished. So the
 * time of the slowest query will be the effective execution time for all queries.
 */
class ParallelExecution extends AbstractPlugin
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'curlmultiselecttimeout' => 0.1,
    ];

    /**
     * Queries to execute coupled with the keys of the endpoints to execute them against.
     *
     * @var array
     */
    protected $queries = [];

    /**
     * Add a query to execute.
     *
     * @param string               $key
     * @param QueryInterface       $query
     * @param string|Endpoint|null $endpoint
     *
     * @return self Provides fluent interface
     */
    public function addQuery(string $key, QueryInterface $query, $endpoint = null): self
    {
        if (\is_object($endpoint)) {
            $endpoint = $endpoint->getKey();
        }

        if (null === $endpoint) {
            $endpoint = $this->client->getEndpoint()->getKey();
        }

        $this->queries[$key] = [
            'query' => $query,
            'endpoint' => $endpoint,
        ];

        return $this;
    }

    /**
     * Get queries and coupled endpoint keys.
     *
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * Clear all queries.
     *
     * @return self Provides fluent interface
     */
    public function clearQueries(): self
    {
        $this->queries = [];

        return $this;
    }

    /**
     * Execute queries parallelly.
     *
     * @throws RuntimeException
     *
     * @return (Result|HttpException)[]
     */
    public function execute(): array
    {
        // create handles and add all handles to the multihandle
        $adapter = $this->client->getAdapter();
        if (!($adapter instanceof Curl)) {
            throw new RuntimeException('Parallel execution requires the CurlAdapter');
        }

        $multiHandle = curl_multi_init();

        $requests = [];
        $endpoints = [];
        $handles = [];
        $overrideResponses = [];
        $overrideResults = [];

        foreach ($this->queries as $key => $data) {
            $event = new PreExecuteEvent($data['query']);
            $this->client->getEventDispatcher()->dispatch($event);
            if (null !== $result = $event->getResult()) {
                $overrideResults[$key] = $result;
                continue;
            }

            $requests[$key] = $this->client->createRequest($data['query']);
            $endpoints[$key] = $this->client->getEndpoint($data['endpoint']);

            $event = new PreExecuteRequestEvent($requests[$key], $endpoints[$key]);
            $this->client->getEventDispatcher()->dispatch($event);
            if (null !== $response = $event->getResponse()) {
                $overrideResponses[$key] = $response;
                continue;
            }

            $handle = $adapter->createHandle($requests[$key], $endpoints[$key]);
            curl_multi_add_handle($multiHandle, $handle);
            $handles[$key] = $handle;
        }

        // executing multihandle (all requests)
        $event = new ExecuteStartEvent();
        $this->client->getEventDispatcher()->dispatch($event);

        do {
            $mrc = curl_multi_exec($multiHandle, $active);
        } while (CURLM_CALL_MULTI_PERFORM === $mrc);

        $timeout = $this->getOption('curlmultiselecttimeout');
        while ($active && CURLM_OK === $mrc) {
            if (-1 === curl_multi_select($multiHandle, $timeout)) {
                // @codeCoverageIgnoreStart
                usleep(100);
                // @codeCoverageIgnoreEnd
            }

            do {
                $mrc = curl_multi_exec($multiHandle, $active);
            } while (CURLM_CALL_MULTI_PERFORM === $mrc);
        }

        while (false !== curl_multi_info_read($multiHandle)) {
            // ↑ this loops over messages from the individual transfers so we can get curl_errno() for each handle
        }

        $event = new ExecuteEndEvent();
        $this->client->getEventDispatcher()->dispatch($event);

        // get the results
        $results = [];

        foreach ($this->queries as $key => $data) {
            if (isset($overrideResults[$key])) {
                $results[$key] = $overrideResults[$key];
            } elseif (isset($overrideResponses[$key])) {
                $results[$key] = $this->client->createResult($data['query'], $overrideResponses[$key]);
            } else {
                try {
                    curl_multi_remove_handle($multiHandle, $handles[$key]);
                    $response = $adapter->getResponse($handles[$key], curl_multi_getcontent($handles[$key]));

                    $event = new PostExecuteRequestEvent($requests[$key], $endpoints[$key], $response);
                    $this->client->getEventDispatcher()->dispatch($event);

                    $results[$key] = $this->client->createResult($data['query'], $response);

                    $event = new PostExecuteEvent($data['query'], $results[$key]);
                    $this->client->getEventDispatcher()->dispatch($event);
                } catch (HttpException $e) {
                    $results[$key] = $e;
                }
            }
        }

        curl_multi_close($multiHandle);

        return $results;
    }

    /**
     * Set cURL adapter (the only type that supports ParallelExecution)
     * if $this->client uses another adapter.
     */
    protected function initPluginType()
    {
        $adapter = $this->client->getAdapter();

        if (!($adapter instanceof Curl)) {
            $curlAdapter = new Curl();

            if ($adapter instanceof TimeoutAwareInterface) {
                $curlAdapter->setTimeout($adapter->getTimeout());
            }

            if ($adapter instanceof ConnectionTimeoutAwareInterface) {
                $curlAdapter->setConnectionTimeout($adapter->getConnectionTimeout());
            }

            $this->client->setAdapter($curlAdapter);
        }
    }
}
