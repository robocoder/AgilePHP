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
       * Returns the static identity map used to track domain model state.
       * 
       * @return array Identity map
       */
      public static function getMap() {

             return self::$map;
      }

      /**
       * Retrieves the requested model from the IdentityMap.
       * 
       * @param DomainModel $model An ActiveRecord model instance to inspect
       * @return mixed The ActiveRecord model if its already been looked up and
       *               cached, false otherwise.  
       */
      public static function get(DomainModel $model) {

             list($class, $key) = self::createKey($model);

             if($cacher = AgilePHP::getCacher()) {

                $cacheKey = 'AGILEPHP_ORM_IDENTITYMAP_' . $class . '_' . $key;
                if($cacher->exists($cacheKey)) return $cacher->get($cacheKey);
             }

             // If the DomainModel has already been pulled, return it
             if(isset(self::$map[$class][$key])) {

                if(isset($cacher)) $cacher->set($cacheKey, self::$map[$class][$key]);
                return self::$map[$class][$key];
             }

             return false;
      }

      /**
       * Adds a new DomainModel model instance to the IdentityMap stack.
       * 
       * @param DomainModel $model The DomainModel instance to add to the stack.
       * @return void
       */
      public static function add(DomainModel $model) {

             list($class, $key) = self::createKey($model);

             self::$map[$class][$key] = $model;

             if($cacher = AgilePHP::getCacher()) {

                $cacheKey = 'AGILEPHP_ORM_IDENTITYMAP_' . $class . '_' . $key;
                $cacher->set($cacheKey, $model);
             }
      }

      /**
       * Removes a model from the IdentityMap (and cache if an AgilePHP CacheProvider
       * is enabled).
       * 
       * @param DomainModel $model The DomainModel instance to remove.
       * @return void
       */
      public static function remove(DomainModel $model) {

             list($class, $key) = self::createKey($model);

             if(isset(self::$map[$class][$key]))
                unset(self::$map[$class][$key]);

             if($cacher = AgilePHP::getCacher()) {

                $cacheKey = 'AGILEPHP_ORM_IDENTITYMAP_' . $class . '_' . $key;
                if($cacher->exists($cacheKey)) $cacher->delete($key);
             }
      }

      /**
       * Create a unique class "key" used to distinguish the DomainModel. This algorithm
   	   * simple appends each primary key value and performs some generic sanitation so the
   	   * values can be used as an associative array key.
   	   *
       * @param DomainModel $model The DomainModel instance to persist
       * @return void
       */
      private static function createKey(DomainModel $model) {

              $table = ORMFactory::getDialect()->getTableByModel($model);

              // If this is a many-to-many relationship, primary keys are foriegn key values
   	          $pkeys = $table->getPrimaryKeyColumns();
   	          $fkeys = $table->getForeignKeyColumns();

   	          $count = count($pkeys);
   	          $key = ''; 
   	          for($i=0; $i<$count; $i++ ) {

   	              $accessor = 'get' . ucfirst($pkeys[$i]->getModelPropertyName());

   	              if(is_object($model->$accessor())) {

    		 		 for($i=0; $i<count($fkeys); $i++) {

    		 		     $fkey = $fkeys[$i]->getForeignKey();
    		 		     $key .= '_' . $fkey->getReferencedTableInstance()->getModel() . '_' . $fkey->getReferencedColumn();
    		 		 }
   	              }
   	              else
   	                $key .= $model->$accessor();

   	              if(($i+1) < $count) $key .= '_-';
   	          }

   	          return array($class = get_class($model), preg_replace('/[\s]+/', '_', $key));
      }
}
?>