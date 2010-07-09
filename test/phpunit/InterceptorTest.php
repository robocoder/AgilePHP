<?php
/**
 * @package com.makeabyte.agilephp.test.interception
 */
class InterceptorTest extends BaseTest {

	  /**
	   * This test case illustrates intercepting a method call and changing
	   * the value that is set, auditing method calls, and dependency injection
	   * in both classes and interceptors with the use of the @In annotation.
	   *
	   * @return void
	   * @test
	   */
	  public function changeMethodParameterAndInjectLoggerTest() {

	  		 $mit = new MockInterceptionTarget();
	  		 $mit->setProperty1('test');

	 	  	 // The call to setProperty1 is intercepted and set to 'intercepted value' by #@TestInterceptor property1Setter #@AroundInvoke method
	  		 PHPUnit_Framework_Assert::assertEquals('intercepted value', $mit->getProperty1(), 'Failed to assert MockInterceptionTarget::property1 equals "intercepted value"');
	  }

 	  /**
	   * @test
	   */
	  public function LoggerInterceptorTest() {

	  		 $mit = new MockInterceptionTarget();
	  		 PHPUnit_Framework_Assert::assertType( 'LogProvider', $mit->getLogger(), 'Logger instance does not exist' );
	  }

	  /**
	   * @test
	   * @expectedException AccessDeniedException
	   */
	  public function RestrictInterceptorTest() {

	  		 try {
			  		 $mit = new MockInterceptionTarget();
			  		 $mit->restrictedMethod( 'test' );
			  		 PHPUnit_Framework_Assert::fail( '#@Restrict did not throw AccessDeniedException' );
	  		 }
	  		 catch( Exception $e ) {

	  		 		throw new AccessDeniedException('');
	  		 }
	  }

	  /**
	   * @test
	   * @expectedException NotLoggedInException
       */
	  public function LoggedInInterceptorTest() {

	  		 try {
			  		 $mit = new MockInterceptionTarget();
			  		 $mit->secureMethod( 'test' );
	  		 }
	  		 catch( Exception $e ) {

	  		 		throw new NotLoggedInException('');
	  		 }
	  }

	  /**
	   * @test
	   */
	  public function userIdAndPasswordInterceptorTest() {

	         $user = new User();
	         $user->setUsername('test');

	         PHPUnit_Framework_Assert::assertNotNull($user->getPassword(), 'Id interceptor failed to retrieve user account');

	         $user->setPassword('test');

	         PHPUnit_Framework_Assert::assertNotEquals('test', $user->getPassword(), 'Password interceptor failed to hash password');
	         PHPUnit_Framework_Assert::assertEquals(64, strlen($user->getPassword()), 'Password interceptor failed to hash password {2}');
	  }
}
?>