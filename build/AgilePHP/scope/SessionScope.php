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
 * Factory responsible for maintaining persistent session data that
 * lives over multiple page requests.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.scope
 */
class SessionScope implements SessionProvider {

	  private static $instance;
	  private $provider;

	  private function __clone() { }

	  /**
	   * Initializes a new SessionScope instance using agilephp.xml configuration
	   * if present, otherwise PhpSessionProvider is used as the default provider.
	   *
	   * @return void
	   */
	  private function __construct() {

	          $xml = AgilePHP::getConfiguration();
              for($i=0; $i<count((array)$xml->scope); $i++ ) {

                  if((string)$xml->scope[$i]->attributes()->type == 'session') {

                      $provider = (string)$xml->scope[$i]->attributes()->provider;
                      $this->provider = $provider ? new $provider : new PhpSessionProvider();
                      return;
                  }
              }

              $this->provider = new PhpSessionProvider();
	  }

	  /**
	   * Returns a singleton instance of SessionScope
	   *
	   * @return SessionScope Singleton instance of SessionScope
	   * @static
	   */
	  public static function getInstance() {

	  	     if( self::$instance == null )
	  	         self::$instance = new self;

	  	     return self::$instance;
	  }

	  /**
	   * Returns the SessionProvider responsible for session persistence
	   * 
	   * @return SessionProvider The session persistence provider
	   */
	  public function getProvider() {

	         return $this->provider;
	  }

	  /**
	   * Returns the session domain model object which maintains the id and data for
	   * the current Session's ActiveRecord.
	   *
	   * @return Session The current Session instance
	   */
	  public function getSession() {

	  		 return $this->provider->getSession();
	  }

	  /**
	   * Returns the session id for the current Session.
	   *
	   * @return String The session id
	   */
	  public function getSessionId() {

	  		 return $this->provider->getSessionId();
	  }

	  /**
	   * Sets the session id and restores a previously persisted Session if one exists.
	   *
	   * @return void
	   */
	  public function setSessionId($id) {

	  		 $this->provider->setSessionId($id);
	  }

	  /**
	   * Returns the value corresponding to the specified key stored in the current Session.
	   *
	   * @param String $key The variable's key/name
	   * @return The value if present, otherwise null.
	   */
	  public function get($key) {

	  		 return $this->provider->get($key);
	  }

	  /**
	   * Sets a new Session variable.
	   *
	   * @param String $key The variable name
	   * @param String $value The variable value
	   * @return void
	   */
	  public function set($key, $value) {

	  		 $this->provider->set($key, $value);
	  }

	  /**
	   * Refreshes the session by loading a fresh version from the database
	   *
	   * @return void
	   */
	  public function refresh() {

	          $this->provider->refresh();
	          Log::debug( 'SessionScope::clear Session refreshed' );
	  }

	  /**
	   * Clears the current Session.
	   *
	   * @return void
	   */
	  public function clear() {

	  		 $this->provider->clear();
	  		 Log::debug( 'SessionScope::clear Session cleared' );
	  }

	  /**
	   * Clears the SessionScope store and deletes the Session ActiveRecord from the
	   * database.
	   *
	   * @return void
	   */
	  public function destroy() {

  		 	 $this->provider->destroy();
  		 	 Log::debug( 'SessionScope::destroy Session destroyed' );
	  }

	  /**
	   * Persists a serialized instance of the current Session.
	   *
	   * @return void
	   */
	  public function persist() {

	  	     $this->provider->persist();
	  	     Log::debug( 'SessionScope::persist Session persisted' );
	  }
}
?>