<?php

class MathTest {

	  /**
	   * The data type specified in this doc block is used by the
	   * #@WSDL interceptor to set the XSD data type for this property.
	   * 
	   * @var int This is an xsd:int
	   */
	  private $a;

	  /**
	   * @var int
	   */
	  private $b;

	  public function setA( $a ) {
	  	
	  		 $this->a = $a;
	  }
	  
	  public function getA() {
	  		
	  		 return $this->a;
	  }
	  
	  public function setB( $b ) {
	  		 
	  		 $this->b = $b;
	  }
	  
	  public function getB() {
	  	
	  		 return $this->b;
	  }

	  /**
	   * Adds two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The number to add to the base
	   * @return int The sum
	   */
	  public function add( $a, $b ) {

	  		 return $a + $b;
	  }

	  /**
	   * Subtracts two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The number to subtract from the base
	   * @return int The difference
	   */
	  public function subtract( $a, $b ) {
	  	
	  		 return $a - $b;
	  }

	  /**
	   * Multiplies two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The number to multiply to the base
	   * @return int The product
	   */
	  public function multiply( $a, $b ) {
	  	
	  		 return $a * $b;
	  }

	  /**
	   * Divides two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The divisor of the base
	   * @return int The quotient
	   */
	  public function divide( $a, $b ) {
	  	
	  		 return $a / $b;
	  }
}
?>