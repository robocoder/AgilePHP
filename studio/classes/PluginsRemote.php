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
 * @package com.makeabyte.agilephp.studio.classes
 */

/**
 * Remoting class responsible for server side processing of plugins.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.classes
 */
/**
 * "Remote" class responsbile for answering client side requests in regards to plugins 
 */
class PluginsRemote {

	  #@RemoteMethod
 	  public function getPlugins() {

	  		 $plugins = array();
	  		 $path = AgilePHP::getWebRoot() . DIRECTORY_SEPARATOR . 'view' .
	  		 		 DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'plugins';

	  		 $h = opendir( $path );
			 while( $file = readdir( $h ) ) {

			 	if( $file != '.' && $file != '..' ) {

			 		$pieces = explode( '.', $file );
			 		if( strtolower( array_pop( $pieces ) ) == 'js' ) {

			 			$o = new stdClass;
			 			$o->path = AgilePHP::getDocumentRoot() . '/view/js/plugins/' . $file;
			 			array_push( $plugins, $o );
			 		}
			 	}
			 }
			 closedir( $h );

			 return $plugins;
	  }
}
?>