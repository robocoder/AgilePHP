<?php

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

	  		 /** @todo remove hard coded role */
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
	   * Confirms a registration
	   *
	   * @param $token Random registration token
	   * @param $sessionId The session id corresponding to the user that registered
	   * @return void
	   */
	  public function confirm( $token, $sessionId ) {

	  		 $request = Scope::getRequestScope();

	  		 Identity::confirm($request->sanitize($token), $request->sanitize($sessionId));

	  		 $this->set('info', 'Activation Successful');
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

	  		 if(!$username = $request->getSanitized('username' )) {

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
	  		 catch( FrameworkException $e ) {

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
	   * Renders the admin view.
	   *
	   * @return void
	   */
	  private function showAdmin() {

	  	      $this->set('title', 'Administration :: Home');
	  	      $this->render('admin');
	  }

	  /**
	   * Renders the register view.
	   *
	   * @return void
	   */
	  public function showRegister() {

	  		 $this->set('title', 'AgilePHP Framework :: Register');
	  		 $this->set('request_token', Scope::getRequestScope()->createToken());
	  	     $this->render('register');
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
	  	      $this->render('forgot_password');
	  }
}
?>