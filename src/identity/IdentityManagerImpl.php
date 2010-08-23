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
 * Default IdentityManager implementation. Manages user, role, session,
 * authentication, and basic email tasks.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 */
class IdentityManagerImpl implements IdentityManager {

	  private $model;
	  private $modelName;
	  private $authenticator;
	  private $forgotPasswdMailer;
	  private $resetPasswdMailer;
	  private $registrationMailer;

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setModel($model)
	   */
	  public function setModel(IdentityModel $model) {

	  		 $this->model = $model;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getModel()
	   */
	  public function getModel() {

	  		 return $this->model;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setModelName($name)
	   */
	  public function setModelName($name) {

	  		 $this->modelName = $name;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getModelName()
	   */
	  public function getModelName() {

	  		 return $this->modelName;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setUsername($username)
	   */
	  public function setUsername($username) {

	  		 $this->getModel()->setUsername($username);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see  src/identity/IdentityManager#setPassword($password)
	   */
	  public function setPassword($password) {

	  		 $this->getModel()->setPassword($password);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getUsername()
	   */
	  public function getUsername() {

	  		 return $this->getModel()->getUsername();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getPassword()
	   */
	  public function getPassword() {

	  		 return $this->getModel()->getPassword();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setEmail($email)
	   */
	  public function setEmail($email) {

	  		 $this->getModel()->setEmail($email);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getEmail()
	   */
	  public function getEmail() {

	  		 return $this->getModel()->getEmail();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setCreated($dateTime)
	   */
	  public function setCreated($dateTime) {

	  		 $this->getModel()->setCreated($dateTime);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getCreated()
	   */
	  public function getCreated() {

	  		 return $this->getModel()->getCreated();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setLastLogin($dateTime)
	   */
	  public function setLastLogin($dateTime) {

	  		 $this->getModel()->setLastLogin($dateTime);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getLastLogin()
	   */
	  public function getLastLogin() {

	  		 return $this->getModel()->getLastLogin();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setEnabled($value)
	   */
	  public function setEnabled($value) {

	  		 $this->getModel()->setEnabled($value);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getEnabled()
	   */
	  public function getEnabled() {

	  		 return $this->getModel()->getEnabled();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setRole($role)
	   */
	  public function setRole(Role $role) {

	  		 $this->getModel()->setRole($role);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getRole()
	   */
	  public function getRole() {

	  		 return $this->getModel()->getRole();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setRoles(array $roles)
	   */
	  public function setRoles(array $roles) {

	         $this->getModel()->setRoles($roles);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getRoles()
	   */
	  public function getRoles() {

	         return $this->getModel()->getRoles();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#addRole(Role $role))
	   */
	  public function addRole(Role $role) {

	         if(!is_array($this->getModel()->getRoles()))
	            $this->setRoles(array());

	         $roles = $this->getRoles();
	         array_push($roles, $role);
	         $this->getModel()->setRoles($roles);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#hasRole(Role $role)
	   */
	  public function hasRole(Role $role) {

	  		 if($this->getModel()->getRole() instanceof Role &&
	  		     $this->getModel()->getRole()->getName() == $role->getName())
	  		 	     return true;

	  		 $roles = $this->getModel()->getRoles();
	  		 if(is_array($roles))
	  		    for($i=0; $i<count($roles); $i++)
	  		        if($roles[$i]->getName() == $role->getName())
	  		           return true;

	  		 return false;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#revokeRole(Role $role)
	   */
	  public function revokeRole(Role $role) {

	  		 if($this->getRole() instanceof Role && $this->getRole()->getName() == $role->getName())
	  		    $this->setRole(null);

	  		 $roles = $this->getModel()->getRoles();
	  		 if(is_array($roles))
	  		   for($i=0; $i<count($roles); $i++)
	  		      if($roles[$i]->getName() == $role->getName())
	  		         array_splice($roles, $i, 1);

	  		 $this->getModel()->setRoles($roles);
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setForgotPasswdMailer($mailer)
	   */
	  public function setForgotPasswdMailer($mailer) {

	  		 $this->forgotPasswdMailer = $mailer;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getForgotPasswdMailer()
	   */
	  public function getForgotPasswdMailer()  {

	  		 return $this->forgotPasswdMailer;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setResetPasswdMailer($mailer)
	   */
	  public function setResetPasswdMailer($mailer) {

	  		 $this->resetPasswdMailer = $mailer;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getResetPasswdMailer()
	   */
	  public function getResetPasswdMailer()  {

	  		 return $this->resetPasswdMailer;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setRegistrationMailer($mailer)
	   */
	  public function setRegistrationMailer($mailer) {

	  		 $this->registrationMailer = $mailer;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getRegistrationMailer()
	   */
	  public function getRegistrationMailer() {

	  		 return $this->registrationMailer;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#setAuthenticator($authenticator)
	   */
	  public function setAuthenticator($authenticator) {

	  		 $this->authenticator = $authenticator;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#getAuthenticator()
	   */
	  public function getAuthenticator() {

	  		 return $this->authenticator;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#forgotPassword()
	   */
	  public function forgotPassword() {

	  		 $token = IdentityUtils::createToken();

	  		 $session = Scope::getSessionScope();
	  		 $session->set('resetPasswordToken', $token);
	  		 $session->set('username', $this->getUsername());

	  		 $table = ORM::getTableByModelName($this->getModelName());
	  		 $emailColumn = $table->getColumnNameByProperty('email');

	  		 ORM::prepare('SELECT ' . $emailColumn . ' FROM ' . $table->getName() .
	  		 			  ' WHERE ' . $emailColumn . '=? AND username=?;');
	  		 $params = array($this->getEmail(), $this->getUsername());

	  		 if(!ORM::execute($params)->fetch())
	  		 	 throw new FrameworkException('The information provided does not match our records.');

	  		 $mailer = $this->getForgotPasswdMailer();

	  		 $Mailer = new $mailer($this->getUsername(), $this->getEmail(), $token);
	  		 $Mailer->send();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#resetPassword($token, $sessionId)
	   */
	  public function resetPassword($token, $sessionId) {

	  		 $session = Scope::getSessionScope();
	  		 $session->setSessionId($sessionId);

	  		 if($token !== $session->get('resetPasswordToken'))
	  		 	throw new FrameworkException('Invalid token: ' . $token);

	  		 $password = IdentityUtils::createToken();

	  		 $this->setUsername($session->get('username'));
	  		 $this->setPassword($password);
	  		 $this->merge();

	  		 $session->destroy();

	  		 $mailer = $this->getResetPasswdMailer();

	  		 $Mailer = new $mailer($this->getUsername(), $password, $this->getEmail());
	  		 $Mailer->send();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#register()
	   */
	  public function register() {

	  		 $token = IdentityUtils::createToken();

	  		 $session = Scope::getSessionScope();

	  		 $session->set('activationToken', $token);
	  		 $session->set('username', $this->getUsername());

	  		 $this->setCreated(strtotime('now'));
	  		 $this->setEnabled(0);
	  		 $this->persist();

	  		 $mailer = $this->getRegistrationMailer();

	  		 $Mailer = new $mailer( $token );
	  		 $Mailer->send();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#confirm($token, $sessionId)
	   */
	  public function confirm($token, $sessionId) {

	  		 $session = Scope::getSessionScope();
	  		 $session->setSessionId($sessionId);

	  		 if($token !== $session->get('activationToken'))
	  		 	 throw new FrameworkException('Invalid token: ' . $token);

	  		 $user = new User();
	  		 $user->setUsername($session->get('username')); // #@Id interceptor performs lookup

	  		 if(!$user->getPassword())
	  		 	 throw new FrameworkException('User not found');

	  		 $this->setModel($user);
	  		 $this->setEnabled(1);
	  		 $this->merge();

	  		 $session->destroy();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#login($username, $password)
	   * @throws AccessDeniedException
	   */
	  public function login($username, $password) {

	  		 $authenticator = new $this->authenticator;
	  		 if($model = $authenticator::authenticate($username, $password)) {

   	  		    if(!$model instanceof IdentityModel)
    	  		   throw new FrameworkException('Authenticator must return an instance of IdentityModel');

	  		    $session = Scope::getSessionScope();
      	  		$session->set('IDENTITY_LOGGEDIN', true);
    	  		$session->set('IDENTITY_MODEL', $model);

   	  		    $model->setLastLogin(strtotime('now'));
   	  		    $this->setModel($model);

   	  		    if($authenticator instanceof DefaultAuthenticator)
   	  		       $this->merge();

    	  		return true;
	  		 }

	  		 return false;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#logout()
	   */
	  public function logout() {

	  		 Scope::getSessionScope()->destroy();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#isLoggedIn()
	   */
	  public function isLoggedIn() {

	  		 return (Scope::getSessionScope()->get('IDENTITY_LOGGEDIN')) ? true : false;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#persist()
	   */
	  public function persist() {

  		     ORMFactory::getDialect()->persist($this->getModel());
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#merge()
	   */
	  public function merge() {

	  		 ORMFactory::getDialect()->merge($this->getModel());
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#delete()
	   */
	  public function delete() {

  	         ORMFactory::getDialect()->delete($this->getModel());
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/identity/IdentityManager#refresh()
	   */
	  public function refresh() {

             $results = ORM::find($this->getModel());
	  		 if(isset($results[0])) $this->setModel($results[0]);
	  }

	  /**
	   * Destructor prints log debug entry notifying that the identity instance has been destroyed.
	   *
	   * @return void
	   */
	  public function __destruct() {

	  		 Log::debug('IdentityManagerImpl::__destruct Instance destroyed');
	  }
}
?>