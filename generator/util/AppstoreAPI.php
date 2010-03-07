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
 * Generated SOAP client by wsdl2php that is responsible for communication
 * to the OpenAppstore API.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.generator.util
 * @version 0.1a
 */
class AppstoreAPI extends SoapClient {

  private static $classmap = array(
                                    'download' => 'download',
                                    'downloadResponse' => 'downloadResponse',
                                    'test' => 'test',
                                    'testResponse' => 'testResponse',
                                    'login' => 'login',
                                    'loginResponse' => 'loginResponse',
                                    'logout' => 'logout',
                                    'logoutResponse' => 'logoutResponse',
                                    'createUser' => 'createUser',
                                    'userWS' => 'userWS',
                                    'createUserResponse' => 'createUserResponse',
                                    'deleteUser' => 'deleteUser',
                                    'deleteUserResponse' => 'deleteUserResponse',
                                    'getAppsByUserId' => 'getAppsByUserId',
                                    'getAppsByUserIdResponse' => 'getAppsByUserIdResponse',
                                    'appWS' => 'appWS',
                                    'appTypeWS' => 'appTypeWS',
                                    'currencyWS' => 'currencyWS',
                                    'getAppsByPlatformId' => 'getAppsByPlatformId',
                                    'getAppsByPlatformIdResponse' => 'getAppsByPlatformIdResponse',
                                    'addAppToCategory' => 'addAppToCategory',
                                    'addAppToCategoryResponse' => 'addAppToCategoryResponse',
                                    'addAppToPlatform' => 'addAppToPlatform',
                                    'addAppToPlatformResponse' => 'addAppToPlatformResponse',
                                   );

  public function AppstoreAPI($wsdl = "http://10.255.1.81:8080/appstore/api?wsdl", $options = array()) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }
    parent::__construct($wsdl, $options);
  }

  /**
   *  Adds an application to a category
   *
   * @param addAppToCategory $addAppToCategory
   * @return addAppToCategoryResponse
   */
  public function addAppToCategory(addAppToCategory $addAppToCategory) {
    return $this->__soapCall('addAppToCategory', array($addAppToCategory),       array(
            'uri' => 'http://webservice.appstore.makeabyte.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  Adds an application to a platform
   *
   * @param addAppToPlatform $addAppToPlatform
   * @return addAppToPlatformResponse
   */
  public function addAppToPlatform(addAppToPlatform $addAppToPlatform) {
    return $this->__soapCall('addAppToPlatform', array($addAppToPlatform),       array(
            'uri' => 'http://webservice.appstore.makeabyte.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   * Creates a new appstore user
   *
   * @param createUser $createUser
   * @return createUserResponse
   */
  public function createUser(createUser $createUser) {
    return $this->__soapCall('createUser', array($createUser),       array(
            'uri' => 'http://webservice.appstore.makeabyte.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   * Deletes an appstore user
   *
   * @param deleteUser $deleteUser
   * @return deleteUserResponse
   */
  public function deleteUser(deleteUser $deleteUser) {
    return $this->__soapCall('deleteUser', array($deleteUser),       array(
            'uri' => 'http://webservice.appstore.makeabyte.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   * Downloads an application from the appstore
   *
   * @param download $download
   * @return downloadResponse
   */
  public function download(download $download) {
    return $this->__soapCall('download', array($download),       array(
            'uri' => 'http://webservice.appstore.makeabyte.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   * Gets all applications for the specified platform
   *
   * @param getAppsByPlatformId $getAppsByPlatformId
   * @return getAppsByPlatformIdResponse
   */
  public function getAppsByPlatformId(getAppsByPlatformId $getAppsByPlatformId) {
    return $this->__soapCall('getAppsByPlatformId', array($getAppsByPlatformId),       array(
            'uri' => 'http://webservice.appstore.makeabyte.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   * Gets all applications for the specified user
   *
   * @param getAppsByUserId $getAppsByUserId
   * @return getAppsByUserIdResponse
   */
  public function getAppsByUserId(getAppsByUserId $getAppsByUserId) {
    return $this->__soapCall('getAppsByUserId', array($getAppsByUserId),       array(
            'uri' => 'http://webservice.appstore.makeabyte.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   * Authenticates to the appstore web service
   *
   * @param login $login
   * @return loginResponse
   */
  public function login(login $login) {
    return $this->__soapCall('login', array($login),       array(
            'uri' => 'http://webservice.appstore.makeabyte.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   * Logs out of the appstore web service
   *
   * @param logout $logout
   * @return logoutResponse
   */
  public function logout(logout $logout) {
    return $this->__soapCall('logout', array($logout),       array(
            'uri' => 'http://webservice.appstore.makeabyte.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   * Tests the appstore web service
   *
   * @param test $test
   * @return testResponse
   */
  public function test(test $test) {
    return $this->__soapCall('test', array($test),       array(
            'uri' => 'http://webservice.appstore.makeabyte.com/',
            'soapaction' => ''
           )
      );
  }

}

class download {
  public $id; // long
}

class downloadResponse {
  public $return; // base64Binary
}

class test {
}

class testResponse {
  public $return; // string
}

class login {
  public $username; // string
  public $password; // string
  public $apiKey; // string
}

class loginResponse {
  public $return; // boolean
}

class logout {
}

class logoutResponse {
  public $return; // boolean
}

class createUser {
  public $user; // userWS
}

class userWS {
  public $apiEnabled; // boolean
  public $apiKey; // string
  public $created; // dateTime
  public $id; // long
  public $lastLogin; // dateTime
  public $password; // string
  public $username; // string
}

class createUserResponse {
}

class deleteUser {
  public $userId; // long
}

class deleteUserResponse {
}

class getAppsByUserId {
  public $id; // long
}

class getAppsByUserIdResponse {
  public $return; // appWS
}

class appWS {
  public $appId; // string
  public $appType; // appTypeWS
  public $cost; // float
  public $currency; // currencyWS
  public $description; // string
  public $extension; // string
  public $id; // long
  public $name; // string
  public $size; // long
  public $source; // base64Binary
}

class appTypeWS {
  public $description; // string
  public $id; // long
  public $name; // string
}

class currencyWS {
  public $code; // string
  public $id; // long
  public $symbol; // string
}

class getAppsByPlatformId {
  public $id; // long
}

class getAppsByPlatformIdResponse {
  public $return; // appWS
}

class addAppToCategory {
  public $appId; // long
  public $categoryId; // long
}

class addAppToCategoryResponse {
}

class addAppToPlatform {
  public $appId; // long
  public $platformId; // long
}

class addAppToPlatformResponse {
}
?>