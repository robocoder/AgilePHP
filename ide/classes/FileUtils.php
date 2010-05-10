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
 * @package com.makeabyte.agilephp.ide.classes
 */

/**
 * File system utilities class. Recursive copy and delete.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.ide.classes
 */
class FileUtils {

	  /**
	   * Performs a recursive delete
	   * 
	   * @param $src The tree source path to delete (colons substitutes for /)
	   * @return void
	   * @throws AgilePHP_Exception
	   */
	  public static function delete( $src ) {

	  		 Logger::getInstance()->debug( 'FileUtils::delete Performing recursive delete on source \'' . $src . '\'.' );

	  		 $dir = opendir( $src );
			 while( false !== ( $file = readdir( $dir ) ) ) {

			     	if( $file != '.' && $file != '..') {

			            if ( is_dir( $src . '/' . $file) )
			                FileUtils::delete( $src . '/' . $file );
			            else {
			                
			            	if( !unlink( $src . '/' . $file ) ) {
			            		
			            		Logger::getInstance()->debug( 'Failed to delete file ' . $src . '/' . $file );
			                	throw new AgilePHP_Exception( 'Could not delete file ' . $src . '/' . $file );
			            	}
			            }
			        }
			 }
			 return rmdir( $src );
	  }

	  /**
	   * Performs recursive copy
	   * 
	   * @param $src The source to copy
	   * @param $dst The destination
	   * @return void
	   */
	  public static function copy( $src, $dst ) {

		     $dir = opendir( $src );
			 mkdir( $dst );
			 while( false !== ( $file = readdir( $dir ) ) ) {

			      	if( $file != '.' && $file != '..' && substr( $file, 0, 4 ) != '.svn' ) {

			            if( is_dir( $src . DIRECTORY_SEPARATOR . $file ) )

			             	FileUtils::copy( $src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file );
			            else {

			             	copy( $src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file );
			            }
			        }
			 }
			 closedir( $dir );
	  }
}
?>