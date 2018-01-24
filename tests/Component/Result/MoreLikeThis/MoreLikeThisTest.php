<?php

namespace Solarium\Tests\Component\Result\MoreLikeThis;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\MoreLikeThis\MoreLikeThis;
use Solarium\Component\Result\MoreLikeThis\Result;
use Solarium\QueryType\Select\Result\Document;

class MoreLikeThisTest extends TestCase
{
    /**
     * @var MoreLikeThis
     */
    protected $mlt;

    protected $results;

    public function setUp()
    {
        $docs = array(
            new Document(array('id'=>1, 'name'=>'test1')),
            new Document(array('id'=>2, 'name'=>'test2')),
        );

        $this->results = array(
            'key1' => new Result(2, 5.13, $docs),
            'key2' => new Result(2, 2.3, $docs),
        );

        $this->mlt = new MoreLikeThis($this->results);
    }

    public function testGetResults()
    {
         $this->assertEquals($this->results, $this->mlt->getResults());
    }

    public function testGetResult()
    {
        $this->assertEquals(
            $this->results['key1'],
            $this->mlt->getResult('key1')
        );
    }

    public function testGetInvalidResult()
    {
        $this->assertEquals(
            null,
            $this->mlt->getResult('invalid')
        );
    }

    public function testIterator()
    {
        $items = array();
        foreach ($this->mlt as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->results, $items);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->results), count($this->mlt));
    }
}
