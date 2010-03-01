<?php

/**
 * Test class used to mimic an API exposing some application logic via SOAP.
 * 
 * NOTE: The AgilePHP #@WebMethod annotation authorizes a method for inclusion during WSDL generation.
 * 	     
 * 		 The AgilePHP #@WSDL interceptor uses the data types specified in 
 *	     the PHP-doc comment blocks (specifically the @param and @return annotations) during
 *	     WSDL generation.
 */

#@WebService( serviceName = 'TestAPIService', targetNamespace = 'http://localhost/test/index.php/SoapRpcLiteralTestAPI' )
#@SOAPBinding( style = SOAPStyle::RPC, use = SOAPStyle::LITERAL )
class SoapRpcLiteralTestAPI extends SOAPService {

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

	  		 return 'TestAPI works!';
	  }

	  /**
	   * Adds two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The number to add to the base
	   * @return int The sum
	   */
	  #@WebMethod
	  public function add( $a, $b ) {

	  		 return $this->MathTest->add( $a, $b );
	  }

	  /**
	   * Subtracts two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The number to subtract from the base
	   * @return int The difference
	   */
	  #@WebMethod
	  public function subtract( $a, $b ) {

	  		 return $this->MathTest->subtract( $a, $b );
	  }

	  /**
	   * Multiplies two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The number to multiply to the base
	   * @return int The product
	   */
	  #@WebMethod
	  public function multiply( $a, $b ) {

	  		 return $this->MathTest->multiply( $a, $b );
	  }

	  /**
	   * Divides two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The divisor of the base
	   * @return int The quotient
	   */
	  #@WebMethod
	  public function divide( $a, $b ) {

	  		 return $this->MathTest->divide( $a, $b );
	  }

	  /**
	   * Tests complex data type 'object' parameter.
	   * 
	   * @param MathTest $MathTest An instance of MathTest
	   * @return int Returns the sum of both A and B fields in the MathTest object.
	   */
	  #@WebMethod
	  public function objectParameterTest( MathTest $MathTest ) {

	  		 return ($MathTest->a + $MathTest->b);
	  }

	  /**
	   * Tests complex data type array parameter.
	   * 
	   * @param MathTest[] $MathTests An array of MathTest instances
	   * @return MathTest[] Returns the same array that was passed in.
	   */
	  #@WebMethod
	  public function arrayOfObjectsTest( array $MathTests ) {

	  		 return $MathTests;
	  }

	  /**
	   * Tests the ability to receive and return a simple string array.
	   * 
	   * @param string[] $strings An array of strings
	   * @return string[] The same strings that were passed in
	   */
	  #@WebMethod
	  public function arrayStringTest( array $strings ) {

	  		 return $strings;
	  }

	  /**
	   * Tests the ability to receive and return a simple string array.
	   * 
	   * @param string[][] $strings A multi-dimensional array of strings
	   * @return string[][] The same multi-dimensional array that was passed in
	   */
	  #@WebMethod
	  public function arrayOfArraysTest( array $strings ) {

	  		 return $strings;
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

	  		 return $sum;
	  }
}
?>