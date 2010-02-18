<?php

/**
 * Tests the #@RequestParam interceptor (sets the annotated property with the
 * corresponding form field value).
 */
class RequestParamController extends BaseController {

	  #@RequestParam( name = 'name' )
	  public $name;

	  #@RequestParam( name = 'comments', sanitize = false )
	  public $comments;

	  #@In( class = Logger::getInstance() )
	  public $logger;

	  public function __construct() {

	  		 parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
	  public function index() {

	  		 $this->getRenderer()->render( 'request-param-example' );
	  }

	  /**
	   * Displays the submitted form values set by the #@RequestParam interceptor
	   * 
	   * @return void
	   */
	  public function process() {

	  		 echo '<hr>';
	  		 echo 'Name: ' . $this->name . '<br>';
	  		 echo 'Comments: ' . $this->comments . '<br>';

	  		 $this->logger->debug( 'RequestParamController::process Name = ' . $this->name . ', comments: ' . $this->comments );
	  }
}
?>