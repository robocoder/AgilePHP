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
 * @package com.makeabyte.agilephp.mvc
 */

/**
 * Provides base EXTJS form processing (formats response as JSON
 * as Ext expects).
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 */
class ExtFormRenderer extends AJAXRenderer {

	  private $result;
	  private $errors;

	  /**
	   * Initializes the ExtFormRenderer instance.
	   * 
	   * @return void
	   */
	  public function __construct() {

	  		 $this->result = new stdClass;
	  		 $this->errors = new stdClass;
	  }

	  /**
	   * Sets an error message which is rendered along with success:false response.
	   * 
	   * @param string $message The error message
	   * @return void
	   * 
	   * <code>
	   * {"success": false, "reason": "$message"}
	   * </code>
	   */
	  public function setError($message) {

	  		 $this->errors->reason = $message;
	  		 $this->result->success = false;
	  		 $this->result->errors = $this->errors;
	  }

	  /**
	   * Renders a form response message.
	   * 
	   * @param boolean $result True to render success:true, false to render success:false.
	   * @return void
	   */
	  public function render($result) {

	  		 $this->result->success = ($result && !property_exists($this->result, 'reason')) ? true : false;
	  		 if($this->getStore()) $this->result->data = $this->getStore();
	  		 parent::render($this->result);
	  }
}
?>