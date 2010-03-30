<?php

class Server {

	private $id;
	private $type;
	private $ip;
	private $hostname;
	private $profile;
	
	private $ServerType;

	public function __construct() { }

	public function setId( $value ) {

		 $this->id = $value;
	}

	public function setType( $value ) {

		 $this->type = $value;
	}

	public function setIp( $value ) {

		 $this->ip = $value;
	}

	public function setHostname( $value ) {

		 $this->hostname = $value;
	}

	public function setProfile( $value ) {

		 $this->profile = $value;
	}

	public function getId() {

		 return $this->id;
	}

	public function getType() {

		 return $this->type;
	}

	public function getIp() {

		 return $this->ip;
	}

	public function getHostname() {

		 return $this->hostname;
	}

	public function getProfile() {

		 return $this->profile;
	}

	public function setServerType( ServerType $st = null ) {

		   $this->ServerType = $st;
	}

	public function getServerType() {

		   return $this->ServerType;
	}
}
?>