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
 * Represents a NamedQuery in the AgilePHP ORM component. "Named queries" allows
 * developer defined SQL statements to be defined in the configuration and used
 * in the application.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 */
class NamedQuery {

    private $name;
    private $model;
    private $query;
    private $prepared;
    private $procedure;

    /**
     * Initializes a new NamedQuery instance
     *
     * @param SimpleXMLElement $query The query element as specified in the orm configuration
     * @return void
     */
    public function __construct(SimpleXMLElement $query) {

        $this->name = (string)$query->attributes()->name;
        $this->model = (string)$query->attributes()->model;
        $this->prepared = (string)$query->attributes()->prepared;
        $this->procedure = (string)$query->attributes()->procedure;
        $this->query = (string)$query;
    }

    /**
     * Sets the (unique) query name
     *
     * @param string $name The query name
     * @return void
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the name of the query
     *
     * @return string The (unique) query name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the DataModel used to map variables/return values.
     *
     * @param string $model The name of the DataModel responsible for the query variable/return values
     * @return void
     */
    public function setModel($model) {
        $this->model = $model;
    }

    /**
     * Returns the DataModel used to map variable/return values.
     *
     * @return string The model that represents the query variable/return values
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Sets the SQL query to execute
     *
     * @param string $query The SQL query
     * @return void
     */
    public function setQuery($query) {
        $this->query = $query;
    }

    /**
     * Returns the SQL query
     *
     * @return void
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Sets flag indicating whether or not this is a prepared statement
     *
     * @param boolean $prepared True if this is a prepared statement
     * @return void
     */
    public function setPrepared($prepared) {
        $this->prepared = $prepared;
    }

    /**
     * Returns flag indicating whether or not this is a prepared statement
     *
     * @return string True if this is a prepared statement, false otherwise
     */
    public function isPrepared() {
        return $this->prepared ? true : false;
    }

    /**
     * Sets flag indicating whether or not this is a stored procedure
     *
     * @param boolean $procedure True if this is a stored procedure
     * @return void
     */
    public function setProcedure($procedure) {
        $this->procedure = $procedure;
    }

    /**
     * Returns boolean flag used to indicate whether or not this is a stored procedure
     *
     * @return boolean True if this is a stored procedure, false otherwise
     */
    public function isProcedure() {
        return $this->proedure ? true : false;
    }
}
?>