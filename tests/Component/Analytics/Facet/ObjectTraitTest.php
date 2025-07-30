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
    protected object $objectTrait;

    public function setUp(): void
    {
        $this->objectTrait = new class() {
            use ObjectTrait;
        };
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testNullReturn(): void
    {
        $this->assertNull($this->objectTrait->ensureObject(AbstractFacet::class, null));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testReturnVariable(): void
    {
        $this->assertInstanceOf(PivotFacet::class, $this->objectTrait->ensureObject(AbstractFacet::class, new PivotFacet()));
    }

    /**
     * Test non existing class.
     */
    public function testNonExistingClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->objectTrait->ensureObject('Foo\Bar', new PivotFacet());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromArray(): void
    {
        $this->assertInstanceOf(Sort::class, $this->objectTrait->ensureObject(Sort::class, []));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFromClassMap(): void
    {
        $this->assertInstanceOf(PivotFacet::class, $this->objectTrait->ensureObject(AbstractFacet::class, ['type' => AbstractFacet::TYPE_PIVOT]));
    }

    /**
     * Test invalid variable type.
     */
    public function testInvalidVariableType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->objectTrait->ensureObject(PivotFacet::class, true);
    }

    /**
     * Test invalid mapping type.
     */
    public function testInvalidMappingType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->objectTrait->ensureObject(AbstractFacet::class, ['type' => 'foo']);
    }
}
