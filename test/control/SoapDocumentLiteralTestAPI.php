<?php

/**
 * Test class used to mimic an API exposing some application logic via SOAP.
 * 
 * NOTE: The AgilePHP #@WebMethod annotation authorizes a method for inclusion during WSDL generation.
 *
 * 		 The AgilePHP #@WSDL interceptor uses the data types specified in 
 *	     the PHP-doc comment blocks (specifically the @param and @return annotations) during
 *	     WSDL generation. If these types are not present, the #@WSDL generator will use xsd:anyType.
 */

#@WebService( serviceName = 'TestAPIService', targetNamespace = 'http://localhost/test/index.php/SoapDocumentLiteralTestAPI' )
class SoapDocumentLiteralTestAPI extends SOAPService {

	  /**
	   * The #@In interceptor performs Dependancy Injection. Will be a new
	   * instance of MathTest at runtime.
	   */
	  public $MathTest;

	  public function __construct() {

	  		 $this->MathTest = new MathTest();
	  }

	  /**
	   * The #@WSDL interceptor will handle generating WSDL document for this web service class
	   * based on annotations and PHP-doc comments that describe parameter data types and return
	   * types.
	   */
	  #@WSDL
	  public function wsdl() {}

	  /**
	   * Tests the TestAPI web service by outputting the string 'TestAPI works!'.
	   * 
	   * @return string
	   */
	  #@WebMethod
	  public function test() {

	  		$o = new testResponse();
	  		$o->return = 'TestAPI works!';

  		    return $o;
	  }

	  /**
	   * Adds two numbers.
	   * 
	   * @param add $addRequest Client add object that holds the a and b properties
	   * @return int The sum of a and b
	   */
	  #@WebMethod
	  public function add( add $addRequest ) {

	  		 $o = new addResponse;
	  		 $o->return = $this->MathTest->add( $addRequest->a, $addRequest->b );
	  		 
	  		 return $o;
	  }

	  /**
	   * Subtracts two numbers.
	   * 
	   * @param subtract $subtractRequest Client subtract object holding the a and b properties.
	   * @return int The difference between a and b
	   */
	  #@WebMethod
	  public function subtract( subtract $subtractRequest ) {

	  		 $o = new subtractResponse;
	  		 $o->return = $this->MathTest->subtract( $subtractRequest->a, $subtractRequest->b );

	  		 return $o;
	  }

	  /**
	   * Multiplies two numbers.
	   * 
	   * @param multiple $multiplyRequest Client multiply object holding the a and b properties.
	   * @return int The product of a and b
	   */
	  #@WebMethod
	  public function multiply( multiply $multiplyRequest ) {

	  		 $o = new multiplyResponse;
	  		 $o->return = $this->MathTest->multiply( $multiplyRequest->a, $multiplyRequest->b );

	  		 return $o;
	  }

	  /**
	   * Divides two numbers.
	   * 
	   * @param divide $divideRequest Client divide object holding the a and b properties
	   * @return int The quotient of a and b
	   */
	  #@WebMethod
	  public function divide( divide $divideRequest ) {

	  		 $o = new divideResponse;
	  		 $o->return = $this->MathTest->divide( $divideRequest->a, $divideRequest->b );

	  		 return $o;
	  }

	  /**
	   * Tests complex data type 'object' parameter.
	   * 
	   * @param MathTest $MathTest An instance of MathTest
	   * @return int Returns the sum of both A and B fields in the MathTest object.
	   */
	  #@WebMethod
	  public function objectParameterTest( MathTest $MathTest ) {

	  		 $o = new objectParameterTestResponse();
	  		 $o->return = ($MathTest->MathTest->a + $MathTest->MathTest->b);

	  		 return $o;
	  }

	  /**
	   * Tests complex data type array parameter.
	   * 
	   * @param MathTest[] $MathTests An array of MathTest instances
	   * @return MathTest[] Returns the same array ob MathTest objects that were passed in.
	   */
	  #@WebMethod
	  public function arrayOfObjectsTest( array $MathTests ) {

	  		 $o = new arrayOfObjectsTestResponse;
	  		 $o->return = $MathTests->MathTests;

	  		 return $o;
	  }

	  /**
	   * Tests the ability to receive and return a simple string array.
	   * 
	   * @param string[] $strings An array of strings
	   * @return string[] The same strings that were passed in
	   */
	  #@WebMethod
	  public function arrayStringTest( array $strings ) {

	  	     $o = new arrayStringTestResponse;
	  	     $o->return = $strings->strings;

	  	     return $o;
	  }

	  /**
	   * Tests the ability to receive and return a simple string array.
	   * 
	   * @param string[][] $strings A multi-dimensional array of strings
	   * @return string[][] The same multi-dimensional array that was passed in
	   */
	  #@WebMethod
	  public function arrayOfArraysTest( array $strings ) {

	  		 Logger::getInstance()->debug( $strings );

	  		 $o = new arrayOfArraysTestResponse;
	  		 $o->return = $strings->strings->strings;

	  		 return $o;
	  }

	  /**
	   * Tests complex data type array of objects parameter.
	   * 
	   * @param MathTest[] $MathTests An array of MathTest instances
	   * @return int Adds A and B for each array and returns the sum of all MathTest objects
	   */
	  #@WebMethod
	  public function arrayAddTest( array $MathTests ) {

			 $sum = 0;
	  		 foreach( $MathTests->MathTests as $MathTest )
	  		 	$sum += ($MathTest->a + $MathTest->b);

	  		 $o = new arrayAddTestResponse;
	  		 $o->return = $sum;

	  		 return $o;
	  }
}
class test {
}

class testResponse {
  /** @var string */
  public $return;
}

class add {
  /** @var int */
  public $a;
  /** @var int */
  public $b;
}

class addResponse {
  /** @var int */
  public $return;
}

class subtract {
  /** @var int */
  public $a;
  /** @var int */
  public $b;
}

class subtractResponse {
  /** @var int */
  public $return;
}

class multiply {
  /** @var int */
  public $a;
  /** @var int */
  public $b;
}

class multiplyResponse {
  /** @var int */
  public $return;
}

class divide {
  /** @var int */
  public $a;
  /** @var int */
  public $b;
}

class divideResponse {
  /** @var int */
  public $return;
}

class objectParameterTest {
  /** @var MathTest */
  public $MathTest;
}

class objectParameterTestResponse {
  /** @var int */
  public $return;
}

class arrayOfObjectsTest {
  /** @var MathTest[]  */
  public $MathTests;
}

class arrayOfObjectsTestResponse {
  /** @var MathTest[] */
  public $return;
}

class arrayStringTest {
  /** @var string[] */
  public $strings;
}

class arrayStringTestResponse {
  /** @var string[] */
  public $return;
}

class arrayOfArraysTest {
  /** @var string[][] */
  public $strings;
}

class arrayOfArraysTestResponse {
  /** @var string[][] */
  public $return;
}

class arrayAddTest {
  /** @var MathTest[] */
  public $MathTests;
}

class arrayAddTestResponse {
  /** @var int */
  public $return;
}
?>