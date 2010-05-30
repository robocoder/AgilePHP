<?php

class LoginController extends BaseController {

	  public function __construct() {

	  	     parent::__construct();
	  }

	  /**
	   * Renders the login page if there is no session present. If a session is present
	   * the admin page is rendered instead.
	   * 
	   * @return void
	   */
	  public function index() {

	  	 	 if( Identity::getInstance()->isLoggedIn() )
	  	     	 $this->showAdmin();
	  	     else
	  	     	$this->showLogin();
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

	  		 if( !$username = $request->get( 'username' ) )
	  		 	 throw new AgilePHP_Exception( 'Username required' );

	  		 if( !$password = $request->get( 'password' ) )
	  		 	 throw new AgilePHP_Exception( 'Password required' );
	  		 	 
	  		 if( !$email = $request->get( 'email' ) )
	  		 	 throw new AgilePHP_Exception( 'Email required' );

	  	     /** @todo remove hard coded role */
	  		 $role = new Role( 'test' );

	  		 $identity = Identity::getInstance();
	  		 $identity->setUsername( $username );
	  		 $identity->setPassword( $password );
	  		 $identity->setEmail( $email );
	  		 $identity->setRole( $role );
	  		 $identity->register();

	  		 $this->getRenderer()->set( 'info', 'Registration successful. Check your email for a confirmation link.' );
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

	  		 $identity = Identity::getInstance();
	  		 $identity->confirm( $request->sanitize( $token ), $request->sanitize( $sessionId ) );

	  		 $this->getRenderer()->set( 'info', 'Activation Successful' );
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

	  		 if( !$username = $request->getSanitized( 'username' ) ) {
	  		 	
	  		 	$this->getRenderer()->set( 'error', 'Username required' );
	  		 	$this->showLogin();
	  		 	return;
	  		 }
	  		 if( !$password = $request->getSanitized( 'password' ) ) {
	  		 	
	  		 	$this->getRenderer()->set( 'error', 'Password required' );
	  		 	$this->showLogin();
	  		 	return;
	  		 }

			 if( !Identity::getInstance()->login( $username, $password ) ) {

			 	 Scope::getRequestScope()->invalidate();
	  	      	 $this->getRenderer()->set( 'error', 'Invalid username/password' );
	  	      	 $this->getRenderer()->render( 'login' );
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

	  	     Identity::getInstance()->logout();
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

	  		 $identity = Identity::getInstance();

	  		 if( !$username = $request->getSanitized( 'username' ) ) {

	  		 	$this->getRenderer()->set( 'error', 'Username required' );
	  		 	$this->showForgotPassword();
	  		 	return;
	  		 }
	  		 if( !$email = $request->getSanitized( 'email' ) ) {
	  		 	
	  		 	$this->getRenderer()->set( 'error', 'Email required' );
	  		 	$this->showForgotPassword();
	  		 	return;
	  		 }

	  		 $identity->setUsername( $username );
	  		 $identity->setEmail( $email );

	  		 try {
	  		 	   Identity::getInstance()->forgotPassword();
	  		 }
	  		 catch( AgilePHP_Exception $e ) {

	  		 		$this->getRenderer()->set( 'error', $e->getMessage() );
	  		 		$this->showForgotPassword();
	  		 }

	  		 $this->getRenderer()->set( 'info', 'Instructions have been sent to your email address.' );
	  		 $this->showForgotPassword();
	  }

	  /**
	   * Uses the AgilePHP Identity component to reset the users password.
	   * 
	   * @param $token The password reset token sent by AgilePHP Identity component
	   * @param $sessionId The session id which created the initial forgot password request
	   * @return void
	   */
	  public function resetPassword( $token, $sessionId ) {

	  		 try {
	  		 	   Identity::getInstance()->resetPassword( $token, $sessionId );
	  		 }
	  		 catch( AgilePHP_Exception $e ) {

	  		 		$this->getRenderer()->set( 'error', $e->getMessage() );
	  		 		$this->getRenderer()->render( 'error' );
	  		 		return;
	  		 }

	  		 $this->getRenderer()->set( 'info', 'Your new password has been sent to your email address.' );
	  		 $this->showLogin();
	  }

	  /**
	   * Renders the admin view.
	   * 
	   * @return void
	   */
	  private function showAdmin() {

	  	      $this->getRenderer()->set( 'title', 'Administration :: Home' );
	  	      $this->getRenderer()->render( 'admin' ); 
	  }

	  /**
	   * Renders the register view.
	   * 
	   * @return void
	   */
	  public function showRegister() {

	  		 $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Register' );
	  		 $this->getRenderer()->set( 'request_token', Scope::getRequestScope()->createToken() );
	  	     $this->getRenderer()->render( 'register' );
	  }

	  /**
	   * Renders the login view.
	   * 
	   * @return void
	   */
	  private function showLogin() {

	  		 $this->getRenderer()->set( 'title', 'Administration :: Login' );
	  		 $this->getRenderer()->set( 'request_token', Scope::getRequestScope()->createToken() );
	  	     $this->getRenderer()->render( 'login' );
	  }

	  /**
	   * Renders the forgot password view.
	   * 
	   * @return void
	   */
	  private function showForgotPassword() {

	  		  $this->getRenderer()->set( 'title', 'Administration :: Login :: Forgot Password' );
	  		  $this->getRenderer()->set( 'request_token', Scope::getRequestScope()->createToken() );
	  	      $this->getRenderer()->render( 'forgot_password' );
	  }
}
?>