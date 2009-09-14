<?php

require_once 'util/AgilePHPGen.php';

class DeleteProject extends AgilePHPGen {

	  public function __construct() {
	  	
	  		 parent::__construct( false );
	  }

	  /**
	   * Deletes the project found in the .agilephp-gen_cache file
	   * 
	   * @return void
	   */
	  public function test_deleteProject() {

	  		 if( !$this->getCache() )
	  		 	 PHPUnit_Framework_Assert::fail( 'No project found in cache' );

	  		 if( !$this->getCache()->getProjectName() )
	  		 	 PHPUnit_Framework_Assert::fail( 'Project name not found in cache' );
	  		 	 
	  		 if( !$this->getCache()->getProjectHome() )
	  		 	 PHPUnit_Framework_Assert::fail( 'Project home not found in cache' );

	  		 echo 'Deleting project: ' . $this->getCache()->getProjectName() . "\n";
	  		 echo 'Project home: ' . $this->getCache()->getProjectHome() . "\n";

	  		 $this->recursiveDelete( $this->getCache()->getProjectRoot() );
	  		 unlink( '.agilephp-gen_cache' );
	  }

	  /**
	   * Performs a recursive file delete
	   * 
	   * @param $src The source directory to delete
	   * @return True if successful or false on failure
	   * @throws PHPUnit_Framework_Assert::fail
	   */
	  public function recursiveDelete( $src ) {

		     $dir = opendir( $src );
			 while( false !== ( $file = readdir( $dir ) ) ) {

			     	if( $file != '.' && $file != '..') {

			            if ( is_dir( $src . '/' . $file) )
			                $this->recursiveDelete( $src . '/' . $file );
			            else {
			                
			            	if( !unlink( $src . '/' . $file ) )
			                	PHPUnit_Framework_Assert::fail( 'Could not delete file ' . $src . '/' . $file );
			            }
			        }
			 }
			 return rmdir( $src );
	  }
}
?>