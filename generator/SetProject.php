<?php

require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';

class SetProject extends AgilePHPGen {

	  /**
	   * Sets the generator to an existing application directory.
	   *  
	   * @return void
	   */
	  public function test_createProject() {

  		 	 $cache = new ProjectCache();

  		 	 // Provide agilephp-gen as the default directory
  		 	 $pieces = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );
  		 	 array_pop( $pieces );
  		 	 $defaultDir = implode( DIRECTORY_SEPARATOR, $pieces );

			 $input = $this->prompt( 'Enter project home directory: (' . $defaultDir . ')' );
	  		 $projectHome = $input ? $input : $defaultDir;
	  		 if( !file_exists( $projectHome ) ) {

  		 	 	 PHPUnit_Framework_Assert::fail( 'Project parent/home directory does not exist at \'' . $projectHome . '\'.' );
	  		 	 return;
	  		 }
	  		 $cache->setProjectHome( $projectHome );
	  		 
	  		 $projectName = $this->prompt( 'Enter project name:' );
	  		 if( !file_exists( $projectHome . DIRECTORY_SEPARATOR . $projectName ) ) {

		  		 if( !mkdir( $projectHome . DIRECTORY_SEPARATOR . $projectName ) ) {

		  		 	 PHPUnit_Framework_Assert::fail( 'Failed to create project directory at \'' . 
		  		 	 			$projectHome . DIRECTORY_SEPARATOR . $projectName . '\'.' );
			  		 return;
			  	 }
		  	 }
		  	 $cache->setProjectName( $projectName );

		  	 $this->saveCache( $cache );
	  }
}
?>