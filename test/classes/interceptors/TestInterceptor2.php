<?php

#@Interceptor
class TestInterceptor2 {

	  public $param1;
	  public $param2 = array();
	  public $param3;
	  public $logger;

	  #@AroundInvoke
	  public function audit( InvocationContext $ic ) {

	  		 $class = new ReflectionClass( $ic->getTarget() );

	  		 $message = 'TestInterceptor2::audit @AroundInvoke intercepted call to class \'' . $class->getName() . 
	  		 			'\', method \'' . $ic->getMethod() . '\', with parameters \'' . 
	  		 			($ic->getParameters() ? implode( ',', $ic->getParameters() ) : '') . 
	  		 			'\'. This is what the interceptor looks like: ' . print_r( $ic->getInterceptor(), true );

	  		 $this->logger->debug( $message );
	  }

	  /**
	   * Returns all TestInterceptor2 fields/properties. These are set
	   * in the annotation declaration. Since this method does not contain
	   * an #@AroundInvoke annotation, it is never called during the interception.
	   * You would need to invoke this method yourself if you wanted to use it.
	   * This shows that really interceptors are still PHP classes at the end
	   * of the day, with just a little bit of magical seasoning :)
	   *  
	   * @return TestInterceptor2 fields/properties
	   */
	  public function getParams() {

	  		 echo "param1 = " . $this->param1 . 
	  		 	  ", param2 = " . implode( ",", $this->param1 ) .
	  		 	  ", param3 = " . $this->param3;
	  }
}
?>