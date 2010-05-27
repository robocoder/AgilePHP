<?php

/** AgilePHP generated controller */

namespace TestComponent\control;

class Table2Controller extends \BaseModelActionController {

  private $model;

  public function __construct() { 

    $this->model = new \TestComponent\model\Table2();
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