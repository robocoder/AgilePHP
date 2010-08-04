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
 * Ensures that each object gets loaded only once by 
 * keeping every loaded object in a map. Looks up
 * objects using the map when referring to them.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 */
class IdentityMap {

      private static $map = array();

      /**
       * Retrieves the requested model from the IdentityMap.
       * 
       * @param ActiveRecord $model An ActiveRecord model instance to inspect
       * @return mixed The ActiveRecord model if its already been looked up and
       *               cached, false otherwise.  
       */
      public static function get(ActiveRecord $model) {

             $table = ORMFactory::getDialect()->getTableByModel($model);

             // If this is a many-to-many relationships, primary keys are foriegn key values
   	         $pkeys = $table->getPrimaryKeyColumns();
	     	 foreach($pkeys as $pkey) {
	     	     if($pkey->isForeignKey()) {
	     	        $pkeys = $table->getForeignKeyColumns();
     	         }
   	         }

   	         // Create a unique class "key" used to distinguish the ActiveRecord
   	         $count = count($pkeys);
   	         $key = ''; 
   	         for($i=0; $i<$count; $i++ ) {

   	             $accessor = 'get' . ucfirst($pkeys[$i]->getModelPropertyName());
   	             $key .= $model->$accessor();
   	             if(($i+1) < $count) $key .= '_-';
   	         }

   	         // Get the model class name
             $class = new ReflectionClass($model);
             $name = $class->getName();

             // If the ActiveRecord has already been pulled, return it
             if(isset(self::$map[$name][$key]))
                return self::$map[$name][$key];

             return false;
      }

      /**
       * Adds a new ActiveRecord model instance to the IdentityMap stack.
       * 
       * @param ActiveRecord $model The ActiveRecord instance to add to the stack.
       * @return void
       */
      public static function addModel(ActiveRecord $model) {

             $table = ORMFactory::getDialect()->getTableByModel($model);

             Log::debug('IdentityMap::addModel Storing model \'' . $table->getModel() . '\'.');
             
             // If this is a many-to-many relationships, primary keys are foriegn key values
   	         $pkeys = $table->getPrimaryKeyColumns();
	     	 foreach($pkeys as $pkey) {
	     	     if($pkey->isForeignKey()) {
	     	        $pkeys = $table->getForeignKeyColumns();
     	         }
   	         }

   	         // Create a unique class "key" used to distinguish the ActiveRecord
   	         $count = count($pkeys);
   	         $key = ''; 
   	         for($i=0; $i<$count; $i++ ) {

   	             $accessor = 'get' . ucfirst($pkeys[$i]->getModelPropertyName());
   	             $key .= $model->$accessor();
   	             if(($i+1) < $count) $key .= '_-';
   	         }

   	         // Get the model class name
             $class = new ReflectionClass($model);
  	         
             self::$map[$class->getName()][$key] = $model;
      }
}
?>