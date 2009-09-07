<?php

class AJAXController extends BaseController {

	  private $renderer;

	  public function __construct() {

	  		 $this->renderer = MVC::getInstance()->createRenderer( 'AJAXRenderer' );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
	  public function index() {

	  		 $stdClass = new stdClass();
	  		 $stdClass->result = 'Some text from the AJAXController';
	  		 $this->renderer->render( $stdClass );
	  }

	  public function testUpdater() {

	  		 echo '<div style="color: #FF0000;">Some text from the AJAXController</div>';
	  }
}
?>