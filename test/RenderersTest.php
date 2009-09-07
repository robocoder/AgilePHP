<?php

require_once 'BaseTest.php';

class RenderersTest extends BaseTest {

	  public function test_AJAXRenderer_XML() {

	  		 $renderer = MVC::getInstance()->createRenderer( 'AJAXRenderer' );
	  		 $renderer->setOutput( 'xml' );
	  		 $renderer->render( $this->getMockObject() );
	  		 
	  		 echo "\n\n<br><br>\n\n";
	  }

	  public function test_AJAXRenderer_JSON() {

	  		 $renderer = MVC::getInstance()->createRenderer( 'AJAXRenderer' );
	  		 $renderer->setOutput( 'json' );
	  		 $renderer->render( $this->getMockObject() );
	  }

	  private function getMockObject() {
	  	
	  		  $role = new Role();
	  		  $role->setName( 'test' );
	  		  
	  		  $role2 = new Role();
	  		  $role2->setName( 'newtest' );
	  		  
	  		  $roles = array( $role, $role2 );

	  		  $user = new User();
	  		  $user->setUsername( 'test' );
	  		  $user->setPassword( '123abc' );
	  		  $user->setCreated( date( 'c', strtotime( 'now' ) ) );
	  		  $user->setLastLogin( date( 'c', strtotime( 'now' ) ) );
	  		  $user->setEmail( 'jeremy.hahn@makeabyte.com' );
	  		  $user->setRole( $role );
	  		  $user->setRoles( $roles );

	  		  return $user;
	  }
}
?>