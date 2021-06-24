<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\MoreLikeThis;
use Solarium\Exception\DomainException;

class MoreLikeThisTest extends TestCase
{
    /**
     * @var MoreLikeThis
     */
    protected $mlt;

    public function setUp(): void
    {
        $this->mlt = new MoreLikeThis();
    }

    public function testConfigMode()
    {
        $options = [
            'fields' => 'fieldA,fieldB',
            'minimumtermfrequency' => 10,
            'minimumdocumentfrequency' => 2,
            'maximumdocumentfrequency' => 20,
            'maximumdocumentfrequencypercentage' => 75,
            'minimumwordlength' => 3,
            'maximumwordlength' => 10,
            'maximumqueryterms' => 4,
            'maximumnumberoftokens' => 20,
            'boost' => true,
            'queryfields' => 'fieldC,fieldD',
            'count' => 5,
            'interestingTerms' => 'none',
        ];

        $this->mlt->setOptions($options);

        $this->assertSame(explode(',', $options['fields']), $this->mlt->getFields());
        $this->assertSame($options['minimumtermfrequency'], $this->mlt->getMinimumTermFrequency());
        $this->assertSame($options['minimumdocumentfrequency'], $this->mlt->getMinimumDocumentFrequency());
        $this->assertSame($options['maximumdocumentfrequency'], $this->mlt->getMaximumDocumentFrequency());
        $this->assertSame($options['maximumdocumentfrequencypercentage'], $this->mlt->getMaximumDocumentFrequencyPercentage());
        $this->assertSame($options['minimumwordlength'], $this->mlt->getMinimumWordLength());
        $this->assertSame($options['maximumwordlength'], $this->mlt->getMaximumWordLength());
        $this->assertSame($options['maximumqueryterms'], $this->mlt->getMaximumQueryTerms());
        $this->assertTrue($this->mlt->getBoost());
        $this->assertSame(explode(',', $options['queryfields']), $this->mlt->getQueryFields());
        $this->assertSame($options['count'], $this->mlt->getCount());
        $this->assertSame($options['interestingTerms'], $this->mlt->getInterestingTerms());
    }

    public function testGetType()
    {
        $this->assertSame(ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS, $this->mlt->getType());
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

    public function testGetFieldsAlwaysReturnsArray()
    {
        $this->mlt->setFields(null);

        $this->assertSame(
            [],
            $this->mlt->getFields()
        );
    }

    public function testSetAndGetFields()
    {
        $value = 'name,description';
        $this->mlt->setFields($value);

        $this->assertSame(
            ['name', 'description'],
            $this->mlt->getFields()
        );
    }

    public function testSetAndGetFieldsWithArray()
    {
        $value = ['name', 'description'];
        $this->mlt->setFields($value);

        $this->assertSame(
            $value,
            $this->mlt->getFields()
        );
    }

    public function testSetAndGetMinimumTermFrequency()
    {
        $value = 2;
        $this->mlt->setMinimumTermFrequency($value);

        $this->assertSame(
            $value,
            $this->mlt->getMinimumTermFrequency()
        );
    }

    public function testMinimumDocumentFrequency()
    {
        $value = 4;
        $this->mlt->setMinimumDocumentFrequency($value);

        $this->assertSame(
            $value,
            $this->mlt->getMinimumDocumentFrequency()
        );
    }

    public function testMaximumDocumentFrequency()
    {
        $value = 4;
        $this->mlt->setMaximumDocumentFrequency($value);

        $this->assertSame(
            $value,
            $this->mlt->getMaximumDocumentFrequency()
        );
    }

    public function testMaximumDocumentFrequencyPercentage()
    {
        $value = 75;
        $this->mlt->setMaximumDocumentFrequencyPercentage($value);

        $this->assertSame(
            $value,
            $this->mlt->getMaximumDocumentFrequencyPercentage()
        );
    }

    /**
     * @testWith [-5]
     *           [120]
     */
    public function testMaximumDocumentFrequencyPercentageDomainException(int $value)
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(sprintf('Maximum percentage %d is not between 0 and 100.', $value));
        $this->mlt->setMaximumDocumentFrequencyPercentage($value);
    }

    public function testSetAndGetMinimumWordLength()
    {
        $value = 3;
        $this->mlt->setMinimumWordLength($value);

        $this->assertSame(
            $value,
            $this->mlt->getMinimumWordLength()
        );
    }

    public function testSetAndGetMaximumWordLength()
    {
        $value = 15;
        $this->mlt->setMaximumWordLength($value);

        $this->assertSame(
            $value,
            $this->mlt->getMaximumWordLength()
        );
    }

    public function testSetAndGetMaximumQueryTerms()
    {
        $value = 5;
        $this->mlt->setMaximumQueryTerms($value);

        $this->assertSame(
            $value,
            $this->mlt->getMaximumQueryTerms()
        );
    }

    public function testSetAndGetMaximumNumberOfTokens()
    {
        $value = 5;
        $this->mlt->setMaximumNumberOfTokens($value);

        $this->assertSame(
            $value,
            $this->mlt->getMaximumNumberOfTokens()
        );
    }

    public function testSetAndGetBoost()
    {
        $this->mlt->setBoost(true);

        $this->assertTrue(
            $this->mlt->getBoost()
        );
    }

    public function testGetQueryFieldsAlwaysReturnsArray()
    {
        $this->mlt->setQueryFields(null);

        $this->assertSame(
            [],
            $this->mlt->getQueryFields()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'content,name';
        $this->mlt->setQueryFields($value);

        $this->assertSame(
            ['content', 'name'],
            $this->mlt->getQueryFields()
        );
    }

    public function testSetAndGetQueryFieldsWithArray()
    {
        $value = ['content', 'name'];
        $this->mlt->setQueryFields($value);

        $this->assertSame(
            $value,
            $this->mlt->getQueryFields()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 8;
        $this->mlt->setCount($value);

        $this->assertSame(
            $value,
            $this->mlt->getCount()
        );
    }

    /**
     * @deprecated Will be removed in Solarium 8. This parameter is only accessible through the MoreLikeThisHandler.
     */
    public function testSetAndGetMatchInclude()
    {
        $this->mlt->setMatchInclude(true);

        // always returns null for MLT Component as this parameter is only for MLT Handler
        $this->assertNull($this->mlt->getMatchInclude());
    }

    /**
     * @deprecated Will be removed in Solarium 8. This parameter is only accessible through the MoreLikeThisHandler.
     */
    public function testSetAndGetMatchOffset()
    {
        $this->mlt->setMatchOffset(20);

        // always returns null for MLT Component as this parameter is only for MLT Handler
        $this->assertNull($this->mlt->getMatchOffset());
    }

    public function testSetAndGetInterestingTerms()
    {
        $value = 'details';
        $this->mlt->setInterestingTerms($value);

        $this->assertSame(
            $value,
            $this->mlt->getInterestingTerms()
        );
    }
}
