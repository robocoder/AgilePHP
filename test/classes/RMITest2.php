<?php

class RMITest2 {

	  public function __construct() { }

	  #@RemoteMethod
	  public function testme( $param1 = null, $param2 ) {

	  		 echo '$param1 = ' . ($param1 ? $param1 : 'null') . "\n";
	  		 echo '$param2 = ' . $param2 . "\n";
	  }

	  #@RemoteMethod
	  public function testme2( $param1 = null, $param2 ) {
	  	
	  		 echo 'this does something 2';
	  }
	  
	  #@RemoteMethod
	  public function testme3() {

	  		 echo 'this does something else';
	  }
	  
	  public function testme4() { }
}
?>