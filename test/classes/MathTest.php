<?php

class MathTest {

	  /** @var int */
	  public $a;

	  /** @var int */
	  public $b;

	  /**
	   * Mutator for $a property.
	   * 
	   * @param int $a
	   * @return void
	   */
	  public function setA( $a ) {
	  	
	  		 $this->a = $a;
	  }
	  
	  /**
	   * Accessor for $a property
	   * 
	   * @return int
	   */
	  public function getA() {
	  		
	  		 return $this->a;
	  }
	  
	  /**
	   * Mutator for $b property
	   * 
	   * @param int $b
	   * @return void
	   */
	  public function setB( $b ) {
	  		 
	  		 $this->b = $b;
	  }
	  
	  /**
	   * Accessor for $b property
	   * 
	   * @return int
	   */
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