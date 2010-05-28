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
 * Creates an AgilePHP project directory structure with all necssary items
 * according to user input.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.generator
 * @version 0.1a
 */
require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';
require_once 'util' . DIRECTORY_SEPARATOR . 'IDEIntegration.php';

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
  		 	 $pieces = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );
  		 	 array_pop( $pieces );
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

	  		 $root = $projectHome . DIRECTORY_SEPARATOR . $projectName;

	  		 $model = $root . DIRECTORY_SEPARATOR . 'model';
	  		 if( !mkdir( $model ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project models directory at \'' . $model . '\'.' );
	  		 	 
	  		 $view = $root . DIRECTORY_SEPARATOR . 'view';
	  		 if( !mkdir( $view ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project view directory at \'' . $view . '\'.' );

	  		 $css = $root . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'css';
	  		 if( !mkdir( $css ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project css directory at \'' . $css . '\'.' );

	  		 $control = $root . DIRECTORY_SEPARATOR . 'control';
	  		 if( !mkdir( $control ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project controllers directory at \'' . $control . '\'.' );
	  		 
	  		 $logs = $root . DIRECTORY_SEPARATOR . 'logs';
	  		 if( !mkdir( $logs ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project logging directory at \'' . $logs . '\'.' );
	  		 
	  		 if( !chmod( $logs, 0777 ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not change permissions to 077 on project logging directory \'' . $logs . '\'.' );

	  		 $components = $root . DIRECTORY_SEPARATOR . 'components';
	  		 if( !mkdir( $components ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project components directory at \'' . $components . '\'.' );

	  		 $classes = $root . DIRECTORY_SEPARATOR . 'classes';
	  		 if( !mkdir( $classes ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create project classes directory at \'' . $classes . '\'.' );

	  		 $agilephp = $root . DIRECTORY_SEPARATOR . 'AgilePHP';
	  		 $this->recursiveCopy( '..' . DIRECTORY_SEPARATOR . 'src', $agilephp );

	  		 /** AgilePHP Configuration */
	  		 //$answer = $this->prompt( 'Would you like to use interceptors in your project? (Y/N)' );
	  		 //$cache->setInterceptors( strtolower( $answer ) == 'y' ? true : false );

	  		 $answer = $this->prompt( 'Would you like to start your default logging level at debug? (Y/N)' );
	  		 $cache->setLogging( strtolower( $answer ) == 'y' ? true : false );

	  		 $answer = $this->prompt( 'Would you like to use the AgilePHP Identity component? (Y/N)' );
	  		 $cache->setIdentity( strtolower( $answer ) == 'y' ? true : false );

	  		 $answer = $this->prompt( 'Would you like to use the AgilePHP Crypto component to secure private information in your project? (Y/N)' );
	  		 $cache->setCrypto( strtolower( $answer ) == 'y' ? true : false );

	  		 $answer = $this->prompt( 'Would you like to use the AgilePHP SessionScope component? (Y/N)' );
	  		 $cache->setSession( strtolower( $answer ) == 'y' ? true : false );

	  		 // Identity and Session components require a database
	  		 if( $cache->getIdentity() != 'y' && $cache->getSession() != 'y' ) {

		  		 $answer = $this->prompt( 'Will you be connecting to a database? (Y/N)' );
		  		 $cache->setDatabase( strtolower( $answer ) == 'y' ? true : false );
	  		 }
	  		 else {
	  		 	 $cache->setDatabase( 'y' );
	  		 }

	  		 $this->saveCache( $cache );

	  		 if( $cache->getDatabase() == 'y' ) {

	  		 	 echo 'What type of database server are you using?' . PHP_EOL . 'AgilePHP> ';
	  		 	 $this->showDBTypes();
	  		 	 $cache->setDBType( $this->getDBType( trim( fgets( STDIN ) ) ) );
	  		 	 
	  		 	 if( $cache->getDBType() != 'sqlite' ) {

		  		 	 $cache->setDBHost( $this->prompt( 'Database Server hostname?' ) ); 
		  		 	 $cache->setDBName( $this->prompt( 'Database name?' ) );
		  		 	 $cache->setDBUser( $this->prompt( 'Username to connect?' ) );
		  		 	 $cache->setDBPass( $this->prompt( 'Password to connect?' ) );
	  		 	 }
	  		 	 else {

	  		 	 	 $cache->setDBHost( $cache->getProjectHome() );
	  		 	 	 $cache->setDBName( $this->prompt( 'Database name?' ) );
	  		 	 }

	  		 	 $this->saveCache( $cache );

	  		 	 $this->createPersistenceXML( $cache->getIdentity(), $cache->getSession(), $cache->getDBName(), $cache->getDBType(),
	  		 	 			$cache->getDBHost(), $cache->getDBUser(), $cache->getDBPass() );
	  		 }

	  		 $this->createAgilePhpXML( $cache->getLogging(), $cache->getInterceptors(), $cache->getIdentity(), $cache->getCrypto() );
	  		 $this->createAccessFile( ($cache->getDBType() == 'sqlite' ? true : false ) );
	  		 $this->createIndexDotPHP();
	  		 $this->createStyleSheet();

	  		 $answer = $this->prompt( 'Would you like IDE support? (Y/N)' );
	  		 if( strtolower( $answer ) == 'y' ) new IDEIntegration();
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

			      	 if( $file != '.' && $file != '..' && substr( $file, 0, 4 ) != '.svn' ) {

			             if( is_dir( $src . DIRECTORY_SEPARATOR . $file ) )

			             	 $this->recursiveCopy( $src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file );
			             else {

			             	copy( $src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file );

			             	// Replace *nix line breaks with windows line breaks if building on windows.
			             	$this->fixLineBreaks( $dst . DIRECTORY_SEPARATOR . $file );
			             }
			        }
			 }
			 closedir( $dir );
	  }

	  private function createPersistenceXML( $identity, $session, $name, $type, $hostname, $username, $password ) {

	  		  $data = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
			  $data .= '<!DOCTYPE persistence SYSTEM "AgilePHP/persistence/persistence.dtd">' . PHP_EOL;
			  $data .= '<persistence>' . PHP_EOL;
			  $data .= "\t<database id=\"db1\" name=\"" . ($type == 'sqlite' ? $this->getCache()->getProjectRoot() . '/' . $name : $name) . "\"" .
			  				($type == 'mssql' ? ' driver="SQL Server Native Client 10.0"' : '') . PHP_EOL . "\t\t\ttype=\"" . $type . "\" hostname=\"" . $hostname .
	  		  				"\" username=\"" . $username . "\" password=\"" . $password . "\">" . PHP_EOL . PHP_EOL;

			  if( $identity ) {

			  	  $data .= "\t\t<!-- AgilePHP Identity -->" . PHP_EOL;
			  	  $data .= "\t\t<table name=\"users\" isIdentity=\"true\" display=\"Users\" model=\"User\" description=\"Actors in the application\">" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"username\" type=\"varchar\" length=\"150\" primaryKey=\"true\" required=\"true\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"password\" type=\"varchar\" length=\"255\" required=\"true\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"email\" type=\"varchar\" length=\"255\" required=\"true\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"created\" type=\"datetime\" required=\"true\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"last_login\" property=\"lastLogin\" display=\"Last Login\" type=\"datetime\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"roleId\" type=\"varchar\" length=\"25\">" . PHP_EOL;
				  $data .= "\t\t\t\t<foreignKey name=\"FK_UserRoles\" type=\"one-to-many\" onDelete=\"SET_NULL\" onUpdate=\"CASCADE\"" . PHP_EOL .
							  		 "\t\t\t\t\ttable=\"roles\" column=\"name\" controller=\"RoleController\" select=\"name\"/>" . PHP_EOL;
				  $data .= "\t\t\t</column>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"sessionId\" type=\"varchar\" length=\"21\">" . PHP_EOL;
				  $data .= "\t\t\t\t<foreignKey name=\"FK_UserSessions\" type=\"one-to-one\" onDelete=\"SET_NULL\" onUpdate=\"CASCADE\"" . PHP_EOL .
							  		 "\t\t\t\t\ttable=\"sessions\" column=\"id\" controller=\"SessionController\"/>" . PHP_EOL;
				  $data .= "\t\t\t</column>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"enabled\" type=\"bit\" length=\"1\"/>" . PHP_EOL;
				  $data .= "\t\t</table>" . PHP_EOL;
				  $data .= "\t\t<table name=\"roles\" display=\"Roles\" model=\"Role\" description=\"Roles used in the application\">" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"name\" type=\"varchar\" length=\"25\" primaryKey=\"true\" required=\"true\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"description\" type=\"text\"/>" . PHP_EOL;
				  $data .= "\t\t</table>" . PHP_EOL;
			  }

			  if( $session ) {

			  	  $data .= "\t\t<!-- AgilePHP Session -->" . PHP_EOL;
			  	  $data .= "\t\t<table name=\"sessions\" display=\"Session\" isSession=\"true\" model=\"Session\" description=\"User sessions\">" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"id\" type=\"varchar\" length=\"21\" primaryKey=\"true\" description=\"Unique ID\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"data\" type=\"text\" description=\"Name of recipient\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"created\" type=\"timestamp\" default=\"CURRENT_TIMESTAMP\"/>" . PHP_EOL;
				  $data .= "\t\t</table>" . PHP_EOL;
			  }

			  $data .= "\t</database>" . PHP_EOL;
			  $data .= '</persistence>';

	  		  $h = fopen( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'persistence.xml', 'w' );
	  		  fwrite( $h, $data );
	  		  fclose( $h );

	  		  if( !file_exists( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'persistence.xml' ) )
	  		  	  PHPUnit_Framework_Assert::fail( 'Could not create default persistence.xml file' );
	  }

	  private function createAgilePhpXML( $debug, $interceptors, $identity, $crypto ) {

	  		  $data = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
	  		  $data .= '<!DOCTYPE agilephp SYSTEM "AgilePHP/agilephp.dtd">' . PHP_EOL;
	  		  $data .= '<agilephp>' . PHP_EOL;

			  if( $debug )
			 	  $data .= "\t<logger level=\"debug\"/>" . PHP_EOL;

			  if( $identity )
			 	  $data .= "\t<identity resetPasswordUrl=\"http://localhost/index.php/LoginController/resetPassword\" confirmationUrl=\"http://localhost/index.php/LoginController/confirm\"/>" . PHP_EOL;

			  if( $crypto )
			 	  $data .= "\t<crypto algorithm=\"sha256\" />" . PHP_EOL;

		 	  if( $interceptors )	  		 
				  $data .= "\t<interceptors/>" . PHP_EOL;

			  $data .= '</agilephp>';

			  $h = fopen( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'agilephp.xml', 'w' );
	  		  fwrite( $h, $data );
	  		  fclose( $h );

	  		  if( !file_exists( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'agilephp.xml' ) )
	  		 	  PHPUnit_Framework_Assert::fail( 'Could not create default agilephp.xml file' );
	  }

	  private function showDBTypes() {
	  	
	  		  echo PHP_EOL . "[1] SQLite" . PHP_EOL;
	  		  echo '[2] MySQL' . PHP_EOL;
	  		  echo '[3] MSSQL' . PHP_EOL;
	  		  echo '[4] PostgreSQL' .PHP_EOL;
	  		  echo '[5] Firebird' . PHP_EOL;
	  		  echo '[6] Informix' . PHP_EOL;
	  		  echo '[7] Oracle' . PHP_EOL;
	  		  echo '[8] dblib' . PHP_EOL;
	  		  echo '[9] IBM' . PHP_EOL;
	  }

	  private function getDBType( $choice ) {
	  	
	  	      switch( (int)$choice ) {
	  	      	
		  	      	case 1:
		  	      		return 'sqlite';
		  	      		
		  	      	case 2:
		  	      		return 'mysql';
		  	      		
		  	      	case 3:
		  	      		return 'mssql';

		  	      	case 4:
				  	    return 'pgsql';
				  	    
		  	      	case 5:
		  	      		return 'firebird';
		  	      		
		  	      	case 6:
		  	      		return 'informix';
	
		  	      	case 7:
				  		 return 'oracle';
				  		 
		  	      	case 8:
				  		 return 'dblib';
	
		  	      	case 9:
		  	      		 return 'ibm';
	
		  	      	default:
		  	      		PHPUnit_Framework_Assert::fail( 'Invalid database type selection' );
	  	      }
	  }

	  private function createAccessFile( $sqlite = false ) {

	  		  $data = '<Files .htaccess>' . PHP_EOL;
	  		  $data .= "\torder deny,allow" . PHP_EOL;
			  $data .= "\tdeny from all" . PHP_EOL;
			  $data .= "</Files>" . PHP_EOL;
			  $data .= "<Files persistence.xml>" . PHP_EOL;
			  $data .= "\torder deny,allow" . PHP_EOL;
			  $data .= "\tdeny from all" . PHP_EOL;
			  $data .= "</Files>" . PHP_EOL;
			  $data .= "<Files agilephp.xml>" . PHP_EOL;
			  $data .= "\torder deny,allow" . PHP_EOL;
			  $data .= "\tdeny from all" . PHP_EOL;
			  $data .= "</Files>";

			  if( $sqlite ) {

			  	  $data .= PHP_EOL . "<Files " . $this->getCache()->getDBName() . ".sqlite>" . PHP_EOL;
			  	  $data .= "\torder deny,allow" . PHP_EOL;
			  	  $data .= "\tdeny from all" . PHP_EOL;
			  	  $data .= "</Files>";
		  	  }

	  		  $htaccess = $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . '.htaccess';

	  		  $h = fopen( $htaccess, 'w' );
	  		  fwrite( $h, $data );
	  		  fclose( $h );

	  		  chmod( $htaccess, 0600 );
	  }

	  private function createIndexDotPHP() {
	  	
	  	      $code = '<?php
/**
 * AgilePHP Generated Index Page
 * ' . $this->getCache()->getProjectName() . '
 * 
 * @package ' . $this->getCache()->getProjectName() . '
 */

/**
 * This is the default index page that handles all requests for the web application.
 * Here, we load the core AgilePHP framework and call upon the Model-View-Control
 * component to parse and handle the current request. All calls are wrapped in a
 * try/catch which redirects the website visitor to the view/error.phtml page on error.
 * 
 * @author AgilePHP Generator
 * @version 0.1
 */
 require_once \'AgilePHP\' . DIRECTORY_SEPARATOR . \'AgilePHP.php\';

 try {
		$agilephp = AgilePHP::getFramework();
		$agilephp->setFrameworkRoot( \'' . $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'AgilePHP\' );
		$agilephp->setDefaultTimezone( \'America/New_York\' );

		MVC::getInstance()->processRequest();
 }
 catch( Exception $e ) {

  	     Logger::error( $e->getMessage() );

		 $renderer = new PHTMLRenderer();
		 $renderer->set( \'title\', \'' . $this->getCache()->getProjectName() . ' :: Error Page\' );
		 $renderer->set( \'error\', $e->getMessage() . ($agilephp->isInDebugMode() ? \'<pre>\' . $e->getTraceAsString() . \'</pre>\' : \'\' ) );
		 $renderer->render( \'error\' );
 } 
?>';
	  	 	  $h = fopen( $this->getCache()->getProjectRoot() . '/index.php', 'w' );
	  		  fwrite( $h, str_replace( "\n", PHP_EOL, $code ) );
	  		  fclose( $h );    
	  }
	  
	  private function createStyleSheet() {
	  	
	  		  $style = '@CHARSET "UTF-8";
	  		  
/** AgilePHP Styles */

a {

	text-decoration: none;	
}

.info {

	color: #43b605;
	font-family:tahoma;
	font-size: 12px;
	padding-bottom: 5px;
}

.error {

	font-family:tahoma;
	line-height:14px;
	font-size: 18px;
	color: #FF0000;
}

.agilephpSearchBar {
	
	text-align: center;
	font-size: 12px;
	padding-bottom: 25px;
}

.agilephpSearchBar input, .agilephpSearchBar select {
	
	font-size: 12px;
}

.agilephpTableDescription {

	color: #000000;
	font-family:tahoma;
	font-size: 16px;
	font-weight: bolder;
	line-height: 14px;
	text-align: center;
	padding-bottom: 20px;
	padding-top: 25px;
}

.agilephpTable {
	
	color:#636363;
	font-family:tahoma;
	font-size: 16px;
	line-height:14px;
	border-collapse : collapse;
}

.agilephpHeader {

	color:#636363;
	font-family:tahoma;
	font-size:11px;
	line-height:14px;
	text-decoration: none;
}

.agilephpHighlight {

	color:#636363;
	font-family:tahoma;
	font-size:10px;
	line-height:14px;
	background-color: #C9C9C9;
}

.agilephpRow1 {

	color:#636363;
	font-family:tahoma;
	font-size:10px;
	line-height:14px;
	background-color: #FFFFFF;
}

.agilephpRow2 {

	color:#636363;
	font-family:tahoma;
	font-size:10px;
	line-height:14px;
	background-color: #F9F9F9;
}

.agilephpPaginationTable {

	color:#636363;
	font-family:tahoma;
	font-size:10px;
	line-height:14px;
}

.agilephpPaginationHeader {
	
	color:#636363;
	font-family:tahoma;
	font-size:12px;
	line-height:14px;
}

.agilephpPaginationRecordCount {
	
	color:#636363;
	font-family:tahoma;
	font-size:12px;
	line-height:14px;
}';
	  		  $h = fopen( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'view' .
	  		  			DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'style.css', 'w' );
	  		  fwrite( $h, str_replace( "\n", PHP_EOL, $style ) );
	  		  fclose( $h );    
	  }
}
?>