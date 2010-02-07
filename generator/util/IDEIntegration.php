<?php

class IDEIntegration extends AgilePHPGen {

	  public function __construct() {

	  		 parent::__construct();

	  		 $this->showSupportedIDE();
			 $answer = $this->prompt( '' );
			 
			 switch( $answer ) {
			 	
			 		 case 1:
			 		 	$this->createEclipse();
			 		 	break;
			 		 	
			 		 case 2:
			 		 	$this->createNetbeans();
			 		 	break;
			 		 	
			 		 default:
			 		 	PHPUnit_Framework_Assert::fail( 'Invalid IDE selection' );
			 }
	  }
	  public function createEclipse() {

	  		  $dotProject = '<?xml version="1.0" encoding="UTF-8"?>
<projectDescription>
	<name>' . $this->getCache()->getProjectName() . '</name>
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

	  		  $h = fopen( $this->getCache()->getProjectRoot() . '/.project', 'w' );
	  		  fwrite( $h, str_replace( "\n", PHP_EOL, $dotProject ) );
	  		  fclose( $h );

	  		  $dotBuildpath = '<?xml version="1.0" encoding="UTF-8"?>
<buildpath>
	<buildpathentry kind="src" path=""/>
	<buildpathentry kind="con" path="org.eclipse.php.core.LANGUAGE"/>
</buildpath>';
	  		  
	  		  $h = fopen( $this->getCache()->getProjectRoot() . '/.buildpath', 'w' );
	  		  fwrite( $h, str_replace( "\n", PHP_EOL, $dotBuildpath ) );
	  		  fclose( $h );
	  }

	  public function createNetbeans() {

	  		 $target = $this->prompt( 'Where is your web server root? (/var/www)' );
	  		 $url = $this->prompt( 'What url will you use to access your website? (http://localhost/' . $this->getCache()->getProjectName() . '/)' );
	  		 $index = $this->prompt( 'What is the name of your default index page? (index.php)' );

	  		 if( !$target ) $target = '/var/www';
	  		 if( !$url ) $url = 'http://localhost/' . $this->getCache()->getProjectName() . '/';
	  		 if( !$index ) $index = 'index.php';

	  		 $nbproject = $this->getCache()->getProjectRoot() . '/nbproject';

	  		 if( !mkdir( $nbproject ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Could not create netbeans project directory \'' . $nbproject . '\'.' );
	  		 
	  		 $projectDotXml = '<?xml version="1.0" encoding="UTF-8"?>
<project xmlns="http://www.netbeans.org/ns/project/1">
    <type>org.netbeans.modules.php.project</type>
    <configuration>
        <data xmlns="http://www.netbeans.org/ns/php-project/1">
            <name>' . $this->getCache()->getProjectName() . '</name>
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
copy.src.target=' . $target . '
index.file=' . $index . '
run.as=LOCAL
url=' . $url;

	  		 if( !mkdir( $nbproject . '/private' ) )
	  		 	 PHPUnit_Framework_Assert( 'Could not create netbeans project private directory \'' . $nbproject . '/private\'.' );

	  		 $h = fopen( $nbproject . '/private/private.properties', 'w' );
	  		 fwrite( $h, str_replace( "\n", PHP_EOL, $privateDotProperties ) );
	  		 fclose( $h );
	  }

	  public function showSupportedIDE() {

	  		 echo "\n[1] Eclipse Galileo (PHP)\n";
	  		 echo "[2] Netbeans (6.7.1)";
	  }
}
?>