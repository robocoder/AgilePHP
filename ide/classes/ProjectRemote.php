<?php

/**
 * Responsible for processing "new project" wizard requests.
 * 
 * @author jhahn
 */
class ProjectRemote {

	  private $root;

	  public function __construct() {

	  		 //set_error_handler( 'ProjectRemote::ErrorHandler' );
	  }

	  /**
	   * Creates a new project
	   * 
	   * @param $configs
	   */
	  #@RemoteMethod
 	  public function create( $configs ) {

 		 	 foreach( $configs as $config ) {

 		 	 	  $name = $config->name;
 	  		   	  $$name = $config->value;

 	  		   	  Logger::getInstance()->debug( $name . ' = ' . $$name );
 		 	 }

 	  		 if( !isset( $workspace ) )
 	  		 	 throw new AgilePHP_Exception( 'Missing workspace value' );

 	  		 if( !isset( $projectName ) )
 	  		 	 throw new AgilePHP_Exception( 'Missing project name' );

 	  		 if( !file_exists( $workspace ) )
  		 	 	 throw new AgilePHP_Exception( 'Workspace does not exist' );

  		 	 $this->root = $workspace . DIRECTORY_SEPARATOR . $projectName;

 	  		 if( file_exists( $this->root ) )
 	  		 	 throw new AgilePHP_Exception( 'Project already exists' );

 	  		 if( !mkdir( $this->root ) )
 	  		 	 throw new AgilePHP_Exception( 'Failed to create project at ' . $this->root );

 	  		 $model = $this->root . DIRECTORY_SEPARATOR . 'model';
	  		 if( !mkdir( $model ) )
	  		 	 throw new AgilePHP_Exception( 'Failed to create project models directory at \'' . $model . '\'.' );
	  		 	 
	  		 $view = $this->root . DIRECTORY_SEPARATOR . 'view';
	  		 if( !mkdir( $view ) )
	  		 	 throw new AgilePHP_Exception( 'Failed to create project view directory at \'' . $view . '\'.' );

	  		 $css = $this->root . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'css';
	  		 if( !mkdir( $css ) )
	  		 	 throw new AgilePHP_Exception( 'Failed to create project css directory at \'' . $css . '\'.' );

	  		 $control = $this->root . DIRECTORY_SEPARATOR . 'control';
	  		 if( !mkdir( $control ) )
	  		 	 throw new AgilePHP_Exception( 'Failed to create project controllers directory at \'' . $control . '\'.' );

	  		 $logs = $this->root . DIRECTORY_SEPARATOR . 'logs';
	  		 if( !mkdir( $logs ) )
	  		 	 throw new AgilePHP_Exception( 'Failed to create project logging directory at \'' . $logs . '\'.' );

	  		 if( !chmod( $logs, 0777 ) )
	  		 	 throw new AgilePHP_Exception( 'Failed to change permissions to 077 on project logging directory \'' . $logs . '\'.' );

	  		 $components = $this->root . DIRECTORY_SEPARATOR . 'components';
	  		 if( !mkdir( $components ) )
	  		 	 throw new AgilePHP_Exception( 'Failed to create project components directory at \'' . $components . '\'.' );

	  		 $classes = $this->root . DIRECTORY_SEPARATOR . 'classes';
	  		 if( !mkdir( $classes ) )
	  		 	 throw new AgilePHP_Exception( 'Failed to create project classes directory at \'' . $classes . '\'.' );

	  		 $agilephp = $this->root . DIRECTORY_SEPARATOR . 'AgilePHP';
	  		 $this->recursiveCopy( '..' . DIRECTORY_SEPARATOR . 'src', $agilephp );

	  		 if( getValue( 'databaseEnable' ) ) {

		  		 $this->createPersistenceXML( getValue( 'identityEnable' ), getValue( 'sessionEnable' ), getValue( 'databaseName' ),
		  		 				getValue( 'databaseType' ), getValue( 'databaseHostname' ), getValue( 'databaseUsername' ), getValue( 'databasePassword' ) );
	  		 }

	  		 $this->createAgilePhpXML( getValue( 'logEnable' ), getValue( 'identityEnable' ), getValue( 'cryptoEnable' ));
	  		 $this->createAccessFile( (getValue( 'databaseType' ) == 'sqlite' ? true : false ) );
	  		 $this->createIndexDotPHP();
	  		 $this->createStyleSheet();

	  		 //if( getValue( 'ideEnable' ) ) {

	  		 	 
	  		 //}

	  		 return true;
	  }

	  /**
	   * Performs connection test to the database server configured on the "Database" wizard step.
	   * 
	   * @type
	   */
	  #@RemoteMethod
	  //public function testDatabaseConnection( $type, $hostname, $name, $username, $password ) {
	  public function testDatabaseConnection( $database ) {

	  		 $Database = new Database();
	  		 $Database->setType( $database->type );
	  		 $Database->setDriver( $database->type );
	  		 $Database->setHostname( $database->hostname );
	  		 $Database->setName( $database->name );
	  		 $Database->setUsername( $database->username );
	  		 $Database->setPassword( $database->password );

	  		 $pm = new PersistenceManager();
	  		 $pm->connect( $Database );

	  		 return $pm->isConnected();
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
	  		  	  throw new AgilePHP_Exception( 'Could not create default persistence.xml file' );
	  }

	  private function createAgilePhpXML( $debug, $identity, $crypto ) {

	  		  $data = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
	  		  $data .= '<!DOCTYPE agilephp SYSTEM "AgilePHP/agilephp.dtd">' . PHP_EOL;
	  		  $data .= '<agilephp>' . PHP_EOL;

			  if( $debug )
			 	  $data .= "\t<logger level=\"debug\"/>" . PHP_EOL;

			  if( $identity )
			 	  $data .= "\t<identity resetPasswordUrl=\"http://localhost/index.php/LoginController/resetPassword\" confirmationUrl=\"http://localhost/index.php/LoginController/confirm\"/>" . PHP_EOL;

			  if( $crypto )
			 	  $data .= "\t<crypto algorithm=\"sha256\" />" . PHP_EOL;

			  $data .= '</agilephp>';

			  $h = fopen( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'agilephp.xml', 'w' );
	  		  fwrite( $h, $data );
	  		  fclose( $h );

	  		  if( !file_exists( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'agilephp.xml' ) )
	  		 	  throw new AgilePHP_Exception( 'Could not create default agilephp.xml file' );
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

  	     Logger::getInstance()->error( $e->getMessage() );

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

	  /**
	   * Custom PHP error handling function which throws an AgilePHP_Exception instead of echoing.
	   * 
	   * @param Integer $errno Error number
	   * @param String $errmsg Error message
	   * @param String $errfile The name of the file that caused the error
	   * @param Integer $errline The line number that caused the error
	   * @return false
	   * @throws AgilePHP_Exception
	   *
 	  public static function ErrorHandler( $errno, $errmsg, $errfile, $errline ) {

 	  		 $entry = PHP_EOL . 'Number: ' . $errno . PHP_EOL . 'Message: ' . $errmsg . 
 	  		 		  PHP_EOL . 'File: ' . $errfile . PHP_EOL . 'Line: ' . $errline;

 	  		 throw new AgilePHP_Exception( $errmsg, $errno, $errfile, $errline );
	  }
	  */

	  public function __destruct() {

	  		 //restore_error_handler();
	  }
}
?>