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
 * Represents a procedure in the AgilePHP orm component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 */
class Procedure {

	  private $name;
	  private $model;
	  private $display;
	  private $description;

	  private $parameters = array();

	  /**
	   * Creates a new Procedure instance.
	   * 
	   * @param SimpleXMLElement $procedure The SimpleXMLElement instance representing the procedure.
	   * @return void
	   */
	  public function __construct(SimpleXMLElement $procedure = null) {

	  		 if($procedure) {

		  		 $this->name =(string)$procedure->attributes()->name;
		  		 $this->model =(string)$procedure->attributes()->model;
		  		 $this->display =(string)$procedure->attributes()->display;
		  		 $this->description =(string)$procedure->attributes()->description;

		  		 foreach($procedure->parameter as $parameter)
		  		 		  array_push($this->parameters, new ProcedureParam($parameter));
	  		 }
	  }

	  /**
	   * Sets the procedure name
	   * 
	   * @param string $name The name of the procedure
	   * @return void
	   */
	  public function setName($name) {

	  		 $this->name = $name;
	  }

	  /**
	   * Gets the name of the procedure
	   * 
	   * @return string The procedure name
	   */
	  public function getName() {

	  		 return $this->name;
	  }

	  /**
	   * Sets the model responsible for the procedure state
	   * 
	   * @param string $model The class name of the model responsible for the procedure's ActiveRecord state.
	   * @return void 
	   */
	  public function setModel($model) {

	  		 $this->model = $model;
	  }

	  /**
	   * Gets the model responsible for the procedure state
	   * 
	   * @return string The class name of the model responsible for the procedure's ActiveRecord state. 
	   */
	  public function getModel() {

	  		 return $this->model;
	  }

	  /**
	   * Sets the display name for the procedure.
	   * 
	   * @param string $display A friendly display name for the procedure
	   * @return void
	   */
	  public function setDisplay($display) {

	  		 $this->display = $display;
	  }

	  /**
	   * Gets the display name for the procedure
	   * 
	   * @return string The friendly display name for the procedure
	   */
	  public function getDisplay() {

	  		 return $this->display;
	  }

	  /**
	   * Sets a description for the procedure
	   * 
	   * @param string $description A user friendly description about the procedure
	   * @return void
	   */
	  public function setDescription($description) {

	  		 $this->description = $description;
	  }

	  /**
	   * Gets the description for the procedure
	   * 
	   * @return string A user friendly description about the procedure
	   */
	  public function getDescription() {

	  		 return $this->description;
	  }

	  /**
	   * Sets a list of parameters
	   * 
	   * @param array<ProcedureParam> An array of ProcedureParam instances which represent IN, OUT, and INOUT parameters
	   * @return void
	   */
	  public function setParameters(array $parameters) {

	         $this->parameters = $parameters;
	  }
	  
	  /**
	   * Gets a list of parameters
	   * 
	   * @return array<ProcedureParam> An array of ProcedureParam instances which represent IN, OUT, and INOUT parameters
	   */
	  public function getParameters() {

	  		 return $this->parameters;
	  }
}
?>