<?php

declare(strict_types=1);

namespace Solarium\Manager\Contract;

use Solarium\Core\Query\Result\ResultInterface;

/**
 * Api V2 Response Normalizer Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface ApiV2ResponseNormalizerInterface
{
    /**
     * Normalize an api v2 response.
     *
     * @param \Solarium\Core\Query\Result\ResultInterface $result
     *
     * @return mixed
     */
    public function normalize(ResultInterface $result);
}
