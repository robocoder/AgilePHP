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
 * @package com.makeabyte.agilephp
 */

/**
 *
 * Includes all identity package dependencies
 */
require_once 'identity/IdentityModel.php';
require_once 'identity/IdentityManager.php';
require_once 'identity/IdentityManagerImpl.php';
require_once 'identity/IdentityManagerFactory.php';

/**
 * Provides a means for tracking a users identity throughout the
 * web application. The Identity component is responsible for
 * persistence, authentication, roles, sessions, password management
 * and email tasks.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
class Identity {

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setModel($model)
	   */
	  public static function setModel($model) {

	  		 IdentityManagerFactory::getManager()->setModel($model);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getModel()
	   */
	  public static function getModel() {

	  		 return IdentityManagerFactory::getManager()->getModel();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setUsername($username)
	   */
	  public static function setUsername($username) {

	  		 IdentityManagerFactory::getManager()->setUsername($username);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see  src/identity/IdentityManager#setPassword($password)
	   */
	  public static function setPassword($password) {

	  		 IdentityManagerFactory::getManager()->setPassword($password);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getUsername()
	   */
	  public static function getUsername() {

	  		 return IdentityManagerFactory::getManager()->getUsername();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getPassword()
	   */
	  public static function getPassword() {

	  		 return IdentityManagerFactory::getManager()->getPassword();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setEmail($email)
	   */
	  public static function setEmail($email) {

	  		 IdentityManagerFactory::getManager()->setEmail($email);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getEmail()
	   */
	  public static function getEmail() {

	  		 return IdentityManagerFactory::getManager()->getEmail();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setCreated($dateTime)
	   */
	  public static function setCreated($dateTime) {

	  		 IdentityManagerFactory::getManager()->setCreated($dateTime);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getCreated()
	   */
	  public static function getCreated() {

	  		 return IdentityManagerFactory::getManager()->getCreated();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setLastLogin($dateTime)
	   */
	  public static function setLastLogin($dateTime) {

	  		 IdentityManagerFactory::getManager()->setLastLogin($dateTime);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getLastLogin()
	   */
	  public static function getLastLogin() {

	  		 return IdentityManagerFactory::getManager()->getLastLogin();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setEnabled($value)
	   */
	  public static function setEnabled($value) {

	  		 IdentityManagerFactory::getManager()->setEnabled($value);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getEnabled()
	   */
	  public static function getEnabled() {

	  		 return IdentityManagerFactory::getManager()->getEnabled();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setRole(Role $role)
	   */
	  public static function setRole(Role $role) {

	  		 IdentityManagerFactory::getManager()->setRole($role);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getRole()
	   */
	  public static function getRole() {

	  		 return IdentityManagerFactory::getManager()->getRole();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setRoles()
	   */
	  public static function setRoles(array $roles) {

	         IdentityManagerFactory::getManager()->setRoles($roles);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getRoles()
	   */
	  public static function getRoles() {

	         return IdentityManagerFactory::getManager()->getRoles();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#addRole(Role $role)
	   */
	  public static function addRole(Role $role) {

	         IdentityManagerFactory::getManager()->addRole($role);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#hasRole(Role $role)
	   */
	  public static function hasRole(Role $role) {

	  		 return IdentityManagerFactory::getManager()->hasRole($role);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#revokeRole()
	   */
	  public static function revokeRole(Role $role) {

	  		 IdentityManagerFactory::getManager()->revokeRole($role);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#login($username, $password)
	   * @throws AccessDeniedException
	   */
	  public static function login($username, $password) {

	  		 return IdentityManagerFactory::getManager()->login($username, $password);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#resetPassword($token, $sessionId)
	   */
	  public static function resetPassword($token, $sessionId) {

	  	     IdentityManagerFactory::getManager()->resetPassword($token, $sessionId);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#forgotPassword()
	   */
	  public static function forgotPassword() {

	  	     IdentityManagerFactory::getManager()->forgotPassword();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#register()
	   */
	  public static function register() {

	  		 IdentityManagerFactory::getManager()->register();
	  }

      /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#confirm($token, $sessionId)
	   */
	  public static function confirm($token, $sessionId) {

	  		 IdentityManagerFactory::getManager()->confirm($token, $sessionId);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#logout()
	   */
	  public static function logout() {

	  		 Log::debug('Identity::logout');
	  		 Scope::getSessionScope()->destroy();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#isLoggedIn()
	   */
	  public static function isLoggedIn() {

	  		 return IdentityManagerFactory::getManager()->isLoggedIn();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#persist()
	   */
	  public static function persist() {

	  		 IdentityManagerFactory::getManager()->persist();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#merge()
	   */
	  public static function merge() {

	  		 IdentityManagerFactory::getManager()->merge();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#delete()
	   */
	  public static function delete() {

	  	     IdentityManagerFactory::getManager()->delete();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#refresh()
	   */
	  public static function refresh() {

	  		 IdentityManagerFactory::getManager()->refresh();
	  }

	  /**
	   * Destructor prints log debug entry notifying that the identity instance has been destroyed.
	   *
	   * @return void
	   */
	  public function __destruct() {

	  		 Log::debug('Identity::__destruct Instance destroyed');
	  }
}
?>