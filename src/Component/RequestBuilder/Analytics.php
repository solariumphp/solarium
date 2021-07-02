<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Analytics Request Builder.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Analytics implements ComponentRequestBuilderInterface
{
    /**
     * Header name.
     */
    private const HEADER_NAME = 'Content-Type';

    /**
     * Content type.
     */
    private const HEADER_CONTENT = 'application/x-www-form-urlencoded';

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        $raw = sprintf('analytics=%s', json_encode($component));
        $header = sprintf('%s: %s', self::HEADER_NAME, self::HEADER_CONTENT);

        if (Request::METHOD_POST !== $request->getMethod()
            || (null === $data = $request->getRawData())
        ) {
            return $request
                ->setMethod(Request::METHOD_POST)
                ->replaceOrAddHeader($header)
                ->setRawData($raw)
            ;
        }

        if ((null !== $currentHeader = $request->getHeader(self::HEADER_NAME))
            && false === strpos($currentHeader, self::HEADER_CONTENT)
        ) {
            throw new \RuntimeException(sprintf('Unable to build analytics request. required content type is %s while current header is %s', self::HEADER_CONTENT, $header));
        }

        // merge raw data currently present in the request
        $raw = sprintf('%s&%s', $data, $raw);

        return $request
            ->replaceOrAddHeader($header)
            ->setRawData($raw)
        ;
    }
}
