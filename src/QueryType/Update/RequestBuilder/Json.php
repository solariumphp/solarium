<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Update\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Command\Add;
use Solarium\QueryType\Update\Query\Command\Commit;
use Solarium\QueryType\Update\Query\Command\Delete;
use Solarium\QueryType\Update\Query\Command\Optimize;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Build a JSON update request.
 */
class Json extends AbstractRequestBuilder
{
    /**
     * Build request for an update query.
     *
     * @param QueryInterface|AbstractQuery|UpdateQuery $query
     *
     * @throws RuntimeException
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $inputEncoding = $query->getInputEncoding();

        if (null !== $inputEncoding && 0 !== strcasecmp('UTF-8', $inputEncoding)) {
            // @see https://www.rfc-editor.org/rfc/rfc8259#section-8.1
            throw new RuntimeException('JSON requests can only be UTF-8');
        }

        $request = parent::build($query);
        $request->setMethod(Request::METHOD_POST);
        $request->setContentType(Request::CONTENT_TYPE_APPLICATION_JSON);
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
    public function getRawData(UpdateQuery $query): string
    {
        $json = [];

        foreach ($query->getCommands() as $command) {
            switch ($command->getType()) {
                case UpdateQuery::COMMAND_ADD:
                    $this->buildAddJson($command, $json);
                    break;
                case UpdateQuery::COMMAND_DELETE:
                    $this->buildDeleteJson($command, $json);
                    break;
                case UpdateQuery::COMMAND_OPTIMIZE:
                    $this->buildOptimizeJson($command, $json);
                    break;
                case UpdateQuery::COMMAND_COMMIT:
                    $this->buildCommitJson($command, $json);
                    break;
                case UpdateQuery::COMMAND_ROLLBACK:
                    $this->buildRollbackJson($json);
                    break;
                default:
                    throw new RuntimeException('Unsupported command type');
            }
        }

        return '{'.implode(',', $json).'}';
    }

    /**
     * Add JSON for an add command.
     *
     * @param Add   $command
     * @param array $json
     */
    public function buildAddJson(Add $command, array &$json): void
    {
        $commitWithin = $command->getCommitWithin();
        $overwrite = $command->getOverwrite();

        foreach ($command->getDocuments() as $doc) {
            $add = [
                'doc' => $doc,
            ];

            if (null !== $commitWithin) {
                $add['commitWithin'] = $commitWithin;
            }

            if (null !== $overwrite) {
                $add['overwrite'] = $overwrite;
            }

            $json[] = '"add":'.json_encode($add);
        }
    }

    /**
     * Add JSON for a delete command.
     *
     * @param Delete $command
     * @param array  $json
     */
    public function buildDeleteJson(Delete $command, array &$json): void
    {
        if (0 !== count($ids = $command->getIds())) {
            $json[] = '"delete":'.json_encode($ids);
        }

        foreach ($command->getQueries() as $query) {
            $json[] = '"delete":'.json_encode(['query' => $query]);
        }
    }

    /**
     * Build JSON for an optimize command.
     *
     * @param Optimize $command
     * @param array    $json
     */
    public function buildOptimizeJson(Optimize $command, array &$json): void
    {
        $optimize = [];

        if (null !== $softCommit = $command->getSoftCommit()) {
            $optimize['softCommit'] = $softCommit;
        }

        if (null !== $waitSearcher = $command->getWaitSearcher()) {
            $optimize['waitSearcher'] = $waitSearcher;
        }

        if (null !== $maxSegments = $command->getMaxSegments()) {
            $optimize['maxSegments'] = $maxSegments;
        }

        $json[] = '"optimize":'.json_encode($optimize, JSON_FORCE_OBJECT);
    }

    /**
     * Build JSON for a commit command.
     *
     * @param Commit $command
     * @param array  $json
     */
    public function buildCommitJson(Commit $command, array &$json): void
    {
        $commit = [];

        if (null !== $softCommit = $command->getSoftCommit()) {
            $commit['softCommit'] = $softCommit;
        }

        if (null !== $waitSearcher = $command->getWaitSearcher()) {
            $commit['waitSearcher'] = $waitSearcher;
        }

        if (null !== $expungeDeletes = $command->getExpungeDeletes()) {
            $commit['expungeDeletes'] = $expungeDeletes;
        }

        $json[] = '"commit":'.json_encode($commit, JSON_FORCE_OBJECT);
    }

    /**
     * Build JSON for a rollback command.
     *
     * @param array $json
     */
    public function buildRollbackJson(array &$json): void
    {
        $json[] = '"rollback":{}';
    }
}
