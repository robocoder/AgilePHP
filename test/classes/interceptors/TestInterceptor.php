<?php

#@Interceptor
class TestInterceptor {

	  #@AroundInvoke 
	  public function test( InvocationContext $ic ) {

	  		 $message = "TestInterceptor::test Executed @AroundInvoke\n";
	  		 $ic->getTarget()->logger->debug( $message );
	  }

	  /**
	   * An example using the InvocationContext getMethod in logic criteria
	   */
	  #@AroundInvoke
	  public function property1Setter( InvocationContext $ic ) {

	  		 $method = $ic->getMethod();

	  		 if( !$method )
	  		 	 throw new AgilePHP_Exception( 'TestInterceptor::specialValue requires a valid method name. You specified \'' . $ic->getMethod() . '\'.' );

	  		 if( $method == 'setProperty1' )
	  		 	 $ic->getTarget()->$method( 'intercepted value' );

	  		 // We can also change the parameter list and then call proceed like the example below.
	  		 // This does not break the execution chain like the example above is doing.
	  		 // The example above must break the chain of execution because if $ic->proceed()
	  		 // is called, the dynamic proxy class will resume as normal with the __call
	  		 // which will result in the original value of 'test' being set.
	  		 //
	  		 // if( $method == 'setProperty1' )
	  		 //     $ic->setParameters( 'another way to change the value which allows the chain/stack to proceed' );
	  		 //
	  		 // $ic->proceed(); # Now the dynamic proxy will continue with the __call as normal using
	  		 //					# the InvocationContext values, which contains the altered parameter.
	  }
}
?>