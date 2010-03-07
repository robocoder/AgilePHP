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
 * Builds a project by minifying all javascript and replacing AgilePHP source
 * code line breaks with the line breaks of the current operating system.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.generator
 * @version 0.1a
 */
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