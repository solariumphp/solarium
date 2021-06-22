<?php

namespace Solarium\Tests\Component\Result\Debug;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Debug\Detail;

class DetailTest extends TestCase
{
    /**
     * @var Detail
     */
    protected $result;

    protected $value;

    protected $match;

    protected $description;

    public function setUp(): void
    {
        $this->value = 1.5;
        $this->match = true;
        $this->description = 'dummy-desc';

        $this->result = new Detail(
            $this->match,
            $this->value,
            $this->description
        );
    }

    public function testGetValue()
    {
        $this->assertEquals($this->value, $this->result->getValue());
    }

    public function testGetMatch()
    {
        $this->assertEquals($this->match, $this->result->getMatch());
    }

    public function testGetDescription()
    {
        $this->assertEquals($this->description, $this->result->getDescription());
    }

    public function testSetSubDetails()
    {
        $subDetailsDummyArrays = [[
            'match' => false,
            'value' => 3.14,
            'description' => 'test',
        ]];

        $subDetailsDummyObjects = [
            new Detail(false, 3.14, 'test'),
        ];

        $this->result->setSubDetails($subDetailsDummyArrays);
        $subDetail = $this->result->getSubDetails()[0];
        $this->assertEquals(array_values($subDetailsDummyArrays[0]), [
            $subDetail['match'],
            $subDetail['value'],
            $subDetail['description'],
        ]);
        $this->assertEquals(array_values($subDetailsDummyArrays[0]), [
            $subDetail->getMatch(),
            $subDetail->getValue(),
            $subDetail->getDescription(),
        ]);

        $this->result->setSubDetails($subDetailsDummyObjects);
        $subDetail = $this->result->getSubDetails()[0];
        $this->assertEquals(array_values($subDetailsDummyArrays[0]), [
          $subDetail['match'],
          $subDetail['value'],
          $subDetail['description'],
        ]);
        $this->assertEquals(array_values($subDetailsDummyArrays[0]), [
            $subDetail->getMatch(),
            $subDetail->getValue(),
            $subDetail->getDescription(),
        ]);
    }
}
