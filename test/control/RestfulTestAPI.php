<?php

class RestfulTestAPI extends BaseController {

	  public function __construct() {

	  		 parent::__construct();
	  		 $this->createRenderer( 'AJAXRenderer' );
	  		 $this->getRenderer()->setOutput( 'xml' );
	  }

	  public function index() {

	  		 $user = new User();
	  		 $user->setUsername( 'admin' );

	  		 // return $user; #PHP 5.3+ supports ReflectionProperty::setAccessible and can use AJAXRenderer to render private properties.

	  		 // Before PHP 5.3, ReflectionProperty::setAccessible doesnt exist
	  		 // and therefore AJAXRenderer can not render the above object
	  		 // to XML since its properties are all private. This is a tedious
	  		 // workaround for older versions of PHP.
	  		 $o = new stdClass();
	  		 $o->username = $user->getUsername();
	  		 $o->password = $user->getPassword();
	  		 $o->email = $user->getEmail();
	  		 $o->created = $user->getCreated();
	  		 $o->lastLogin = $user->getLastLogin();

	  		 $this->getRenderer()->render( $o );
	  }

	  public function getRole( $name ) {

	  		 $role = new Role();
	  		 $role->setName( $name );

	  		 $o = new stdClass;
	  		 $o->name = $role->getName();
	  		 $o->description = $role->getDescription();

	  		 $this->getRenderer()->render( $o );
	  }

	  public function getRoles() {

	  		 $pm = new PersistenceManager();
	  		 $roles = $pm->find( new Role() );
	  		 $retval = array();

	  		 foreach( $roles as $role ) {

		  		 $o = new stdClass;
		  		 $o->name = $role->getName();
		  		 $o->description = $role->getDescription();

	  		 	 /*
	  		 	  * This could also be a multi-dimensional array and it would the same way
	  		 	 $o = array();
	  		 	 $o['name'] = $role->getName();
	  		 	 $o['description'] = $role->getDescription();
	  		 	 */

		  		 array_push( $retval, $o );
	  		 }

	  		 $this->getRenderer()->render( $retval, 'Role' );
	  }
}
?>