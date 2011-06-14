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
 * Base implementation for domain models (a model with business logic). Provides
 * helper methods for CRUD (create/read/update/delete) operations.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 * @abstract
 */
abstract class DomainModel extends DataModel {

    /**
     * Persists/saves the DomainModel ActiveRecord state to
     * the data source.
     *
     * @return void
     * @throws ORMException
     */
    public function persist() {

        $dialect = ORMFactory::getDialect();
        $dialect->persist($this);
        return $dialect->getLastInsertId();
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

        if(!$model = ORMFactory::getDialect()->get($this)) return false;

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

        return ORMFactory::getDialect()->merge($this);
    }

    /**
     * Deletes/destroys the data source record mapped by the
     * DomainModel ActiveRecord state.
     *
     * @return void
     * @throws ORMException
     */
    public function delete() {

        return ORMFactory::getDialect()->delete($this);
    }

    /**
     * Performs a SELECT * on the mapped table.
     *
     * @return array An array of records or an empty array if no records were located
     */
    public function find($maxResults = 25) {

        ORMFactory::getDialect()->setMaxResults($maxResults);
        return ORMFactory::getDialect()->find($this);
    }

    /**
     * Nulls out all model property values
     *
     * @return void
     */
    public function clear() {

        $table = ORMFactory::getDialect()->getTableByModel($this);
        $columns = $table->getColumns();

 			    for($i=0; $i<count($columns); $i++) {

 			        $mutator = 'set' . ucfirst($columns[$i]->getModelPropertyName());
 			        $this->$mutator(null);
 			    }
    }

    /**
     * Calls a stored procedure
     *
     * @return mixed Single scalar value or DomainModel instance depending on the return values configured
     */
    public function call() {

        return ORMFactory::getDialect()->call($this);
    }
}
?>