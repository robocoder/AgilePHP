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
 * @package com.makeabyte.agilephp.generator.util
 */

/**
 * Base AgilePHP generator class responsible for maintaining configuration state
 * for a project. Note, the generator package will only work on one project at
 * a time. If you want to use the generator package on multiple files, simply copy
 * the entire generator folder into your project. The generator will be replaced
 * with the AgilePHP IDE once it has been completed. This is just here to hold us over.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.generator.util
 * @version 0.1a
 */

ini_set( 'display_errors', '1' );
error_reporting( E_ALL );

require_once 'PHPUnit' . DIRECTORY_SEPARATOR . 'Framework.php';

class AgilePHPGen extends PHPUnit_Framework_TestCase {

	  private $cacheFile = '.agilephp-gen_cache';
	  private $cache;

	  public function __construct() {

	  		 $this->cache = $this->getCachedData();
	  }

	  /**
	   * Returns the agilephp-gen project cache instance
	   * 
	   * @return The current ProjectCache instance
	   */
	  public function getCache() {

	  		 return $this->cache;
	  }

	  /**
	   * Sets the agilephp-gen project cache instance
	   * 
	   * @param ProjectCache $cache The project instance to cache
	   * @return void
	   */
	  public function setCache( ProjectCache $cache ) {

	  		 $this->cache = $cache;
	  }

	  /**
	   * Returns the cached ProjectCache instance
	   * 
	   * @return The unserialized cache data
	   */
	  private function getCachedData() {

	  		  if( !file_exists( $this->cacheFile ) )
	  		   	  return false;

			  $cache = null;
		      $h = fopen( $this->cacheFile, 'r' );
			  while( !feof( $h ) )
				     $cache .= fgets( $h, 1024 );
			  fclose( $h );

			  return unserialize( $cache );
	  }

	  /**
	   * Saves the specified data in a serialized cache file
	   * 
	   * @param $data The data to serialize and cache
	   * @return void
	   */
	  public function saveCache( ProjectCache $cache ) {

			 $h = fopen( $this->cacheFile, 'w' );
			 fwrite( $h, serialize( $cache ) );
			 fclose( $h );

			 $this->cache = $cache;
	  }

	  /**
	   * Deletes the cache file
	   * 
	   * @return void
	   */
	  public function clearCache() {

	  		 unlink( $this->cacheFile );
	  }

	  /**
	   * Prompts the user with a question via STDOUT and collects
	   * the response via STDIN.
	   *  
	   * @param $question The question to prompt via STDIN
	   * @return The collected response via STDIN
	   */
	  public function prompt( $question ) {

	  	     echo $question . PHP_EOL . 'AgilePHP> ';
	  		 return trim( fgets( STDIN ) );
	  }

	  /**
	   * Replace *nix line breaks with windows line breaks if building on windows.
	   * 
	   * @param String $file The fully qualified file path
	   * @return void
	   */
	  protected function fixLineBreaks( $file ) {
	  	
	  			if( substr( getcwd(), 0, 1 ) != '/' ) {

	       		    $h = fopen( $file, 'r' );
	      		    $data = '';
	      		    while( !feof( $h ) )
	      		  		  $data .= fgets( $h, 4096 );
	      		    fclose( $h );

	      		    $data = str_replace( "\n", PHP_EOL, $data );

             		$h = fopen( $file, 'w' );
			  		fwrite( $h, $data );
			  		fclose( $h );
             	}
	  }
}

class ProjectCache {

	  private $projectName;
	  private $projectHome;

	  // Boolean flags for agilephp.xml/persistence.xml configuration 
	  private $interceptors;
	  private $logging;
	  private $identity;
	  private $crypto;
	  private $session;
	  private $database;
	  private $dbhost;
	  private $dbname;
	  private $dbtype;
	  private $dbuser;
	  private $dbpass;

	  public function __construct( $projectName = null, $projectHome = null ) {

	  		 $this->projectName = $projectName;
	  		 $this->projectHome = $projectHome;
	  }

	  public function setProjectName( $projectName ) {

	  		 $this->projectName = $projectName;
	  }

	  public function getProjectName() {

	  		 return $this->projectName;
	  }

	  public function setProjectHome( $projectHome ) {

	  		 $this->projectHome = $projectHome;
	  }

	  public function getProjectHome() {

	  		 return $this->projectHome;
	  }

	  public function getProjectRoot() {

	  		 return $this->projectHome . DIRECTORY_SEPARATOR . $this->projectName;
	  }

	  public function setInterceptors( $bool ) {

	  		 $this->interceptors = $bool;
	  }

	  public function getInterceptors() {

	  		 return $this->interceptors;
	  }

	  public function setLogging( $bool ) {

	  		 $this->logging = $bool;
	  }

	  public function getLogging() {

	  		 return $this->logging;
	  }
	  
	  public function setCrypto( $bool ) {
	  	
	  		 $this->crypto = $bool;
	  }
	  
	  public function getCrypto() {

	  		 return $this->crypto;
	  }

	  public function setIdentity( $bool ) {

	  		 $this->identity = $bool;
	  }

	  public function getIdentity() {

	  		 return $this->identity;
	  }

	  public function setSession( $bool ) {

	  		 $this->session = $bool;
	  }

	  public function getSession() {

	  		 return $this->session;
	  }

	  public function setDatabase( $database ) {

	  		 $this->database = $database;
	  }

	  public function getDatabase() {

	  		 return $this->database;
	  }

	  public function setDBHost( $hostname ) {

	  		 $this->dbhost = $hostname;
	  }

	  public function getDBHost() {

	  		 return $this->dbhost;
	  }
	  
	  public function setDBType( $type ) {
	  	
	  		 $this->dbtype = $type;
	  }
	  
	  public function getDBType() {
	  	
	  		 return $this->dbtype;
	  }
	  
	  public function setDBName( $dbname ) {
	  	
	  		 $this->dbname = $dbname;
	  }
	  
	  public function getDBName() {
	  	
	  		 return $this->dbname;
	  }
	  
	  public function setDBUser( $user ) {
	  	
	  		 $this->dbuser = $user;
	  }
	  
	  public function getDBUser() {
	  	
	  		 return $this->dbuser;
	  }
	  
	  public function setDBPass( $pass ) {
	  	
	  		 $this->dbpass = $pass;
	  }
	  
	  public function getDBPass() {
	  	
	  		 return $this->dbpass;
	  }
}
?>