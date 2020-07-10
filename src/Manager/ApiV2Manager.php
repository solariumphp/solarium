<?php

declare(strict_types=1);

namespace Solarium\Manager;

use Solarium\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Manager\Command\CommandCollection;
use Solarium\Manager\Contract\ApiV2ConfigurationInterface;
use Solarium\Manager\Contract\ApiV2ResponseNormalizerInterface;
use Solarium\Manager\Normalizer\NoopResponseNormalizer;

/**
 * Api V2 Manager.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ApiV2Manager
{
    /**
     * @var \Solarium\Client
     */
    private $client;

    /**
     * @var \Solarium\Manager\Contract\ApiV2ConfigurationInterface
     */
    private $config;

    /**
     * @var \Solarium\Manager\Contract\ApiV2ResponseNormalizerInterface
     */
    private $normalizer;

    /**
     * @var \Solarium\Core\Client\Endpoint|null
     */
    private $endpoint;

    /**
     * @var \Solarium\QueryType\Server\Api\Query
     */
    private $query;

    /**
     * @var \Solarium\Manager\Command\CommandCollection
     */
    private $commands;

    /**
     * @param \Solarium\Client                                                 $client
     * @param \Solarium\Manager\Contract\ApiV2ConfigurationInterface           $config
     * @param \Solarium\Manager\Contract\ApiV2ResponseNormalizerInterface|null $normalizer
     * @param \Solarium\Core\Client\Endpoint|null                              $endpoint
     */
    public function __construct(Client $client, ApiV2ConfigurationInterface $config, ApiV2ResponseNormalizerInterface $normalizer = null, Endpoint $endpoint = null)
    {
        $this->client = $client;
        $this->config = $config;
        $this->normalizer = $normalizer ?? new NoopResponseNormalizer();
        $this->endpoint = $endpoint;

        $this->commands = new CommandCollection($config->getCommands());
        $this->query = $this->client->createApi()
            ->setVersion(Request::API_V2)
            ->setMethod(Request::METHOD_POST)
            ->setContentType('application/json')
        ;
    }

    /**
     * @param \Solarium\Core\Client\Endpoint $endpoint
     *
     * @return $this
     */
    public function withEndpoint(Endpoint $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @param string            $command
     * @param \JsonSerializable $data
     *
     * @return $this
     */
    public function addCommand(string $command, \JsonSerializable $data): self
    {
        if (false === \array_key_exists($command, $this->config->getCommands())) {
            throw new InvalidArgumentException(sprintf('unknown command: %s. available commands: %s', $command, implode(', ', $this->config->getCommands())));
        }

        $this->commands->add($command, $data);

        return $this;
    }

    /**
     * @param string $subPath
     *
     * @return mixed
     */
    public function call(string $subPath)
    {
        if (false === \in_array($subPath, $this->config->getSubPaths(), true)) {
            throw new InvalidArgumentException(sprintf('unknown sub path: %s. available sub paths: %s', $subPath, implode(', ', $this->config->getSubPaths())));
        }

        $query = $this->client
            ->createApi()
            ->setHandler(sprintf('%s/%s', $this->config->getHandler(), $subPath))
            ->setVersion(Request::API_V2)
            ->setMethod(Request::METHOD_GET)
        ;

        $response = $this->client->execute($query, $this->endpoint);

        return $this->normalizer->normalize($response);
    }

    /**
     * @return \Solarium\Core\Query\Result\ResultInterface
     */
    public function persist(): ResultInterface
    {
        $this->query
            ->setHandler($this->getHandler())
            ->setRawData(json_encode($this->commands, JSON_THROW_ON_ERROR, 512))
        ;

        $this->commands = new CommandCollection($this->config->getCommands());

        return $this->client->execute($this->query, $this->endpoint);
    }

    /**
     * @return \Solarium\Core\Client\Response
     */
    public function flush(): Response
    {
        $request = (new Request())
            ->setHandler('cores')
            ->addParam('core', $this->endpoint->getCore())
            ->addParam('action', 'RELOAD')
        ;

        return $this->client->executeRequest($request, new Endpoint(['collection' => 'admin']));
    }

    /**
     * @return string
     */
    private function getHandler(): string
    {
        return sprintf('cores/%s/%s', $this->endpoint->getCore(), $this->config->getHandler());
    }
}
