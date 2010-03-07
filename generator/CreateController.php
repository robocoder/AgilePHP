<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp.generator
 */

/**
 * Creates a new controller by copying from the generator/templates/controllers directory.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.generator
 * @version 0.1a
 */
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