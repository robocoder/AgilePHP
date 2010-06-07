<?php
/**
 * @package com.makeabyte.agilephp.test.persistence
 */
class SQLiteTest extends BaseTest {

	  private $persistence;

	  public function __construct() {

	  		 parent::__construct();
	  		 $this->persistence = PersistenceManager::getInstance( 'testcase_sqlite' );
	  }

	  public function testPersistenceNotNull() {

	  	     PHPUnit_Framework_Assert::assertNotNull( $this->persistence, "Failed to create persistence manager for testcase_sqlite" );
	  }

	  public function testCreate() {

	  		 $this->persistence->create();
	  }

	  public function testCreateTable() {

	  		 $this->persistence->createTable( $this->getMockTable() );
	  }

	  public function testDropTable() {

	  		 $this->persistence->dropTable( $this->getMockTable() );

	  		 try {
	  		 		$this->persistence->query( 'desc ' . $this->getMockTable()->getName() . ';' );
	  		 }
	  		 catch( PersistenceException $e ) {

	  		 		if( preg_match( '/doesn\'t exist/', $e->getMessage() ) ) return;
	  		 		PHPUnit_Framework_Assert::fail( $e->getMessage() );
	  		 }
	  		 catch( Exception $e ) {

	  		 		PHPUnit_Framework_Assert::fail( $e->getMessage() );
	  		 }
	  }

	  public function testConstraint() {

	  		 try {
	  		 	$user = $this->getMockData();
	  		 	$this->persistence->persist( $user );
	  		 }
	  		 catch( PersistenceException $e ) {

	  		 		if( preg_match( '/violates foreign key constraint/', $e->getMessage() ) ) return;
	  		 		PHPUnit_Framework_Assert::fail( $e->getMessage() );
	  		 }

	  		 PHPUnit_Framework_Assert::fail( 'Constraint test failed' );
	  }
	  
	  public function testPersist() {

	  		 $user = $this->getMockData();

	  		 $this->persistence->persist( $this->getMockRole() );

	  		 $this->persistence->persist( $user );
			 $persisted = $this->persistence->find( $user );

			 PHPUnit_Framework_Assert::assertNotNull( $persisted, 'Error persisting new record' );
	  }
	  
	  public function testFind() {
	  	
	  		 $user = $this->getMockData();
			 $persisted = $this->persistence->find( $user );
			 PHPUnit_Framework_Assert::assertNotNull( $persisted, 'Error finding persisted record' );
	  }

	  public function testRestrictedFind() {

	  		 $this->persistence->setRestrictions( array( 'username' => 'phpunit' ) );
	  		 $persisted = $this->persistence->find( new User() );

	  		 PHPUnit_Framework_Assert::assertNotNull( $persisted, 'Error finding persisted record using restrictions logic' );
	  		 PHPUnit_Framework_Assert::assertEquals( $persisted[0]->getUsername(), 'phpunit', 'Error finding persisted record using restrictions logic' );
	  }

	  public function testMerge() {
	  	
	  		 $user = $this->getMockData();
	  		 $user->setPassword( 'new password' );
	  		 $user->setRole( $this->getMockRole() );

	  		 $this->persistence->merge( $user );
	  }

	  public function testDelete() {

	  		 $this->persistence->delete( $this->getMockData() );
	  		 $persisted = $this->persistence->find( $this->getMockData() );

			 PHPUnit_Framework_Assert::assertType( 'array', $persisted, 'Error deleting persisted record' );
			 PHPUnit_Framework_Assert::assertEquals( false, isset( $persisted[0] ), 'Error deleting persisted record' );
	  }
	  
	  public function testGetTableByModel() {

	  		 $user = new User();
	  		 $table = $this->persistence->getTableByModel( $user );

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'getTableByModel returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getTableByModel' );
	  }

	  public function testGetTableByModelName() {

	  		 $table = $this->persistence->getTableByModelName( 'User' );

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'getTableByModelName returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getTableByModelName' );
	  }
	  
	  public function testGetTableByName() {

	  		 $table = $this->persistence->getTableByName( 'roles' );

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'getTableByName returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'roles', $table->getName(), 'Failed to getTableByName' );
	  }

	  public function testGetIdentityTable() {

	  		 $table = $this->persistence->getIdentityTable();

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'Identity table is null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getIdentityTable' );
	  }

	  public function testGetIdentityModel() {

	  		 $model = $this->persistence->getIdentityModel();

	  		 $class = new ReflectionClass( $model );

	  		 PHPUnit_Framework_Assert::assertNotNull( $model, 'Identity model is null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'User', $class->getName(), 'Failed to getIdentityModel' );
	  }

	  public function testGetSessionTable() {

			 $table = $this->persistence->getSessionTable();

			 PHPUnit_Framework_Assert::assertNotNull( $table, 'Session table is null' );
			 PHPUnit_Framework_Assert::assertEquals( 'sessions', $table->getName(), 'Failed to getSessionTable' );
	  }

	  public function testGetSessionModel() {

	  		 $model = $this->persistence->getSessionModel();

	  		 $class = new ReflectionClass( $model );

	  		 PHPUnit_Framework_Assert::assertNotNull( $model, 'Session model is null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'Session', $class->getName(), 'Failed to getSessionModel' );
	  }

	  public function testGetPrimaryKeyColumns() {

	  		 $table = $this->persistence->getTableByName( 'roles' );
	  		 $columns = $table->getPrimaryKeyColumns();

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'testGetPrimaryKeyColumns \'getTableByName\' returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'roles', $table->getName(), 'Failed to getPrimaryKeyColumns' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'name', $columns[0]->getName(), 'Failed to locate \'name\'.' );
	  }

	  public function testGetForeignKeyColumns() {

	  		 $table = $this->persistence->getTableByName( 'users' );
	  		 $columns = $table->getForeignKeyColumns();

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'testGetForeignKeyColumns \'getTableByName\' returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getForeignKeyColumns' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'roleId', $columns[0]->getName(), 'Failed to locate foreign key column \'roleId\'.' );
	  }

	  public function testGetForeignKeys() {

	  		 $table = $this->persistence->getTableByName( 'users' );
	  		 $columns = $table->getForeignKeyColumns();

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'testGetForeignKeys \'getTableByName\' returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to testGetForeignKeys' );

	 		 $fKey = $columns[0]->getForeignKey();

  	 		 PHPUnit_Framework_Assert::assertEquals( 'FK_UserRoles', $fKey->getName(), 'testGetForeignKeys failed to get foreign key name \'FK_UserRoles\'' );
  	 		 PHPUnit_Framework_Assert::assertEquals( 'one-to-many', $fKey->getType(), 'testGetForeignKeys failed to get foreign key type \'one-to-many\'' );
  	 		 PHPUnit_Framework_Assert::assertEquals( 'roles', $fKey->getReferencedTable(), 'testGetForeignKeys failed to get foreign key table \'roles\'' );
  	 		 PHPUnit_Framework_Assert::assertEquals( 'name', $fKey->getReferencedColumn(), 'testGetForeignKeys failed to get referenced column \'name\'' );
	  }

	  public function testGetForeignKeyTableAndColumnInstances() {

	  		 $table = $this->persistence->getTableByName( 'users' );
	  		 $columns = $table->getForeignKeyColumns();

	  		 foreach( $columns as $column ) {

	  		 		  $fkey = $column->getForeignKey();

	  		 		  PHPUnit_Framework_Assert::assertNotNull( $fkey->getReferencedTableInstance(), 'testGetForeignKeyTableAndColumnInstances failed to get table instance' );
	  		 		  PHPUnit_Framework_Assert::assertNotNull( $fkey->getReferencedColumnInstance(), 'testGetForeignKeyTableAndColumnInstances failed to get column instance' );	  		 		  
	  		 }
	  }

	  public function testDrop() {

	  		 $this->persistence->drop();

	  		 $name = './' . $this->persistence->getDatabase()->getName() . '.sqlite';
	  		 if( file_exists( './' . $name ) )
	  		 	 PHPUnit_Framework_Assert::fail( 'Failed to drop database ' . $name );
	  }

	  private function getMockTable() {

	  		  $Table = new Table();
	  		  $Table->setName( 'TestTable' );
	  		  $Table->setModel( 'TestModel' );

	  		  $Column1 = new Column( null, 'TestTable' );
	  		  $Column1->setName( 'id' );
	  		  $Column1->setType( 'integer' );
	  		  $Column1->setLength( '255' );
	  		  $Column1->setProperty( 'p1' );
	  		  $Column1->setDisplay( 'Field 1' );
	  		  $Column1->setRequired( true );
	  		  $Column1->setPrimaryKey( true );
	  		  $Column1->setAutoIncrement( true );

	  		  $Column2 = new Column( null, 'TestTable' );
	  		  $Column2->setName( 'fld2' );
	  		  $Column2->setType( 'varchar' );
	  		  $Column2->setLength( '100' );
	  		  $Column2->setProperty( 'p2' );
	  		  $Column2->setDisplay( 'Field 2' );
	  		  $Column2->setDefault( 'This is a test value' );

	  		  $Table->addColumn( $Column1 );
	  		  $Table->addColumn( $Column2 );

	  		  return $Table;
	  }

	  private function getMockRole() {

	  		  return new Role( 'test' );
	  }
	  
	  private function getMockData() {
	  	
	 	     $user = new User();
	  		 $user->setUsername( 'phpunit' );
	  		 $user->setPassword( 'phpunit' );
	  		 $user->setEmail( 'root@localhost' );
	  		 $user->setCreated( date( 'c', strtotime( 'now' ) ) );
	  		 $user->setLastLogin( date( 'c', strtotime( 'now' ) ) );

	  		 $user->setRole( $this->getMockRole() );

	  		 return $user;
	  }
}
?>