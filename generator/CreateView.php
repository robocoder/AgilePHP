<?php

require_once 'util/AgilePHPGen.php';
require_once 'util/jsmin-1.1.1.php';

class CreateView extends AgilePHPGen {

	  /**
	   * Displays a list of template views from AgilePHP/generator/templates/views
	   * that when selected, is copied to the current web application project directory.
	   *  
	   * @return void
	   */
	  public function testCreateController() {

	  		 echo "Please select from the following views:\n\n";

	  		 $views = array();

	  		 $i = 0;
	  		 $it = new RecursiveDirectoryIterator( './templates/views' );
		 	 foreach( new RecursiveIteratorIterator( $it ) as $file ) {

		   	      if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

		   	      	  $file = str_replace( './templates/views/', '', $file );
		   	      	  if( substr( $file, 0, 4 ) == '.svn' ) continue;

		   	      	  $i++;
			 		  $views[$i] = $file;
			      }
		 	 }
		 	 foreach( $views as $index => $name )
		 	 		echo "[$index] $name\n";
		 	 
		 	 $input = $this->prompt( '' );

		 	 if( !array_key_exists( $input, $views ) )
		 	 	 PHPUnit_Framework_Assert::fail( 'Invalid selection!' );

		 	 $view = $views[$input];
		 	 copy( './templates/views/' . $view, $this->getCache()->getProjectRoot() . '/view/' . $view );
	  }
}
?>