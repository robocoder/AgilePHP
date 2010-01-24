<?php

class Mailing {
	
	  private $id;
	  private $name;
	  private $email;
	  private $enabled;
	  
	  public function Mailing() { }

	  #@Id
	  public function setId( $id ) {
	  	
	  	     $this->id = $id;
	  }
	  
	  public function getId() {
	  	
	  	     return $this->id;
	  }
	  
	  public function setName( $name ) {
	  	
	  	     $this->name = $name;
	  }
	  
	  public function getName() {
	  	
	  	     return $this->name;
	  }
	  
	  public function setEmail( $email ) {
	  	
	  	     $this->email = $email;
	  }
	  
	  public function getEmail() {
	  	
	  	     return $this->email;
	  }
	  
	  public function setEnabled( $bool ) {
	  	
	  	     $this->enabled = $bool;
	  }
	  
	  public function getEnabled() {

	  	     return $this->enabled;
	  }
	  
	  public function isEnabled() {

	  	     return $this->enabled == true ? true : false;
	  }
}
?>