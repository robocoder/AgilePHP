<?php

class BaseTest extends PHPUnit_Framework_TestCase {

	  private $agilephp;

	  public function __construct() {

	  	      $this->agilephp = AgilePHP::getFramework();
	  	      $this->agilephp->setDisplayPhpErrors( true );
      	      $this->agilephp->setDefaultTimezone( 'America/New_York' );
	  }

	  public function testAgilePHPNotNull() {

	  	     PHPUnit_Framework_Assert::assertNotNull( $this->agilephp, "Failed to create an instance of the AgilePHP framework." );
	  }
}
?>