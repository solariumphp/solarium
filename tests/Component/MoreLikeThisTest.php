<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\MoreLikeThis;
use Solarium\Exception\DomainException;

class MoreLikeThisTest extends TestCase
{
    protected MoreLikeThis $mlt;

    public function setUp(): void
    {
        $this->mlt = new MoreLikeThis();
    }

    public function testConfigMode(): void
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

    public function testGetType(): void
    {
        $this->assertSame(ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS, $this->mlt->getType());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\MoreLikeThis',
            $this->mlt->getResponseParser()
        );
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\MoreLikeThis',
            $this->mlt->getRequestBuilder()
        );
    }

    public function testGetFieldsAlwaysReturnsArray(): void
    {
        $this->assertSame(
            [],
            $this->mlt->getFields()
        );
    }

    public function testSetAndGetFields(): void
    {
        $value = 'name,description';
        $this->mlt->setFields($value);

        $this->assertSame(
            ['name', 'description'],
            $this->mlt->getFields()
        );
    }

    public function testSetAndGetFieldsWithArray(): void
    {
        $value = ['name', 'description'];
        $this->mlt->setFields($value);

        $this->assertSame(
            $value,
            $this->mlt->getFields()
        );
    }

    public function testSetAndGetMinimumTermFrequency(): void
    {
        $value = 2;
        $this->mlt->setMinimumTermFrequency($value);

        $this->assertSame(
            $value,
            $this->mlt->getMinimumTermFrequency()
        );
    }

    public function testMinimumDocumentFrequency(): void
    {
        $value = 4;
        $this->mlt->setMinimumDocumentFrequency($value);

        $this->assertSame(
            $value,
            $this->mlt->getMinimumDocumentFrequency()
        );
    }

    public function testMaximumDocumentFrequency(): void
    {
        $value = 4;
        $this->mlt->setMaximumDocumentFrequency($value);

        $this->assertSame(
            $value,
            $this->mlt->getMaximumDocumentFrequency()
        );
    }

    public function testMaximumDocumentFrequencyPercentage(): void
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
    public function testMaximumDocumentFrequencyPercentageDomainException(int $value): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(sprintf('Maximum percentage %d is not between 0 and 100.', $value));
        $this->mlt->setMaximumDocumentFrequencyPercentage($value);
    }

    public function testSetAndGetMinimumWordLength(): void
    {
        $value = 3;
        $this->mlt->setMinimumWordLength($value);

        $this->assertSame(
            $value,
            $this->mlt->getMinimumWordLength()
        );
    }

    public function testSetAndGetMaximumWordLength(): void
    {
        $value = 15;
        $this->mlt->setMaximumWordLength($value);

        $this->assertSame(
            $value,
            $this->mlt->getMaximumWordLength()
        );
    }

    public function testSetAndGetMaximumQueryTerms(): void
    {
        $value = 5;
        $this->mlt->setMaximumQueryTerms($value);

        $this->assertSame(
            $value,
            $this->mlt->getMaximumQueryTerms()
        );
    }

    public function testSetAndGetMaximumNumberOfTokens(): void
    {
        $value = 5;
        $this->mlt->setMaximumNumberOfTokens($value);

        $this->assertSame(
            $value,
            $this->mlt->getMaximumNumberOfTokens()
        );
    }

    public function testSetAndGetBoost(): void
    {
        $this->mlt->setBoost(true);

        $this->assertTrue(
            $this->mlt->getBoost()
        );
    }

    public function testGetQueryFieldsAlwaysReturnsArray(): void
    {
        $this->assertSame(
            [],
            $this->mlt->getQueryFields()
        );
    }

    public function testSetAndGetQueryFields(): void
    {
        $value = 'content,name';
        $this->mlt->setQueryFields($value);

        $this->assertSame(
            ['content', 'name'],
            $this->mlt->getQueryFields()
        );
    }

    public function testSetAndGetQueryFieldsWithArray(): void
    {
        $value = ['content', 'name'];
        $this->mlt->setQueryFields($value);

        $this->assertSame(
            $value,
            $this->mlt->getQueryFields()
        );
    }

    public function testSetAndGetCount(): void
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
    public function testSetAndGetMatchInclude(): void
    {
        $this->mlt->setMatchInclude(true);

        // always returns null for MLT Component as this parameter is only for MLT Handler
        $this->assertNull($this->mlt->getMatchInclude());
    }

    /**
     * @deprecated Will be removed in Solarium 8. This parameter is only accessible through the MoreLikeThisHandler.
     */
    public function testSetAndGetMatchOffset(): void
    {
        $this->mlt->setMatchOffset(20);

        // always returns null for MLT Component as this parameter is only for MLT Handler
        $this->assertNull($this->mlt->getMatchOffset());
    }

    public function testSetAndGetInterestingTerms(): void
    {
        $value = 'details';
        $this->mlt->setInterestingTerms($value);

        $this->assertSame(
            $value,
            $this->mlt->getInterestingTerms()
        );
    }
}
