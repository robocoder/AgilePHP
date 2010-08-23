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
 * Base implementation for "business" stored procedures (sprocs with business logic). Provides
 * helper methods for CRUD (create/read/update/delete) operations.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 * @abstract
 */
abstract class StoredProcedure extends DomainModel {

         const ACTION_DEFAULT = NULL;
         const ACTION_PERSIST = 1;
         const ACTION_MERGE = 2;
         const ACTION_DELETE = 3;
         const ACTION_GET = 4;
         const ACTION_FIND = 5;

         /**
          * Persists/saves the StoredProcedure ActiveRecord state to
          * the data source.
          *
          * @return void
          * @throws ORMException
          */
         public function persist() {

                return ORMFactory::getDialect()->call($this, self::ACTION_PERSIST);
         }

		 /**
          * Looks up the ActiveRecord state from the data source using
          * values for fields which map to primary keys.
          *
          * @return void
          * @throws ORMException
          * @throws FrameworkException
          */
         public function get() {

                if(!$model = ORMFactory::getDialect()->call($this, self::ACTION_GET)) return false;

                // @todo Interceptors are still being somewhat intrusive to reflection operations
	  		    if(method_exists($model, 'getInterceptedInstance')) {

	  		       $data = $model->getInterceptedInstance();
	  		       $class = new ReflectionClass($data);
	  		    }
	  		    else {

	  		       $class = new ReflectionClass($model);
	  		       $data = $model;
	  		    }

                foreach($class->getProperties() as $property) {

		  		 		 $context = null;
		  		 		 if($property->isPublic())
		  		 		  	$context = 'public';
		  		 		 else if($property->isProtected())
		  		 		 	$context = 'protected';
		  		 		 else if($property->isPrivate())
		  		 		  	 $context = 'private';

		  		 		 $value = null;
		  		 		 if($context != 'public') {

		  		 		  	$property->setAccessible(true);
				  		 	$value = $property->getValue($data);
				  		 	$property->setAccessible(false);
		  		 		 }
		  		 		 else
		  		 		  	$value = $property->getValue($data);

		  		 		 $mutator = 'set' . ucfirst($property->getName());
		  		 		 $this->$mutator($value);
                }
         }

		 /**
          * Merges/updates the data source record mapped by the
          * DomainModel ActiveRecord state.
          *
          * @return void
          * @throws ORMException
          */
         public function merge() {

                return ORMFactory::getDialect()->call($this, self::ACTION_MERGE);
         }

		 /**
          * Deletes/destroys the data source record mapped by the
          * StoredProcedure ActiveRecord state.
          *
          * @return void
          * @throws ORMException
          */
         public function delete() {

                return ORMFactory::getDialect()->call($this, self::ACTION_DELETE);
         }

		 /**
          * Calls/executes a stored procedure mapped to this StoredProcedure in orm.xml
          *
          * @return void
          * @throws ORMException
          */
         public function call() {

                return ORMFactory::getDialect()->call($this, self::ACTION_DEFAULT);
         }

         /**
          * Calls/executes the stored procedure
          *
          * @return array An array of records or an empty array if no records were located
          */
         public function find($maxResults = 25) {

                ORMFactory::getDialect()->setMaxResults($maxResults);
                return ORMFactory::getDialect()->call($this, self::ACTION_FIND);
         }

		 /**
          * Nulls out all model property values
          * 
          * @return void
          */
         public function clear() {

                $proc = ORMFactory::getDialect()->getProcedureByModel($this);
	  	        $params = $proc->getParameters();

 			    for($i=0; $i<count($params); $i++) {

	  	     	    $mutator = ORMFactory::getDialect()->toMutator($params[$i]->getModelPropertyName());
	  	     	    $this->$mutator(null);
	  	        }
         }
}
?>