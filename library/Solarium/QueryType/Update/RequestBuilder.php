<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\QueryType\Update;

use Solarium\Core\Client\Request;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\Exception\RuntimeException;

/**
 * Build an update request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for an update query.
     *
     * @param QueryInterface|UpdateQuery $query
     *
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        $request = parent::build($query);
        $request->setMethod(Request::METHOD_POST);
        $request->setRawData($this->getRawData($query));

        return $request;
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
    public function getRawData($query)
    {
        $xml = '<update>';
        foreach ($query->getCommands() as $command) {
            switch ($command->getType()) {
                case UpdateQuery::COMMAND_ADD:
                    $xml .= $this->buildAddXml($command, $query);
                    break;
                case UpdateQuery::COMMAND_DELETE:
                    $xml .= $this->buildDeleteXml($command);
                    break;
                case UpdateQuery::COMMAND_OPTIMIZE:
                    $xml .= $this->buildOptimizeXml($command);
                    break;
                case UpdateQuery::COMMAND_COMMIT:
                    $xml .= $this->buildCommitXml($command);
                    break;
                case UpdateQuery::COMMAND_ROLLBACK:
                    $xml .= $this->buildRollbackXml();
                    break;
                default:
                    throw new RuntimeException('Unsupported command type');
                    break;
            }
        }
        $xml .= '</update>';

        return $xml;
    }

    /**
     * Build XML for an add command.
     *
     * @param \Solarium\QueryType\Update\Query\Command\Add $command
     * @param UpdateQuery $query
     *
     * @return string
     */
    public function buildAddXml($command, $query = null)
    {
        $xml = '<add';
        $xml .= $this->boolAttrib('overwrite', $command->getOverwrite());
        $xml .= $this->attrib('commitWithin', $command->getCommitWithin());
        $xml .= '>';

        foreach ($command->getDocuments() as $doc) {
            $xml .= '<doc';
            $xml .= $this->attrib('boost', $doc->getBoost());
            $xml .= '>';

            foreach ($doc->getFields() as $name => $value) {
                $boost = $doc->getFieldBoost($name);
                $modifier = $doc->getFieldModifier($name);
                $xml .= $this->buildFieldsXml($name, $boost, $value, $modifier, $query);
            }

            $version = $doc->getVersion();
            if ($version !== null) {
                $xml .= $this->buildFieldXml('_version_', null, $version);
            }

            $xml .= '</doc>';
        }

        $xml .= '</add>';

        return $xml;
    }

    /**
     * Build XML for a delete command.
     *
     * @param \Solarium\QueryType\Update\Query\Command\Delete $command
     *
     * @return string
     */
    public function buildDeleteXml($command)
    {
        $xml = '<delete>';
        foreach ($command->getIds() as $id) {
            $xml .= '<id>' . htmlspecialchars($id, ENT_NOQUOTES, 'UTF-8') . '</id>';
        }
        foreach ($command->getQueries() as $query) {
            $xml .= '<query>' . htmlspecialchars($query, ENT_NOQUOTES, 'UTF-8') . '</query>';
        }
        $xml .= '</delete>';

        return $xml;
    }

    /**
     * Build XML for an update command.
     *
     * @param \Solarium\QueryType\Update\Query\Command\Optimize $command
     *
     * @return string
     */
    public function buildOptimizeXml($command)
    {
        $xml = '<optimize';
        $xml .= $this->boolAttrib('softCommit', $command->getSoftCommit());
        $xml .= $this->boolAttrib('waitSearcher', $command->getWaitSearcher());
        $xml .= $this->attrib('maxSegments', $command->getMaxSegments());
        $xml .= '/>';

        return $xml;
    }

    /**
     * Build XML for a commit command.
     *
     * @param \Solarium\QueryType\Update\Query\Command\Commit $command
     *
     * @return string
     */
    public function buildCommitXml($command)
    {
        $xml = '<commit';
        $xml .= $this->boolAttrib('softCommit', $command->getSoftCommit());
        $xml .= $this->boolAttrib('waitSearcher', $command->getWaitSearcher());
        $xml .= $this->boolAttrib('expungeDeletes', $command->getExpungeDeletes());
        $xml .= '/>';

        return $xml;
    }

    /**
     * Build XMl for a rollback command.
     *
     * @return string
     */
    public function buildRollbackXml()
    {
        return '<rollback/>';
    }

    /**
     * Build XML for a field.
     *
     * Used in the add command
     *
     * @param string $name
     * @param float $boost
     * @param mixed $value
     * @param string $modifier
     * @param UpdateQuery $query
     *
     * @return string
     */
    protected function buildFieldXml($name, $boost, $value, $modifier = null, $query = null)
    {
        if ($value instanceof \DateTime) {
            $value = $query->getHelper()->formatDate($value);
        }

        $xml = '<field name="' . $name . '"';
        $xml .= $this->attrib('boost', $boost);
        $xml .= $this->attrib('update', $modifier);
        if ($value === null) {
            $xml .= $this->attrib('null', 'true');
        } elseif ($value === false) {
            $value = 'false';
        } elseif ($value === true) {
            $value = 'true';
        }

        $xml .= '>' . htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
        $xml .= '</field>';

        return $xml;
    }

    /**
     * @param string $key
     *
     * @param float $boost
     * @param mixed $value
     * @param string $modifier
     * @param UpdateQuery $query
     * @return string
     */
    private function buildFieldsXml($key, $boost, $value, $modifier, $query)
    {
        $xml = '';
        if (is_array($value)) {
            foreach ($value as $multival) {
                if (is_array($multival)) {
                    $xml .= '<doc>';
                    foreach ($multival as $k => $v) {
                        $xml .= $this->buildFieldsXml($k, $boost, $v, $modifier, $query);
                    }
                    $xml .= '</doc>';

                } else {
                    $xml .= $this->buildFieldXml($key, $boost, $multival, $modifier, $query);
                }
            }

        } else {
            $xml .= $this->buildFieldXml($key, $boost, $value, $modifier, $query);
        }

        return $xml;
    }
}
