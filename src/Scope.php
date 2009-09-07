<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009 Make A Byte, inc
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
 * @package com.makeabyte.agilephp
 */

/**
 * Includes all scope dependencies
 */
require_once 'scope/ApplicationScope.php';
require_once 'scope/RequestScope.php';
require_once 'scope/Session.php';
require_once 'scope/SessionScope.php';

/**
 * AgilePHP :: Scope
 * Facade for AgilePHP scopes.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class Scope {

	  private static $instance;

	  private function __construct() {}
	  private function __clone() {}

	  /**
	   * Returns a singleton instance of Scope.
	   * 
	   * @return An instance of Scope
	   */
	  public static function getInstance() {

	     	 if( self::$instance == null )
	  	         self::$instance = new self;

	  	     return self::$instance;
	  }

	  /**
	   * Returns a singleton instance of ApplicationScope.
	   * 
	   * @param $appName An optional application name. Defaults to the
	   * 				 HTTP HOST header.
	   * @return A singleton instance of ApplicationScope
	   */
	  public function getApplicationScope( $appName = null ) {

	  		 require_once 'scope/ApplicationScope.php';

	  	     return ApplicationScope::getInstance( $appName );
	  }

	  /**
	   * Returns a singleton instance of RequestScope.
	   * 
	   * @return void
	   */
	  public function getSessionScope() {

	  		 require_once 'scope/SessionScope.php';

	  		 return SessionScope::getInstance();
	  }

	  /**
	   * Returns a singleton instance of RequestScope.
	   * 
	   * @return A singleton instance of RequestScope
	   */
	  public function getRequestScope() {

	  		 require_once 'scope/RequestScope.php';

	  	     return RequestScope::getInstance(); 
	  }
}
?>