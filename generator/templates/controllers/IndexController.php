<?php

class IndexController extends BaseController {

	  public function __construct() {

	  		 parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseModelController#getModel()
	   */
	  public function getModel() {

	  	     return $this->model;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
	  public function index() {

	  	     $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Home' );
	  	     $this->getRenderer()->set( 'content', 'Welcome to the demo application. This is the default PHTML renderer.' );
	  	     $this->getRenderer()->render( 'index' );
	  }

	  /**
	   * Renders the 'about us' page.
	   * 
	   * @return void
	   */
	  public function about() {

	  	     $this->getRenderer()->set( 'title', 'AgilePHP Framework :: About' );
	  	     $this->getRenderer()->render( 'about' );
	  }

	  /**
	   * Renders the 'services' page.
	   * 
	   * @return void
	   */
	  public function services() {

	  	     $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Services' );
	  	     $this->getRenderer()->render( 'services' );
	  }

	  /**
	   * Renders the 'contact us' page.
	   * 
	   * @return void
	   */
	  public function contact() {

	  	     $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Contact' );
	  	     $this->getRenderer()->render( 'contact' );
	  }

	  /**
	   * Handles 'contact us' form submittal.
	   * 
	   * @return void
	   */
	  public function contactSubmit() {

	  		 $request = Scope::getInstance()->getRequestScope();

	  	     $body = 'Name: ' . $request->get( 'name' ) . 
	  	     		 "\nEmail: " . $request->get( 'email' ) .
	  	     		 "\nComments: " . $request->get( 'comments' );

	  		 $mailer = Component::getInstance( 'Mailer' );

	  		 try {
	  	     	    $mailer->setToName( 'AgilePHP Development Team' );
	  	     	    $mailer->setTo( 'root@localhost' );
	  	     	    $mailer->setSubject( 'AgilePHP Demo Applicaiton :: Contact Form Submission' );
	  	     	    $mailer->setBody( $body );
	  	     	    $mailer->send();
  	     	  }
  	     	  catch( AgilePHP_Exception $e ) {

  	     	  		 array_push( $failed, $model->getEmail() );
  	     	  }

  	     	  $result = 'Thank you, ' . $request->get( 'name' ) . '. We have received your comments.';

  	     	  $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Contact Us' );
  	     	  $this->getRenderer()->set( 'formResult', $result );
  	     	  $this->getRenderer()->render( 'contact' );
	  }

	  /**
	   * Demonstrates the ability to easily render and process forms
	   *  
	   * @return void
	   */
	  public function formExample() {

	  	 	 $user = new User();
	  	 	 $user->setUsername( 'username' );
	  	 	 $user->setPassword( 'password' );
	  	 	 $user->setEmail( 'root@localhost' );
	  	 	 $user->setCreated( date( 'c', strtotime( 'now' ) ) );
	  	 	 $user->setLastLogin( date( 'c', strtotime( 'now' ) ) );
	  	 	 $user->setRole( new Role( 'asdfasdf' ) );

	  		 $form = new Form( $user, 'frmUserExample', 'frmUserExample', 'formExamplePOST', null, null );
	  		 $form->setRequestToken( Scope::getRequestScope()->createToken() );

	  		 $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Form Example' );
	  		 $this->getRenderer()->set( 'form', $form->getXSL() );
	  	     $this->getRenderer()->render( 'form-example' );
	  }

	  /**
	   * Shows the array of POST variables and their values.
	   * 
	   * @return void
	   */
	  public function formExamplePOST() {

	  		 $params = Scope::getInstance()->getRequestScope()->getParameters();

	  		 $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Form Example - POSTED!' );
	  		 $this->getRenderer()->set( 'parameters', $params );
	  		 $this->getRenderer()->render( 'form-example' );
	  }

	  /**
	   * Renders the admin PHTML view
	   * 
	   * @return void
	   */
	  public function admin() {

	  		 $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Administration' );
	  	     $this->getRenderer()->render( 'admin' );
	  }

	  /**
	   * Sets error variable for PHTML renderer and loads system messages view.
	   * 
	   * @param $message The error message to display
	   * @return void
	   */
	  private function handleError( $message ) {

	  	      $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Error Page' );
	  		  $this->getRenderer()->set( 'error', $message );
		  	  $this->getRenderer()->render( 'error' );
		  	  exit;
	  }
}
?>