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
	  public function getXmlAsNonAdmin() {

	  		 $client = new RestClient( $this->endpoint );
	  		 $client->authenticate( 'test', 'test' );
	  		 $response = $client->get();

	  		 echo $response;
	  		 
	  		 //PHPUnit_Framework_Assert::assertEquals( 401, $client->getResponseCode(), 'Failed to get HTTP 401 Unauthorized' );
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
	  public function postXMLgetXML() {

	  	     $client = new RestClient( $this->endpoint . '/phpunit2' );
	  	     $client->authenticate( 'admin', 'test' );
			 $client->setHeaders( array(
				'Accept: application/xml',
				'Content-Type: application/xml',
			 ));

			 $pm = PersistenceManager::getInstance( 'agilephp_test' );

			 $user = new User();
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
}
?>