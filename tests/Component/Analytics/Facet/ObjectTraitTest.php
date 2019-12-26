<?php

declare(strict_types=1);

namespace Solarium\Tests\Component\Analytics\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Analytics\Facet\AbstractFacet;
use Solarium\Component\Analytics\Facet\ObjectTrait;
use Solarium\Component\Analytics\Facet\PivotFacet;
use Solarium\Component\Analytics\Facet\Sort\Sort;
use Solarium\Exception\InvalidArgumentException;

/**
 * Object Trait Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ObjectTraitTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNullReturn(): void
    {
        $mock = $this->getMockForTrait(ObjectTrait::class);

        $this->assertNull($mock->ensureObject(AbstractFacet::class, null));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testReturnVariable(): void
    {
        $mock = $this->getMockForTrait(ObjectTrait::class);

        $this->assertInstanceOf(PivotFacet::class, $mock->ensureObject(AbstractFacet::class, new PivotFacet()));
    }

    /**
     * Test non existing class.
     */
    public function testNonExistingClass(): void
    {
        $mock = $this->getMockForTrait(ObjectTrait::class);

        $this->expectException(InvalidArgumentException::class);

        $mock->ensureObject('Foo\Bar', new PivotFacet());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromArray(): void
    {
        $mock = $this->getMockForTrait(ObjectTrait::class);

        $this->assertInstanceOf(Sort::class, $mock->ensureObject(Sort::class, []));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromClassMap(): void
    {
        $mock = $this->getMockForTrait(ObjectTrait::class);

        $this->assertInstanceOf(PivotFacet::class, $mock->ensureObject(AbstractFacet::class, ['type' => AbstractFacet::TYPE_PIVOT]));
    }

    /**
     * Test invalid variable type.
     */
    public function testInvalidVariableType(): void
    {
        $mock = $this->getMockForTrait(ObjectTrait::class);

        $this->expectException(InvalidArgumentException::class);

        $mock->ensureObject(PivotFacet::class, true);
    }

    /**
     * Test invalid mapping type.
     */
    public function testInvalidMappingType(): void
    {
        $mock = $this->getMockForTrait(ObjectTrait::class);

        $this->expectException(InvalidArgumentException::class);
        $mock->ensureObject(AbstractFacet::class, ['type' => 'foo']);
    }
}
