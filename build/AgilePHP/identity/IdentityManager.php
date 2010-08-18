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
		   * @param IdentityModel $model The IdentityModel for IdentityManager to manage
		   * @return void
		   */
		  public function setModel(IdentityModel $model);

		  /**
		   * Returns the domain object model which the IdentityManager is managing.
		   * 
		   * @return An instance of the domain object model which IdentityManager is managing
		   */
		  public function getModel();

		  /**
		   * Sets the name of the domain model which the IdentityManager is managing
		   * 
		   * @param string $name The domain model name
		   * @return void
		   */
		  public function setModelName($name);

		  /**
		   * Gets the domain model name
		   * 
		   * @return string The name of the domain model which the IdentityManager is managing
		   */
		  public function getModelName();

		  /**
		   * Sets the username for the identity which IdentityManager is managing.
		   *   
		   * @param String $username The username of the identity.
		   * @return void
		   */
		  public function setUsername($username);

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
		  public function setPassword($password);

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
		  public function setEmail($email);

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
		  public function setCreated($dateTime);

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
		  public function setLastLogin($dateTime);

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
		  public function setEnabled($value);

		  /**
		   * Gets the enabled status of the user 
		   * @return boolean
		   */
		  public function getEnabled();

		  /**
		   * Sets the name of the authenticator responsible for performing authentication.
		   * 
		   * @param string $authenticator The name of the authenticator responsible for Identity authentication
		   * @return void
		   */
		  public function setAuthenticator($authenticator);

		  /**
		   * Gets the name of the authenticator responsible for performing authentication
		   * 
		   * @return string The name of the authenticator responsible for Identity authentication
		   */
		  public function getAuthenticator();
		  
		  /**
		   * Sets the Mailer responsible for sending forgot password emails
		   * 
		   * @param string $mailer The Mailer responsible for sending forgot password emails
		   * @return void
		   */
		  public function setForgotPasswdMailer($mailer);

		  /**
		   * Gets the Mailer responsible for sending forgot password emails
		   * 
		   * @return string $mailer The Mailer responsible for sending forgot password emails
		   */
		  public function getForgotPasswdMailer();

		  /**
		   * Sets the Mailer responsible for sending emails for reset passwords
		   * 
		   * @param string $mailer The Mailer responsible for sending emails for reset passwords
		   * @return void
		   */
		  public function setResetPasswdMailer($mailer);

		  /**
		   * Returns the Mailer responsible for sending emails for reset passwords
		   * 
		   * @return string The Mailer responsible for sending emails for reset passwords
		   */
		  public function getResetPasswdMailer();

		  /**
		   * Sets the Mailer used to send registration/confirmation emails
		   * 
		   * @param string $mailer The Mailer instance responsible for sending registration/confirmation emails. 
		   * @return void
		   */
		  public function setRegistrationMailer($mailer);

		  /**
		   * Returns the Mailer instance responsible for sending registration/confirmation emails
		   * 
		   * @return string The registration/confirmation Mailer
		   */
		  public function getRegistrationMailer();

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
		  public function resetPassword($token, $sessionId);

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
		  public function confirm($token, $sessionId);

  		  /**
		   * Sets the identity's role
		   * 
		   * @param Role $role A Role domain model object 
		   * @return void
		   */
		  public function setRole(Role $role);

		  /**
		   * Returns the Role object
		   * 
		   * @return The Role object
		   */
		  public function getRole();

		  /**
		   * Adds an array of Role instances to the identity
		   * 
		   * @param array $roles An array of Role instances to add
		   * @return void
		   */
		  public function setRoles(array $roles);

		  /**
		   * Returns an array of Role instances which belong to the identity
		   * 
		   * @return mixed An array of Role instances which the current identity belongs
		   *               or null if no roles exist
		   */
		  public function getRoles();

		  /**
		   * Adds a new role to the IdentityModel Roles array.
		   * 
		   * @param Role $role The new role to assign to the identity
		   * @return void
		   */
		  public function addRole(Role $role);

		  /**
		   * Checks to see if the identity has the specified role.
		   *  
		   * @param String $role The name of a role
		   * @return True if the identity has the specified role, false otherwise.
		   */
		  public function hasRole(Role $role);

		  /**
		   * Revokes/removes a role from the identity.
		   *  
		   * @return void
		   */
		  public function revokeRole(Role $role);

		  /**
		   * Authenticates/logs in an identity and returns a boolean response.
		   * 
		   * @param String $username The username to authenticate
		   * @param String $password The password to authenticate
		   * @return True if the username and password are valid, false otherwise.
		   */
		  public function login($username, $password);

		  /**
		   * Destroys the current session.
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