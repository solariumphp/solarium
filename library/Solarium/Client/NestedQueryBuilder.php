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
 * @subpackage Client
 * @author Robert Elwell <robert@wikia-inc.com>
 */

class Solarium_Client_NestedQueryBuilder extends Solarium_Client_Builder
{
    protected $subQueryParams = array();
    
    /**
     * Build nested query string 
     * @see Solarium_Client_Builder::build()
     * @param Solarium_Query_Select $query
     * @return string
     */
    public function build( $query )
    {
        return sprintf('_query_:"{!%s %s}%s"', $this->getDefType($query), 
                                               $this->constructParamString($this->getSubQueryParams($this->getParamsFromQuery($query))),
                                               $query->getQuery()
                      );       
        
    }
    
    /**
     * Filters out non-subquery params 
     * @param array $params
     * @return array
     */
    protected function getSubQueryParams( array $params )
    {
        return array_intersect_key($params, array_flip($this->subQueryParams));
    }
    
    /**
     * Provides def type for subquery
     * 
     * @param Solarium_Query_Select $query
     * @return string
     */
    public function getDefType( Solarium_Query_Select $query )
    {
        return 'lucene';
    }
    
    /**
     * Iterate over specified parameters to include nested query params
     * @param array $params
     * @return string
     */
    protected function constructParamString(array $params)
    {
        $paramString = '';
        foreach ( $params as $name => $value )
        {
            if ( $value !== null ) {
                $paramString .= sprintf("%s=\\'%s\\' ", $name, $value);
            }
        }
        return $paramString;
    } 
    
    /**
     * OO way of grabbing params from query so "components" can add theirs
     * 
     * @param Solarium_Query_Select $query
     * @return array
     */
    protected function getParamsFromQuery( Solarium_Query_Select $query )
    {
        return $query->getParams();
    }
} 