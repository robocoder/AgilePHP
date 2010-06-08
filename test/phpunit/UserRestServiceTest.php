<?php
/**
 * @package com.makeabyte.agilephp.test.webservice.rest
 */
class RestTests extends PHPUnit_Framework_TestCase {

	  private $endpoint = 'http://localhost/test/index.php/users';

	  /**
	   * @test
	   * @expectedException RestClientException
	   */
	  public function getXmlUnauthenticated() {

	  		 $client = new RestClient( $this->endpoint );
	  		 $response = $client->get();

	  		 PHPUnit_Framework_Assert::assertEquals( 401, $client->getResponseCode(), 'Failed to get HTTP 401 Unauthorized' );
	  }

	  /**
	   * @test
	   */
	  public function getXml() {

	  		 $client = new RestClient( $this->endpoint );
	  		 $client->authenticate( 'admin', 'test' );
	  		 $response = $client->get();
	  		 
	  		 $xml = simplexml_load_string( $response );
	  		 PHPUnit_Framework_Assert::assertNotNull( $response, 'Failed to get a response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertNotNull( $xml, 'Failed to get XML response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertType( 'SimpleXMLElement', $xml, 'Failed to convert response to SimpleXMLElement' );
	  		 PHPUnit_Framework_Assert::assertEquals( 200, $client->getResponseCode(), 'Failed to get HTTP 200 OK' );
	  }

	  /**
	   * @test
	   * @expectedException RestClientException
	   */
	  public function getJson() {

			 $client = new RestClient( $this->endpoint . '/test' );
			 $client->authenticate( 'admin', 'test' );
			 $client->setHeaders( array(
				'Accept: application/json',
				'Content-Type: application/json',
			 ));
			 $client->put( 'this is some data that should never make it to the server because the service has a #@ProduceMime which we arent accepting.' );
	  }

	  /**
	   * @test
	   */
	  public function putXMLgetXML() {

	  		 $client = new RestClient( $this->endpoint . '/test' );
	  		 $client->authenticate( 'admin', 'test' );
			 $client->setHeaders( array(
				'Accept: application/xml',
				'Content-Type: application/xml',
			 ));

			 $pm = PersistenceManager::getInstance( 'agilephp_test' );

			 $username = 'phpunit';
			 $password = 'test';
			 $email = 'root@localhost';
			 $enabled = true;
			 $newRole = 'test';
			 $created = 'now';

			 $user = new User();
			 $user->setUsername( $username );
			 $user->setPassword( $password );
			 $user->setCreated( $created );
			 $user->setEmail( $email );

			 $pm->persist( $user );

			 // Load the test user
			 $user = new User();
			 $user->setUsername( $username );
			 $user->setPassword( 'test2' );
			 $user->setEmail( 'root@localhost.localdomain' );
			 $user->setEnabled( false );

			 // Create new role
			 $role = new Role();
			 $role->setName( $newRole );

			 // Assign admin role to test account
			 $user->setRole( $role );

			 // The server will be expecting XML as indicated in the Content-Type header above
			 $renderer = new AJAXRenderer();
			 $data = $renderer->toXML( $user );

			 $response = $client->put( $data );

			 // clean up after unit test
			 $pm->delete( $user );

			 $xml = simplexml_load_string( $response );
	  		 PHPUnit_Framework_Assert::assertNotNull( $response, 'Failed to get a response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertNotNull( $xml, 'Failed to get XML response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertType( 'SimpleXMLElement', $xml, 'Failed to convert response to SimpleXMLElement' );
	  		 PHPUnit_Framework_Assert::assertEquals( $username, (string)$xml->username, 'Expected username \'' . $username . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'root@localhost.localdomain', (string)$xml->email, 'Expected email \'' . $email . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( $newRole, (string)$xml->Role->name, 'Expected role \'' . $newRole . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 202, $client->getResponseCode(), 'Failed to get HTTP 202 Accepted' );
	  }

	  /**
	   * @test
	   */
	  public function putJSONgetJSON() {

	  		 $client = new RestClient( $this->endpoint . '/test/json' );
	  		 $client->authenticate( 'admin', 'test' );
			 $client->setHeaders( array(
				'Accept: application/json',
				'Content-Type: application/json',
			 ));

			 $pm = PersistenceManager::getInstance( 'agilephp_test' );

			 $username = 'phpunit-json';
			 $password = 'test';
			 $email = 'root@localhost';
			 $enabled = true;
			 $newRole = 'test';
			 $created = 'now';

			 $user = new User();
			 $user->setUsername( $username );
			 $user->setPassword( $password );
			 $user->setCreated( $created );
			 $user->setEmail( $email );

			 $pm->persist( $user );

			 // Load the test user
			 $user = new User();
			 $user->setUsername( $username );
			 $user->setPassword( 'test2' );
			 $user->setEmail( 'root@localhost.localdomain' );
			 $user->setEnabled( false );

			 // Create new role
			 $role = new Role();
			 $role->setName( $newRole );

			 // Assign admin role to test account
			 $user->setRole( $role );

			 // The server will be expecting XML as indicated in the Content-Type header above
			 $renderer = new AJAXRenderer();
			 $data = $renderer->toJSON( $user );

			 $response = $client->put( $data );

			 // clean up after unit test
			 $pm->delete( $user );

			 $json = json_decode( $response );
			 PHPUnit_Framework_Assert::assertType( 'stdClass', $json, 'Failed to decode JSON data' );
			 PHPUnit_Framework_Assert::assertNotNull( $response, 'Failed to get a response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertNotNull( $json, 'Failed to get JSON response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertEquals( $username, $json->User->username, 'Expected username \'' . $username . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'root@localhost.localdomain', $json->User->email, 'Expected email \'' . $email . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( $newRole, $json->User->Role->name, 'Expected role \'' . $newRole . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 202, $client->getResponseCode(), 'Failed to get HTTP 202 Accepted' );
	  }
	  
	  /**
	   * @test
	   */
	  public function putJSONgetXML() {

	  		 $client = new RestClient( $this->endpoint . '/test/wildcard' );
	  		 $client->authenticate( 'admin', 'test' );
			 $client->setHeaders( array(
				'Accept: application/xml',
				'Content-Type: application/json',
			 ));

			 $pm = PersistenceManager::getInstance( 'agilephp_test' );

			 $username = 'phpunit-json2';
			 $password = 'test';
			 $email = 'root@localhost';
			 $enabled = true;
			 $newRole = 'test';
			 $created = 'now';

			 $user = new User();
			 $user->setUsername( $username );
			 $user->setPassword( $password );
			 $user->setCreated( $created );
			 $user->setEmail( $email );

			 $pm->persist( $user );

			 // Load the test user
			 $user = new User();
			 $user->setUsername( $username );
			 $user->setPassword( 'test2' );
			 $user->setEmail( 'root@localhost.localdomain' );
			 $user->setEnabled( false );

			 // Create new role
			 $role = new Role();
			 $role->setName( $newRole );

			 // Assign admin role to test account
			 $user->setRole( $role );

			 // The server will be expecting XML as indicated in the Content-Type header above
			 $renderer = new AJAXRenderer();
			 $data = $renderer->toJSON( $user );

			 $response = $client->put( $data );

			 // clean up after unit test
			 $pm->delete( $user );

			 $xml = simplexml_load_string( $response );
	  		 PHPUnit_Framework_Assert::assertNotNull( $response, 'Failed to get a response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertNotNull( $xml, 'Failed to get XML response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertType( 'SimpleXMLElement', $xml, 'Failed to convert response to SimpleXMLElement' );
	  		 PHPUnit_Framework_Assert::assertEquals( $username, (string)$xml->username, 'Expected username \'' . $username . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'root@localhost.localdomain', (string)$xml->email, 'Expected email \'' . $email . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( $newRole, (string)$xml->Role->name, 'Expected role \'' . $newRole . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 202, $client->getResponseCode(), 'Failed to get HTTP 202 Accepted' );
	  }
	  
	  /**
	   * Starting to get confusing now... Using /test/wildcard to put JSON and get YAML.
	   * Since the resource method does not have a #@ConsumeMime or #@ProduceMime, the HTTP
	   * Accept and Content-Type headers are used to negotiate the data transformation/exchange.
	   * 
	   * @test
	   */
	  public function putJSONgetYAML() {

	  		 $client = new RestClient( $this->endpoint . '/test/wildcard' );
	  		 $client->authenticate( 'admin', 'test' );
			 $client->setHeaders( array(
				'Accept: application/x-yaml',
				'Content-Type: application/json',
			 ));

			 $pm = PersistenceManager::getInstance( 'agilephp_test' );

			 $username = 'phpunit-json2';
			 $password = 'test';
			 $email = 'root@localhost';
			 $enabled = true;
			 $newRole = 'test';
			 $created = 'now';

			 $user = new User();
			 $user->setUsername( $username );
			 $user->setPassword( $password );
			 $user->setCreated( $created );
			 $user->setEmail( $email );

			 $pm->persist( $user );

			 // Load the test user
			 $user = new User();
			 $user->setUsername( $username );
			 $user->setPassword( 'test2' );
			 $user->setEmail( 'root@localhost.localdomain' );
			 $user->setEnabled( false );

			 // Create new role
			 $role = new Role();
			 $role->setName( $newRole );

			 // Assign admin role to test account
			 $user->setRole( $role );

			 // The server will be expecting XML as indicated in the Content-Type header above
			 $renderer = new AJAXRenderer();
			 $data = $renderer->toJSON( $user );

			 $response = $client->put( $data );

			 // clean up after unit test
			 $pm->delete( $user );

			 $yaml = yaml_parse( $response );
	  		 PHPUnit_Framework_Assert::assertNotNull( $response, 'Failed to get a response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertNotNull( $yaml, 'Failed to get YAML response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertType( 'User', $yaml, 'Failed to convert response to YAML' );
	  		 PHPUnit_Framework_Assert::assertEquals( $username, $yaml->getUsername(), 'Expected username \'' . $username . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'root@localhost.localdomain', $yaml->getEmail(), 'Expected email \'' . $email . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( $newRole, $yaml->getRole()->getName(), 'Expected role \'' . $newRole . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 202, $client->getResponseCode(), 'Failed to get HTTP 202 Accepted' );
	  }

	  /**
	   * Same confusing story... but this time put YAML and get XML back
	   * 
	   * @test
	   */
	  public function putYAMLgetXML() {

	  		 $client = new RestClient( $this->endpoint . '/test/wildcard' );
	  		 $client->authenticate( 'admin', 'test' );
			 $client->setHeaders( array(
				'Accept: application/xml',
				'Content-Type: application/x-yaml',
			 ));

			 $pm = PersistenceManager::getInstance( 'agilephp_test' );

			 $username = 'phpunit-json2';
			 $password = 'test';
			 $email = 'root@localhost';
			 $enabled = true;
			 $newRole = 'test';
			 $created = 'now';

			 $user = new User();
			 $user->setUsername( $username );
			 $user->setPassword( $password );
			 $user->setCreated( $created );
			 $user->setEmail( $email );

			 $pm->persist( $user );

			 // Load the test user
			 $user = new User();
			 $user->setUsername( $username );
			 $user->setPassword( 'test2' );
			 $user->setEmail( 'root@localhost.localdomain' );
			 $user->setEnabled( false );

			 // Create new role
			 $role = new Role();
			 $role->setName( $newRole );

			 // Assign admin role to test account
			 $user->setRole( $role );

			 // The server will be expecting XML as indicated in the Content-Type header above
			 $renderer = new AJAXRenderer();
			 $data = $renderer->toYAML( $user );

			 $response = $client->put( $data );

			 // clean up after unit test
			 $pm->delete( $user );

			 $xml = simplexml_load_string( $response );
	  		 PHPUnit_Framework_Assert::assertNotNull( $response, 'Failed to get a response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertNotNull( $xml, 'Failed to get XML response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertType( 'SimpleXMLElement', $xml, 'Failed to convert response to SimpleXMLElement' );
	  		 PHPUnit_Framework_Assert::assertEquals( $username, (string)$xml->username, 'Expected username \'' . $username . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'root@localhost.localdomain', (string)$xml->email, 'Expected email \'' . $email . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( $newRole, (string)$xml->Role->name, 'Expected role \'' . $newRole . '\'.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 202, $client->getResponseCode(), 'Failed to get HTTP 202 Accepted' );
	  }

	  /**
	   * @test
	   */
	  public function postXMLgetXML() {

	  	     $client = new RestClient( $this->endpoint . '/phpunit2' );
	  	     $client->authenticate( 'admin', 'test' );
			 $client->setHeaders( array(
				'Accept: application/xml',
				'Content-Type: application/xml',
			 ));

			 $pm = PersistenceManager::getInstance( 'agilephp_test' );

			 $user = new User();
			 $user->setUsername( 'phpunit2' );
			 $user->setPassword( 'test' );
			 $user->setCreated( 'now' );
			 $user->setEmail( 'root@localhost' );
			 $user->setRoleId( 'test' );
			 $user->setEnabled( false );

			 // Create new role
			 $role = new Role();
			 $role->setName( 'test' );

			 // Assign admin role to test account
			 $user->setRole( $role );

			 // The server will be expecting XML as indicated in the Content-Type header above
			 $renderer = new AJAXRenderer();
			 $data = $renderer->toXML( $user );

			 $response = $client->post( $data );

			 // clean up after unit test
			 $user->setUsername( 'phpunit2' );
			 $pm->delete( $user );

			 $xml = simplexml_load_string( $response );
	  		 PHPUnit_Framework_Assert::assertNotNull( $response, 'Failed to get a response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertNotNull( $xml, 'Failed to get XML response from the REST service.' );
	  		 PHPUnit_Framework_Assert::assertType( 'SimpleXMLElement', $xml, 'Failed to convert response to SimpleXMLElement' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'phpunit2', (string)$xml->username, 'Expected username phpunit2.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'root@localhost', (string)$xml->email, 'Expected email root@localhost.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'test', (string)$xml->Role->name, 'Expected role test.' );
	  		 PHPUnit_Framework_Assert::assertEquals( 201, $client->getResponseCode(), 'Failed to get HTTP 201 Created' );
	  }

	  /**
	   * @test
	   */
	  public function deleteXMLgetXML() {

	  	     $pm = PersistenceManager::getInstance( 'agilephp_test' );

	  		 $user = new User();
	  		 $user->setUsername( 'phpunit3' );
	  		 $user->setPassword( 'test' );
	  		 $user->setEmail( 'root@localhost' );
	  		 $user->setCreated( 'now' );
	  		 
	  		 $role = new Role();
	  		 $role->setName( 'test' );

	  		 $user->setRole( $role );
			 $pm->persist( $user );

	  	     $client = new RestClient( $this->endpoint . '/phpunit3' );
	  	     $client->authenticate( 'admin', 'test' );
			 $response = $client->delete();

	  		 PHPUnit_Framework_Assert::assertEquals( 204, $client->getResponseCode(), 'Failed to get HTTP 204 no content' );
	  }

	  /**
	   * @test
	   */
	  public function transformXML() {

	  		 $data = '<?xml version="1.0" encoding="UTF-8" ?><User><username>admin</username><password>9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08</password><email>root@localhost</email><created>2009-09-06 15:27:44</created><lastLogin>1969-12-31 19:00:00</lastLogin><roleId>admin</roleId><sessionId>WL10Dc97YUI53v0qT92a0</sessionId><enabled>1</enabled><Session><id>WL10Dc97YUI53v0qT92a0</id><data>a:2:{s:17:"IDENTITY_LOGGEDIN";b:1;s:17:"IDENTITY_USERNAME";s:5:"admin";}</data><created>2010-06-07 19:13:10</created></Session><Role><name>admin</name><description>This is an administrator account</description></Role></User>';

	  		 $t = new XMLTransformer();
			 $o = $t->transform( $data );

			 PHPUnit_Framework_Assert::assertType( 'User', $o, 'Failed to transform XML data to PHP object' );
	  }

	  /**
	   * @test
	   */
	  public function transformJSON() {
	  	
	  		 $data = '{ "User" : { "username" : "admin", "password" : "9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08", "email" : "root@localhost", "created" : "2009-09-06 15:27:44", "lastLogin" : "1969-12-31 19:00:00", "roleId" : "admin", "sessionId" : "178Pgrib43Zw0Awlz7qj5", "enabled" : "1", "Session" : { "id" : "178Pgrib43Zw0Awlz7qj5", "data" : "a:2:{s:17:\"IDENTITY_LOGGEDIN\";b:1;s:17:\"IDENTITY_USERNAME\";s:5:\"admin\";}", "created" : "2010-06-07 19:14:00"} , "Role" : { "name" : "admin", "description" : "This is an administrator account"} , "Roles" : null } }';
	  		 
	  		 $t = new JSONTransformer();
	  		 $o = $t->transform( $data );

	  		 PHPUnit_Framework_Assert::assertType( 'User', $o, 'Failed to transform JSON data to PHP object' );
	  }
	  
	  /**
	   * @test
	   */
	  public function transformYAML() {
	  	
	  		 $data = '--- !php/object "O:4:\"User\":1:{s:12:\"\0User\0object\";O:16:\"User_Intercepted\":11:{s:26:\"\0User_Intercepted\0username\";s:5:\"admin\";s:26:\"\0User_Intercepted\0password\";s:64:\"9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08\";s:23:\"\0User_Intercepted\0email\";s:14:\"root@localhost\";s:25:\"\0User_Intercepted\0created\";s:19:\"2009-09-06
  15:27:44\";s:27:\"\0User_Intercepted\0lastLogin\";s:19:\"1969-12-31 19:00:00\";s:24:\"\0User_Intercepted\0roleId\";s:5:\"admin\";s:27:\"\0User_Intercepted\0sessionId\";s:21:\"178Pgrib43Zw0Awlz7qj5\";s:25:\"\0User_Intercepted\0enabled\";s:1:\"1\";s:25:\"\0User_Intercepted\0Session\";O:7:\"Session\":3:{s:11:\"\0Session\0id\";s:21:\"178Pgrib43Zw0Awlz7qj5\";s:13:\"\0Session\0data\";s:72:\"a:2:{s:17:\"IDENTITY_LOGGEDIN\";b:1;s:17:\"IDENTITY_USERNAME\";s:5:\"admin\";}\";s:16:\"\0Session\0created\";s:19:\"2010-06-07
  19:14:00\";}s:22:\"\0User_Intercepted\0Role\";O:4:\"Role\":1:{s:12:\"\0Role\0object\";O:16:\"Role_Intercepted\":2:{s:22:\"\0Role_Intercepted\0name\";s:5:\"admin\";s:29:\"\0Role_Intercepted\0description\";s:32:\"This
  is an administrator account\";}}s:23:\"\0User_Intercepted\0Roles\";N;}}"
...';

	  		  $t = new YAMLTransformer();
	  		  $o = $t->transform( $data );

	  		  PHPUnit_Framework_Assert::assertType( 'User', $o, 'Failed to transform YAML data to PHP object' );
	  }
}
?>