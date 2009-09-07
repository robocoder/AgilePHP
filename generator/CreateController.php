<?php

require_once 'util/AgilePHPGen.php';
require_once 'util/jsmin-1.1.1.php';

class CreateController extends AgilePHPGen {

	  public function testCreateController() {

	  		 echo "Please select from the following controllers:\n\n";

	  		 $controllers = array();

	  		 $i = 0;
	  		 $it = new RecursiveDirectoryIterator( './templates/controllers' );
		 	 foreach( new RecursiveIteratorIterator( $it ) as $file ) {

		   	      if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

		   	      	  $i++;

		   	      	  $file = str_replace( './templates/controllers/', '', $file );
			 		  $file = str_replace( '.php', '', $file );
			 		  
			 		  if( substr( $file, -4 ) == '.svn' ) continue;
			 		  
			 		  $controllers[$i] = $file;
			 		  echo "[$i] $file\n";
			      }
		 	 }
		 	 echo "\nAgilePHP> ";

		 	 $input = trim( fgets( STDIN ) );

		 	 if( !array_key_exists( $input, $controllers ) )
		 	 	 PHPUnit_Framework_Assert::fail( 'Invalid selection!' );

		 	 $controller = $controllers[$input] . '.php';
		 	 copy( './templates/controllers/' . $controller, $this->getCache()->getProjectRoot() . '/control/' . $controller );
	  }
}
?>