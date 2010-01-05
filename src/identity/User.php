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
 * @package com.makeabyte.agilephp.identity
 */

/**
 * Domain model used by the Identity component. Represents a persistable
 * user. 
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 * @version 0.2a
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

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#setUsername($username)
	   */
	  public function setUsername( $username ) {

	  	     $this->username = $username;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getUsername()
	   */
	  public function getUsername() { 

	  	     return $this->username;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#setPassword($password)
	   */
	  public function setPassword( $password ) {

	  	     $this->password = $password;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getPassword()
	   */
	  public function getPassword() {

	  	     return $this->password;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#setEmail($email)
	   */
	  public function setEmail( $email ) {

	  		 $this->email = $email;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getEmail()
	   */
	  public function getEmail() {
	  	
	  		 return $this->email;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#setCreated($dateTime)
	   */
	  public function setCreated( $dateTime ) {

	  	     $this->created = date( 'c', strtotime( $dateTime ) );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getCreated()
	   */
	  public function getCreated() {

	  	     return (string)$this->created;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#setLastLogin($dateTime)
	   */
	  public function setLastLogin( $timestamp ) {

	  	     $this->lastLogin = date( 'c', strtotime( $timestamp ) );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getLastLogin()
	   */
	  public function getLastLogin() {

	  	     return $this->lastLogin;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#setRole($role)
	   */
	  public function setRole( Role $role ) {

	  	     $this->Role = $role;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getRole()
	   */
	  public function getRole() {

	  	     return ($this->Role instanceof Role) ? $this->Role : new Role();
	  }

	  /**
	   * Sets an array of roles of which the user belongs.
	   * 
	   * @param array $roles The array of Role instances belonging to the user
	   * @return void
	   */
	  public function setRoles( array $roles ) {

	  		 $this->Roles = $roles;
	  }

	  /**
	   * Gets an array of roles belonging to the user.
	   * 
	   * @return Array Roles belonging to the user
	   */
	  public function getRoles() {

	  		 return $this->Roles;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#setSessionId($sessionId)
	   */
	  public function setSessionId( $sessionId ) {

	  		 $this->sessionId = $sessionId;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getSessionId()
	   */
	  public function getSessionId() {

	  		return $this->sessionId;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#setEnabled($value)
	   */
	  public function setEnabled( $value ) {

	  		 $this->enabled = $value;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getEnabled()
	   */
	  public function getEnabled() {

	  		 return $this->enabled;
	  }

	  /**
	   * Sets the AgilePHP Session object belonging to the user.
	   * 
	   * @param Session $session AgilePHP Session instance following the user.
	   * @return void
	   */
	  public function setSession( Session $session ) {

	  		 $this->Session = $session;
	  }

	  /**
	   * Returns the AgilePHP Session instance belonging to the user.
	   * 
	   * @return Session AgilePHP Session object following the user
	   */
	  public function getSession() {

	  		 return ($this->Session instanceof Session) ? $this->Session : new Session();
	  }

	  /*
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