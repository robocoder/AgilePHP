<?php

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

	  	     echo "$question\nAgilePHP> ";
	  		 return trim( fgets( STDIN ) );
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