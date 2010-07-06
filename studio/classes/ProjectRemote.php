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
 * Remoting class responsible for server side processing of agilephp projects.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.classes
 */
class ProjectRemote {

	  private $projectName;
	  private $root;

	  public function __construct() { }

	  /**
	   * Creates a new project
	   *
	   * @param array $configs An array of project configuration values
	   * @return boolean True if the project was created successfully
	   * @throws FrameworkException
	   */
	  #@RemoteMethod
 	  public function create( $configs ) {

 	         // Dynamically assign form names/values as php variable names/values
 		 	 foreach( $configs as $config ) {

 		 	 	  $name = $config->name;
 	  		   	  $$name = $config->value;

 	  		   	  Log::debug( $name . ' = ' . $$name );
 		 	 }

 		 	 $workspace = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $workspace );

 	  		 if( !isset( $workspace ) )
 	  		 	 throw new FrameworkException( 'Missing workspace value' );

 	  		 if( !isset( $projectName ) || !$this->projectName = $projectName )
 	  		 	 throw new FrameworkException( 'Missing project name' );

 	  		 if( !file_exists( $workspace ) )
  		 	 	 throw new FrameworkException( 'Workspace does not exist' );

  		 	 $this->root = $workspace . DIRECTORY_SEPARATOR . $projectName;

 	  		 if( file_exists( $this->root ) )
 	  		 	 throw new FrameworkException( 'Project already exists' );

 	  		 if( !mkdir( $this->root ) )
 	  		 	 throw new FrameworkException( 'Failed to create project at ' . $this->root );

  		 	 try {
		 	  		 $model = $this->root . DIRECTORY_SEPARATOR . 'model';
			  		 if( !mkdir( $model ) )
			  		 	 throw new FrameworkException( 'Failed to create project models directory at \'' . $model . '\'.' );

			  		 $view = $this->root . DIRECTORY_SEPARATOR . 'view';
			  		 if( !mkdir( $view ) )
			  		 	 throw new FrameworkException( 'Failed to create project view directory at \'' . $view . '\'.' );

			  		 $css = $this->root . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'css';
			  		 if( !mkdir( $css ) )
			  		 	 throw new FrameworkException( 'Failed to create project css directory at \'' . $css . '\'.' );

			  		 $control = $this->root . DIRECTORY_SEPARATOR . 'control';
			  		 if( !mkdir( $control ) )
			  		 	 throw new FrameworkException( 'Failed to create project controllers directory at \'' . $control . '\'.' );

			  		 $logs = $this->root . DIRECTORY_SEPARATOR . 'logs';
			  		 if( !mkdir( $logs ) )
			  		 	 throw new FrameworkException( 'Failed to create project logging directory at \'' . $logs . '\'.' );

			  		 if( !chmod( $logs, 0777 ) )
			  		 	 throw new FrameworkException( 'Failed to change permissions to 077 on project logging directory \'' . $logs . '\'.' );

			  		 $components = $this->root . DIRECTORY_SEPARATOR . 'components';
			  		 if( !mkdir( $components ) )
			  		 	 throw new FrameworkException( 'Failed to create project components directory at \'' . $components . '\'.' );

			  		 $classes = $this->root . DIRECTORY_SEPARATOR . 'classes';
			  		 if( !mkdir( $classes ) )
			  		 	 throw new FrameworkException( 'Failed to create project classes directory at \'' . $classes . '\'.' );

			  		 $agilephp = $this->root . DIRECTORY_SEPARATOR . 'AgilePHP';
			  		 FileUtils::copy( '..' . DIRECTORY_SEPARATOR . 'src', $agilephp );

			  		 if( $databaseEnable )
				  		 $this->createORMXML( $identityEnable, $sessionProvider, $databaseName, $databaseType,
				  		 			$databaseHostname, $databaseUsername, $databasePassword, $databaseType /* instead of driver - maybe driver should be passed as hidden */ );

				  	 // Create component configuration variables 
				  	 $crypto = array('algorithm' => $cryptoAlgorithm);
				  	 if(isset($cryptoIv) && $cryptoIv) $crypto['iv'] = $cryptoIv;
				  	 if(isset($cryptoKey) && $cryptoKey) {
				  	     
				  	     if(!$cryptoKey == $cryptoKeyConfirm)
				  	        throw new FrameworkException('Crypto keys don\'t match');

				  	     $crypto['key'] = $cryptoKey;
				  	 }
				  	 $logLevel = isset($logEnable) ? strtolower($logLevel) : false;

				  	 // Create project
			  		 $this->createAgilePhpXML( $logLevel, $identityEnable, $crypto, $sessionProvider );
			  		 $this->createAccessFile( ($databaseType) == 'sqlite' ? true : false, $databaseName );
			  		 $this->createIndexDotPHP();
			  		 $this->createStyleSheet();
			  		 $this->createBuildXml();

			  		 if( $ideEnable ) {

			  		 	 switch( $idePlatform ) {

			  		 	 		 case 'eclipse':
			  		 	 		 	$this->createEclipse();
			  		 	 		 	break;

			  		 	 		 case 'netbeans':
			  		 	 		 	$this->createNetbeans();
			  		 	 		 	break;
			  		 	 }
			  		 }

			  		 return true;
  		 	 }
  		 	 catch( FrameworkException $e ) {

  		 	 		if( file_exists( $this->root ) )
  		 	 			FileUtils::delete( $this->root );

  		 	 		throw new FrameworkException( $e->getMessage() );
  		 	 }
	  }

	  #@RemoteMethod
	  public function getCryptoAlgorithms() {

	         $algorithms = Crypto::getInstance()->getSupportedHashAlgorithms();
	         $algos = array();

	         foreach($algorithms as $algo)
	             array_push($algos, array($algo, $algo));

	         return $algos;
	             
	  }

	  /**
	   * Utility method to replace *nix line breaks with windows line breaks if building on windows.
	   *
	   * @param String $file The fully qualified file path
	   * @return void
	   */
	  private function fixLineBreaks( $file ) {

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

	  private function createORMXML( $identity, $session, $name, $type, $hostname, $username, $password, $driver ) {

	  		  $data = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
			  $data .= '<!DOCTYPE orm SYSTEM "AgilePHP/orm/orm.dtd">' . PHP_EOL;
			  $data .= '<orm>' . PHP_EOL;
			  $data .= "\t<database name=\"" . ($type == 'sqlite' ? $this->root . '/' . $name . '.sqlite' : $name) . "\"" .
			  				($type == 'mssql' ? ' driver="' . $db->getDriver() .'"' : '') . PHP_EOL . "\t\t\ttype=\"" . $type . "\" hostname=\"" . $hostname .
	  		  				"\" username=\"" . $username . "\" password=\"" . $password . "\">" . PHP_EOL . PHP_EOL;

			  if( $identity ) {

			  	  $data .= "\t\t<!-- AgilePHP Identity -->" . PHP_EOL;
			  	  $data .= "\t\t<table name=\"users\" isIdentity=\"true\" display=\"Users\" model=\"User\" description=\"Actors in the application\">" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"username\" type=\"varchar\" length=\"150\" primaryKey=\"true\" required=\"true\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"password\" type=\"varchar\" length=\"255\" required=\"true\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"email\" type=\"varchar\" length=\"255\" required=\"true\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"created\" type=\"datetime\" required=\"true\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"last_login\" property=\"lastLogin\" display=\"Last Login\" type=\"datetime\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"roleId\" property=\"Role\" type=\"varchar\" length=\"25\">" . PHP_EOL;
				  $data .= "\t\t\t\t<foreignKey name=\"FK_UserRoles\" type=\"one-to-many\" onDelete=\"SET_NULL\" onUpdate=\"CASCADE\"" . PHP_EOL .
							  		 "\t\t\t\t\ttable=\"roles\" column=\"name\" controller=\"RoleController\" select=\"name\"/>" . PHP_EOL;
				  $data .= "\t\t\t</column>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"enabled\" type=\"bit\" length=\"1\"/>" . PHP_EOL;
				  $data .= "\t\t</table>" . PHP_EOL;
				  $data .= "\t\t<table name=\"roles\" display=\"Roles\" model=\"Role\" description=\"Roles used in the application\">" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"name\" type=\"varchar\" length=\"25\" primaryKey=\"true\" required=\"true\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"description\" type=\"text\"/>" . PHP_EOL;
				  $data .= "\t\t</table>" . PHP_EOL;
			  }

			  if( $session == 'ORM' ) {

			  	  $data .= "\t\t<!-- AgilePHP Session -->" . PHP_EOL;
			  	  $data .= "\t\t<table name=\"sessions\" display=\"Session\" isSession=\"true\" model=\"Session\" description=\"User sessions\">" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"id\" type=\"varchar\" length=\"21\" primaryKey=\"true\" description=\"Unique ID\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"data\" type=\"text\" description=\"Name of recipient\"/>" . PHP_EOL;
				  $data .= "\t\t\t<column name=\"created\" type=\"timestamp\" default=\"CURRENT_TIMESTAMP\"/>" . PHP_EOL;
				  $data .= "\t\t</table>" . PHP_EOL;
			  }

			  $data .= "\t</database>" . PHP_EOL;
			  $data .= '</orm>';

	  		  $h = fopen( $this->root . DIRECTORY_SEPARATOR . 'orm.xml', 'w' );
	  		  fwrite( $h, $data );
	  		  fclose( $h );

	  		  if( !file_exists( $this->root . DIRECTORY_SEPARATOR . 'orm.xml' ) )
	  		  	  throw new FrameworkException( 'Could not create default orm.xml file' );
	  }

	  private function createAgilePhpXML( $logLevel, $identity, $crypto, $sessionProvider ) {
 
	  		  $data = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
	  		  $data .= '<!DOCTYPE agilephp SYSTEM "AgilePHP/agilephp.dtd">' . PHP_EOL;
	  		  $data .= '<agilephp>' . PHP_EOL;

			  if( $logLevel )
			 	  $data .= "\t<logger level=\"$logLevel\"/>" . PHP_EOL;

			  if($sessionProvider == 'ORM')
			      $data .= "\t<scope type=\"session\" provider=\"OrmSessionProvider\"/>" . PHP_EOL;

			  if( $crypto ) {

			 	  $data .= "\t<crypto algorithm=\"" . $crypto['algorithm'] . "\"";
			 	  $data .= (isset($crypto['iv'])) ? ' iv="' . $crypto['iv'] . '"' : '';
			 	  $data .= (isset($crypto['key'])) ? ' key="' . $crypto['key'] . '"' : '';
			 	  $data .= '/>' . PHP_EOL;
			  }

			  $data .= '</agilephp>';

			  $h = fopen( $this->root . DIRECTORY_SEPARATOR . 'agilephp.xml', 'w' );
	  		  fwrite( $h, $data );
	  		  fclose( $h );

	  		  if( !file_exists( $this->root . DIRECTORY_SEPARATOR . 'agilephp.xml' ) )
	  		 	  throw new FrameworkException( 'Could not create default agilephp.xml file' );
	  }

	  private function createAccessFile( $sqlite = false, $dbname = false ) {

	  		  if( $sqlite && !$dbname )
	  		  	  throw new FrameworkException( 'dbname parameter required when passing \'sqlite\'.' );

	  		  $data = '<Files .htaccess>' . PHP_EOL;
	  		  $data .= "\torder deny,allow" . PHP_EOL;
			  $data .= "\tdeny from all" . PHP_EOL;
			  $data .= "</Files>" . PHP_EOL;
			  $data .= "<Files orm.xml>" . PHP_EOL;
			  $data .= "\torder deny,allow" . PHP_EOL;
			  $data .= "\tdeny from all" . PHP_EOL;
			  $data .= "</Files>" . PHP_EOL;
			  $data .= "<Files agilephp.xml>" . PHP_EOL;
			  $data .= "\torder deny,allow" . PHP_EOL;
			  $data .= "\tdeny from all" . PHP_EOL;
			  $data .= "</Files>";

			  if( $sqlite ) {

			  	  $data .= PHP_EOL . "<Files " . $dbname . ">" . PHP_EOL;
			  	  $data .= "\torder deny,allow" . PHP_EOL;
			  	  $data .= "\tdeny from all" . PHP_EOL;
			  	  $data .= "</Files>";
		  	  }

	  		  $htaccess = $this->root . DIRECTORY_SEPARATOR . '.htaccess';

	  		  $h = fopen( $htaccess, 'w' );
	  		  fwrite( $h, $data );
	  		  fclose( $h );

	  		  chmod( $htaccess, 0600 );
	  }

	  private function createIndexDotPHP() {

	  	      $code = '<?php
/**
 * AgilePHP Generated Index Page
 * ' . $this->projectName . '
 *
 * @package ' . $this->projectName . '
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
		$agilephp->setDefaultTimezone( \'America/New_York\' );
		$agilephp->setFrameworkRoot( realpath( dirname( __FILE__ ) . \'/AgilePHP\' ) );

		MVC::getInstance()->dispatch();
 }
 catch( Exception $e ) {

  	     Log::error( $e->getMessage() );

		 $renderer = new PHTMLRenderer();
		 $renderer->set( \'title\', \'' . $this->projectName . ' :: Error Page\' );
		 $renderer->set( \'error\', $e->getMessage() . ($agilephp->isInDebugMode() ? \'<pre>\' . $e->getTraceAsString() . \'</pre>\' : \'\' ) );
		 $renderer->render( \'error\' );
 }
?>';
	  	 	  $h = fopen( $this->root . '/index.php', 'w' );
	  		  fwrite( $h, str_replace( "\n", PHP_EOL, $code ) );
	  		  fclose( $h );
	  }

	  private function createStyleSheet() {

	  		  $style = '@CHARSET "UTF-8";

a {

	text-decoration: none;
}


/** AgilePHP Styles */

.info {

	color: #43b605;
	font-family:tahoma;
	font-size: 12px;
	padding-bottom: 5px;
}

.error {

	font-family:tahoma;
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
	font-family: tahoma;
	font-size: 16px;
	font-weight: bolder;
	text-align: center;
	padding-bottom: 20px;
	padding-top: 25px;
}

.agilephpTable {

	color: #636363;
	font-family: tahoma;
	font-size: 16px;
	border-collapse: collapse;
}

.agilephpHeader {

	color: #636363;
	font-family: tahoma;
	font-size: 11px;
	text-decoration: none;
}

.agilephpHighlight {

	color: #636363;
	font-family: tahoma;
	font-size: 10px;
	background-color: #C9C9C9;
}

.agilephpRow1 {

	color: #636363;
	font-family: tahoma;
	font-size: 10px;
	background-color: #FFFFFF;
}

.agilephpRow2 {

	color: #636363;
	font-family: tahoma;
	font-size: 10px;
	background-color: #F9F9F9;
}

.agilephpPaginationTable {

	color: #636363;
	font-family: tahoma;
	font-size: 10px;
}

.agilephpPaginationHeader {

	color: #636363;
	font-family: tahoma;
	font-size: 12px;
}

.agilephpPaginationRecordCount {

	color: #636363;
	font-family: tahoma;
	font-size: 12px;
}';
	  		  $h = fopen( $this->root . DIRECTORY_SEPARATOR . 'view' .
	  		  			DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'style.css', 'w' );
	  		  fwrite( $h, str_replace( "\n", PHP_EOL, $style ) );
	  		  fclose( $h );
	  }

	  public function createEclipse() {

	  		  $dotProject = '<?xml version="1.0" encoding="UTF-8"?>
<projectDescription>
	<name>' . $this->projectName . '</name>
	<comment>AgilePHP Generated Project</comment>
	<projects>
	</projects>
	<buildSpec>
		<buildCommand>
			<name>org.eclipse.wst.validation.validationbuilder</name>
			<arguments>
			</arguments>
		</buildCommand>
		<buildCommand>
			<name>org.eclipse.dltk.core.scriptbuilder</name>
			<arguments>
			</arguments>
		</buildCommand>
	</buildSpec>
	<natures>
		<nature>org.eclipse.php.core.PHPNature</nature>
	</natures>
</projectDescription>';

	  		  $h = fopen( $this->root . '/.project', 'w' );
	  		  fwrite( $h, str_replace( "\n", PHP_EOL, $dotProject ) );
	  		  fclose( $h );

	  		  $dotBuildpath = '<?xml version="1.0" encoding="UTF-8"?>
<buildpath>
	<buildpathentry kind="src" path=""/>
	<buildpathentry kind="con" path="org.eclipse.php.core.LANGUAGE"/>
</buildpath>';

	  		  $h = fopen( $this->root . '/.buildpath', 'w' );
	  		  fwrite( $h, str_replace( "\n", PHP_EOL, $dotBuildpath ) );
	  		  fclose( $h );
	  }

	  public function createNetbeans() {

	  		 $nbproject = $this->root . '/nbproject';

	  		 if( !mkdir( $nbproject ) )
	  		 	 throw new FrameworkException( 'Could not create netbeans project directory \'' . $nbproject . '\'.' );

	  		 $projectDotXml = '<?xml version="1.0" encoding="UTF-8"?>
<project xmlns="http://www.netbeans.org/ns/project/1">
    <type>org.netbeans.modules.php.project</type>
    <configuration>
        <data xmlns="http://www.netbeans.org/ns/php-project/1">
            <name>' . $this->projectName . '</name>
        </data>
    </configuration>
</project>';

	  		 $h = fopen( $nbproject . '/project.xml', 'w' );
	  		 fwrite( $h, str_replace( "\n", PHP_EOL, $projectDotXml ) );
	  		 fclose( $h );

	  		 $projectDotProperties = 'include.path=${php.global.include.path}
source.encoding=UTF-8
src.dir=.
tags.asp=false
tags.short=true
web.root=.';

	  		 $h = fopen( $nbproject . '/project.properties', 'w' );
	  		 fwrite( $h, str_replace( "\n", PHP_EOL, $projectDotProperties ) );
	  		 fclose( $h );

	  		 $privateDotProperties = 'copy.src.files=false
copy.src.target=' . $this->root . '
index.file=index.php' . '
run.as=LOCAL
url=http://localhost/index.php';

	  		 if( !mkdir( $nbproject . '/private' ) )
	  		 	 throw new FrameworkException( 'Could not create netbeans project private directory \'' . $nbproject . '/private\'.' );

	  		 $h = fopen( $nbproject . '/private/private.properties', 'w' );
	  		 fwrite( $h, str_replace( "\n", PHP_EOL, $privateDotProperties ) );
	  		 fclose( $h );
	  }

	  private function createBuildXml() {

	          $xml = '<?xml version="1.0" encoding="UTF-8"?>

<project name="' . $this->projectName . '" basedir="." default="main">

    <property file="${project.basedir}/build.properties"/>

    <property name="package" value="${phing.project.name}"/>
    <property name="build.dir" value="${project.basedir}/build"/>
	<property name="apidoc.dir" value="${project.basedir}/build/apidoc"/>
    <property name="log.dir" value="${project.basedir}/logs"/>
    <property name="test.dir" value="${project.basedir}/tests"/>
    <property name="phpunit.reports" value="${build.dir}/reports/phpunit"/>

    <version releasetype="Minor" file="VERSION" property="build.version"/>

    <fileset dir="${project.basedir}" id="distfiles">
        <include name="**"/>
		<exclude name="build.properties"/>
		<exclude name="build.xml"/>
		<exclude name="VERSION"/>
    </fileset>

    <tstamp>
        <format property="buildtime" pattern="%a, %d %b %Y, %H:%M"/>
    </tstamp>

    <target name="main" description="Default target" depends="clean,test">

        <phplint>
            <fileset dir="${project.basedir}">
                <include name="**/*.php"/>
            </fileset>
        </phplint>

        <jsllint>
            <fileset dir="${project.basedir}">
                <include name="**/*.js"/>
            </fileset>
        </jsllint>

        <copy todir="${build.dir}/${phing.project.name}">
            <fileset refid="distfiles"/>
        </copy>
		
		<delete dir="${build.dir}/${phing.project.name}/tests" includeemptydirs="true"/>
		<delete dir="${build.dir}/${phing.project.name}/build" includeemptydirs="true"/>

        <jsMin targetDir="${build.dir}/${phing.project.name}" failOnError="true">
            <fileset dir="${build.dir}/${phing.project.name}">
                <include name="**/*.js"/>
            </fileset>
        </jsMin>
    </target>

    <target name="apidoc" description="Generates apidoc using phpDocumentor">
        <exec dir="${project.basedir}" logoutput="true"
         command="phpdoc -d . -t build/apidoc -o HTML:frames:phpdoc.de -ti \'${phing.project.name}\' -ue on -s on"/>
    </target>

    <target name="archive" description="Creates a distributable gzip archive">
        <zip destfile="${build.dir}/${phing.project.name}-${build.version}-src.zip">
            <fileset dir=".">
                <include name="**/**"/>
            </fileset>
        </zip>
		<zip destfile="${build.dir}/${phing.project.name}-${build.version}-apidoc.zip">
            <fileset dir="${apidoc.dir}">
                <include name="**/**"/>
            </fileset>
        </zip>
        <zip destfile="${build.dir}/${phing.project.name}-${build.version}-dist.zip">
            <fileset refid="distfiles"/>
        </zip>
    </target>

    <target name="test" description="Runs PHPUnit test suites">
        <delete dir="${phpunit.reports}" includeemptydirs="true" verbose="true"/>
        <mkdir dir="${phpunit.reports}"/>
        <mkdir dir="${phpunit.reports}/tests"/>
        <mkdir dir="${phpunit.reports}/coverage"/>
        <coverage-setup database="${phpunit.reports}/coverage/db">
      		<fileset dir="${project.basedir}">
    			<include name="**/*.php"/>
  			</fileset>
    	</coverage-setup>
        <phpunit bootstrap="${test.dir}/bootstrap.php" codecoverage="true"
        			 haltonfailure="true" haltonerror="true" printsummary="true">
            <formatter todir="${phpunit.reports}/tests" type="xml"/>
            <batchtest>
                <fileset dir="${test.dir}">
					<include name="*Test.php" />
                </fileset>
            </batchtest>
        </phpunit>
        <phpunitreport infile="${phpunit.reports}/tests/testsuites.xml"  styledir="${phpunit.styles.dir}"
         					format="frames" todir="${phpunit.reports}/tests"/>
         <coverage-report outfile="${phpunit.reports}/coverage/coverage.xml">
      		<report todir="${phpunit.reports}/coverage"/>
    	</coverage-report>
    </target>

	<target name="dist" depends="main,apidoc,archive"/>

    <target name="clean" description="Deletes build related artifacts" depends="delete-logs">
        <delete dir="${build.dir}"/>
    </target>

    <target name="delete-logs" description="Deletes log files from test and studio packages">
        <delete dir="${log.dir}" includeemptydirs="true" verbose="true"/>
		<mkdir dir="${log.dir}"/>
		<chmod file="${log.dir}" mode="0777" verbose="true" />
    </target>

    <!-- Helper targets used to install build and test tools -->

    <target name="install-phpdocumentor" description="Installs phpDocumentor via PEAR">
        <exec dir="${project.basedir}" logoutput="true"
         command="pear install phpDocumentor"/>
    </target>

    <target name="install-phpunit" description="Installs PHPUnit via PEAR">
        <exec dir="${project.basedir}" logoutput="true"
         command="pear channel-discover pear.phpunit.de"/>
        <exec dir="${project.basedir}" logoutput="true"
         command="pear install phpunit/PHPUnit"/>
    </target>

</project>';

	         // Write build.xml
	         $h = fopen( $this->root . DIRECTORY_SEPARATOR . 'build.xml', 'w' );
	  		 fwrite( $h, $xml );
	  		 fclose( $h );

	  		 // Write build.properties
	  		 $h = fopen( $this->root . DIRECTORY_SEPARATOR . 'build.properties', 'w' );
	  		 fwrite( $h, 'phpunit.styles.dir=/usr/share/php/data/phing/etc' );
	  		 fclose( $h );

	  		 // Write VERSION
	  		 $h = fopen( $this->root . DIRECTORY_SEPARATOR . 'VERSION', 'w' );
	  		 fwrite( $h, '0.0.0' );
	  		 fclose( $h );
	  }
}
?>