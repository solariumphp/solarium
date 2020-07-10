<?php

declare(strict_types=1);

namespace Solarium\Manager\Normalizer;

use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Manager\Contract\ApiV2ResponseNormalizerInterface;

/**
 * No-operation Response Normalizer.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class NoopResponseNormalizer implements ApiV2ResponseNormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize(ResultInterface $result)
    {
        return $result;
    }
}
