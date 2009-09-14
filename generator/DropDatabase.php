<?php

require_once 'util/AgilePHPGen.php';
require_once '../src/AgilePHP.php';

class DropDatabase extends AgilePHPGen {

	  /**
	   * Drops/destroys the database defined in the current web application
	   * persistence.xml file.
	   * 
	   * @return void
	   */
	  public function testCreateDatabase() {

	  		 $agilephp = AgilePHP::getFramework();
	  	     $agilephp->setDisplayPhpErrors( true );
      	     $agilephp->setWebRoot( $this->getCache()->getProjectRoot() );
      	     $agilephp->setFrameworkRoot( $this->getCache()->getProjectRoot() . '/AgilePHP' );
      	     $agilephp->setDefaultTimezone( 'America/New_York' );

	  		 $pm = new PersistenceManager();
	  		 $pm->drop();
	  }
}
?>