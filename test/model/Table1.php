<?php

/** AgilePHP generated domain model */
 
class Table1 {

  public function __construct() { }

  private $id;
  private $field1;
  private $field2;
  private $field3;
  
  private $Table2;

  #@Id
  public function setId( $value ) {

     $this->id = $value;
  }

  public function getId() {

     return $this->id;
  }

  public function setField1( $value ) {

     $this->field1 = $value;
  }

  public function getField1() {

     return $this->field1;
  }

  public function setField2( $value ) {

     $this->field2 = $value;
  }

  public function getField2() {

     return $this->field2;
  }

  public function setField3( $value ) {
  	
  		$this->field3 = $value;
  }
  
  public function getField3() {
  	
  		return $this->field3;
  }
  
  public function setTable2( Table2 $value = null ) {

     $this->Table2 = $value;
  }

  public function getTable2() {

     return $this->Table2;
  }

}
?>