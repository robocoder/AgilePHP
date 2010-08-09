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
 * @package com.makeabyte.agilephp.test.control
 */

/**
 * Responsible for all login/logout operations
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 */
class LoginController extends BaseController {

	  /**
	   * Renders the login page if there is no session present. If a session is present
	   * the admin page is rendered instead.
	   * 
	   * @return void
	   */
	  public function index() {

	  	 	 Identity::isLoggedIn() ? $this->showAdmin() : $this->showLogin();
	  }
	  
	  /**
	   * Registers a new user account.
	   * 
	   * @param $username The username to register
	   * @param $email The email address of the user
	   * @param $password The password to authenticate the user
	   * @param $role The role for the user
	   * @return unknown_type
	   */
	  public function register() {

	  		 $request = Scope::getRequestScope();

	  		 if(!$username = $request->getSanitized('username'))
	  		 	 throw new FrameworkException('Username required');

	  		 if(!$password = $request->getSanitized('password'))
	  		 	 throw new FrameworkException('Password required');

	  		 if(!$email = $request->getSanitized('email'))
	  		 	 throw new FrameworkException('Email required');

	  		 $role = new Role('test');

	  		 Identity::setUsername($username);
	  		 Identity::setPassword($password);
	  		 Identity::setEmail($email);
	  		 Identity::setRole($role);
	  		 Identity::register();

	  		 $this->set('info', 'Registration successful. Check your email for a confirmation link.');
	  		 $this->showRegister();
	  }

	  /**
	   * Confirms a pending registration
	   * 
	   * @param $token Random registration token
	   * @param $sessionId The session id corresponding to the user that registered
	   * @return void
	   */
	  public function confirm($token, $sessionId) {

	  		 $request = Scope::getRequestScope();

	  		 Identity::confirm($token, $sessionId);

	  		 $this->getRenderer()->set('info', 'Activation Successful');
	  		 $this->showLogin();
	  }

	  /**
	   * Authenticates a user account using AgilePHP Identity and Scope components.
	   * If the login fails the user is taken back to the login page, otherwise
	   * the admin page is rendered.
	   * 
	   * @return void
	   */
	  public function login() {

	  		 $request = Scope::getRequestScope();

	  		 if(!$username = $request->getSanitized('username')) {
	  		 	
	  		 	$this->set('error', 'Username required');
	  		 	$this->showLogin();
	  		 	return;
	  		 }
	  		 if(!$password = $request->getSanitized('password')) {
	  		 	
	  		 	$this->set('error', 'Password required');
	  		 	$this->showLogin();
	  		 	return;
	  		 }

			 if(!Identity::login($username, $password)) {

			 	 $this->set('title', 'Administration :: Home');
	  	      	 $this->set('error', 'Invalid username/password');
	  	      	 $this->set('request_token', Scope::getRequestScope()->createToken());
	  	      	 $this->render('login');
	  	      	 return;
			 }

	  	     $this->showAdmin();
	  }

	  /**
	   * Authenticates the visitor using HTTP basic authentication. By default
	   * the #@BasicAuthentication interceptor uses the AgilePHP Identity component.
	   *
	   * @return void
	   * @throws AccessDeniedException
	   */
	  #@BasicAuthentication
	  public function basicAuthentication() {

	  		 echo 'LoginController::basicAuthentication invoked';
	  }

	  /**
	   * Authenticates the visitor using HTTP basic authentication and a custom realm.
	   *
	   * @return void
	   * @throws AccessDeniedException
	   */
	  #@BasicAuthentication(realm = 'test')
	  public function basicAuthWithCustomRealm() {

	  		 echo 'LoginController::basicAuthWithCustomRealm invoked';
	  }

	  /**
	   * Authenticates the visitor using HTTP basic authentication and a custom
	   * authenticator method. LoginController::basicLogin is responsible for
	   * performing the authentication logic in this case.
	   * 
	   * @return void
	   * @throws AccessDeniedException
	   */
	  #@BasicAuthentication(authenticator = 'basicAuthenticator')
	  public function basicAuthWithCustomAuthenticator() {

	  		 echo 'LoginController::basicAuthWithCustomAuthenticator invoked';
	  }

	  /**
	   * Custom authentication method used by #@BasicAuthentication interceptor
	   * 
	   * @param string $username The username entered into the HTTP basic authentication box
	   * @param string $password The password entered into the HTTP basic authentication box
	   * @return boolean True if the username and password are set to 'test', false otherwise.
	   */
	  public function basicAuthenticator($username, $password) {

	  		 return $username == 'test' && $password == 'test';
	  }

	  /**
	   * Destorys the session which was created by login() and renders the login page.
	   * 
	   * @return void
	   */
	  public function logout() {

	  	     Identity::logout();
	  	     $this->showLogin();
	  }

	  /**
	   * Displays the forgot password form :)
	   * 
	   * @return void
	   */
	  public function oops() {

	  		 $this->showForgotPassword(); 
	  }

	  /**
	   * Uses the AgilePHP Identity component to send the user a link
	   * to click which resets their password when clicked.
	   * 
	   * @return void
	   */
	  public function forgotPassword() {

	  		 $request = Scope::getRequestScope();

	  		 if(!$username = $request->getSanitized('username')) {

	  		 	$this->set('error', 'Username required');
	  		 	$this->showForgotPassword();
	  		 	return;
	  		 }
	  		 if(!$email = $request->getSanitized('email')) {
	  		 	
	  		 	$this->set('error', 'Email required');
	  		 	$this->showForgotPassword();
	  		 	return;
	  		 }

	  		 Identity::setUsername($username);
	  		 Identity::setEmail($email);

	  		 try {
	  		 	   Identity::forgotPassword();
	  		 }
	  		 catch(FrameworkException $e) {

	  		 		$this->set('error', $e->getMessage());
	  		 		$this->showForgotPassword();
	  		 }

	  		 $this->set('info', 'Instructions have been sent to your email address.');
	  		 $this->showForgotPassword();
	  }

	  /**
	   * Uses the AgilePHP Identity component to reset the users password.
	   * 
	   * @param $token The password reset token sent by AgilePHP Identity component
	   * @param $sessionId The session id which created the initial forgot password request
	   * @return void
	   */
	  public function resetPassword($token, $sessionId) {

	  		 Identity::resetPassword($token, $sessionId);
	  		 $this->set('info', 'Your new password has been sent to your email address.');
	  		 $this->showLogin();
	  }
	  
	  /**
	   * Renders the register view.
	   * 
	   * @return void
	   */
	  public function showRegister() {

	  		 $this->set('title', 'Administration :: Home');
	  		 $this->set('request_token', Scope::getRequestScope()->createToken());
	  	     $this->render('register');
	  }

	  /**
	   * Renders the admin view.
	   * 
	   * @return void
	   */
	  private function showAdmin() {

	  	      $this->set('title', 'Administration :: Home');
	  	      $this->render('admin'); 
	  }

	  /**
	   * Renders the login view.
	   * 
	   * @return void
	   */
	  private function showLogin() {

	  		 $this->set('title', 'Administration :: Login');
	  		 $this->set('request_token', Scope::getRequestScope()->createToken());
	  	     $this->render('login');
	  }

	  /**
	   * Renders the forgot password view.
	   * 
	   * @return void
	   */
	  private function showForgotPassword() {

	  		  $this->set('title', 'Administration :: Login :: Forgot Password');
	  		  $this->set('request_token', Scope::getRequestScope()->createToken());
	  	      $this->render('forgotPassword');
	  }
}
?>