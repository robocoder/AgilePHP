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
 * Uses the server temp directory to store PHP application data
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.scope
 */
class ApplicationScope {

	  private static $instance;
	  private static $appName;

	  private $store = array();

	  /**
	   * Inializes the ApplicationScope. If a file is already present in the servers temp
	   * directory (determined by HTTP HOST header in filename), the ApplicationScope is
	   * initalized from the previously serialized state; Otherwise a new file is created
	   * with a fresh ApplicationScope state.
	   *   
	   * @return void
	   */
	  private function __construct() {

	  	 	  $file = self::getAppTempFile();

	  	 	  if(file_exists($file) && !count($this->store)) {

	  	 	  	  $data = null;
	  	 	  	  $h = fopen($file, 'r');
	  	 	  	  while(!feof($h))
	  	 	  	         $data .= fgets($h, filesize($file));
	  	 	  	  fclose($h);

	  	 	  	  $this->store = unserialize($data);
	  	 	  } 	   
	  }

	  /**
	   * Returns a singleton instance of ApplicationScope
	   * 
	   * @param String $appName An optional application name. Defaults to the HTTP HOST header.
	   * @return An instance of ApplicationScope which contains the state for the specified application
	   * @static
	   */
	  public static function getInstance() {

	  	     if(self::$instance == null)
	  	         self::$instance = new self;

	  	     return self::$instance;
	  }

	  /**
	   * Returns the value corresponding to the specified key.
	   * 
	   * @param String $key The key to retrieve from the ApplicationScope store.
	   * @return mixed The key value if its present, otherwise null is returned.
	   */
	  public function get($key) {

	  	     if(isset($this->store[$key]) && !empty($this->store[$key]))
	  	     	 return $this->store[$key];
	  }

	  /**
	   * Sets an ApplicationScope variable
	   * 
	   * @param String $key The variable name
	   * @param mixed $value The variable value
	   * @return void
	   */
	  public function set($key, $value) {

	  		 $this->store[$key] = $value;
	  }

	  /**
	   * Clears the ApplicationScope state
	   * 
	   * @return void
	   */
	  public function clear() {

	  		 $this->store = array();
	  }

	  /**
	   * Clears the ApplicationScope store and deletes the temp file
	   * associated with the application.
	   * 
	   * @return void
	   */
	  public function destroy() {

	  		 $this->clear();
	  		 if(file_exists(self::getAppTempFile()))
	  		    unlink(self::getAppTempFile());
	  }

	  /**
	   * Persist the ApplicationScope state to disk
	   * 
	   * @return void
	   */
	  public function __destruct() {

	  		  $this->persist();
	  }

	  /**
	   * Persists the ApplicationScope state
	   * 
	   * @return void
	   */
	  public function persist() {

			 if(count($this->store)) {

		  	     $h = fopen(self::getAppTempFile(), 'w');
				 fwrite($h, serialize($this->store));
				 fclose($h);
			 }

			 if(!count($this->store) && file_exists(self::getAppTempFile()))
			 	 unlink(self::getAppTempFile());
	  }

	  /**
	   * Returns a string representing the full path to the temp file
	   * which stores the ApplicationScope state.
	   * 
	   * @return void
	   */
	  private static function getAppTempFile() {

	  	 	  $tmp = tempnam('agilephp', 'agilephp-');
 			  $path = dirname($tmp) . '/agilephp-' . AgilePHP::getAppName();

 			  if(file_exists($tmp)) unlink($tmp);

 			  return $path;
	  }
}
?>