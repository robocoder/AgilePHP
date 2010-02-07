<?php

require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';
require_once 'util' . DIRECTORY_SEPARATOR . 'jsmin-1.1.1.php';

class CreateController extends AgilePHPGen {

	  /**
	   * Displays a list of template controllers from AgilePHP/generator/templates/controllers
	   * that when selected, is copied to the current web application project directory.
	   *  
	   * @return void
	   */
	  public function testCreateController() {

	  		 echo 'Please select from the following controllers:' . PHP_EOL . PHP_EOL;

			 $i=0;
	  		 $it = new RecursiveDirectoryIterator( '.' . DIRECTORY_SEPARATOR . 'templates' .
	  		 		DIRECTORY_SEPARATOR . 'controllers' );
		 	 foreach( new RecursiveIteratorIterator( $it ) as $file ) {

		   	      if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

		   	      	  $file = str_replace( '.' . DIRECTORY_SEPARATOR . 'templates' .
		   	      	  			DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR, '', $file );
			 		  $file = str_replace( '.php', '', $file );

			 		  if( substr( $file, 0, 4 ) == '.svn' ) continue;

			 		  $i++;
			 		  $controllers[$i] = $file;
			      }
		 	 }

		 	 foreach( $controllers as $index => $name )
		 	 		echo "[$index] $name" . PHP_EOL;

		 	 echo PHP_EOL . 'AgilePHP> ';

		 	 $input = trim( fgets( STDIN ) );

		 	 if( !array_key_exists( $input, $controllers ) )
		 	 	 PHPUnit_Framework_Assert::fail( 'Invalid selection!' );

		 	 $controller = $controllers[$input] . '.php';
		 	 copy( '.' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'controllers' .
		 	 	   DIRECTORY_SEPARATOR . $controller, $this->getCache()->getProjectRoot() . 
		 	 	   DIRECTORY_SEPARATOR . 'control' . DIRECTORY_SEPARATOR . $controller );

		 	 $this->fixLineBreaks( $this->getCache()->getProjectRoot() . 
		 	 	   DIRECTORY_SEPARATOR . 'control' . DIRECTORY_SEPARATOR . $controller );
	  }
}
?>