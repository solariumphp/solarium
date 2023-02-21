<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Doc;

use Solarium\Core\Query\DocumentInterface;

/**
 * Retrieved doc information.
 */
class DocInfo
{
    /**
     * @var int
     */
    protected $docId;

    /**
     * @var DocFieldInfo[]
     */
    protected $lucene;

    /**
     * @var DocumentInterface
     */
    protected $solr;

    /**
     * Constructor.
     *
     * @param int $docId Lucene documentID
     */
    public function __construct(int $docId)
    {
        $this->docId = $docId;
    }

    /**
     * Returns the Lucene documentID.
     *
     * @return int
     */
    public function getDocId(): int
    {
        return $this->docId;
    }

    /**
     * Returns the document fields information.
     *
     * @return DocFieldInfo[]
     */
    public function getLucene(): array
    {
        return $this->lucene;
    }

    /**
     * @param DocFieldInfo[] $lucene
     *
     * @return self
     */
    public function setLucene(array $lucene): self
    {
        $this->lucene = $lucene;

        return $this;
    }

    /**
     * Returns the Solr document.
     *
     * @return DocumentInterface
     */
    public function getSolr(): DocumentInterface
    {
        return $this->solr;
    }

    /**
     * @param DocumentInterface $solr
     *
     * @return self
     */
    public function setSolr(DocumentInterface $solr): self
    {
        $this->solr = $solr;

        return $this;
    }
}
