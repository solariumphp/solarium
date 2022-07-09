<?php

declare(strict_types=1);

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Analytics\Analytics as Component;
use Solarium\Component\RequestBuilder\Analytics as RequestBuilder;
use Solarium\Core\Client\Request;

/**
 * AnalyticsTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AnalyticsTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \RuntimeException
     */
    public function testBuildComponent(): void
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->addFunction('sale()', 'mult(price,quantity)');

        $request = $builder->buildComponent($component, $request);

        $this->assertSame(
            Request::METHOD_POST,
            $request->getMethod()
        );
        $this->assertSame(
            sprintf('Content-Type: %s', Request::CONTENT_TYPE_APPLICATION_X_WWW_FORM_URLENCODED),
            $request->getHeader('Content-Type')
        );

        parse_str($request->getRawData(), $data);

        $this->assertArrayHasKey('analytics', $data);
        $analytics = json_decode($data['analytics'], true);
        $this->assertArrayHasKey('functions', $analytics);
        $this->assertArrayHasKey('sale()', $analytics['functions']);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \RuntimeException
     */
    public function testMergingPostData(): void
    {
        $builder = new RequestBuilder();
        $request = new Request();
        $request
            ->setMethod(Request::METHOD_POST)
            ->setRawData('json='.json_encode([
                'query' => '*:*',
                'rows' => '1',
            ]))
        ;

        $component = new Component();
        $component->addFunction('sale()', 'mult(price,quantity)');

        $request = $builder->buildComponent($component, $request);

        $this->assertSame(
            Request::METHOD_POST,
            $request->getMethod()
        );
        $this->assertSame(
            sprintf('Content-Type: %s', Request::CONTENT_TYPE_APPLICATION_X_WWW_FORM_URLENCODED),
            $request->getHeader('Content-Type')
        );

        parse_str($request->getRawData(), $data);

        $this->assertArrayHasKey('analytics', $data);
        $this->assertArrayHasKey('json', $data);

        $analytics = json_decode($data['analytics'], true);

        $this->assertArrayHasKey('functions', $analytics);
    }

    /**
     * @throws \RuntimeException
     */
    public function testInvalidContentType(): void
    {
        $builder = new RequestBuilder();
        $request = new Request();
        $request
            ->setMethod(Request::METHOD_POST)
            ->addHeader('Content-Type: application/xml, charset=utf-8')
            ->setRawData(json_encode([
                'query' => '*:*',
                'rows' => '1',
            ]))
        ;

        $component = new Component();
        $component->addFunction('sale()', 'mult(price,quantity)');

        $this->expectException(\RuntimeException::class);
        $builder->buildComponent($component, $request);
    }
}
