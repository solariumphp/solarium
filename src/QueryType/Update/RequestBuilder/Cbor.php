<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Update\RequestBuilder;

use CBOR\CBORObject;
use CBOR\IndefiniteLengthListObject;
use CBOR\IndefiniteLengthMapObject;
use CBOR\ListObject;
use CBOR\MapItem;
use CBOR\MapObject;
use CBOR\NegativeIntegerObject;
use CBOR\OtherObject\DoublePrecisionFloatObject;
use CBOR\OtherObject\FalseObject;
use CBOR\OtherObject\NullObject;
use CBOR\OtherObject\TrueObject;
use CBOR\TextStringObject;
use CBOR\UnsignedIntegerObject;
use Composer\InstalledVersions;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Command\Add;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Build a CBOR update request.
 */
class Cbor extends AbstractRequestBuilder
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Build request for an update query.
     *
     * @param QueryInterface|UpdateQuery $query
     *
     * @throws RuntimeException
     *
     * @return Request
     */
    public function build(QueryInterface|UpdateQuery $query): Request
    {
        if (!InstalledVersions::isInstalled('spomky-labs/cbor-php')) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('spomky-labs/cbor-php is not available, install it to use CBOR formatted requests');
            // @codeCoverageIgnoreEnd
        }

        $inputEncoding = $query->getInputEncoding();

        if (null !== $inputEncoding && 0 !== strcasecmp('UTF-8', $inputEncoding)) {
            // @see https://www.rfc-editor.org/rfc/rfc8949#section-3.1-2.8
            // @see https://www.rfc-editor.org/rfc/rfc8949#section-5.3.1-2.4
            throw new RuntimeException('CBOR requests can only contain UTF-8 strings');
        }

        $this->request = parent::build($query);
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setContentType(Request::CONTENT_TYPE_APPLICATION_CBOR);
        $this->request->setRawData($this->getRawData($query));

        return $this->request;
    }

    /**
     * Generates raw POST data.
     *
     * Each commandtype is delegated to a separate builder method.
     *
     * @param UpdateQuery $query
     *
     * @throws RuntimeException
     *
     * @return string
     */
    public function getRawData(UpdateQuery $query): string
    {
        $cbor = IndefiniteLengthListObject::create();

        foreach ($query->getCommands() as $command) {
            if (UpdateQuery::COMMAND_ADD === $command->getType()) {
                /* @var Add $command */
                $this->addDocuments($command->getDocuments(), $cbor);

                if (null !== $overwrite = $command->getOverwrite()) {
                    $this->request->addParam('overwrite', $overwrite, true);
                }

                if (null !== $commitWithin = $command->getCommitWithin()) {
                    $this->request->addParam('commitWithin', $commitWithin, true);
                }
            } else {
                throw new RuntimeException('Unsupported command type, CBOR queries can only be used to add documents');
            }
        }

        return (string) $cbor;
    }

    /**
     * Add documents.
     *
     * @param DocumentInterface[]        $documents
     * @param IndefiniteLengthListObject $cbor
     */
    protected function addDocuments(array $documents, IndefiniteLengthListObject $cbor): void
    {
        foreach ($documents as $doc) {
            $fields = IndefiniteLengthMapObject::create();

            foreach ($doc->getFields() as $name => $value) {
                $modifier = $doc->getFieldModifier($name);

                $fields->add(
                    TextStringObject::create($name),
                    $this->buildFieldCborObject($value, $modifier)
                );
            }

            if (null !== $version = $doc->getVersion()) {
                $fields->add(
                    TextStringObject::create('_version_'),
                    0 > $version ? NegativeIntegerObject::create($version) : UnsignedIntegerObject::create($version)
                );
            }

            $cbor->add($fields);
        }
    }

    /**
     * Build a CBOR object that represents a document field value.
     *
     * @param mixed       $value
     * @param string|null $modifier
     *
     * @return CBORObject
     */
    protected function buildFieldCborObject($value, ?string $modifier = null): CBORObject
    {
        if (\is_array($value)) {
            if (empty($value)) {
                $cbor = ListObject::create();
            } elseif (is_numeric(array_key_first($value))) {
                $cbor = ListObject::create(array_map(
                    fn ($v): CBORObject => $this->buildFieldCborObject($v),
                    $value
                ));
            } else {
                $cbor = IndefiniteLengthMapObject::create();

                foreach ($value as $k => $v) {
                    $cbor->add(
                        TextStringObject::create($k),
                        $this->buildFieldCborObject($v)
                    );
                }
            }
        } else {
            $cbor = $this->buildScalarCborObject($value);
        }

        if (null !== $modifier) {
            $cbor = MapObject::create([
                MapItem::create(TextStringObject::create($modifier), $cbor),
            ]);
        }

        return $cbor;
    }

    /**
     * Build a CBOR object that represents a scalar value.
     *
     * @param scalar $value
     *
     * @return CBORObject
     */
    protected function buildScalarCborObject($value): CBORObject
    {
        if (null === $value) {
            return NullObject::create();
        } elseif (false === $value) {
            return FalseObject::create();
        } elseif (true === $value) {
            return TrueObject::create();
        } elseif (\is_int($value)) {
            return 0 > $value ? NegativeIntegerObject::create($value) : UnsignedIntegerObject::create($value);
        } elseif (\is_float($value)) {
            return DoublePrecisionFloatObject::create(pack('E', $value));
        } elseif ($value instanceof \DateTimeInterface) {
            return TextStringObject::create($this->getHelper()->formatDate($value));
        } else {
            return TextStringObject::create($value);
        }
    }
}
