<?php

require_once 'util/AgilePHPGen.php';
require_once 'util/jsmin-1.1.1.php';

class BuildProject extends AgilePHPGen {

	  public function testBuildProject() {

	  		 // Minify AgilePHP client side JavaScript
	  	     $root = $this->getCache()->getProjectRoot();
	  	     $file = $root . '/AgilePHP/AgilePHP.js';
	  	     $data = JSMin::minify( file_get_contents( $file ) );
	  	     $h = fopen( $file, 'w' );
	  	     if( !fwrite( $h, $data ) )
	  	     	 PHPUnit_Framework_Assert::fail( 'Error saving AgilePHP minified source' );
	  	     fclose( $h );
	  }
}
?>