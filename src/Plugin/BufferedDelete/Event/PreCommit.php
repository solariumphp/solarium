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
use Solarium\Plugin\BufferedDelete\DeleteInterface;

/**
 * PreCommit event, see {@see Events} for details.
 */
class PreCommit extends AbstractPreCommit
{
    /**
     * @var DeleteInterface[]
     */
    protected array $buffer;
}
