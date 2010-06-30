<?php
/**
 * @package com.makeabyte.agilephp.test.orm
 */
class SQLiteTest extends PHPUnit_Framework_TestCase {

	  /**
	   * @test
	   */
	  public function coreTests() {

	  		 $orm = ORMFactory::load( AgilePHP::getFramework()->getWebRoot() . '/orm_sqlite_test.xml' );

	  		 $orm->create();	// create the unit testing database agilephp_test_mysql

	  		 // persist a new user, role, and session
	  	     $user = new User();
	  	     $user->setUsername( 'phpunit' );
	  	     $user->setPassword( 'phpunit123' );
	  	     $user->setEmail( 'phpunit@localhost' );
	  	     $user->setCreated( 'now' );
	  	     $user->setEnabled( 0 );

	  	     $role = new Role();
	  	     $role->setName( 'phpunit' );
	  	     $role->setDescription( 'Users who are used for phpunit testing' );

	  	     $session = Scope::getSessionScope();
	  	     $sessionId = $session->getSessionId();
	  	     $session->set( 'phpunit', 'this is some test data being stored in the session' );

	  	     $user->setRole( $role );
	  	     $user->setSession( $session->getSession() );

	  	     $o = $orm->persist( $user );

	  	     PHPUnit_Framework_Assert::assertType( 'PDOStatement', $o, 'Failed to persist new user' );

	  	     // tests looking up the user using the model state to define search criteria
	  	     $result = $orm->find( new User( 'phpunit' ) );
	  	     $phpunit = $result[0];

	  	     $crypto = new Crypto();
	  	     $digest = $crypto->getDigest( 'phpunit123' );

	  	     PHPUnit_Framework_Assert::assertEquals( 'phpunit', $phpunit->getUsername(), 'Failed to find persisted username' );
	  	     PHPUnit_Framework_Assert::assertEquals( $digest, $phpunit->getPassword(), 'Failed to find persisted username' );
	  	     PHPUnit_Framework_Assert::assertEquals( 'phpunit@localhost', $phpunit->getEmail(), 'Failed to find persisted email' );
	  	     PHPUnit_Framework_Assert::assertEquals( false, $phpunit->getEnabled(), 'Failed to find persisted enabled flag' );
	  	     PHPUnit_Framework_Assert::assertEquals( 'phpunit', $phpunit->getRole()->getName(), 'Failed to find persisted role' );
	  	     PHPUnit_Framework_Assert::assertEquals( $sessionId, $phpunit->getSession()->getId(), 'Failed to find persisted session' );

	  	     // now update the users role and email address
	  	     $user2 = new User( 'phpunit' );
	  	     $user2->setPassword( 'phpunit123' );
	  	     $user2->setEmail( 'phpunit2@localhost' );
	  	     $user2->setCreated( $user->getCreated() );
	  	     $user2->setEnabled( 1 );

	  	     $role2 = new Role();
	  	     $role2->setName( 'test' );
	  	     $role2->setDescription( 'This is another unit testing role' );
	  	     $orm->persist( $role2 );

	  	     $user2->setRole( $role2 );

	  	     $o2 = $orm->merge( $user2 );

	  	     PHPUnit_Framework_Assert::assertType( 'PDOStatement', $o2, 'Failed to merge user' );

	  	     // tests looking up the user using "restrictions" suite
  	         $orm->setRestrictions( array( 'username' => 'phpunit', 'roleId' => 'test' ) );
	  		 $result = ORM::find( new User() );
	  		 $phpunit2 = $result[0];

	  		 PHPUnit_Framework_Assert::assertNotNull( $result, 'Error finding merged record using restrictions logic' );
	  		 PHPUnit_Framework_Assert::assertEquals( $phpunit2->getUsername(), 'phpunit', 'Error finding merged record using restrictions logic' );
	  	     PHPUnit_Framework_Assert::assertEquals( $digest, $phpunit2->getPassword(), 'Failed to find merged username' );
	  	     PHPUnit_Framework_Assert::assertEquals( 'phpunit2@localhost', $phpunit2->getEmail(), 'Failed to find merged email' );
	  	     PHPUnit_Framework_Assert::assertEquals( 'test', $phpunit2->getRole()->getName(), 'Failed to find merged role' );
	  	     PHPUnit_Framework_Assert::assertEquals( 1, $phpunit2->getEnabled(), 'Failed to find merged enabled flag' );

	  	     // test delete 
	  	     $orm->delete( $user2 );
	  	     $user3 = $orm->find( $user2 );
	  	     PHPUnit_Framework_Assert::assertEquals( false, isset( $user3[0] ), 'Failed to delete user3' );

	  	     // test reverse engineer
	  	     $Database = $orm->reverseEngineer();
	  	     $tables = $Database->getTables();
	  	     PHPUnit_Framework_Assert::assertEquals( 'agilephp_test_sqlite', $Database->getName(), 'Failed to reverse engineer database name' );
	  	     PHPUnit_Framework_Assert::assertEquals( 'sqlite', $Database->getType(), 'Failed to reverse engineer database type' );
	  	     PHPUnit_Framework_Assert::assertType( 'array', $tables, 'Failed to reverse engineer database tables' );
	  	     foreach( $tables as $table ) {

	  	     	PHPUnit_Framework_Assert::assertNotNull( $table->getName(), 'Failed to reverse engineer database table name' );

	  	     	foreach( $table->getColumns() as $column )
	  	     		PHPUnit_Framework_Assert::assertNotNull( $column->getName(), 'Failed to reverse engineer database column name' );
	  	     }

	  	     // destroy agilephp_test_mysql database
	  	     $orm->drop();
	  }

	  /* tests utility operations used by various components */

	  public function testGetTableByModel() {

	  		 $user = new User();
	  		 $table = ORM::getTableByModel( $user );

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'getTableByModel returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getTableByModel' );
	  }

	  public function testGetTableByModelName() {

	  		 $table = ORM::getTableByModelName( 'User' );

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'getTableByModelName returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getTableByModelName' );
	  }
	  
	  public function testGetTableByName() {

	  		 $table = ORM::getTableByName( 'roles' );

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'getTableByName returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'roles', $table->getName(), 'Failed to getTableByName' );
	  }

	  public function testGetPrimaryKeyColumns() {

	  		 $table = ORM::getTableByName( 'roles' );
	  		 $columns = $table->getPrimaryKeyColumns();

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'testGetPrimaryKeyColumns \'getTableByName\' returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'roles', $table->getName(), 'Failed to getPrimaryKeyColumns' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'name', $columns[0]->getName(), 'Failed to locate \'name\'.' );
	  }

	  public function testGetForeignKeyColumns() {

	  		 $table = ORM::getTableByName( 'users' );
	  		 $columns = $table->getForeignKeyColumns();

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'testGetForeignKeyColumns \'getTableByName\' returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getForeignKeyColumns' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'roleId', $columns[0]->getName(), 'Failed to locate foreign key column \'roleId\'.' );
	  }

	  public function testGetForeignKeys() {

	  		 $table = ORM::getTableByName( 'users' );
	  		 $columns = $table->getForeignKeyColumns();

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'testGetForeignKeys \'getTableByName\' returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to testGetForeignKeys' );

	 		 $fKey = $columns[0]->getForeignKey();

  	 		 PHPUnit_Framework_Assert::assertEquals( 'FK_UserRoles', $fKey->getName(), 'testGetForeignKeys failed to get foreign key name \'FK_UserRoles\'' );
  	 		 PHPUnit_Framework_Assert::assertEquals( 'many-to-one', $fKey->getType(), 'testGetForeignKeys failed to get foreign key type \'many-to-one\'' );
  	 		 PHPUnit_Framework_Assert::assertEquals( 'roles', $fKey->getReferencedTable(), 'testGetForeignKeys failed to get foreign key table \'roles\'' );
  	 		 PHPUnit_Framework_Assert::assertEquals( 'name', $fKey->getReferencedColumn(), 'testGetForeignKeys failed to get referenced column \'name\'' );
	  }

	  public function testGetForeignKeyTableAndColumnInstances() {

	  		 $table = ORM::getTableByName( 'users' );
	  		 $columns = $table->getForeignKeyColumns();

	  		 foreach( $columns as $column ) {

	  		 		  $fkey = $column->getForeignKey();

	  		 		  PHPUnit_Framework_Assert::assertNotNull( $fkey->getReferencedTableInstance(), 'testGetForeignKeyTableAndColumnInstances failed to get table instance' );
	  		 		  PHPUnit_Framework_Assert::assertNotNull( $fkey->getReferencedColumnInstance(), 'testGetForeignKeyTableAndColumnInstances failed to get column instance' );	  		 		  
	  		 }
	  }
}
?>