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
 * Maintains persistent session data that lives over multiple page requests. This
 * data is stored in the database to allow greater flexibility and easier clustering
 * compared to the native PHP local file system strategy.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.scope
 */
class OrmSessionProvider implements SessionProvider {

	  private static $instance;

	  private $session;
	  private $persisted = false;

	  /**
	   * Initalizes the 'SessionScope' object with a default sessionId. If an
	   * AGILEPHP_SESSION_ID is present, the session id from the cookie is used
	   * to retrieve a previously persisted Session, otherwise a new session is
	   * created and a new cookie is given to the client.
	   *
	   * @return void
	   */
	  public function __construct() {

	  	      $this->session = new Session();

	  	      if(isset($_COOKIE['AGILEPHP_SESSION_ID'])) {

	  	      	 Log::debug('OrmSessionProvider::__construct Initalizing session from previous cookie.');

	  	      	 $this->session->setId($_COOKIE['AGILEPHP_SESSION_ID']);

  		 	 	 $persisted = ORM::find($this->session);
  		 	 	 if(!isset($persisted[0])) return;

  		 	 	 $this->persisted = true;
  		 	 	 $data = unserialize($persisted[0]->getData());
  		 	 	 $this->session->setData($data);
	  	      }
	  	      else {

	  	      	 Log::debug('OrmSessionProvider::__construct Initalizing session with a new cookie.');

		  	     $this->createSessionId();
		  	     setcookie('AGILEPHP_SESSION_ID', $this->session->getId(), (time()+3600*24*30), '/'); // 30 days
	  	      }
	  }

	  /**
	   * Sets the session model
	   * 
	   * @param SessionModel $session The session model
	   * @return void
	   */
	  public function setSession(SessionModel $session) {

	         $this->session = $session;
	  }

	  /**
	   * Returns the session domain model object which maintains the id and data for
	   * the current Session's ActiveRecord.
	   *
	   * @return Session The current Session instance
	   */
	  public function getSession() {

	  		 return $this->session;
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
	   * Sets the session id and restores a previously persisted Session if one exists.
	   *
	   * @return void
	   */
	  public function setSessionId($id) {

	  		 $this->session->setId($id);

	  		 setcookie('AGILEPHP_SESSION_ID', $id, time()+3600*24*30, '/'); // 30 days
	  		 Log::debug('OrmSessionProvider::setSessionId Initalizing session from specified session id and dropping a new session cookie');

	  		 if($persistedSession = ORM::find($this->getSession())) {

	  		     $this->persisted = true;
	  		     $data = unserialize($persisted[0]->getData());
  		 	 	 $this->session->setData($data);
	  		 }
	  }

	  /**
	   * Returns the value corresponding to the specified key stored in the current Session.
	   *
	   * @param String $key The variable's key/name
	   * @return The value if present, otherwise null.
	   */
	  public function get($key) {

	  		 if(!$store = $this->session->getData()) return;
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

	  		 $store = $this->getSession()->getData();
	  		 $store[$key] = $value;
	  		 $this->getSession()->setData($store);
	  }

	  /**
	   * Refreshes the session by loading a fresh version from the database
	   *
	   * @return void
	   */
	  public function refresh() {

	         $this->persisted = true;
  	 	 	 $persisted = ORM::find($this->session);

  	 	 	 if($persisted) {

  	 	 	    $data = unserialize($persisted->getData());
  	 	 	  	$this->session->setData($data);
  	 	 	 }
  	 	 	 else {

  	 	 	 	$this->session->setData(array());
  	 	 	 }
	  }

	  /**
	   * Clears the current Session.
	   *
	   * @return void
	   */
	  public function clear() {

	  		 setcookie('AGILEPHP_SESSION_ID', '', time()-3600, '/');
	  		 $this->session->setData(array());
	  		 Log::debug('OrmSessionProvider::clear Session cleared');
	  }

	  /**
	   * Clears the SessionScope store and deletes the Session ActiveRecord from the
	   * database.
	   *
	   * @return void
	   */
	  public function destroy() {

  		 	 ORM::delete($this->getSession());
  		 	 $this->clear();
	  }

	  /**
	   * Persists a serialized instance of the current Session.
	   *
	   * @return void
	   */
	  public function persist() {

	  		 Log::debug('OrmSessionProvider::persist Persisting session');

	  	     if(!$store = $this->getSession()->getData()) {

	  	        if($this->persisted) $this->destroy();
	  	        return;
	  	     }

	  	     if(is_array($this->getSession()->getData())) {

  		 	 	$data = serialize($this->getSession()->getData());
                $this->getSession()->setData($data);
	  	     }

	 	     if(!$this->persisted) {

	 	     	$this->getSession()->setCreated('now');
			 	ORM::persist($this->getSession());
			 	$this->persisted = true;
			 	return;
			 }

		     ORM::merge($this->getSession());
	  }

	  /**
	   * Returns boolean flag indicating whether or not the current session
	   * data is persisted.
	   *
	   * @return bool True if the session data is persisted, false otherwise
	   */
	  private function isPersisted() {

	  		  return $this->persisted === true;
	  }
	  
	  /**
	   * Generates a 21 character session id
	   *
	   * @return String The generated session id
	   */
	  private function createSessionId() {

			  $numbers = '1234567890';
			  $lcase = 'abcdefghijklmnopqrstuvwzyz';
			  $ucase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			  $id = null;
			  for($i=0; $i<21; $i++) {

			  	   if(rand(0, 1)) {

			  	   	   $cRand = rand(0, 25);
			  	   	   $id .= (rand(0, 1)) ? $lcase[$cRand] : $ucase[$cRand];
			  	   }
			  	   else {

			  	   	   $nRand = rand(0, 9);
			  	   	   $id .= $numbers[$nRand];
			  	   }
			  }

	  		  $this->session->setId($id);
	  }

	  /**
	   * Persist the Session state just before the object is destroyed.
	   *
	   * @return void
	   */
	  public function __destruct() {

		  	 try {
		  	       if(!ORMFactory::getDialect()->getPDO())
		  	           ORMFactory::getDialect()->__construct(ORMFactory::getDialect()->getDatabase());

		  	       if(!$this->persisted) $this->persist();
		  	 }
		  	 catch(Exception $e) {

		  	 	    $message = 'OrmSessionProvider::__destruct ' . $e->getMessage();
		  		    Log::error($message);
		  	}
	  }
}
?>