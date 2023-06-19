<?php

namespace Solarium\Tests\QueryType\Extract;

use Solarium\QueryType\Extract\Query as ExtractQuery;
use Solarium\QueryType\Extract\Result as ExtractResult;
use Solarium\Tests\QueryType\Update\AbstractResultTestCase;

class ResultTest extends AbstractResultTestCase
{
    public function setUp(): void
    {
        $this->result = new ExtractResultDummy();
    }

    public function testGetFile()
    {
        $this->assertSame('dummy data', $this->result->getFile());
    }

    public function testGetFileMetadata()
    {
        $this->assertSame(['foo' => 'bar'], $this->result->getFileMetadata());
    }

    public function testGetDataOldStyle()
    {
        $result = new ExtractResultDummyPre8Point6();
        $data = $result->getData();

        $this->assertSame('dummy data', $data['document.pdf']);
        $this->assertSame('dummy data', $data['file']);

        $this->assertSame(['foo', 'bar'], $data['document.pdf_metadata']);
        $this->assertSame(['foo', 'bar'], $data['file_metadata']);
    }

    public function testGetDataNewStyle()
    {
        $result = new ExtractResultDummyPost8Point6();
        $data = $result->getData();

        $this->assertSame('dummy data', $data['file']);
        $this->assertSame('dummy data', $data['document.pdf']);

        $this->assertSame(['foo', 'bar'], $data['file_metadata']);
        $this->assertSame(['foo', 'bar'], $data['document.pdf_metadata']);
    }
}

class ExtractResultDummy extends ExtractResult
{
    protected $parsed = true;

    public function __construct()
    {
        $this->file = 'dummy data';
        $this->fileMetadata = ['foo' => 'bar'];
        $this->responseHeader = ['status' => 1, 'QTime' => 12];

        $this->query = new ExtractQuery();
        $this->query->setFile('/path/to/document.pdf');
        $this->query->setExtractOnly(true);
        $this->query->setResourceName('document.pdf');
    }
}

class ExtractResultDummyPre8Point6 extends ExtractResultDummy
{
    protected $data = [
        'document.pdf' => 'dummy data',
        'document.pdf_metadata' => ['foo', 'bar'],
    ];
}

class ExtractResultDummyPost8Point6 extends ExtractResultDummy
{
    protected $data = [
        'file' => 'dummy data',
        'file_metadata' => ['foo', 'bar'],
    ];
}
