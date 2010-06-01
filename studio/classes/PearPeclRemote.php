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
 * Responsible for PEAR and PECL integration
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.classes
 */
class PearPeclRemote {

	  private $pear;
	  private $pecl;

	  public function __construct() {

	  		 $config = new Config();

 	  		 $config->setName( 'pecl_bin' );
 	  		 $this->pecl = $config->getValue();

 	  		 $config->setName( 'pear_bin' );
 	  		 $this->pear = $config->getValue();
	  }

	  #@RemoteMethod
	  public function getInstalledPearExts() {

	  		 $cmd = $this->pear . ' l';

			 Log::debug( $cmd );

			 ob_start();
			 passthru( $cmd, $result );
			 $data = ob_get_contents();
			 ob_end_clean();

			 $packages = explode( "\n", $data );

			 if( !isset( $packages[2] ) ) return false;

			 // headers that arent needed
			 array_shift( $packages );
			 array_shift( $packages );
			 array_shift( $packages );

			 Log::debug( $packages );

			 $o = new stdClass;
			 $o->exts = array();

			 for( $i=0; $i<count( $packages ); $i++ ) {

			 	  if( !$packages[$i] ) continue;

			 	  $package = array();
			  	  $pieces = preg_split( '/\s/', $packages[$i] );
			  	  foreach( $pieces as $piece ) {
			  	  	
			  	  		if( !$piece ) continue;

			  	  		array_push( $package, $piece );
			  	  }
			  	  array_push( $o->exts, $package );
			 }

			 Log::debug( $o );
			 
			 return $o;
	  }
}
?>