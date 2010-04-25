<?php

class ServerType {

	private $id;
	private $type;
	private $name;
	private $vendor;

	public function __construct() { }

	public function setId( $value ) {

		 $this->id = $value;
	}

	public function setType( $value ) {

		 $this->type = $value;
	}

	public function setName( $value ) {

		 $this->name = $value;
	}

	public function getId() {

		 return $this->id;
	}

	public function getType() {

		 return $this->type;
	}

	public function getName() {

		 return $this->name;
	}
	
	public function setVendor( $vendor ) {
		
		   $this->vendor = $vendor;
	}
	
	public function getVendor() {

		   return $this->vendor;
	}
}
?>