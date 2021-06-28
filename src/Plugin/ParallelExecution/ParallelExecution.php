<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\ParallelExecution;

use Solarium\Component\QueryInterface;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Plugin\AbstractPlugin;
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
 *
 * @codeCoverageIgnoreStart
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
    public function addQuery(string $key, QueryInterface $query, $endpoint = null)
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

    // @codeCoverageIgnoreStart

    /**
     * Execute queries parallelly.
     *
     * @throws RuntimeException
     *
     * @return \Solarium\Core\Query\Result\Result[]
     */
    public function execute(): array
    {
        // create handles and add all handles to the multihandle
        $adapter = $this->client->getAdapter();
        if (!($adapter instanceof Curl)) {
            throw new RuntimeException('Parallel execution requires the CurlAdapter');
        }
        $multiHandle = curl_multi_init();
        $handles = [];
        foreach ($this->queries as $key => $data) {
            $request = $this->client->createRequest($data['query']);
            $endpoint = $this->client->getEndpoint($data['endpoint']);
            $handle = $adapter->createHandle($request, $endpoint);
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
                usleep(100);
            }

            do {
                $mrc = curl_multi_exec($multiHandle, $active);
            } while (CURLM_CALL_MULTI_PERFORM === $mrc);
        }

        $event = new ExecuteEndEvent();
        $this->client->getEventDispatcher()->dispatch($event);

        // get the results
        $results = [];
        foreach ($handles as $key => $handle) {
            try {
                curl_multi_remove_handle($multiHandle, $handle);
                $response = $adapter->getResponse($handle, curl_multi_getcontent($handle));
                $results[$key] = $this->client->createResult($this->queries[$key]['query'], $response);
            } catch (HttpException $e) {
                $results[$key] = $e;
            }
        }

        curl_multi_close($multiHandle);

        return $results;
    }

    /*
     * @codeCoverageIgnoreEnd
     */

    /**
     * Set cURL adapter (the only type that supports ParallelExecution).
     */
    protected function initPluginType()
    {
        $this->client->setAdapter(new Curl());
    }
}
