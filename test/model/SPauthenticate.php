<?php
class SPauthenticate extends DomainModel {

	  private $userId;
	  private $passwd;
	  private $result;

	  public function __construct() { }
	  
	  public function setUserId($userId) {

	  		 $this->userId = $userId;
	  }

	  public function getUserId() {

	  		 return $this->userId;
	  }

	  public function setPasswd($password) {

	  		 $this->passwd = $password;
	  }

	  public function getPasswd() {

	  		 return $this->passwd;
	  }

	  public function setResult($result) {

	  		 $this->result = $result;
	  } 

	  public function getResult() {

	  		 return $this->result;
	  }
}
?>