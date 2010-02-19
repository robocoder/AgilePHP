<?php

require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';
require_once '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'AgilePHP.php';
require_once '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PersistenceManager.php';

class DropDatabase extends AgilePHPGen {

	  /**
	   * Drops/destroys the database defined in the current web application
	   * persistence.xml file.
	   * 
	   * @return void
	   */
	  public function testDropDatabase() {

	  		 $agilephp = AgilePHP::getFramework();
	  	     $agilephp->setDisplayPhpErrors( true );
      	     $agilephp->setWebRoot( $this->getCache()->getProjectRoot() );
      	     $agilephp->setFrameworkRoot( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'AgilePHP' );
      	     $agilephp->setDefaultTimezone( 'America/New_York' );

	  		 $pm = new PersistenceManager();
	  		 $pm->drop();
	  }
}
?>