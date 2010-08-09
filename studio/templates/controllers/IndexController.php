<?php

class IndexController extends BaseController {

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
	  public function index() {

	  	     $this->set('title', 'AgilePHP Framework :: Home');
	  	     $this->set('content', 'Welcome to the demo application. This is the default PHTML renderer.');
	  	     $this->render('index');
	  }

	  /**
	   * Renders the admin PHTML view
	   *
	   * @return void
	   */
	  public function admin() {

	  		 $this->set('title', 'AgilePHP Framework :: Administration');
	  	     $this->render('admin');
	  }
}
?>