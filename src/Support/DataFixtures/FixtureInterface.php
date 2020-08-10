<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Support\DataFixtures;

use Solarium\Core\Client\ClientInterface;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 */
interface FixtureInterface
{
    /**
     * @param ClientInterface $client
     */
    public function load(ClientInterface $client);
}
