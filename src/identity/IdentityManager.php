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
 * Interface for AgilePHP Identity component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.identity
 */
interface IdentityManager {

		  /**
		   * Sets the domain object model which the IdentityManager will manage.
		   * 
		   * @param Object $model The domain object model for IdentityManager to manage
		   * @return void
		   */
		  public function setModel( $model );

		  /**
		   * Returns the domain object model which the IdentityManager is managing.
		   * 
		   * @return An instance of the domain object model which IdentityManager is managing
		   */
		  public function getModel();

		  /**
		   * Sets the username for the identity which IdentityManager is managing.
		   *   
		   * @param String $username The username of the identity.
		   * @return void
		   */
		  public function setUsername( $username );

		  /**
		   * Returns the username for the identity which IdentityManager is managing.
		   * 
		   * @return The username of the identity
		   */
		  public function getUsername();

		  /**
		   * Sets the password for the identity which IdentityManager is managing.
		   * 
		   * @param String $password The identity's password
		   * @return void
		   */
		  public function setPassword( $password );

		  /**
		   * Returns the password for the identity which IdentityManager is managing. Unless
		   * overwritten, this is the persisted value.
		   *  
		   * @return The identity's password
		   */
		  public function getPassword();
		  
		  /**
		   * Sets the identity's email address.
		   * 
		   * @param String $email A valid email addresss containing for the idenity
		   * @return void 
		   */
		  public function setEmail( $email );

		  /**
		   * Returns the identity's email address
		   * 
		   * @return The email address of the identity
		   */
		  public function getEmail();

		  /**
		   * Denotes when the identity was created.
		   * 
		   * @param Date $dateTime The dateTime when this identity was created.
		   * @return void
		   */
		  public function setCreated( $dateTime );

		  /**
		   * Returns the dateTime when the identity was created.
		   * 
		   * @return The date indicating when the identity was created.
		   */
		  public function getCreated();

		  /**
		   * Sets the dateTime when this identity last logged in
		   * 
		   * @param Date $dateTime The dateTime when the identity last logged in
		   * @return void
		   */
		  public function setLastLogin( $dateTime );

		  /**
		   * Returns the dateTime the identity last logged in
		   *  
		   * @return The dateTime the identity last logged in
		   */
		  public function getLastLogin();

		  /**
		   * Sets the enabled status of the user
		   *  
		   * @param bool True to enable the user account, false to disable. 
		   * @return void
		   */
		  public function setEnabled( $value );

		  /**
		   * Gets the enabled status of the user 
		   * @return boolean
		   */
		  public function getEnabled();

		  /**
		   * Sets the url used to reset the identity's password.
		   * 
		   * @param String $url The url which should reset the users password when its clicked
		   * @return void
		   */
		  public function setPasswordResetUrl( $url );

		  /**
		   * Returns the url which is sent to the identity that when clicked resets their password.
		   * 
		   * @return String
		   */
		  public function getPasswordResetUrl();

		  /**
		   * Returns the url which is sent to the identity that when clicked confirms/activates their account.
		   * 
		   * @return String
		   */
		  public function getConfirmationUrl();

		  /**
		   * Sends the identity an email to the address stored in the stateful domain object
		   * model being managed by IdentityManager. Uses the state of the 'email' field
		   * within the domain object model the IdentityManager is managing.
		   * 
		   * @return void
		   * @throws FrameworkException If there was an error sending the forgotten password email.
		   */
		  public function forgotPassword();

		  /**
		   * Resets the password to a hashed random string. This operation uses the AgilePHP
		   * Crypto component to ensure standard hashing across the application. 
		   * 
		   * @param String $token A randomly generated token required to reset the password
		   * @param String $sessionId The sessionId of the user who requested the new password
		   * @return void
		   */
		  public function resetPassword( $token, $sessionId );

		  /**
		   * Registers a new user account by creating a disabled user and sending
		   * an activation email to the new user. The activation email calls activate
		   * to allow the user to enable the account.
		   *
		   * @return void
		   * @throws FrameworkException IF there was an error sending the registration email.
		   */
		  public function register();

		  /**
		   * Confirms/activates a pending registration
		   * 
		   * @param String $token The confirmation token
		   * @param String $sessionId The session id used to register
		   * @return void
		   * @throws FrameworkException If token is invalid
		   */
		  public function confirm( $token, $sessionId );

  		  /**
		   * Sets the identity's role
		   * 
		   * @param Role $role A Role domain model object 
		   * @return void
		   */
		  public function setRole( Role $role );

		  /**
		   * Returns the Role object
		   * 
		   * @return The Role object
		   */
		  public function getRole();

		  /**
		   * Checks to see if the identity has the specified role.
		   *  
		   * @param String $role The name of a role
		   * @return True if the identity has the specified role, false otherwise.
		   */
		  public function hasRole( $role );

		  /**
		   * Revokes/removes a role from the identity.
		   *  
		   * @return void
		   */
		  public function revokeRole();

		  /**
		   * Authenticates/logs in an identity and returns a boolean response.
		   * 
		   * @param String $username The username to authenticate
		   * @param String $password The password to authenticate
		   * @return True if the username and password are valid, false otherwise.
		   */
		  public function login( $username, $password );

		  /**
		   * Destroys the current SessionScope.
		   * 
		   * @return void
		   */
		  public function logout();

		  /**
		   * Returns boolean response based on the identity's logged in status.
		   * 
		   * @return True if the identity is currently logged in, false otherwise.
		   */
		  public function isLoggedIn();

		  /**
		   * Persists a new identity.
		   * 
		   * @return void
		   */
		  public function persist();

		  /**
		   * Updates the identity using the current state of the domain model object
		   * which the IdentityManager is managing.
		   * 
		   * @return void
		   */
		  public function merge();

		  /**
		   * Deletes the domain object model which the IdentityManager is managing.
		   * 
		   * @return void
		   */
		  public function delete();

		  /**
		   * Refreshes an entity by performing a 'find' operation on the domain
		   * object model which the IdentityManager is managing.
		   * 
		   * @return void
		   */
		  public function refresh();
}
?>