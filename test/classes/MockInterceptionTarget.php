<?php

#@TestInterceptor2( param1 = "test", param2 = { key1 = "test2", "test3", key2 = "test4" }, param3 = new Role( 'phpunit' ), logger = Logger::getInstance() )
class MockInterceptionTarget {

	  private $property1;

	  #@In( class = Logger::getInstance() )
	  public $logger;

	  public function __construct() { }

	  /**
	   * Property1 mutator
	   * 
	   * @param $value The value
	   * @return void
	   */
	  #@TestInterceptor
	  #@TestInterceptor2( param1 = Crypto::getInstance()->setAlgorithm( 'md5' ), param2 = { key1 = "test2", "test3", key2 = "test4" }, param3 = new Role( 'phpunit' ), logger = Logger::getInstance() )
	  public function setProperty1( $value ) {

	  		 $this->logger->debug( 'MockInterceptionTarget setProperty1 with value \'' . $value . '\' and this is using the injected instance of Logger :D' );

	  		 $this->property1 = $value;
	  }

	  /**
	   * Property1 accessor
	   * 
	   * @return Property1 value
	   * 
	   * #@TestInterceptor2
	   */
	  public function getProperty1() {

	  		 return $this->property1;
	  }

	  /**
	   * Restricted method. Only users with a role of
	   * 'admin' can invoke this method.
	   * 
	   * @return The string 'restrictedMethod'
	   * 
	   * #@Restrict( role = 'admin' )
	   */
	  
	  public function restrictedMethod() {

	  		 return 'restrictedMethod';
	  }

	  /**
	   * Secure method. Only logged in users can invoke
	   * this method.
	   * 
	   * @return The string 'secureMethod'
	   * #@LoggedIn
	   */
	  public function secureMethod() {

	  		 return 'secureMethod';
	  }
}
?>