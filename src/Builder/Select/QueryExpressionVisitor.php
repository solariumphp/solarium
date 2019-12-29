<?php

declare(strict_types=1);

namespace Solarium\Builder\Select;

use Solarium\Builder\AbstractExpressionVisitor;
use Solarium\Builder\Comparison;
use Solarium\Builder\CompositeComparison;
use Solarium\Builder\ExpressionInterface;
use Solarium\Builder\Value;
use Solarium\Core\Query\Helper;
use Solarium\Exception\RuntimeException;

/**
 * Query Expression Visitor.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class QueryExpressionVisitor extends AbstractExpressionVisitor
{
    /**
     * @var Helper
     */
    private $helper;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->helper = new Helper();
    }

    /**
     * @param \Solarium\Builder\ExpressionInterface $expression
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return mixed|string
     */
    public function walkExpression(ExpressionInterface $expression)
    {
        $field = $expression->getField();
        $value = $expression->getValue()->getValue();

        switch ($expression->getOperator()) {
            case Comparison::EQ:
            case Comparison::NEQ:
                $strValue = $this->valueToString($value, ',', '"');

                if ($value instanceof \DateTime) {
                    $strValue = sprintf('[%1$s TO %1$s]', $strValue);
                }

                $not = (Comparison::NEQ === $expression->getOperator()) ? '-' : '';

                return sprintf('%s%s:%s', $not, $field, $strValue);
            case Comparison::GT:
                return sprintf('%s:{%s TO *]', $field, $this->valueToString($value));
            case Comparison::GTE:
                return sprintf('%s:[%s TO *]', $field, $this->valueToString($value));
            case Comparison::LT:
                return sprintf('%s:[* TO %s}', $field, $this->valueToString($value));
            case Comparison::LTE:
                return sprintf('%s:[* TO %s]', $field, $this->valueToString($value));
            case Comparison::RANGE:
                if (\is_array($value)) {
                    if (2 === \count($value)) {
                        return sprintf('%s:[%s TO %s]', $field, $this->valueToString($value[0]), $this->valueToString($value[1]));
                    }

                    if (1 === \count($value)) {
                        return sprintf('%s:[%s TO *]', $field, $this->valueToString($value[0]));
                    }
                }

                throw new RuntimeException(sprintf('Invalid range value: %s', $value));
            case Comparison::IN:
                if (\is_array($value)) {
                    return sprintf('%s:(%s)', $field, $this->valueToString($value, ' OR ', '"'));
                }

                return sprintf('%s:%s', $field, $this->valueToString($value, ',', '"'));
            case Comparison::LIKE:
            case Comparison::MATCH:
                if (\is_array($value)) {
                    return sprintf('%s:(%s)', $field, $this->valueToString($value, ' OR ', '', false));
                }

                return sprintf('%s:%s', $field, $this->valueToString($value, ',', '', false));
            case Comparison::NIN:
                if (\is_array($value)) {
                    return sprintf('-%s:(%s)', $field, $this->valueToString($value, ' OR ', '"'));
                }

                return sprintf('-%s:%s', $field, $this->valueToString($value, ',', '"'));
            case Comparison::REGEXP:
                if ('/' !== $value[0]) {
                    $value = sprintf('/%s/', $value);
                }

                return sprintf('%s:%s', $field, $this->valueToString($value, ',', '', false));
            default:
                throw new RuntimeException('Unknown comparison operator: '.$expression->getOperator());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function walkValue(Value $value)
    {
        return $value->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Solarium\Exception\RuntimeException
     */
    public function walkCompositeExpression(ExpressionInterface $expr)
    {
        $comparisons = [];

        foreach ($expr->getComparisons() as $child) {
            $comparisons[] = $this->dispatch($child);
        }

        switch ($expr->getType()) {
            case CompositeComparison::TYPE_AND:
                return implode(' AND ', $comparisons);
            case CompositeComparison::TYPE_OR:
                return implode(' OR ', $comparisons);
            default:
                throw new RuntimeException('Unknown composite '.$expr->getType());
        }
    }

    /**
     * @param mixed  $value
     * @param string $separator
     * @param string $quote
     * @param bool   $escape
     *
     * @return string
     */
    private function valueToString($value, string $separator = ',', string $quote = '', bool $escape = true): string
    {
        if (\is_array($value)) {
            $ret = [];

            foreach ($value as $v) {
                $ret[] = $this->typedValueToString($v, $quote, $escape);
            }

            return implode($separator, $ret);
        }

        return $this->typedValueToString($value, $quote, $escape);
    }

    /**
     * @param mixed  $value
     * @param string $quote
     * @param bool   $escape
     *
     * @return string
     */
    private function typedValueToString($value, string $quote = '', $escape = true): string
    {
        if (null === $value) {
            return '[* TO *]';
        }

        if ($value instanceof \DateTime) {
            return $this->helper->formatDate($value);
        }

        if (true === $escape && \is_string($value)) {
            $value = $this->helper->escapeTerm($value);
        }

        if (\is_string($value)) {
            $value = sprintf('%1$s%2$s%1$s', $quote, $value);
        }

        return (string) $value;
    }
}
