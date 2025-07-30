<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Stream;

use Solarium\Exception\InvalidArgumentException;

/**
 * Stream expression builder.
 *
 * @method string abs(...$args)
 * @method string acos(...$args)
 * @method string add(...$args)
 * @method string akima(...$args)
 * @method string analyze(...$args)
 * @method string and(...$args)
 * @method string anova(...$args)
 * @method string array(...$args)
 * @method string asc(...$args)
 * @method string asin(...$args)
 * @method string atan(...$args)
 * @method string betaDistribution(...$args)
 * @method string bicubicSpline(...$args)
 * @method string binomialCoefficient(...$args)
 * @method string binomialDistribution(...$args)
 * @method string canberra(...$args)
 * @method string cartesianProduct(...$args)
 * @method string cbrt(...$args)
 * @method string ceil(...$args)
 * @method string chiSquareDataset(...$args)
 * @method string classify(...$args)
 * @method string col(...$args)
 * @method string colAt(...$args)
 * @method string columnCount(...$args)
 * @method string commit(...$args)
 * @method string complement(...$args)
 * @method string concat(...$args)
 * @method string constantDistribution(...$args)
 * @method string conv(...$args)
 * @method string convexHull(...$args)
 * @method string copyOf(...$args)
 * @method string copyOfRange(...$args)
 * @method string corr(...$args)
 * @method string cos(...$args)
 * @method string cosh(...$args)
 * @method string cosineSimilarity(...$args)
 * @method string cov(...$args)
 * @method string cumulativeProbability(...$args)
 * @method string daemon(...$args)
 * @method string derivative(...$args)
 * @method string describe(...$args)
 * @method string diff(...$args)
 * @method string distance(...$args)
 * @method string div(...$args)
 * @method string dotProduct(...$args)
 * @method string earthMovers(...$args)
 * @method string ebeAdd(...$args)
 * @method string ebeDivide(...$args)
 * @method string ebeMultiply(...$args)
 * @method string ebeSubtract(...$args)
 * @method string echo(...$args)
 * @method string empiricalDistribution(...$args)
 * @method string enclosingDisk(...$args)
 * @method string enumeratedDistribution(...$args)
 * @method string eor(...$args)
 * @method string eq(...$args)
 * @method string euclidean(...$args)
 * @method string eval(...$args)
 * @method string executor(...$args)
 * @method string expMovingAge(...$args)
 * @method string expMovingAvg(...$args)
 * @method string facet(...$args)
 * @method string factorial(...$args)
 * @method string features(...$args)
 * @method string fetch(...$args)
 * @method string fft(...$args)
 * @method string finddelay(...$args)
 * @method string floor(...$args)
 * @method string freqTable(...$args)
 * @method string fuzzyKmeans(...$args)
 * @method string gammaDistribution(...$args)
 * @method string gaussfit(...$args)
 * @method string geometricDistribution(...$args)
 * @method string getArea(...$args)
 * @method string getAttribute(...$args)
 * @method string getAttributes(...$args)
 * @method string getBaryCenter(...$args)
 * @method string getBoundarySize(...$args)
 * @method string getCache(...$args)
 * @method string getCenter(...$args)
 * @method string getCentroids(...$args)
 * @method string getColumnLabels(...$args)
 * @method string getMembershipMatrix(...$args)
 * @method string getRadius(...$args)
 * @method string getRowLabels(...$args)
 * @method string getSupportPoints(...$args)
 * @method string getValue(...$args)
 * @method string getVertices(...$args)
 * @method string grandSum(...$args)
 * @method string gt(...$args)
 * @method string gteq(...$args)
 * @method string gTestDataSet(...$args)
 * @method string harmfit(...$args)
 * @method string harmonicFit(...$args)
 * @method string hashJoin(...$args)
 * @method string haversineMeters(...$args)
 * @method string having(...$args)
 * @method string hist(...$args)
 * @method string hsin(...$args)
 * @method string if(...$args)
 * @method string indexOf(...$args)
 * @method string innerJoin(...$args)
 * @method string integrate(...$args)
 * @method string intersect(...$args)
 * @method string jdbc(...$args)
 * @method string kmeans(...$args)
 * @method string knnRegress(...$args)
 * @method string knnSearch(...$args)
 * @method string kolmogorovSmirnov(...$args)
 * @method string ks(...$args)
 * @method string l1norm(...$args)
 * @method string l2norm(...$args)
 * @method string latlonVectors(...$args)
 * @method string leftOuterJoin(...$args)
 * @method string length(...$args)
 * @method string lerp(...$args)
 * @method string let(...$args)
 * @method string linfnorm(...$args)
 * @method string list(...$args)
 * @method string listCache(...$args)
 * @method string loess(...$args)
 * @method string log(...$args)
 * @method string log10(...$args)
 * @method string logNormalDistribution(...$args)
 * @method string lt(...$args)
 * @method string lteq(...$args)
 * @method string ltrim(...$args)
 * @method string manhattan(...$args)
 * @method string mannWhitney(...$args)
 * @method string markovChain(...$args)
 * @method string matrix(...$args)
 * @method string matrixMult(...$args)
 * @method string mean(...$args)
 * @method string meanDifference(...$args)
 * @method string merge(...$args)
 * @method string minMaxScale(...$args)
 * @method string mod(...$args)
 * @method string model(...$args)
 * @method string monteCarlo(...$args)
 * @method string movingAvg(...$args)
 * @method string movingMedian(...$args)
 * @method string mult(...$args)
 * @method string multiKmeans(...$args)
 * @method string multivariateNormalDistribution(...$args)
 * @method string nodes(...$args)
 * @method string normalDistribution(...$args)
 * @method string normalizeSum(...$args)
 * @method string not(...$args)
 * @method string null(...$args)
 * @method string olsRegress(...$args)
 * @method string or(...$args)
 * @method string oscillate(...$args)
 * @method string outerHashJoin(...$args)
 * @method string pairedTtest(...$args)
 * @method string pairSort(...$args)
 * @method string parallel(...$args)
 * @method string percentile(...$args)
 * @method string pivot(...$args)
 * @method string plist(...$args)
 * @method string poissonDistribution(...$args)
 * @method string polyFit(...$args)
 * @method string pow(...$args)
 * @method string precision(...$args)
 * @method string predict(...$args)
 * @method string primes(...$args)
 * @method string priority(...$args)
 * @method string probability(...$args)
 * @method string putCache(...$args)
 * @method string random(...$args)
 * @method string rank(...$args)
 * @method string raw(...$args)
 * @method string recip(...$args)
 * @method string reduce(...$args)
 * @method string regress(...$args)
 * @method string removeCache(...$args)
 * @method string rev(...$args)
 * @method string rollup(...$args)
 * @method string round(...$args)
 * @method string rowAt(...$args)
 * @method string rowCount(...$args)
 * @method string rtrim(...$args)
 * @method string sample(...$args)
 * @method string scalarAdd(...$args)
 * @method string scalarDivide(...$args)
 * @method string scalarMultiply(...$args)
 * @method string scalarSubtract(...$args)
 * @method string scale(...$args)
 * @method string scoreNodes(...$args)
 * @method string search(...$args)
 * @method string select(...$args)
 * @method string sequence(...$args)
 * @method string setAttributes(...$args)
 * @method string setColumnLabels(...$args)
 * @method string setRowLabels(...$args)
 * @method string setValue(...$args)
 * @method string shortestPath(...$args)
 * @method string shuffle(...$args)
 * @method string significantTerms(...$args)
 * @method string sin(...$args)
 * @method string sinh(...$args)
 * @method string sort(...$args)
 * @method string spline(...$args)
 * @method string sqrt(...$args)
 * @method string standardize(...$args)
 * @method string stats(...$args)
 * @method string sub(...$args)
 * @method string sumColumns(...$args)
 * @method string sumDifference(...$args)
 * @method string sumRows(...$args)
 * @method string sumSq(...$args)
 * @method string tan(...$args)
 * @method string tanh(...$args)
 * @method string termVectors(...$args)
 * @method string timeseries(...$args)
 * @method string top(...$args)
 * @method string topFeatures(...$args)
 * @method string topic(...$args)
 * @method string train(...$args)
 * @method string transpose(...$args)
 * @method string triangularDistribution(...$args)
 * @method string ttest(...$args)
 * @method string tuple(...$args)
 * @method string uniformDistribution(...$args)
 * @method string uniformIntegerDistribution(...$args)
 * @method string unique(...$args)
 * @method string unitize(...$args)
 * @method string update(...$args)
 * @method string weibullDistribution(...$args)
 * @method string zipFDistribution(...$args)
 * @method string zscores(...$args)
 */
class ExpressionBuilder
{
    /**
     * @param string $name
     * @param array  $arguments
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function __call(string $name, array $arguments): string
    {
        return $name.'('.implode(', ', array_filter($arguments, function ($value) {
            if (\is_array($value) || (\is_object($value) && !method_exists($value, '__toString'))) {
                throw new InvalidArgumentException('An expression argument must be a scalar value or an object that provides a __toString() method.');
            }
            if (\is_string($value)) {
                $value = trim($value);
            }

            // Eliminate empty string arguments.
            return '' !== $value;
        })).')';
    }

    /**
     * Format and indent a streaming expression.
     *
     * @param string $expression
     *
     * @return string
     */
    public static function indent(string $expression): string
    {
        $currentIndentation = 0;
        $indentationStep = 2;
        $indentedExpression = '';
        $len = \strlen($expression);
        for ($c = 0; $c < $len; ++$c) {
            if ('(' === $expression[$c]) {
                $indentedExpression .= $expression[$c].PHP_EOL;
                $currentIndentation += $indentationStep;
                $indentedExpression .= str_pad('', $currentIndentation);
            } elseif (')' === $expression[$c]) {
                $currentIndentation -= $indentationStep;
                $indentedExpression .= PHP_EOL;
                $indentedExpression .= str_pad('', $currentIndentation).$expression[$c];
            } elseif (',' === $expression[$c]) {
                $indentedExpression .= $expression[$c].PHP_EOL.str_pad('', $currentIndentation);
                // swallow space if any
                if (' ' === @$expression[$c + 1]) {
                    ++$c;
                }
            } else {
                $indentedExpression .= $expression[$c];
            }
        }

        return $indentedExpression;
    }
}
