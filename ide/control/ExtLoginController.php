<?php

class ExtLoginController extends BaseExtController {

	  public function __construct() {

	  	     parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/mvc/BaseController#index()
	   */
	  public function index() { }

	  /**
	   * Authenticates a user account using AgilePHP Identity and Scope components.
	   * 
	   * @return void
	   */
	  public function login() {

	  		 $request = Scope::getInstance()->getRequestScope();

	  		 if( !$username = $request->getSanitized( 'username' ) ) {

	  		 	$this->getRenderer()->setError( 'Username required' );
	  		 	$this->getRenderer()->render( false );
	  		 }

	  		 if( !$password = $request->getSanitized( 'password' ) ) {

	  		 	 $this->getRenderer()->setError( 'Password required' );
	  		 	 $this->getRenderer()->render( false );
	  		 }

			 if( !Identity::getInstance()->login( $username, $password ) ) {

			 	 Scope::getInstance()->getRequestScope()->invalidate();
			 	 $this->getRenderer()->setError( 'Invalid username/password' );
	  		 	 $this->getRenderer()->render( false );
			 }

	  	     $this->getRenderer()->render( true );
	  }

	  /**
	   * Destroys the session which was created by login()
	   * 
	   * @return void
	   */
	  public function logout() {

	  	     Identity::getInstance()->logout();
	  }
}
?>