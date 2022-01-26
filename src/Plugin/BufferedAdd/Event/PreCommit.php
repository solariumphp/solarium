<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\Core\Query\DocumentInterface;
use Solarium\Plugin\AbstractBufferedUpdate\Event\AbstractPreCommit;

/**
 * PreCommit event, see {@see Events} for details.
 */
class PreCommit extends AbstractPreCommit
{
    /**
     * @var DocumentInterface[]
     */
    protected $buffer;

    /**
     * @var bool|null
     */
    protected $overwrite;

    /**
     * Event constructor.
     *
     * @param DocumentInterface[] $buffer
     * @param bool|null           $overwrite
     * @param bool|null           $softCommit
     * @param bool|null           $waitSearcher
     * @param bool|null           $expungeDeletes
     */
    public function __construct(array $buffer, ?bool $overwrite, ?bool $softCommit, ?bool $waitSearcher, ?bool $expungeDeletes)
    {
        parent::__construct($buffer, $softCommit, $waitSearcher, $expungeDeletes);

        $this->overwrite = $overwrite;
    }

    /**
     * Optionally override the value.
     *
     * @param bool|null $overwrite
     *
     * @return self Provides fluent interface
     */
    public function setOverwrite(?bool $overwrite): self
    {
        $this->overwrite = $overwrite;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getOverwrite(): ?bool
    {
        return $this->overwrite;
    }
}
