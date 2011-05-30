<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 */

/**
 * Escape data for usage in a Solr query
 *
 * Any (user) input for a query can be passed to one of the escape methods to
 * prevent any issues with special characters.
 *
 * Do mind that you cannot build a complete query first and then pass it to
 * this method, the whole query will be escaped. You need to escape only the
 * 'content' of your query.
 *
 * @package Solarium
 */
class Solarium_Escape
{

    /**
     * Escape a term
     *
     * A term is a single word.
     * All characters that have a special meaning in a Solr query are escaped.
     *
     * If you want to use the input as a phrase please use the {@link phrase()}
     * method, because a phrase requires much less escaping.\
     *
     * @link http://lucene.apache.org/java/docs/queryparsersyntax.html#Escaping%20Special%20Characters
     *
     * @param string $input
     * @return string
     */
    static public function term($input)
    {
        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';

        return preg_replace($pattern, '\\\$1', $input);
    }

    /**
     * Escape a phrase
     *
     * A phrase is a group of words.
     * Special characters will be escaped and the phrase will be surrounded by
     * double quotes to group the input into a single phrase. So don't put
     * quotes around the input.
     *
     * @param string $input
     * @return string
     */
    static public function phrase($input)
    {
        return '"' . preg_replace('/("|\\\)/', '\\\$1', $input) . '"';
    }

    

}