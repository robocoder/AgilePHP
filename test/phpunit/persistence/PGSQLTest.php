<?php
/**
 * @package com.makeabyte.agilephp.test.persistence
 */
class PGSQLTest extends BaseTest {

	  private $persistence;

	  public function __construct() {

	  		 parent::__construct();
	  		 $this->persistence = PersistenceManager::getInstance( 'testcase_pgsql' );
	  }

	  /**
	   * @test
	   */
	  public function persistenceNotNull() {

	  	     PHPUnit_Framework_Assert::assertNotNull( $this->persistence, 'Failed to create persistence manager for testcase_pgsql' );
	  }

	  /**
	   * @test
	   */
	  public function create() {

	  		 $this->persistence->create();
	  }

	  /**
	   * @test
	   */
	  public function createTable() {

	  		 $this->persistence->createTable( $this->getMockTable() );
	  }

	  /**
	   * @test
	   */
	  public function dropTable() {

	  		 $this->persistence->dropTable( $this->getMockTable() );

	  		
	  }
	  
	  /** Tests using Table1 and Table2 **/
  
	  /**
	   * @test
	   */
	  public function persistWithNewForeignKey() {

			 $Table2 = new Table2();
      		 $Table2->setName( 'phpunit' );
      		 $Table2->setDescription( 'This is a phpunit test' );

      		 $Table1 = new Table1();
      		 $Table1->setField1( 'phpunit' );
      		 $Table1->setField2( 'phpunit' );

      		 $Table1->setTable2( $Table2 );	// set instance of Table2

      		 $this->persistence->persist( $Table1 );		// should persist both Table1 and Table2 data

      		 $persisted = $this->persistence->find( $Table1 );

      		 PHPUnit_Framework_Assert::assertNotNull( $persisted, 'Error persisting new records' );
      		 PHPUnit_Framework_Assert::assertEquals( 1, $persisted[0]->getId(), 'Error persisting Table1 record' );
      		 PHPUnit_Framework_Assert::assertEquals( 1, $persisted[0]->getTable2()->getId(), 'Error persisting Table2 record' );
	  }

	  /**
	   * @test
	   */
	  public function persistWithExistingForeignKey() {

	 	  	 // Referenced table
			 $Table2 = new Table2();
			 $Table2->setId( 1 );	#@Id interceptor populates ActiveRecord state

			 // Parent table
      		 $Table1 = new Table1();
      		 $Table1->setField1( 'phpunit2' );
      		 $Table1->setField2( 'phpunit2' );

      		 $Table1->setTable2( $Table2 );	// set instance of Table2

      		 $this->persistence->persist( $Table1 );	// should persist only Table1

      		 $persisted = $this->persistence->find( $Table1 );

      		 $stmt = $this->persistence->query( 'SELECT * FROM table2;' );
      		 $totalTable2Records = $stmt->fetchAll();

      		 PHPUnit_Framework_Assert::assertNotNull( $persisted, 'Error persisting new records' );
      		 PHPUnit_Framework_Assert::assertEquals( 2, $persisted[0]->getId(), 'Error persisting Table1 record' );
      		 PHPUnit_Framework_Assert::assertEquals( 1, $persisted[0]->getTable2()->getId(), 'Error merging Table2 with existing Table2 record' );
      		 PHPUnit_Framework_Assert::assertEquals( 1, count( $totalTable2Records ), 'Persisted referenced table instead of using existing foreign key value' );
	  }

	  /**
	   * @test
	   */
	  public function mergeWithNewForeignKey() {

	  		 $Table2 = new Table2();
	  		 $Table2->setName( 'phpunit3' );
	  		 $Table2->setDescription( 'phpunit3' );

	  		 $Table1 = new Table1();
	  		 $Table1->setId( 2 );	#@Id interceptor populates ActiveRecord state
	  		 $Table1->setField1( 'phpunit3' );
	  		 $Table1->setField2( 'phpunit3' );
	  		 $Table1->setTable2( $Table2 );

	  		 $this->persistence->merge( $Table1 );

	  		 // Null out foreign key value and instance so they arent used in the ->find
	  		 $Table1->setField3( null );
	  		 $Table1->setTable2( null );

	  		 $persistedTable1 = $this->persistence->find( $Table1 );

	  		 PHPUnit_Framework_Assert::assertEquals( 'phpunit3', $persistedTable1[0]->getField1(), 'Failed to merge Table1.field1' );
      		 PHPUnit_Framework_Assert::assertEquals( 'phpunit3', $persistedTable1[0]->getField2(), 'Failed to merge Table1.field2' );
      		 PHPUnit_Framework_Assert::assertEquals( 'phpunit3', $persistedTable1[0]->getTable2()->getName(), 'Failed to merge Table2.name' );
      		 PHPUnit_Framework_Assert::assertEquals( 'phpunit3', $persistedTable1[0]->getTable2()->getDescription(), 'Failed to merge Table2.description' );
      		 PHPUnit_Framework_Assert::assertEquals( $persistedTable1[0]->getField3(), $persistedTable1[0]->getTable2()->getId(), 'Failed to preserve reference between Table1.field3 and Table2.id' );
	  }

	  /**
	   * @test
	   */
	  public function mergeWithExistingForeignKey() {

	  		 // Referenced table
	  		 $Table2 = new Table2();
			 $Table2->setId( 1 );	  #@Id interceptor populates ActiveRecord state

			 PHPUnit_Framework_Assert::assertEquals( 'phpunit', $Table2->getName(), '#@Id interceptor could not populate ActiveRecord state for Table2' );

			 $Table2->setName( 'merge' );
			 $Table2->setDescription( 'merge' );

			 // Parent table
	  		 $Table1 = new Table1();
      		 $Table1->setId( 1 );	  #@Id interceptor populates ActiveRecord state

      		 PHPUnit_Framework_Assert::assertEquals( 'phpunit', $Table1->getField1(), '#@Id interceptor could not populate ActiveRecord state for Table1' );

     		 $Table1->setField1( 'merge' );
     		 $Table1->setField2( 'merge' );

     		 $Table1->setTable2( $Table2 );
      		 $this->persistence->merge( $Table1 );

      		 $persistedTable1 = $this->persistence->find( $Table1 );

      		 PHPUnit_Framework_Assert::assertNotNull( $persistedTable1[0], 'Error getting merged record' );

      		 PHPUnit_Framework_Assert::assertEquals( 'merge', $persistedTable1[0]->getField1(), 'Failed to merge Table1.field1' );
      		 PHPUnit_Framework_Assert::assertEquals( 'merge', $persistedTable1[0]->getField2(), 'Failed to merge Table1.field2' );
      		 PHPUnit_Framework_Assert::assertEquals( 1, $persistedTable1[0]->getField3(), 'Failed to keep Table1.field3 foreign key value reference to Table2.id' );
      		 PHPUnit_Framework_Assert::assertEquals( 1, $persistedTable1[0]->getTable2()->getId(), 'Failed to look up Table2.id using Table::getTable2 instance accessor' );
      		 PHPUnit_Framework_Assert::assertEquals( 'merge', $persistedTable1[0]->getTable2()->getName(), 'Failed to merge Table2.name' );
      		 PHPUnit_Framework_Assert::assertEquals( 'merge', $persistedTable1[0]->getTable2()->getDescription(), 'Failed to merge Table2.description' );
	  }

	  /**
	   * @test
	   */
	  public function table1Find() {

	  		 $Table1 = new Table1();
	  		 $Table1->setId( 1 );		#@Id interceptor and populates ActiveRecord state

			 $persisted = $this->persistence->find( $Table1 );

			 PHPUnit_Framework_Assert::assertNotNull( $persisted, 'Error testing find operation' );
			 PHPUnit_Framework_Assert::assertEquals( 'merge', $persisted[0]->getField1(), 'Error asserting located record value' );
	  }

	   /** Tests using User and Role **/

	   /**
	   * @test
	   */
	   public function constraintsAreWorking() {

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

	  /**
	   * @test
	   */
	  public function persist() {

	  		 $user = $this->getMockData();

	  		 $this->persistence->persist( $this->getMockRole() );

	  		 $this->persistence->persist( $user );
			 $persisted = $this->persistence->find( $user );

			 PHPUnit_Framework_Assert::assertNotNull( $persisted, 'Error persisting new record' );
	  }

	  /**
	   * @test
	   */
	  public function find() {
	  	
	  		 $user = $this->getMockData();
			 $persisted = $this->persistence->find( $user );
			 PHPUnit_Framework_Assert::assertNotNull( $persisted, 'Error finding persisted record' );
	  }

	  /**
	   * @test
	   */
	  public function restrictedFind() {

	  		 $this->persistence->setRestrictions( array( 'username' => 'phpunit' ) );
	  		 $persisted = $this->persistence->find( new User() );

	  		 PHPUnit_Framework_Assert::assertNotNull( $persisted, 'Error finding persisted record using restrictions logic' );
	  		 PHPUnit_Framework_Assert::assertEquals( $persisted[0]->getUsername(), 'phpunit', 'Error finding persisted record using restrictions logic' );
	  }

	  /**
	   * @test
	   */
	  public function merge() {
	  	
	  		 $user = $this->getMockData();	  		 
	  		 $user->setPassword( 'new password' );

	  		 $this->persistence->merge( $user );
	  }
	
	  /**
	   * @test
	   */
	  public function delete() {

	  		 $this->persistence->delete( $this->getMockData() );
	  		 $persisted = $this->persistence->find( $this->getMockData() );

			 PHPUnit_Framework_Assert::assertType( 'array', $persisted, 'Error deleting persisted record' );
			 PHPUnit_Framework_Assert::assertEquals( 0, count($persisted), 'Error deleting persisted record' );
	  }

	  /**
	   * @test
	   */
	  public function getTableByModel() {

	  		 $user = new User();
	  		 $table = $this->persistence->getTableByModel( $user );

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'getTableByModel returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getTableByModel' );
	  }

	  /**
	   * @test
	   */
	  public function getTableByModelName() {

	  		 $table = $this->persistence->getTableByModelName( 'User' );

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'getTableByModelName returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getTableByModelName' );
	  }
	  
	  /**
	   * @test
	   */
	  public function getTableByName() {

	  		 $table = $this->persistence->getTableByName( 'roles' );

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'getTableByName returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'roles', $table->getName(), 'Failed to getTableByName' );
	  }

	  /**
	   * @test
	   */
	  public function getIdentityTable() {

	  		 $table = $this->persistence->getIdentityTable();

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'Identity table is null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getIdentityTable' );
	  }

	  /**
	   * @test
	   */
	  public function getIdentityModel() {

	  		 $model = $this->persistence->getIdentityModel();

	  		 $class = new ReflectionClass( $model );

	  		 PHPUnit_Framework_Assert::assertNotNull( $model, 'Identity model is null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'User', $class->getName(), 'Failed to getIdentityModel' );
	  }

	  /**
	   * @test
	   */
	  public function getSessionTable() {

			 $table = $this->persistence->getSessionTable();

			 PHPUnit_Framework_Assert::assertNotNull( $table, 'Session table is null' );
			 PHPUnit_Framework_Assert::assertEquals( 'sessions', $table->getName(), 'Failed to getSessionTable' );
	  }

	  /**
	   * @test
	   */
	  public function getSessionModel() {

	  		 $model = $this->persistence->getSessionModel();

	  		 $class = new ReflectionClass( $model );

	  		 PHPUnit_Framework_Assert::assertNotNull( $model, 'Session model is null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'Session', $class->getName(), 'Failed to getSessionModel' );
	  }

	  /**
	   * @test
	   */
	  public function getPrimaryKeyColumns() {

	  		 $table = $this->persistence->getTableByName( 'roles' );
	  		 $columns = $table->getPrimaryKeyColumns();

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'testGetPrimaryKeyColumns \'getTableByName\' returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'roles', $table->getName(), 'Failed to getPrimaryKeyColumns' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'name', $columns[0]->getName(), 'Failed to locate \'name\'.' );
	  }

	  /**
	   * @test
	   */
	  public function getForeignKeyColumns() {

	  		 $table = $this->persistence->getTableByName( 'users' );
	  		 $columns = $table->getForeignKeyColumns();

	  		 PHPUnit_Framework_Assert::assertNotNull( $table, 'testGetForeignKeyColumns \'getTableByName\' returned null' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'users', $table->getName(), 'Failed to getForeignKeyColumns' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'roleId', $columns[0]->getName(), 'Failed to locate foreign key column \'roleId\'.' );
	  }

	  /**
	   * @test
	   */
	  public function getForeignKeys() {

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

	  /**
	   * @test
	   */
	  public function getForeignKeyTableAndColumnInstances() {

	  		 $table = $this->persistence->getTableByName( 'users' );
	  		 $columns = $table->getForeignKeyColumns();

	  		 foreach( $columns as $column ) {

	  		 		  $fkey = $column->getForeignKey();

	  		 		  PHPUnit_Framework_Assert::assertNotNull( $fkey->getReferencedTableInstance(), 'testGetForeignKeyTableAndColumnInstances failed to get table instance' );
	  		 		  PHPUnit_Framework_Assert::assertNotNull( $fkey->getReferencedColumnInstance(), 'testGetForeignKeyTableAndColumnInstances failed to get column instance' );	  		 		  
	  		 }
	  }

	  /**
	   * @test
	   */
	  public function drop() {

	  		 try {
	  		 	   return $this->persistence->drop();
	  		 }
	  		 catch( Exception $e ) {

	  		 		PHPUnit_Framework_Assert::fail( $e->getMessage() );
	  		 }

	  		 PHPUnit_Framework_Assert::fail( 'Failed to drop database' );
	  }

	  private function getMockRole() {

	  		  $role = new Role();
	  		  $role->setName( 'test' );

	  		  return $role;
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

	  private function getMockTable() {

	  		  $Table = new Table();
	  		  $Table->setName( 'TestTable' );
	  		  $Table->setModel( 'TestModel' );

	  		  $Column1 = new Column( null, 'TestTable' );
	  		  $Column1->setName( 'id' );
	  		  $Column1->setType( 'integer' );
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
}
?>