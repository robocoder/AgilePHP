<?php

/** AgilePHP generated domain model */

class Table2 {

  public function __construct() { }

  private $id;
  private $name;
  private $description;

  #@Id
  public function setId( $value ) {

     $this->id = $value;
  }

  public function getId() {

     return $this->id;
  }

  public function setName( $value ) {

     $this->name = $value;
  }

  public function getName() {

     return $this->name;
  }
  
  public function setDescription( $description ) {
  	
  		$this->description = $description;
  }
  
  public function getDescription() {
  	
  		return $this->description;
  }

}
?>