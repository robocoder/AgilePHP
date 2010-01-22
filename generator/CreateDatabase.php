<?php

require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';
require_once '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'AgilePHP.php';

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
      	     $agilephp->setFrameworkRoot( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'AgilePHP' );

	  		 $pm = new PersistenceManager();
	  		 $pm->create();
	  }
}
?>