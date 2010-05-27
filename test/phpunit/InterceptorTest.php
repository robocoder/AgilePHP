<?php

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
	  		 $mit->setProperty1( 'test' );

	 	  	 // The call to setProperty1 is intercepted and set to 'intercepted value' by #@TestInterceptor property1Setter #@AroundInvoke method
	  		 PHPUnit_Framework_Assert::assertEquals( 'intercepted value', $mit->getProperty1(), 'Failed to assert MockInterceptionTarget::property1 equals "intercepted value"' );

	  		 // The dependency injection @In interceptor set the public logger property to an instance of Logger :)
	  		 PHPUnit_Framework_Assert::assertType( 'Logger', $mit->logger, 'Failed to assert MockInterceptionTarget::logger is type Logger. Dependancy injection failed' );
	  		 PHPUnit_Framework_Assert::assertTrue( $mit->logger instanceof Logger, 'Failed to assert MockInterceptionTarget::logger is an instance of Logger. Dependency injection failed.' );
	  }

	  /**
	   * @test
	   * @expectedException AgilePHP_AccessDeniedException
	   */
	  public function RestrictInterceptorTest() {

	  		 try {
			  		 $mit = new MockInterceptionTarget();
			  		 $mit->restrictedMethod( 'test' );
			  		 PHPUnit_Framework_Assert::fail( '#@Restrict did not throw AgilePHP_AccessDeniedException' );
	  		 }
	  		 catch( Exception $e ) {

	  		 		throw new AgilePHP_AccessDeniedException('');
	  		 }
	  }
	  
	  /**
	   * @test
       * @expectedException AgilePHP_NotLoggedInException
       */
	  public function LoggedInInterceptorTest() {

	  		 try {
			  		 $mit = new MockInterceptionTarget();
			  		 $mit->secureMethod( 'test' );
	  		 }
	  		 catch( Exception $e ) {

	  		 		throw new AgilePHP_NotLoggedInException('');
	  		 }
	  }
}
?>