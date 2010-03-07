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
 * Sets the current working project. This is useful if you have an existing project
 * you want to set the generator package to use.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.generator
 * @version 0.1a
 */
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