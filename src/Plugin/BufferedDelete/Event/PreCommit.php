<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedDelete\Event;

use Solarium\Plugin\AbstractBufferedUpdate\Event\AbstractPreCommit;
use Solarium\Plugin\BufferedDelete\AbstractDelete;

/**
 * PreCommit event, see {@see Events} for details.
 */
class PreCommit extends AbstractPreCommit
{
    /**
     * @var AbstractDelete[]
     */
    protected $buffer;
}
