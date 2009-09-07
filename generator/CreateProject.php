<?php

require_once 'util/AgilePHPGen.php';

class CreateProject extends AgilePHPGen {

	  /**
	   * Creates the default web application structure and copies the AgilePHP framework source
	   * into the project directory. Allows the invoker to pre-configure agilephp.xml and
	   * persistence.xml files.
	   *  
	   * @return void
	   */
	  public function test_createProject() {

  		 	 $cache = new ProjectCache();

  		 	 // Provide agilephp-gen as the default directory
  		 	 $pieces = explode( '/', dirname( __FILE__ ) );
  		 	 array_pop( $pieces );
  		 	 $defaultDir = implode( '/', $pieces );

			 $input = $this->prompt( 'Enter project home directory: (' . $defaultDir . ')' );
	  		 $projectHome = $input ? $input : $defaultDir;
	  		 if( !file_exists( $projectHome ) ) {

  		 	 	 PHPUnit_Framework_Assert::fail( 'Project parent/home directory does not exist at \'' . $projectHome . '\'.' );
	  		 	 return;
	  		 }
	  		 $cache->setProjectHome( $projectHome );

	  		 $projectName = $this->prompt( 'Enter project name' );
	  		 if( !file_exists( $projectHome . '/' . $projectName ) ) {

		  		 if( !mkdir( $projectHome . '/' . $projectName ) ) {

		  		 	 PHPUnit_Framework_Assert::fail( 'Failed to create project directory at \'' . $projectHome . '/' . $projectName . '\'.' );
			  		 return;
			  	 }
		  	 }
		  	 $cache->setProjectName( $projectName );

	  		 $root = $projectHome . '/' . $projectName;

	  		 $model = $root . '/model';
	  		 if( !mkdir( $model ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project models directory at \'' . $model . '\'.' );
	  		 	 
	  		 $view = $root . '/view';
	  		 if( !mkdir( $view ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project view directory at \'' . $view . '\'.' );
	  		 
	  		 $control = $root . '/control';
	  		 if( !mkdir( $control ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project controllers directory at \'' . $control . '\'.' );
	  		 
	  		 $logs = $root . '/logs';
	  		 if( !mkdir( $logs ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project logging directory at \'' . $logs . '\'.' );
	  		 
	  		 if( !chmod( $logs, 0777 ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not change permissions to 077 on project logging directory \'' . $logs . '\'.' );

	  		 $components = $root . '/components';
	  		 if( !mkdir( $components ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project components directory at \'' . $components . '\'.' );

	  		 $classes = $root . '/classes';
	  		 if( !mkdir( $classes ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project classes directory at \'' . $classes . '\'.' );

	  		 $agilephp = $root . '/AgilePHP';
	  		 $this->recursiveCopy( '../src', $agilephp );

	  		 /** AgilePHP Configuration */
	  		 $answer = $this->prompt( 'Would you like to use interceptors in your project? (Y/N)' );
	  		 $cache->setInterceptors( strtolower( $answer ) == 'y' ? true : false );

	  		 $answer = $this->prompt( 'Would you like to start your default logging level at debug? (Y/N)' );
	  		 $cache->setLogging( strtolower( $answer ) == 'y' ? true : false );

	  		 $answer = $this->prompt( 'Would you like to use the AgilePHP Identity component? (Y/N)' );
	  		 $cache->setIdentity( strtolower( $answer ) == 'y' ? true : false );

	  		 $answer = $this->prompt( 'Would you like to use the AgilePHP Crypto component to secure private information in your project? (Y/N)' );
	  		 $cache->setCrypto( strtolower( $answer ) == 'y' ? true : false );

	  		 $answer = $this->prompt( 'Would you like to use the AgilePHP SessionScope component? (Y/N)' );
	  		 $cache->setSession( strtolower( $answer ) == 'y' ? true : false );

	  		 $answer = $this->prompt( 'Will you be connecting to a database? (Y/N)' );
	  		 $cache->setDatabase( strtolower( $answer ) == 'y' ? true : false );

	  		 $this->saveCache( $cache );

	  		 if( $cache->getDatabase() == 'y' ) {

	  		 	 echo "What type of database server are you using?\nAgilePHP> ";
	  		 	 $this->showDBTypes();
	  		 	 $cache->setDBType( $this->getDBType( trim( fgets( STDIN ) ) ) );
	  		 	 $cache->setDBHost( $this->prompt( 'Database Server hostname?' ) ); 
	  		 	 $cache->setDBName( $this->prompt( 'Database name?' ) );
	  		 	 $cache->setDBUser( $this->prompt( 'Username to connect?' ) );
	  		 	 $cache->setDBPass( $this->prompt( 'Password to connect?' ) );

	  		 	 $this->saveCache( $cache );

	  		 	 $this->createPersistenceXML( $cache->getIdentity(), $cache->getSession(), $cache->getDBName(), $cache->getDBType(),
	  		 	 			$cache->getDBHost(), $cache->getDBUser(), $cache->getDBPass() );
	  		 }

	  		 $this->createAgilePhpXML( $cache->getLogging(), $cache->getInterceptors(), $cache->getIdentity(), $cache->getCrypto() );
	  		 $this->createAccessFile();
	  }

	  /**
	   * Performs recursive file copy
	   * 
	   * @param $src The source to copy
	   * @param $dst The destination
	   * @return void
	   */
	  private function recursiveCopy( $src, $dst ) {

		      $dir = opendir( $src );
			  mkdir( $dst );
			  while( false !== ( $file = readdir( $dir ) ) ) {

			      	 if( $file != '.' && $file != '..' ) {

			             if( is_dir( $src . '/' . $file ) )
			                $this->recursiveCopy( $src . '/' . $file, $dst . '/' . $file );
			            else
			                copy( $src . '/' . $file, $dst . '/' . $file );
			        }
			 }
			 closedir( $dir );
	  }

	  private function createPersistenceXML( $identity, $session, $name, $type, $hostname, $username, $password ) {

	  		  $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			  $data .= "<!DOCTYPE persistence SYSTEM \"AgilePHP/persistence/persistence.dtd\">\n";
			  $data .= "<persistence>\n";
			  $data .= "\t<database id=\"db1\" name=\"" . ($type == 'sqlite' ? $this->getCache()->getProjectRoot() . '/' . $name : $name) . "\" type=\"" . $type . "\" hostname=\"" . $hostname .
	  		  							 "\" username=\"" . $username . "\" password=\"" . $password . "\">\n\n";

			  if( $identity ) {

			  	  $data .= "\t\t<!-- AgilePHP Identity -->\n";
			  	  $data .= "\t\t<table name=\"users\" isIdentity=\"true\" display=\"Users\" model=\"User\" description=\"Actors in the application\">\n";
				  $data .= "\t\t\t<column name=\"username\" type=\"varchar\" length=\"150\" primaryKey=\"true\" required=\"true\"/>\n";
				  $data .= "\t\t\t<column name=\"password\" type=\"varchar\" length=\"255\" required=\"true\"/>\n";
				  $data .= "\t\t\t<column name=\"email\" type=\"varchar\" length=\"255\" required=\"true\"/>\n";
				  $data .= "\t\t\t<column name=\"created\" type=\"datetime\" required=\"true\"/>\n";
				  $data .= "\t\t\t<column name=\"last_login\" property=\"lastLogin\" display=\"Last Login\" type=\"datetime\"/>\n";
				  $data .= "\t\t\t<column name=\"roleId\" type=\"varchar\" length=\"25\">\n";
				  $data .= "\t\t\t\t<foreignKey name=\"FK_UserRoles\" type=\"one-to-many\" onDelete=\"SET_NULL\" onUpdate=\"CASCADE\"
							  		 table=\"roles\" column=\"name\" controller=\"RoleController\" select=\"name\"/>\n";
				  $data .= "\t\t\t</column>\n";
				  $data .= "\t\t\t<column name=\"sessionId\" type=\"varchar\" length=\"21\">\n";
				  $data .= "\t\t\t\t<foreignKey name=\"FK_UserSessions\" type=\"one-to-one\" onDelete=\"SET_NULL\" onUpdate=\"CASCADE\"
							  		 table=\"sessions\" column=\"id\" controller=\"SessionController\"/>\n";
				  $data .= "\t\t\t</column>\n";
				  $data .= "\t\t</table>\n";
				  $data .= "\t\t<table name=\"roles\" display=\"Roles\" model=\"Role\" description=\"Roles used in the application\">\n";
				  $data .= "\t\t\t<column name=\"name\" type=\"varchar\" length=\"25\" primaryKey=\"true\" required=\"true\"/>\n";
				  $data .= "\t\t\t<column name=\"description\" type=\"text\"/>\n";
				  $data .= "\t\t</table>\n";
			  }

			  if( $session ) {

			  	  $data .= "\t\t<!-- AgilePHP Session -->\n";
			  	  $data .= "\t\t<table name=\"sessions\" display=\"Session\" isSession=\"true\" model=\"Session\" description=\"User sessions\">\n";
				  $data .= "\t\t\t<column name=\"id\" type=\"varchar\" length=\"21\" primaryKey=\"true\" description=\"Unique ID\"/>\n";
				  $data .= "\t\t\t<column name=\"data\" type=\"text\" description=\"Name of recipient\"/>\n";
				  $data .= "\t\t\t<column name=\"created\" type=\"timestamp\" default=\"CURRENT_TIMESTAMP\"/>\n";
				  $data .= "\t\t</table>\n";
			  }

			  $data .= "\t</database>\n";
			  $data .= '</persistence>';

	  		  $h = fopen( $this->getCache()->getProjectRoot() . '/persistence.xml', 'w' );
	  		  fwrite( $h, $data );
	  		  fclose( $h );

	  		  if( !file_exists( $this->getCache()->getProjectRoot() . '/persistence.xml' ) )
	  		  	  PHPUnit_Framework_Assert::fail( 'Could not create default persistence.xml file' );
	  }

	  private function createAgilePhpXML( $debug, $interceptors, $identity, $crypto ) {

	  		  $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	  		  $data .= "<!DOCTYPE agilephp SYSTEM \"AgilePHP/agilephp.dtd\">\n";
	  		  $data .= "<agilephp>\n";

			  if( $interceptors )	  		 
				  $data .= "\t<interceptors/>\n";

			  if( $debug )
			 	  $data .= "\t<logger level=\"debug\"/>\n";

			  if( $identity )
			 	  $data .= "\t<identity resetPasswordUrl=\"http://localhost/index.php/LoginController/resetPassword\"/>\n";

			  if( $crypto )
			 	  $data .= "\t<crypto algorithm=\"sha256\" />\n";

			  $data .= '</agilephp>';

			  $h = fopen( $this->getCache()->getProjectRoot() . '/agilephp.xml', 'w' );
	  		  fwrite( $h, $data );
	  		  fclose( $h );

	  		  if( !file_exists( $this->getCache()->getProjectRoot() . '/agilephp.xml' ) )
	  		 	  PHPUnit_Framework_Assert::fail( 'Could not create default agilephp.xml file' );
	  }
	  
	  private function showDBTypes() {
	  	
	  		  echo "\n[1] SQLite\n";
	  		  echo "[2] MySQL\n";
	  		  echo "[3] PostgreSQL\n";
	  		  echo "[4] Firebird\n";
	  		  echo "[5] Informix\n";
	  		  echo "[6] Oracle\n";
	  		  echo "[7] dblib\n";
	  		  echo "[8] IBM\n";
	  }
	  
	  private function getDBType( $choice ) {
	  	
	  	      switch( (int)$choice ) {
	  	      	
		  	      	case 1:
		  	      		return 'sqlite';
		  	      		
		  	      	case 2:
		  	      		return 'mysql';
		  	      		
		  	      	case 3:
				  	    return 'pgsql';
				  	    
		  	      	case 4:
		  	      		return 'firebird';
		  	      		
		  	      	case 5:
		  	      		return 'informix';
	
		  	      	case 6:
				  		 return 'oracle';
				  		 
		  	      	case 7:
				  		 return 'dblib';
	
		  	      	case 8:
		  	      		 return 'ibm';
	
		  	      	default:
		  	      		PHPUnit_Framework_Assert::fail( 'Invalid database type selection' );
	  	      }
	  }

	  private function createAccessFile() {

	  		  $data = "<Files .htaccess>\n";
	  		  $data .= "\torder deny,allow\n";
			  $data .= "\tdeny from all\n";
			  $data .= "</Files>\n";
			  $data .= "<Files persistence.xml>\n";
			  $data .= "\torder deny,allow\n";
			  $data .= "\tdeny from all\n";
			  $data .= "</Files>\n";
			  $data .= "<Files agilephp.xml>\n";
			  $data .= "\torder deny,allow\n";
			  $data .= "\tdeny from all\n";
			  $data .= "</Files>";

	  		  $htaccess = $this->getCache()->getProjectRoot() . '/.htaccess';

	  		  $h = fopen( $htaccess, 'w' );
	  		  fwrite( $h, $data );
	  		  fclose( $h );

	  		  chmod( $htaccess, 0600 );
	  }
}
?>