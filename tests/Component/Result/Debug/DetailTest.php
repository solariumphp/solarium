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

    public function setUp()
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
        $subDetailsDummy = ['dummy', 'testing'];
        $this->result->setSubDetails($subDetailsDummy);
        $this->assertEquals($subDetailsDummy, $this->result->getSubDetails());
    }
}
