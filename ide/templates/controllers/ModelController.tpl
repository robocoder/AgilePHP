<?php

/**
 * @package #projectName#.control
 */

class #ClassName# extends BaseModelActionController {

	  private $model;

	  public function __construct() {

			 $this->model = new #model#();
	  		 parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/mvc/BaseModelController#getModel()
	   */
	  public function getModel() {

	  	     return $this->model;
	  }
}
?>