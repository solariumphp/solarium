<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\TermsInterface;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Build a Terms query request.
 */
class Terms implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for Terms.
     *
     * @param TermsInterface $component
     * @param Request        $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        $request->addParam('terms', true);
        $request->addParam('terms.lower', $component->getLowerbound());
        $request->addParam('terms.lower.incl', $component->getLowerboundInclude());
        $request->addParam('terms.mincount', $component->getMinCount());
        $request->addParam('terms.maxcount', $component->getMaxCount());
        $request->addParam('terms.prefix', $component->getPrefix());
        $request->addParam('terms.regex', $component->getRegex());
        $request->addParam('terms.limit', $component->getLimit());
        $request->addParam('terms.upper', $component->getUpperbound());
        $request->addParam('terms.upper.incl', $component->getUpperboundInclude());
        $request->addParam('terms.raw', $component->getRaw());
        $request->addParam('terms.sort', $component->getSort());

        foreach ($component->getFields() as $field) {
            $request->addParam('terms.fl', trim($field));
        }

        foreach ($component->getRegexFlags() as $flag) {
            $request->addParam('terms.regex.flag', trim($flag));
        }

        return $request;
    }
}
