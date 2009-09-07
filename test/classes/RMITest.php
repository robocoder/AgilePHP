<?php

class RMITest {

	  private $test1;
	  private $test2;
	  private $test3;

	  public function __construct( $test1, $test2 = null, $test3 = 'test' ) {

	  		 $this->test1 = $test1;
	  		 $this->test2 = $test2;
	  		 $this->test3 = $test3;
	  }

	  #@RemoteMethod
	  public function testme( $param1 = null, $param2 ) {

	  		 echo '$this->test1 = ' . $this->test1 . "\n";
	  		 echo '$this->test2 = ' . $this->test2 . "\n";
	  		 echo '$this->test3 = ' . $this->test3 . "\n";

	  		 echo '$param1 = ' . ($param1 ? $param1 : 'null') . "\n";
	  		 echo '$param2 = ' . $param2 . "\n";
	  		 
	  		 return 'testme done';
	  }

	  #@RemoteMethod
	  #@Restrict( role = 'foo' )
	  public function testme2( $param1 = null, $param2 ) {
	  	
	  		 echo 'this does something 2';
	  		 
	  		 return 'testme2 done';
	  }

	  #@RemoteMethod
	  public function testme3() {

	  		 echo 'this does something else';
	  		 
	  		 return 'testme3 done';
	  }

	  public function testme4() { }

	  #@RemoteMethod
	  public function setTest2( $val ) {

	  		 $this->test2 = $val;
	  		 echo 'set test2 to: ' . $val;
	  }
	  
	  #@RemoteMethod
	  public function getTest2() {

	  		 return $this->test2;
	  }

	  #@RemoteMethod
	  public function show() {

	  		 print_r( $this );
	  		 return 'RMITest::show';
	  }
}
?>