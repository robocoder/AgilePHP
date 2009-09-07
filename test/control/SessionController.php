<?php

class SessionController extends BaseModelActionController {

	  private $model;

	  public function __construct() {

	 	  	 Scope::getInstance()->getSessionScope();
	  		 $this->model = new Session();

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

	  		 parent::modelList();
	  }
}
?>