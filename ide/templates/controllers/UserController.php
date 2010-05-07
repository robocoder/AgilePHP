<?php

class UserController extends BaseModelActionController {

	  private $model;

	  public function __construct() {

	  		 $this->model = new User();
	  		 parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseModelController#getModel()
	   */
	  public function getModel() {

	  	     return $this->model;
	  }
}
?>