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

class Solarium_Client_NestedQueryBuilder_Select_Component_DisMax extends Solarium_Client_NestedQueryBuilder
{
    /**
     * The components apparently overwrite the parent Solarium_Configurable functionality in a less usable way. Boo.
     */
    protected $subQueryMethods = array(  'qf'        =>    'getQueryFields',
                                         'q.alt'     =>    'getQueryAlternative',
                                         'mm'        =>    'getMinimumMatch',
                                         'pf'        =>    'getPhraseFields',
                                         'ps'        =>    'getPhraseSlop',
                                         'qs'        =>    'getQueryPhraseSlop',
                                         'tie'       =>    'getTie',
                                         'bq'        =>    'getBoostQuery',
                                         'bf'        =>    'getBoostFunctions'
                                      );
    
    protected $subQueryParams = array(  'qf',
                                        'q.alt',
                                        'qf',
                                        'mm',
                                        'pf',
                                        'ps',
                                        'qs',
                                        'tie',
                                        'bq',
                                        'bf'
                                    );
    
    /**
     * @see Solarium_Client_NestedQueryBuilder::getDefType()
     */
    public function getDefType($query)
    {
        return $query->getComponent(Solarium_Query_Select::COMPONENT_DISMAX)->getQueryParser();
    }
    
    protected function getParamsFromQuery( Solarium_Query_Select $query )
    {
        $params = $query->getParams();

        $dismaxComponent = $query->getComponent(Solarium_Query_Select::COMPONENT_DISMAX);
        var_dump($params); 
        foreach ($this->subQueryMethods as $paramKey => $componentMethod) 
        {
            $params[$paramKey] = $dismaxComponent->{$componentMethod}();
        }
        var_dump($dismaxComponent);
        var_dump($params);
        return $params;
        
    }
}