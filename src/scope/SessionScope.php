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
 * compared to the native local file system approach PHP uses.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.scope
 */
class SessionScope {

	  private static $instance;

	  private $oldSession;
	  private $session;
	  private $persisted = false;

	  private function __clone() { }

	  /**
	   * Initalizes the 'SessionScope' object with a default sessionId. If an
	   * AGILEPHP_SESSION_ID is present, the session id from the cookie is used
	   * to retrieve a previously persisted Session, otherwise a new session is
	   * created and a new cookie is given to the client.
	   * 
	   * @return void
	   */
	  private function __construct() {

	  		  $this->oldSession = new Session();
	  	      $this->session = new Session();

	  	      if( isset( $_COOKIE['AGILEPHP_SESSION_ID'] ) ) {

	  	      	  Log::debug( 'SessionScope::__construct Initalizing session from previous cookie.' );

	  	      	  $this->session->setId( $_COOKIE['AGILEPHP_SESSION_ID'] );
	  	      	  $this->oldSession->setId( $_COOKIE['AGILEPHP_SESSION_ID'] );

	  	      	  $pm = new PersistenceManager();
  		 	 	  $persisted = $pm->find( $this->session );
  		 	 	  if( !isset( $persisted[0] ) ) return;

  		 	 	  $this->persisted = true;
  		 	 	  $this->session->setData( $persisted[0]->getData() );
  		 	 	  $this->oldSession->setData( $persisted[0]->getData() );
	  	      }
	  	      else {

	  	      	  Log::debug( 'SessionScope::__construct Initalizing session with a new cookie.' );

		  	      $this->createSessionId();
		  	      setcookie( 'AGILEPHP_SESSION_ID', $this->session->getId(), (time()+3600*24*30), '/' ); // 30 days
	  	      }
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
	  public function setSessionId( $id ) {

	  		 $this->session->setId( $id );

	  		 setcookie( 'AGILEPHP_SESSION_ID', $id, time()+3600*24*30, '/' ); // 30 days
	  		 Log::debug( 'SessionScope::setSessionId Initalizing session from specified session id and dropping a new session cookie' );

	  		 $pm = new PersistenceManager();
	  		 if( $persistedSession = $pm->find( $this->getSession() ) ) {

  		 	 	 $this->session->setData( $persistedSession[0]->getData() );
  		 	 	 $this->oldSession->setData( $persistedSession[0]->getData() );
	  		 }
	  }

	  /**
	   * Returns the value corresponding to the specified key stored in the current Session.
	   * 
	   * @param String $key The variable's key/name
	   * @return The value if present, otherwise null.
	   */
	  public function get( $key ) {

	  		 if( !$this->session->getData() )
	  		 	 return;

	  		 $store = unserialize( $this->session->getData() );
	  		 if( isset( $store[$key] ) )
  	     	 	 return $store[$key];
	  }

	  /**
	   * Sets a new Session variable.
	   * 
	   * @param String $key The variable name
	   * @param String $value The variable value
	   * @return void
	   */
	  public function set( $key, $value ) {

	  		 $store = unserialize( $this->getSession()->getData() );
	  		 $store[$key] = $value;
	  		 $this->getSession()->setData( serialize( $store ) );
	  }

	  /**
	   * Refreshes the session by loading a fresh version from the database
	   * 
	   * @return void
	   */
	  public function refresh() {

		     $pm = new PersistenceManager();
  	 	 	 $persisted = $pm->find( $this->session );

  	 	 	 if( $persisted ) {

  	 	 	  	 $this->session->setData( $persisted->getData() );
  	 	 	  	 $this->oldSession->setData( $persisted->getData() );
  	 	 	 }
  	 	 	 else {
  	 	 	 	
  	 	 	 	 $this->session->setData( array() );
  	 	 	  	 $this->oldSession->setData( array() );
  	 	 	 }
	  }

	  /**
	   * Clears the current Session.
	   * 
	   * @return void
	   */
	  public function clear() {

	  		 setcookie( 'AGILEPHP_SESSION_ID', '', time()-3600, '/' );

	  		 $this->session->setData( array() );
	  		 $this->oldSession->setData( array() );

	  		 Log::debug( 'SessionScope::clear Session cleared' );
	  }

	  /**
	   * Clears the SessionScope store and deletes the Session ActiveRecord from the
	   * database.
	   * 
	   * @return void
	   */
	  public function destroy() {

	  		 $pm = new PersistenceManager();
  		 	 $pm->delete( $this->getSession() );

  		 	 $this->clear();
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
	   * Persist the Session data state to database just before the object
	   * is destroyed.
	   * 
	   * @return void
	   */
	  public function __destruct() {

		  	 try {
		  		   $this->persist();
		  	 }
		  	 catch( Exception $e ) {

		  	 	    $message = 'SessionScope::__destruct ' . $e->getMessage();
		  		    Log::error( $message );
		  	}
	  }

	  /**
	   * Persists a serialized instance of the current Session.
	   * 
	   * @return void
	   */
	  public function persist() {

	  		 Log::debug( 'SessionScope::persist Persisting session' );

	  	     if( !$this->getSession()->getData() ) return;

	  	     $pm = new PersistenceManager();

	 	     if( !$this->isPersisted() ) {

	 	     	 $this->getSession()->setCreated( 'now' );
			 	 $pm->persist( $this->getSession() );
			 	 $this->oldSession->setData( $this->session->getData() );
			 	 return;
			 }

	  		 if( $this->oldSession->getData() != $this->session->getData() ) {

			     $pm->merge( $this->getSession() );
			     return;
	  		 }

			 if( !$this->getSession()->getData() && $this->oldSession )
			 	 $this->destroy();
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
			  for( $i=0; $i<21; $i++ ) {

			  	   if( rand( 0, 1 ) ) {

			  	   	   $cRand = rand( 0, 25 );
			  	   	   $id .= (rand( 0, 1) ) ? $lcase[$cRand] : $ucase[$cRand];
			  	   }
			  	   else {

			  	   	   $nRand = rand( 0, 9 );
			  	   	   $id .= $numbers[$nRand];
			  	   }			  	     
			  }

	  		  $this->session->setId( $id );
	  }
}
?>