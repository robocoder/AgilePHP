<?php

class IndexController extends BaseController {

	  public function __construct() {

	  		 parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
	  public function index() {

	  	     $this->getRenderer()->set( 'title', 'AgilePHP Framework :: Integrated Development Environment' );
	  	     $this->getRenderer()->render( 'index' );
	  }
}
?>