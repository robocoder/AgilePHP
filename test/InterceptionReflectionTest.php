<?php

require_once 'BaseTest.php';

class InterceptedClassTest extends BaseTest {

	  public function test1() {

	  		 $user = new User();
	  		 $class = new InterceptedClass( $user );

	  		 PHPUnit_Framework_Assert::assertEquals( 'User', $class->getName() );
	  }
}
?>