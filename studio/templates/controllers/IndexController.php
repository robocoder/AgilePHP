<?php

class IndexController extends BaseController {

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
	   * Renders the admin PHTML view
	   *
	   * @return void
	   */
	  public function admin() {

	  		 $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Administration' );
	  	     $this->getRenderer()->render( 'admin' );
	  }
}
?>