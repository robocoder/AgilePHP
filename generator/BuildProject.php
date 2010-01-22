<?php

require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';
require_once 'util' . DIRECTORY_SEPARATOR . 'jsmin-1.1.1.php';

class BuildProject extends AgilePHPGen {

	  /**
	   * Builds the main project. Minimizes AgilePHP.js JavaScript.
	   * 
	   * @return void
	   */	  
	  public function testBuildProject() {

	  		 // Minify AgilePHP client side JavaScript
	  	     $root = $this->getCache()->getProjectRoot();
	  	     $file = $root . DIRECTORY_SEPARATOR . 'AgilePHP' . DIRECTORY_SEPARATOR . 'AgilePHP.js';
	  	     $data = JSMin::minify( file_get_contents( $file ) );
	  	     $h = fopen( $file, 'w' );
	  	     if( !fwrite( $h, $data ) )
	  	     	 PHPUnit_Framework_Assert::fail( 'Error saving AgilePHP minified source' );
	  	     fclose( $h );
	  }
}
?>