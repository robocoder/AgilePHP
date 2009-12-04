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
 * @package com.makeabyte.agilephp.identity
 */

/**
 * AgilePHP :: User
 * User domain model 
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 * @version 0.1a
 */
class User implements IdentityModel {

	  private $username;
	  private $password;
	  private $email;
	  private $created;
	  private $lastLogin;
	  private $roleId;
	  private $sessionId;
	  private $enabled;

	  private $Session;
	  private $Role;
	  private $Roles;

	  public function User() { }

	  public function setUsername( $username ) {

	  	     $this->username = $username;
	  }

	  public function getUsername() { 

	  	     return $this->username;
	  }

	  public function setPassword( $password ) {

	  	     $this->password = $password;
	  }

	  public function getPassword() {

	  	     return $this->password;
	  }

	  public function setEmail( $email ) {

	  		 $this->email = $email;
	  }
	  
	  public function getEmail() {
	  	
	  		 return $this->email;
	  }

	  public function setCreated( $dateTime ) {

	  	     $this->created = date( 'c', strtotime( $dateTime ) );
	  }

	  public function getCreated() {

	  	     return (string)$this->created;
	  }

	  public function setLastLogin( $timestamp ) {

	  	     $this->lastLogin = date( 'c', strtotime( $timestamp ) );
	  }

	  public function getLastLogin() {

	  	     return $this->lastLogin;
	  }
	  
	  public function setRoleId( $roleId ) {

	  		 $this->roleId = $roleId;
	  }

	  public function getRoleId() {

	  		 return $this->roleId;
	  }

	  public function setRole( Role $role ) {

	  	     $this->Role = $role;
	  }

	  public function getRole() {

	  	     return ($this->Role instanceof Role) ? $this->Role : new Role();
	  }

	  public function setRoles( array $roles ) {

	  		 $this->Roles = $roles;
	  }

	  public function getRoles() {

	  		 return $this->Roles;
	  }

	  public function setSessionId( $sessionId ) {

	  		 $this->sessionId = $sessionId;
	  }

	  public function getSessionId() {

	  		return $this->sessionId;
	  }

	  public function setEnabled( $value ) {

	  		 $this->enabled = $value;
	  }

	  public function getEnabled() {

	  		 return $this->enabled;
	  }

	  public function setSession( Session $session ) {

	  		 $this->Session = $session;
	  }

	  public function getSession() {

	  		 return ($this->Session instanceof Session) ? $this->Session : new Session();
	  }

	  /**
	   * Clean up stale sessions. The user should never have multiple sessions open.
	   * 
	   * @param array $sessions An array of Session objects
	   * @return void
	   *
	  public function setSessions( array $sessions ) {

	  		 $pm = AgilePHP::getFramework()->getComponent( 'PersistenceManager' );

	  		 for( $i=0; $i<count( $sessions ); $i++ ) {

	  		 	  if( ($i+1) < count( $sessions ) ) {

	  		 	  	  $pm->delete( $sessions[$i] );
	  		 	  	  continue;
	  		 	  }
	  		 }

	  		 $this->setSession( $sessions[ count( $sessions ) ] );
	  }
	  */
}
?>