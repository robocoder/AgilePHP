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
 * Maintains persistent session data that lives over multiple page requests. Data
 * is stored in a traditional PHP session.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.scope
 */
class PhpSessionProvider implements SessionProvider {

      private $session;

	  /**
	   * @return void
	   */
	  public function __construct() {

	  		 if(!@session_id()) @session_start();

	  	     if(isset($_SESSION['AGILEPHP_SESSION'])) {

	  	      	 Log::debug('SessionScope::__construct Initalizing session from previous PHP session.');

	  	      	 $this->session = unserialize($_SESSION['AGILEPHP_SESSION']);
	  	      	 if(!$this->session) $this->session = new Session();
	  	      	 $this->session->setId(session_id());
	  	      }
	  	      else {

  	      	      Log::debug('SessionScope::__construct Initalizing session with new PHP session.');

  	      	      $this->session = new Session();
  	      	      $this->session->setId(session_id());
  	      	      $_SESSION['AGILEPHP_SESSION'] = serialize($this->session);
	  	      }
	  }

	  /**
	   * Returns boolean flag indicating whether or not the current session
	   * data is persisted.
	   *
	   * @return bool True if the session data is persisted, false otherwise
	   */
	  public function isPersisted() {

	  		 return $this->persisted == true;
	  }

	  /**
	   * Returns the session domain model which maintains the id and data for
	   * the current session.
	   *
	   * @return Session The current Session instance
	   */
	  public function getSession() {

	  		 return $this->session;
	  }

	  /**
	   * Sets the session id and restores a previously persisted Session if one exists.
	   *
	   * @return void
	   */
	  public function setSessionId($id) {

	         session_unset();
	  		 $this->session->setId($id);
	  		 session_id($id);
	  }

	  /**
	   * Returns the session id for the current Session.
	   *
	   * @return String The session id
	   */
	  public function getSessionId() {

	  		 return $this->session->getId();
	  }

	  /**
	   * Returns the value corresponding to the specified key stored in the current Session.
	   *
	   * @param String $key The variable's key/name
	   * @return The value if present, otherwise null.
	   */
	  public function get($key) {

	  		 if(!$this->session->getData()) return;

	  		 $store = unserialize($this->session->getData());
	  		 if(isset($store[$key])) return $store[$key];
	  }

	  /**
	   * Sets a new Session variable.
	   *
	   * @param String $key The variable name
	   * @param String $value The variable value
	   * @return void
	   */
	  public function set($key, $value) {

	  		 $store = unserialize($this->getSession()->getData());
	  		 $store[$key] = $value;
	  		 $this->session->setData(serialize($store));
	  }

	  /**
	   * Clears the current Session.
	   *
	   * @return void
	   */
	  public function clear() {

	         $this->session->setId(null);
	  		 $this->session->setData(array());
	  		 $_SESSION['AGILEPHP_SESSION'] = serialize(null);
	  		 Log::debug('SessionScope::clear Session cleared');
	  }

	  /**
	   * Clears the SessionScope store and deletes the Session ActiveRecord from the
	   * database.
	   *
	   * @return void
	   */
	  public function destroy() {

	         unset($_SESSION);
  		 	 session_unset();
	  }

	  /**
	   * Persists a serialized instance of the current Session.
	   *
	   * @return void
	   */
	  public function persist() {

	  		 Log::debug('SessionScope::persist Persisting session');
	  	     $_SESSION['AGILEPHP_SESSION'] = serialize($this->session);
			 if(!$this->getSession()->getData()) $this->destroy();
	  }
  
	  /**
	   * Here to make interface happy. Doesn't do anything
	   *
	   * @return void
	   */
	  public function refresh() {

	         session_id($this->session->getId());
	  }

	  /**
	   * Persist the Session data state to database just before the object
	   * is destroyed.
	   *
	   * @return void
	   */
	  public function __destruct() {

		  	 try {
		  		   $this->persist();
		  	 }
		  	 catch(Exception $e) {

		  	 	    $message = 'SessionScope::__destruct ' . $e->getMessage();
		  		    Log::error($message);
		  	}
	  }
}
?>