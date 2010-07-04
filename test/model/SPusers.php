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
 * Model for getusers stored procedure
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.model
 */
class SPusers {

	  private $username;
	  private $password;
	  private $email;
	  private $created;
	  private $lastLogin;
	  private $enabled;
	  private $roleId;

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

	  	     $this->created = $dateTime;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getCreated()
	   */
	  public function getCreated() {

	  	     return $this->created;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#setLastLogin($dateTime)
	   */
	  public function setLastLogin( $timestamp ) {

	  	     $this->lastLogin = $timestamp;
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
	   * @see src/identity/IdentityModel#setRoles($roles)
	   */
	  public function setRoleId( $roleId ) {

	  	     $this->roleId = $roleId;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getRoles()
	   */
	  public function getRoleId() {

	  	     return $this->roleId;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#setEnabled($value)
	   */
	  public function setEnabled( $value ) {

	  		 if( $value == '1' ) {

	  		 	 $this->enabled = $value;
	  		 	 return;
	  		 }

	  		 $this->enabled = (ord($value) == 1) ? '1' : null;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityModel#getEnabled()
	   */
	  public function getEnabled() {

	  		 return $this->enabled;
	  }
}
?>