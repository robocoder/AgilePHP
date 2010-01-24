<?php

/**
 * This class is used by the CacheTest unit test.
 * 
 * @author jhahn
 */
class MockCacher {

	  private $markup;

	  public function __construct() {

	  		 $this->resetMarkup();
	  }

	  /**
	   * Outputs the value of the markup property with a 1 second cache expiry time. 
	   * 
	   */
	  #@Cache( minutes = 1 )
	  public function expires() {

	  		 echo $this->getMarkup();
	  }

	  /**
	   * Outputs the value of the markup property with a cache time of 'never expire'.
	   */
	  #@Cache
	  public function neverExpires() {

	  		 echo $this->getMarkup();
	  }

	  /**
	   * Sets the markup property used as an output value from the cached method.
	   * 
	   * @param mixed $value The value to have the cached method output
	   * @return void
	   */
	  public function setMarkup( $value ) {

	  		 $this->markup = $value;
	  }

	  /**
	   * Returns the value of the markup property.
	   * 
	   * @return mixed The value of the markup property.
	   */
	  public function getMarkup() {

	  		 return $this->markup;
	  }

	  /**
	   * Resets the markup property back to default value.
	   * 
	   * @return void
	   */
	  public function resetMarkup() {

	  		 $this->markup = "This is some default output to get cached.\n";
	  }
}
?>