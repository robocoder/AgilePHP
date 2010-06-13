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
 * Responsible for OpenAppstore integration
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.classes
 * 
 * @todo Rename to AppstoreRemote and move setProperty to its own class - ComponentsRemote
 */
class ComponentsRemote {

	  private $api;
	  private $platformId;

	  public function __construct() {

	  		 $config = new Config();

 	  		 $config->setName( 'appstore_endpoint' );
 	  		 $endpoint = $config->getValue();

 	  		 $config->setName( 'appstore_username' );
 	  		 $username = $config->getValue();

 	  		 $config->setName( 'appstore_password' );
 	  		 $password = $config->getValue();

 	  		 $config->setName( 'appstore_apikey' );
 	  		 $apikey = $config->getValue();

 	  		 $config->setName( 'appstore_platformId' );
	  		 $this->platformId = $config->getValue();

	  		 $this->api = new AppstoreAPI();
	  		 $this->api->login( $username, $password, $apikey );
	  }

	  #@RemoteMethod
	  public function getApps() {

	  		 $o = new stdClass;
	  		 $o->apps = $this->api->getAppsByPlatform( $this->platformId );

	  		 return $o;
	  }

	  #@RemoteMethod
	  public function install( $projectRoot, $id, $appId ) {

	  		 $projectRoot = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $projectRoot );
	  		 $file = $this->download( $projectRoot, $id, $appId );

			 if( !$this->unzip( $projectRoot, $file ) )
	             throw new FrameworkException( 'Could not extract downloaded component \'' . $file . '\'.' );

	         $this->copyController( $projectRoot, $appId );

	         // Load database schema
	         $component_xml = $projectRoot . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'component.xml';
	         if( file_exists( $component_xml ) ) {
	
	         	 $orm_xml = $projectRoot . DIRECTORY_SEPARATOR . 'orm.xml';
		  		 $xml = simplexml_load_file( $component_xml );
	
		  		 if( isset( $xml->component->orm ) ) {

		  		 	 foreach( $xml->component->orm->table as $table ) {

				  		 	  $Table = new Table( $table );
				  		 	  ORM::getDialect()->createTable( $table );
			  		 }
		  		 }
	         }

	         return true;
	  }

	  #@RemoteMethod
	  public function setProperty( $componentId, $name, $value ) {

	  		 $path = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $componentId );
	  		 if( !file_exists( $path ) )
	  		 	 throw new FrameworkException( 'Component path not found \'' . $path . '\'.' );

	  		 $file = $path . DIRECTORY_SEPARATOR . 'component.xml';

	  		 $xml = simplexml_load_file( $file );

	  		 foreach( $xml->component->param as $param )
	  		 	if( $param->attributes()->name == $name )
	  		 		$param['value'] = $value;

	  		 $xml->asXML( $file );

	  		 return true;
	  }

	  /**
	   * Downloads a component to the web application components directory
	   * 
	   * @param $id The id of the application in OpenAppstore
	   * @param $appId The appId of the application in OpenAppstore
	   * @return The file path to the downloaded file
	   */
	  private function download( $projectRoot, $id, $appId ) {

	  		  $path = $projectRoot . DIRECTORY_SEPARATOR . 'components';
	  		  return $this->api->download( $id, $appId, $path );
	  }

	  /**
	   * Unzips a downloaded component
	   * 
	   * @param $file The file path of the archive to extract
	   * @return True if the archive was successfully extracted or false if on failure
	   */
	  private function unzip( $projectRoot, $file ) {

			  $zip = new ZipArchive();

              if( $zip->open( $file ) === TRUE ) {

                   $zip->extractTo( $projectRoot . DIRECTORY_SEPARATOR . 'components' );
                   $zip->close();

                   unlink( $file );
                   return true;
              }

              // try to unzip using shell as last resort
              exec( 'cd ' . $projectRoot . DIRECTORY_SEPARATOR . 'components; unzip ' . $file, $output );
              if( !$output ) return false;

              return unlink( $file );
	  }

	  /**
	   * Copies componentcontroller.php to the project/control directory if it exists
	   * 
	   * @param string $projectRoot The full file path to the project
	   * @param string $appId The appId of the component as it lives in the appstore.
	   * @return void
	   * @throws FrameworkException
	   */
	  private function copyController( $projectRoot, $appId ) {

	  		  $it = new RecursiveDirectoryIterator( $projectRoot . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $appId );
		 	  foreach( new RecursiveIteratorIterator( $it ) as $file ) {

		   	      	   if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

		   	      	   	   $thisFile = basename( $file );
				 		   if( $thisFile == $appId . '.phar' ) {

				 		   		if( !copy( $file, $projectRoot . DIRECTORY_SEPARATOR . 'control' . DIRECTORY_SEPARATOR . $appId . '.phar' ) )
				 		   			 throw new FrameworkException( 'Failed to copy phar component to project controller directory.' );

				 		   		if( !unlink( $file ) ) throw new FrameworkException( 'Failed to clean up downloaded component.' );
				 		   }
		   	      	   	   else if( $thisFile == $appId . '.php' ) {

				 		   		if( !copy( $file, $projectRoot . DIRECTORY_SEPARATOR . 'control' . DIRECTORY_SEPARATOR . $appId . '.php' ) )
				 		   			 throw new FrameworkException( 'Failed to copy component controller to project controller directory.' );

				 		   		if( !unlink( $file ) ) throw new FrameworkException( 'Failed to clean up downloaded component.' );
				 		   }
		   	      	   }
		 	  }
	  }
}
?>