<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp.orm
 */

/**
 * AgilePHP interceptor responsible for performing SQL select to hydrate a
 * domain model object ActiveRecord with its current record state when its
 * 'id' mutator is called.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 * <code>
 * #@Id
 * public function setId($id) {
 *
 * 		  $this->id = $id;
 * }
 * </code>
 */
#@Interceptor
class Id {

    /**
     * Hydrates model ActiveRecord state
     *
     * @param InvocationContext $ic The interceptor invocation context
     * @return mixed InvocationContext if the context of the call has changed, null otherwise.
     */
    #@AroundInvoke
    public function hydrate(InvocationContext $ic) {

        $callee = $ic->getCallee();
        $pieces = explode(DIRECTORY_SEPARATOR, $callee['file']);
        $className = str_replace('.php', '', array_pop($pieces));

        // Dont populate calls made from ORM
        if($className == 'Id' || preg_match('/dialect$/i', $className))
        return $ic->proceed();

        $class = $callee['class'];
        $mutator = $callee['function'];

        $params = $ic->getParameters();
        if(!$params[0] || $params[0] == '') return;

        $model = new $class;
        $model->$mutator($params[0]);

        $activeRecord = ORM::find($model);

        return(count($activeRecord)) ?
            ClassUtils::copy($activeRecord[0]->getInterceptedInstance(), $ic->getTarget()) :
            $ic->proceed();
    }
}
?>