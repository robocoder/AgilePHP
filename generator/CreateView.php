<?php

require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';
require_once 'util' . DIRECTORY_SEPARATOR . 'jsmin-1.1.1.php';

class CreateView extends AgilePHPGen {

	  /**
	   * Displays a list of template views from AgilePHP/generator/templates/views
	   * that when selected, is copied to the current web application project directory.
	   *  
	   * @return void
	   */
	  public function testCreateController() {

	  		 echo 'Please select from the following views:' . PHP_EOL . PHP_EOL;

	  		 $views = array();

	  		 $i = 0;
	  		 $it = new RecursiveDirectoryIterator( '.' . DIRECTORY_SEPARATOR . 'templates' .
	  		 			DIRECTORY_SEPARATOR . 'views' );
		 	 foreach( new RecursiveIteratorIterator( $it ) as $file ) {

		   	      if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

		   	      	  $file = str_replace( '.' . DIRECTORY_SEPARATOR . 'templates' .
		   	      	  			DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR, '', $file );
		   	      	  if( substr( $file, 0, 4 ) == '.svn' ) continue;

		   	      	  $i++;
			 		  $views[$i] = $file;
			      }
		 	 }
		 	 foreach( $views as $index => $name )
		 	 		echo "[$index] $name" . PHP_EOL;

		 	 $input = $this->prompt( '' );

		 	 if( !array_key_exists( $input, $views ) )
		 	 	 PHPUnit_Framework_Assert::fail( 'Invalid selection!' );

		 	 $view = $views[$input];
		 	 copy( '.' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'views' . 
		 	 			 DIRECTORY_SEPARATOR . $view, $this->getCache()->getProjectRoot() . 
		 	 			 DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $view );
		 	 			 
		 	 $this->fixLineBreaks( $this->getCache()->getProjectRoot() . 
		 	 			 DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $view );
	  }
}
?>