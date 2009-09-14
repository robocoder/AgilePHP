<?php

require_once 'util/AgilePHPGen.php';
require_once 'util/jsmin-1.1.1.php';

class CreateController extends AgilePHPGen {

	  /**
	   * Displays a list of template controllers from AgilePHP/generator/templates/controllers
	   * that when selected, is copied to the current web application project directory.
	   *  
	   * @return void
	   */
	  public function testCreateController() {

	  		 echo "Please select from the following controllers:\n\n";

			 $i=0;
	  		 $it = new RecursiveDirectoryIterator( './templates/controllers' );
		 	 foreach( new RecursiveIteratorIterator( $it ) as $file ) {

		   	      if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

		   	      	  $file = str_replace( './templates/controllers/', '', $file );
			 		  $file = str_replace( '.php', '', $file );

			 		  if( substr( $file, 0, 4 ) == '.svn' ) continue;

			 		  $i++;
			 		  $controllers[$i] = $file;
			      }
		 	 }

		 	 foreach( $controllers as $index => $name )
		 	 		echo "[$index] $name\n";

		 	 echo "\nAgilePHP> ";

		 	 $input = trim( fgets( STDIN ) );

		 	 if( !array_key_exists( $input, $controllers ) )
		 	 	 PHPUnit_Framework_Assert::fail( 'Invalid selection!' );

		 	 $controller = $controllers[$input] . '.php';
		 	 copy( './templates/controllers/' . $controller, $this->getCache()->getProjectRoot() . '/control/' . $controller );
	  }
}
?>