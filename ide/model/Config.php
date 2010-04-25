<?php

class Config {

	  private $name;
	  private $value;

	  public function __construct( $name = null, $value = null ) {

	  		 $this->name = $name;
	  		 $this->value = $value;
	  }

	  #@Id
	  public function setName( $name ) {
	  	
	  		 $this->name = $name;
	  }
	  
	  public function getName() {
	  	
	  		 return $this->name;
	  }
	  
	  public function setValue( $value ) {
	  	
	  		 $this->value = $value;
	  }
	  
	  public function getValue() {
	  	
	  		 return $this->value;
	  }
}
?>