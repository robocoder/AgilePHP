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
 * @package com.makeabyte.agilephp.ide.classes
 */

/**
 * Web service client for OpenAppstore.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.ide.classes
 */
class AppstoreAPI extends SoapClient {

	  private static $classmap = array(
			    'addAppToCategory' => 'addAppToCategory',
			    'addAppToCategoryResponse' => 'addAppToCategoryResponse',
			    'addAppToPlatform' => 'addAppToPlatform',
			    'addAppToPlatformResponse' => 'addAppToPlatformResponse',
			    'createUser' => 'createUser',
			    'userWS' => 'userWS',
			    'createUserResponse' => 'createUserResponse',
			    'deleteUser' => 'deleteUser',
			    'deleteUserResponse' => 'deleteUserResponse',
			    'download' => 'download',
			    'downloadResponse' => 'downloadResponse',
			    'getAppsByPlatformId' => 'getAppsByPlatformId',
			    'getAppsByPlatformIdResponse' => 'getAppsByPlatformIdResponse',
			    'appWS' => 'appWS',
			    'appTypeWS' => 'appTypeWS',
			    'currencyWS' => 'currencyWS',
			    'getAppsByUserId' => 'getAppsByUserId',
			    'getAppsByUserIdResponse' => 'getAppsByUserIdResponse',
			    'login' => 'login',
			    'loginResponse' => 'loginResponse',
			    'logout' => 'logout',
			    'logoutResponse' => 'logoutResponse',
			    'test' => 'test',
			    'testResponse' => 'testResponse'
	  );

	  public function __construct( $endpoint = 'http://appstore.makeabyte.com:8080/appstore/api?wsdl' ) { 

	  		 parent::__construct( $endpoint, array( 'classmap' => self::$classmap ) );
	  }

	  /**
	   * Tests the AppstoreAPI
	   * 
	   * @return String "AppstoreAPI Works!" is returned if everything is working correctly.
	   * @throws SoapFault
	   */
	  public function test() {

	  		 $input = new test();
			 $response = parent::test($input);

			 return (is_object($response)) ? $response->return : null; 
	  }
 
	  /**
	   * Authenticates to the web service
	   * 
	   * @param String $username Appstore API username
	   * @param String $password Appstore API password
	   * @param String $apikey Appstore API key
	   * @return Boolean True if successful, false otherwise
	   * @throws SoapFault
	   */
	  public function login( $username, $password, $apikey ) {

	  		 $o = new login();
			 $o->arg0 = $username;
			 $o->arg1 = $password;
			 $o->arg2 = $apikey;

			 $response = parent::login( $o );

			 return is_object( $response ) ? $response->return : false;
	  }

	  /**
	   * Returns an array of applications available for the specified platform
	   * 
	   * @param int $platformId The platform id to retrieve the applications for
	   * @return array A list of AppWS objects
	   */
	  public function getAppsByPlatform( $platformId ) {

	  		 $o = new getAppsByPlatformId();
		     $o->arg0 = $platformId;

		     $response = parent::getAppsByPlatformId($o);

		     return is_object( $response ) ? $response->return : false;
	  }

	  /**
	   * Downloads the specified application to the local disk
	   * 
	   * @param int $id The application unique identifier
	   * @param string $appId The appId
	   * @param string $path The directory to store the downloaded application
	   * @return boolean The file path to the download if successful, false otherwise
	   * @throws SoapFault
	   * @throws AgilePHP_Exception
	   */
	  public function download( $id, $appId, $path ) {

	  		 $o = new download();
	  		 $o->arg0 = $id;

	  		 try {
	  		 		$data = parent::download( $o );

			  		 if( is_object( $data ) ) {
		
				  		 $download = $path . DIRECTORY_SEPARATOR . $appId . '.zip';
				  		 $h = fopen( $download, 'w' );	  		  
				  		 fwrite( $h, $data->return );
				  		 fclose( $h );
		
				  		 return $download;
			  		 }
	  		 }
	  		 catch( SoapFault $e ) {
	  		 	
	  		 		throw new AgilePHP_Exception( $e->getMessage() );
	  		 }

	  		 throw new AgilePHP_Exception( 'Failed to download your component from the Appstore' );
	  }
}

// PHP classes corresponding to the data types in defined in WSDL
 
class addAppToCategory {
 
    /**
     * @var long
     */
    public $arg0;
 
    /**
     * @var long
     */
    public $arg1;
 
}
 
class addAppToCategoryResponse {
 
}
 
class addAppToPlatform {
 
    /**
     * @var long
     */
    public $arg0;
 
    /**
     * @var long
     */
    public $arg1;
 
}
 
class addAppToPlatformResponse {
 
}
 
class createUser {
 
    /**
     * @var (object)userWS
     */
    public $arg0;
 
}
 
class userWS {
 
    /**
     * @var boolean
     */
    public $apiEnabled;
 
    /**
     * @var string
     */
    public $apiKey;
 
    /**
     * @var dateTime
     */
    public $created;
 
    /**
     * @var long
     */
    public $id;
 
    /**
     * @var dateTime
     */
    public $lastLogin;
 
    /**
     * @var string
     */
    public $password;
 
    /**
     * @var string
     */
    public $username;
 
}
 
class createUserResponse {
 
}
 
class deleteUser {
 
    /**
     * @var long
     */
    public $arg0;
 
}
 
class deleteUserResponse {
 
}
 
class download {
 
    /**
     * @var long
     */
    public $arg0;
 
}
 
class downloadResponse {
 
    // You need to set only one from the following two vars
 
    /**
     * @var Plain Binary
     */
    public $return;
 
    /**
     * @var base64Binary
     */
    public $return_encoded;
 
 
}
 
class getAppsByPlatformId {
 
    /**
     * @var long
     */
    public $arg0;
 
}
 
class getAppsByPlatformIdResponse {
 
    /**
     * @var array[0, unbounded] of (object)appWS
     */
    public $return;
 
}
 
class appWS {
 
    /**
     * @var string
     */
    public $appId;
 
    /**
     * @var (object)appTypeWS
     */
    public $appType;
 
    /**
     * @var float
     */
    public $cost;
 
    /**
     * @var (object)currencyWS
     */
    public $currency;
 
    /**
     * @var string
     */
    public $description;
 
    /**
     * @var string
     */
    public $extension;
 
    /**
     * @var long
     */
    public $id;
 
    /**
     * @var string
     */
    public $name;
 
    /**
     * @var long
     */
    public $size;
 
    // You need to set only one from the following two vars
 
    /**
     * @var Plain Binary
     */
    public $source;
 
    /**
     * @var base64Binary
     */
    public $source_encoded;
 
 
}
 
class appTypeWS {
 
    /**
     * @var string
     */
    public $description;
 
    /**
     * @var long
     */
    public $id;
 
    /**
     * @var string
     */
    public $name;
 
}
 
class currencyWS {
 
    /**
     * @var string
     */
    public $code;
 
    /**
     * @var long
     */
    public $id;
 
    /**
     * @var string
     */
    public $symbol;
 
}
 
class getAppsByUserId {
 
    /**
     * @var long
     */
    public $arg0;
 
}
 
class getAppsByUserIdResponse {
 
    /**
     * @var array[0, unbounded] of (object)appWS
     */
    public $return;
 
}
 
class login {
 
    /**
     * @var string
     */
    public $arg0;
 
    /**
     * @var string
     */
    public $arg1;
 
    /**
     * @var string
     */
    public $arg2;
 
}
 
class loginResponse {
 
    /**
     * @var boolean
     */
    public $return;
 
}
 
class logout {
 
}
 
class logoutResponse {
 
    /**
     * @var boolean
     */
    public $return;
 
}
 
class test {
 
}
 
class testResponse {
 
    /**
     * @var string
     */
    public $return;
 
}
?>