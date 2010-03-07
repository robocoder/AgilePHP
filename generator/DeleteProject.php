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
 * Completely destroys a project by deleting it from the file system. This simply
 * applies to the files only - this does not do anything with data sources. Use
 * DropDatabase to perform that operation.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.generator
 * @version 0.1a
 */
require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';

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

	  		 echo 'Deleting project: ' . $this->getCache()->getProjectName() . PHP_EOL;
	  		 echo 'Project home: ' . $this->getCache()->getProjectHome() . PHP_EOL;

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

			            if ( is_dir( $src . DIRECTORY_SEPARATOR . $file) )
			                $this->recursiveDelete( $src . DIRECTORY_SEPARATOR . $file );
			            else {
			                
			            	if( !unlink( $src . DIRECTORY_SEPARATOR . $file ) )
			                	PHPUnit_Framework_Assert::fail( 'Could not delete file ' . $src . DIRECTORY_SEPARATOR . $file );
			            }
			        }
			 }
			 return rmdir( $src );
	  }
}
?>