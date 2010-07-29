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
 * @package com.makeabyte.agilephp.scope
 */

/**
 * Stores PHP $_POST variables, performs sanitizing, and has built
 * in support to guard against CSFR attacks using a double cookie
 * submit approach.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.scope
 */
class RequestScope {

	  private static $instance;
	  private $store;

	  /**
	   * Initialize RequestScope by storing all HTTP POST variables.
	   * 
	   * $_GET variables are not used in AgilePHP for two reasons: 
	   * 1) 255 character limit
	   * 2) GET requests make XSS attacks easier to execute
	   * 
	   * @return void
	   */
	  private function __construct() {

	  	 	  foreach($_POST as $key => $value)
	  	      	      $this->store[$key] = $_POST[$key];

	  	      if(isset($_COOKIE['AGILEPHP_REQUEST_TOKEN']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

	  	      	  if(!isset($this->store['AGILEPHP_REQUEST_TOKEN']) || 
	  	      	  			$this->store['AGILEPHP_REQUEST_TOKEN'] != $_COOKIE['AGILEPHP_REQUEST_TOKEN']) {

	  	      	  	  $rt = (!isset($this->store['AGILEPHP_REQUEST_TOKEN'])) ? null : $this->store['AGILEPHP_REQUEST_TOKEN'];

	  	      	  	  Log::debug('RequestScope::__construct Found invalid request token \'' . $rt . '\', expected \'' . $_COOKIE['AGILEPHP_REQUEST_TOKEN'] . '\'.');
	  	      	  	  throw new FrameworkException('Invalid request token \'' . $rt . '\'. Possible Cross-Site Forgery Request (CSFR) attempt.');
	  	      	  }
	  	      	  else {

	  	      	  	  $this->invalidate();
	  	      	  }
	  	      }
	  }

	  /**
	   * Returns singleton instance of RequestScope
	   * 
	   * @return Singleton instance of RequestScope
	   * @static
	   */
	  public static function getInstance() {

	  	     if(self::$instance == null)
	  	         self::$instance = new self;

	  	     return self::$instance;
	  }

	  /**
	   * Sets a request variable.
	   * 
	   * @param String $key The key/index of for the variable
	   * @param String $value The variable value
	   * @return void
	   */
	  public function set($key, $value) {

	  		 $this->store[$key] = $value;
	  }

	  /**
	   * Returns a request variable.
	   * 
	   * @param $key The key/index of the variable
	   * @return The variable value
	   */
	  public function get($key) {

	  	     if(isset($this->store[$key]) && !empty($this->store[$key]))
	  	     	 return (is_array($this->store[$key])) ? $this->store[$key] : urldecode($this->store[$key]);
	  }

	  /**
	   * Returns a sanitized store variable which protects against SQL injection, 
	   * XSS and XSFR attacks. The variable value is passed first through strip_tags,
	   * followed by addslashes and finally htmlspecialchars. Use htmlspecialchars_decode
	   * to decode the sanitized value.
	   * 
	   * @param String $key The key/index of the variable
	   * @return String The sanitized value
	   * @see http://en.wikipedia.org/wiki/Cross-site_scripting
	   */
	  public function getSanitized($key) {

	  		 if(isset($this->store[$key]) && !empty($this->store[$key])) {

	  		 	 if(is_array($this->store[$key])) {

	  		 	 	 for($i=0; $i<count($this->store[$key]); $i++)
	  		 	 	 	 $this->store[$key][$i] = htmlspecialchars(addslashes(strip_tags(urldecode($this->store[$key][$i]))));

	  		 	 	 return $this->store[$key];
	  		 	 }

	  		 	 return htmlspecialchars(addslashes(strip_tags(urldecode($this->store[$key]))));
	  		 }
	  }

	  /**
	   * Returns the variable store.
	   * 
	   * @return array Returns an array containing all PHP $_GET and $_POST variables
	   */
	  public function getParameters() {

	  		 return $this->store;
	  }

 	  /**
	   * Creates a 20-30 character token used to guard against CSFR attacks.
	   * 
	   * @return String The generated request token
	   * @see http://en.wikipedia.org/wiki/Cross-site_request_forgery
	   */
	  public function createToken() {

			 $numbers = '1234567890';
			 $lcase = 'abcdefghijklmnopqrstuvwzyz';
			 $ucase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			 $token = null;
			 for($i=0; $i<21; $i++) {

			  	  if(rand(0, 1)) {

			  	  	  $cRand = rand(0, 25);
			  	   	  $token .= (rand(0, 1)) ? $lcase[$cRand] : $ucase[$cRand];
			  	  }
			  	  else {

			  	   	  $nRand = rand(0, 9);
			  	   	  $token .= $numbers[$nRand];
			  	  }			  	     
			 }

		 	 setcookie('AGILEPHP_REQUEST_TOKEN', $token, time()+3600, '/'); // 1 hour

	  		 return $token;
	  }

	  /**
	   * Expires request token cookie.
	   * 
	   * @return void
	   */
	  public function invalidate() {

 		 	 setcookie('AGILEPHP_REQUEST_TOKEN', '', time()-3600, '/');
	  }

	  /**
	   * Sanitizes the specified data by running it through htmlspecialchars,
	   * addslashes, and strip_tags. 
	   * 
	   * @param String $data The data to sanitize
	   * @return The sanitized data
	   */
	  public function sanitize($data) {

	  		 return htmlspecialchars(addslashes(strip_tags($data)));
	  }
}
?>