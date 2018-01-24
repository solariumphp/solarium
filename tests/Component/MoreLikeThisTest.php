<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\MoreLikeThis;

class MoreLikeThisTest extends TestCase
{
    /**
     * @var MoreLikeThis
     */
    protected $mlt;

    public function setUp()
    {
        $this->mlt = new MoreLikeThis();
    }

    public function testConfigMode()
    {
        $options = [
            'fields' => 'fieldA,fieldB',
            'minimumtermfrequency' => 10,
            'minimumdocumentfrequency' => 2,
            'minimumwordlength' => 3,
            'maximumwordlength' => 10,
            'maximumqueryterms' => 4,
            'maximumnumberoftokens' => 20,
            'boost' => 1.5,
            'queryfields' => 'fieldC,fieldD',
            'count' => 5,
        ];

        $this->mlt->setOptions($options);

        $this->assertEquals($options['fields'], $this->mlt->getFields());
        $this->assertEquals($options['minimumtermfrequency'], $this->mlt->getMinimumTermFrequency());
        $this->assertEquals($options['minimumdocumentfrequency'], $this->mlt->getMinimumDocumentFrequency());
        $this->assertEquals($options['minimumwordlength'], $this->mlt->getMinimumWordLength());
        $this->assertEquals($options['maximumwordlength'], $this->mlt->getMaximumWordLength());
        $this->assertEquals($options['maximumqueryterms'], $this->mlt->getMaximumQueryTerms());
        $this->assertEquals($options['boost'], $this->mlt->getBoost());
        $this->assertEquals($options['queryfields'], $this->mlt->getQueryFields());
        $this->assertEquals($options['count'], $this->mlt->getCount());
    }

    public function testGetType()
    {
        $this->assertEquals(ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS, $this->mlt->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\MoreLikeThis',
            $this->mlt->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\MoreLikeThis',
            $this->mlt->getRequestBuilder()
        );
    }

    public function testSetAndGetFields()
    {
        $value = 'name,description';
        $this->mlt->setFields($value);

        $this->assertEquals(
            ['name', 'description'],
            $this->mlt->getFields()
        );
    }

    public function testSetAndGetFieldsWithArray()
    {
        $value = ['name', 'description'];
        $this->mlt->setFields($value);

        $this->assertEquals(
            $value,
            $this->mlt->getFields()
        );
    }

    public function testSetAndGetMinimumTermFrequency()
    {
        $value = 2;
        $this->mlt->setMinimumTermFrequency($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMinimumTermFrequency()
        );
    }

    public function testMinimumDocumentFrequency()
    {
        $value = 4;
        $this->mlt->setMinimumDocumentFrequency($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMinimumDocumentFrequency()
        );
    }

    public function testSetAndGetMinimumWordLength()
    {
        $value = 3;
        $this->mlt->setMinimumWordLength($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMinimumWordLength()
        );
    }

    public function testSetAndGetMaximumWordLength()
    {
        $value = 15;
        $this->mlt->setMaximumWordLength($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMaximumWordLength()
        );
    }

    public function testSetAndGetMaximumQueryTerms()
    {
        $value = 5;
        $this->mlt->setMaximumQueryTerms($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMaximumQueryTerms()
        );
    }

    public function testSetAndGetMaximumNumberOfTokens()
    {
        $value = 5;
        $this->mlt->setMaximumNumberOfTokens($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMaximumNumberOfTokens()
        );
    }

    public function testSetAndGetBoost()
    {
        $value = true;
        $this->mlt->setBoost($value);

        $this->assertEquals(
            $value,
            $this->mlt->getBoost()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'content,name';
        $this->mlt->setQueryFields($value);

        $this->assertEquals(
            ['content', 'name'],
            $this->mlt->getQueryFields()
        );
    }

    public function testSetAndGetQueryFieldsWithArray()
    {
        $value = ['content', 'name'];
        $this->mlt->setQueryFields($value);

        $this->assertEquals(
            $value,
            $this->mlt->getQueryFields()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 8;
        $this->mlt->setCount($value);

        $this->assertEquals(
            $value,
            $this->mlt->getCount()
        );
    }
}
