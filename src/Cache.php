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
 * @package com.makeabyte.agilephp
 */

/**
 * Allows caching dynamic data that doesn't require real-time rendering.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * <code>
 * class MyClass {
 * 
 * #@Cache
 * public function index() {
 * 
 * 		  // A call that might perform some process intensive operation
 * 		  // or a database call thats only needed once (like building
 * 		  // a CMS that looks up form input types to build a contact page, etc,
 * 		  // and once the site is live they dont change)
 * }
 * 
 * #@Cache( minutes = 5 )
 * public function index() {
 * 
 * 		  // A call that might perform some processing intensive action
 * 		  // or a database call thats not really needed for every page request
 * }
 * }
 * </code>
 */
#@Interceptor
class Cache {

	  /**
	   * Cache annotation argument containing the number of minutes to cache the intercepted content.
	   * Defaults to 0 (never expires).
	   *  
	   * @var String The number of minutes to store the cached content
	   */
	  public $minutes = 0;

	  // Cache file
	  private $file;

	  private $mode;
	  
	  /**
	   * Handles serving up cached content vs real time content.
	   * 
	   * @param InvocationContext $ic The invocation context instance responsible for the interception
	   * @return void
	   */
	  #@AroundInvoke
	  public function process( InvocationContext $ic ) {

	  		 // Build directory and create it if it doesn't exist
	  		 $dir = '.cache';
	  		 if( !file_exists( $dir ) ) mkdir( $dir );

	  		 // Build file path
	  		 $name = get_class( $ic->getTarget() );
	  		 $this->file = $dir . DIRECTORY_SEPARATOR . $name. '_' . $ic->getMethod();

	  		 // The requested content needs to be served real-time and cached
	  		 if( !file_exists( $this->file ) )
	  		 	 return $this->serveAndCache( $ic );

	  		 if( !$this->minutes )

  		 		 // Cache never expires
		 		 return $this->serveFromCache();

		 	 else if( $this->minutes > 0 ) { // Serve from cache if the file is less than $this->minutes old

  		 			// Convert from seconds to minutes
  		 			$this->minutes = $this->minutes * 60;

  		 			if( time() - $this->minutes < filemtime( $this->file ) )
	  		 			return $this->serveFromCache();
	  		 }

	  		 return $this->serveAndCache( $ic );
	  }

	  /**
	   * Renders a real-time request and caches the output.
	   * 
	   * @return void
	   */
	  private function serveAndCache( InvocationContext $ic ) {

        	  ob_start();

        	  $clsName = get_class( $ic->getTarget() );
        	  $o = new $clsName();

        	  $class = new ReflectionClass( $o );
        	  $m = $class->getMethod( $ic->getMethod() );
        	  $return = $ic->getParameters() ? $m->invokeArgs( $o, $ic->getParameters() ) : $m->invoke( $o );

        	  if( $return ) {

        	  	  $this->mode = 1; // return
        	  	  $data = $return;
        	  }
        	  else {
        	     $data = ob_get_contents();
        	  	 ob_end_flush();
        	  }

        	  $h = fopen( $this->file, 'w' );
        	  fwrite( $h, $data );
			  fclose( $h );

			  return $data;

	   	      Log::debug( '#@Cache::serveAndCache Cached ' . $this->file );
	  }

	  /**
	   * Serves up cached content with a prefixed HTML comment indicating when the file was cached.
	   * 
	   * @return void
	   */
	  private function serveFromCache() {

	  		 $data = '<!-- Cached ' . date( 'c', filemtime( $this->file ) ) . "-->\n";
	  		 $data .= file_get_contents( $this->file );

	  		 return $data;
	  }
}
?>