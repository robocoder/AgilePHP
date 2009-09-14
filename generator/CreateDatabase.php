<?php

require_once 'util/AgilePHPGen.php';
require_once '../src/AgilePHP.php';

class CreateDatabase extends AgilePHPGen {

	  /**
	   * Creates the database schema for the current web application
	   * based on persistence.xml configuration. 
	   *  
	   * @return void
	   */
	  public function testCreateDatabase() {

	  		 $agilephp = AgilePHP::getFramework();
      	     $agilephp->setWebRoot( $this->getCache()->getProjectRoot() );
      	     $agilephp->setFrameworkRoot( $this->getCache()->getProjectRoot() . '/AgilePHP' );

	  		 $pm = new PersistenceManager();
	  		 $pm->create();
	  }
}
?>