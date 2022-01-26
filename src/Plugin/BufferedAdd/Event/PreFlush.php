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
use Solarium\Plugin\AbstractBufferedUpdate\Event\AbstractPreFlush;

/**
 * PreFlush event, see {@see Events} for details.
 */
class PreFlush extends AbstractPreFlush
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
     * @var int|null
     */
    protected $commitWithin;

    /**
     * Event constructor.
     *
     * @param DocumentInterface[] $buffer
     * @param bool|null           $overwrite
     * @param int|null            $commitWithin
     */
    public function __construct(array $buffer, ?bool $overwrite, ?int $commitWithin)
    {
        parent::__construct($buffer);

        $this->overwrite = $overwrite;
        $this->commitWithin = $commitWithin;
    }

    /**
     * Optionally override the value.
     *
     * @param int|null $commitWithin
     *
     * @return self Provides fluent interface
     */
    public function setCommitWithin(?int $commitWithin): self
    {
        $this->commitWithin = $commitWithin;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCommitWithin(): ?int
    {
        return $this->commitWithin;
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
