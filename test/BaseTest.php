<?php

ini_set( 'include_path', ini_get('include_path') . PATH_SEPARATOR . '/usr/share/php/PHPUnit' );

require_once 'PHPUnit/Framework.php';
require_once '../src/AgilePHP.php';

class BaseTest extends PHPUnit_Framework_TestCase {

	  private $agilephp;

	  public function __construct() {

	  	      $this->agilephp = AgilePHP::getFramework();
	  	      $this->agilephp->setDisplayPhpErrors( true );
      	      $this->agilephp->setWebRoot( '/home/jhahn/Apps/eclipse-galileo/workspace/AgilePHP/test' );
      	      $this->agilephp->setFrameworkRoot( '/home/jhahn/Apps/eclipse-galileo/workspace/AgilePHP/src' );
      	      $this->agilephp->setDefaultTimezone( 'America/New_York' );
	  }

	  public function testAgilePHPNotNull() {

	  	     PHPUnit_Framework_Assert::assertNotNull( $this->agilephp, "Failed to create an instance of the AgilePHP framework." );
	  }	 
}
?>