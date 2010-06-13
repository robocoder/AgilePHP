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
 * Remoting class responsible for server side processing on behalf of Database Manager.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.classes
 */
class DatabaseManagerRemote {

	  public function __construct() { }

	  #@RemoteMethod
	  public function getServers() {

	  		 $boxes = array();

	  		 try {
		  		   $servers = ORM::find( new Server() );

		  		   foreach( $servers as $server ) {

		  		   		$box = array();
		  		 		$box[0] = $server->getId();
		  		 		$box[1] = $server->getIp() . ' (' . $server->getProfile() . ')';

			  		 	array_push( $boxes, $box );
		  		 }

		  		 $o = new stdClass;
		  		 $o->servers = $boxes;

		  		 return $o;
	  		 }
	  		 catch( Exception $e ) {

	  		 	 throw new RemotingException( $e );
	  		 }
	  }

	  /**
	   * Performs connection test to the specified database server
	   * 
	   * @type
	   */
	  #@RemoteMethod
	  public function testConnection( $database ) {

	  		 $Database = new Database();
	  		 $Database->setType( $database->type );
	  		 $Database->setDriver( $database->type );
	  		 $Database->setHostname( $database->hostname );
	  		 $Database->setName( $database->name );
	  		 $Database->setUsername( $database->username );
	  		 $Database->setPassword( $database->password );

	  		 try {
	  		 		$db = ORM::connect( $Database );
	  		 		return $db->isConnected();
	  		 }
	  		 catch( ORMException $e ) {
	  		 	
	  		 		return -1;
	  		 }
	  }

	  /**
	   * Creates a new database from projects orm.xml configuration
	   * 
	   * @throws ORMException
	   */
	  #@RemoteMethod
	  public function create( $workspace, $projectName ) {

	  		 $workspace = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $workspace );
	  		 $orm_xml = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'orm.xml';

	  		 $dialect = ORMFactory::loadDialect( $orm_xml );
	  		 $dialect->create();

	  		 return true;
	  }

	  /**
	   * Drops a database from projects orm.xml configuration
	   * 
	   * @throws ORMException
	   */
	  #@RemoteMethod
	  public function drop( $workspace, $projectName ) {

	  		 $workspace = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $workspace );
	  		 $orm_xml = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'orm.xml';

	  		 $dialect = ORMFactory::loadDialect( $orm_xml );
	  		 $dialect->drop();

	  		 return true;
	  }

	  /**
	   * Creates orm.xml configuration by reverse engineering a database.
	   * 
	   * @return bool True if the reverse engineer process was successful
	   * @throws ORMException
	   */
	  #@RemoteMethod
	  public function reverseEngineer( $workspace, $projectName ) {

	  		 $workspace = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $workspace );
	  		 $projectPath = $workspace . DIRECTORY_SEPARATOR . $projectName;
	  		 $orm_xml =  $projectPath . DIRECTORY_SEPARATOR . 'orm.xml';

	  		 $dialect = ORMFactory::loadDialect( $orm_xml );
	  		 $Database = $dialect->reverseEngineer();

	  		 $data = $this->format( $Database );

	  		 Log::debug( $Database );
	  		 Log::debug( $data );

	  		 $this->createModels( $projectPath, $data['models'] );
      	     $this->writeOrmXml( $projectPath, $data['xml'] );
      	     $this->createControllers( $projectPath, $data['models'] );
      	     $this->updateNavigation( $projectPath, $data['models'] );

	  		 return true;
	  }

	  /** Helper methods */

	  /**
       * Formats the Database structure for use by this script.
       * 
       * @param Database $db The database object to reverse engineer.
       * @return array Associative array containing both models and orm.xml configuration
       */
      private function format( Database $db ) {

      		  $models = array();
      		  $tableXml = '';

      		  foreach( $db->getTables() as $table ) {

      		  	  $properties = array();

	      		  $tableXml .= "\t\t" . '<table name="' . $table->getName() . '" model="' . $table->getModel() . '" display="' . $table->getModel() . '" description="">';
	      	      $tableXml .= PHP_EOL;

	      	      foreach( $table->getColumns() as $column ) {

	      	      		   $columnXml = "\t\t\t";
	      	      		   $columnXml .= '<column name="' . $column->getName() . '" type="' . $column->getType() . '"';
	      	      		   $columnXml .= ($column->getProperty()) ? ' property="' . $column->getProperty() . '"' : '';
	      	      		   $columnXml .= ($column->getLength()) ? ' length="' . $column->getLength() . '"' : '';
	      	      		   $columnXml .= ($column->isPrimaryKey()) ? ' primaryKey="true"' : '';
	      	      		   $columnXml .= ($column->isAutoIncrement()) ? ' autoIncrement="true"' : '';
	      	      		   $columnXml .= ($column->getDefault()) ? ' default="' . $column->getDefault() . '"' : '';
	      	      		   $columnXml .= ($column->isRequired()) ? ' required="true"' : '';
	      	      		   
	      	      		   if( $column->isForeignKey() ) {

	      	      		   	   $foreignKey = $column->getForeignKey();
      	      		   	   	   $foreignKeyXml = "\t\t\t\t";
      	      		   	   	   $foreignKeyXml .= '<foreignKey name="' . $foreignKey->getName() . '" type="' . $foreignKey->getType() . '" ';
      	      		   	   	   $foreignKeyXml .= 'onDelete="' . str_replace( ' ', '_', $foreignKey->getOnDelete() ) . '" onUpdate="' . str_replace( ' ', '_', $foreignKey->getOnUpdate() ) . '" ' . PHP_EOL;
      	      		   	   	   $foreignKeyXml .= "\t\t\t\t\t" . 'table="' . $foreignKey->getReferencedTable() . '" column="' . $foreignKey->getReferencedColumn() . '" ';
      	      		   	   	   $foreignKeyXml .= 'controller="' . $foreignKey->getReferencedController() . '"/>';

      	      		   	   	   $columnXml .= '>' . PHP_EOL . $foreignKeyXml . PHP_EOL . "\t\t\t</column>" . PHP_EOL;
	      	      		   }
	      	      		   else
	      	      		   		$columnXml .= '/>' . PHP_EOL;

	      	      		   $tableXml .= $columnXml;

	      	      		   $name = ($column->getProperty()) ? $column->getProperty() : $column->getName();

	      	      		   // Add interceptor ORM annotations to applicable data types
	      	      		   if( $column->isPrimaryKey() )
								array_push( $properties, array( 'name' => $name, 'interceptor' => '#@Id' ) );

							else if( strtolower( $column->getName() ) == 'password' )
      	      		   	   	   array_push( $properties, array( 'name' => $name, 'interceptor' => '#@Password' ) );

      	      		   	    else
      	      		   	   	   array_push( $properties, array( 'name' => $name, 'interceptor' => null ) );
	      	      }

	      	      $tableXml .= "\t\t</table>" . PHP_EOL;
	      	      array_push( $models, array( ucfirst( $table->getName() ) => $properties ) );
      		  }

      		  return array( 'models' => $models, 'xml' => $tableXml );
      }

      /**
       * Creates domain model code
       * 
       * @return void
       */
      private function createModels( $projectPath, $models ) {

	      	foreach( $models as $model ) {
	      		
	      			foreach( $model as $name => $properties ) {
	
	      				  $class = "class $name {" . PHP_EOL . PHP_EOL;
			              foreach( $properties as $values ) {
	
		                           $class .= "\tprivate \$" . $values['name'] . ';' . PHP_EOL;
			              }
			              $class .= PHP_EOL . "\tpublic function __construct() { }" . PHP_EOL . PHP_EOL;
			              foreach( $properties as $values ) {
			
			              		   if( $values['interceptor'] !== null )
			              			   $class .= "\t" . $values['interceptor'] . PHP_EOL;
	
			              		   $class .= "\tpublic function set" . ucfirst( $values['name'] ) . "( \$value ) {" . PHP_EOL . PHP_EOL . "\t\t \$this->" . $values['name'] . " = \$value;" . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL;
			              }
			              foreach( $properties as $key => $values ) {
	
			              		   $class .= "\tpublic function get" . ucfirst( $values['name'] ) . "() {" . PHP_EOL . PHP_EOL . "\t\t return \$this->" . $values['name'] . ";" . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL;
						  }
			      	      $class .= "}";
	
			      	      $file = $projectPath . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . ucfirst( $name ) . '.php';
			      	      $h = fopen( $file, 'w' );
			      	      fwrite( $h, '<?php' . PHP_EOL . PHP_EOL . '/** AgilePHP generated domain model */' . PHP_EOL . PHP_EOL . $class . PHP_EOL . '?>' );
			      	      fclose( $h );
	
			      	      if( !file_exists( $file ) )
			      	     	  throw new FrameworkException( 'Failed to create domain model \'' . ucfirst( $name ) . '\'.' );
	      			}
	      	}
      }

      /**
       * Update orm.xml file if desired.
       * 
       * @return void
       */
      private function writeOrmXml( $projectPath, $tableXml ) {

      		  $file = $projectPath . DIRECTORY_SEPARATOR . 'orm.xml';

      		  $h = fopen( $file, 'r' );
      		  $data = '';
      		  while( !feof( $h ) )
      		  	  $data .= fgets( $h, 4096 );
      		  fclose( $h );

      		  $tableXml .= PHP_EOL . "\t</database>" . PHP_EOL . PHP_EOL . '</orm>';

      		  $data = str_replace( '</database>', '', $data );

      		  $h = fopen( $file, 'w' );
      		  fwrite( $h, str_replace( '</orm>', $tableXml, $data ) );
      		  fclose( $h );
      }

      /**
       * Creates controllers for each of the models.
       * 
       * @param array $models List of models to generate controllers for.
       * @return void
       */
      private function createControllers( $projectPath, $models ) {

		      foreach( $models as $model ) {

	      			foreach( $model as $name => $properties ) {

	      				  $class = 'class ' . $name . 'Controller extends BaseModelActionController {' . PHP_EOL . PHP_EOL;
			              $class .= "\tprivate \$model;" . PHP_EOL;
			              $class .= PHP_EOL . "\tpublic function __construct() { " . PHP_EOL . PHP_EOL . "\t\t" . '$this->model = new ' . $name . '();' . PHP_EOL . "\t\tparent::__construct();" . PHP_EOL . "\t}" . PHP_EOL; 
			              $class .= PHP_EOL . "\t/**" . PHP_EOL;
			              $class .= "\t * (non-PHPdoc)" . PHP_EOL;
			              $class .= "\t * @see AgilePHP/mvc/BaseModelController#getModel()" . PHP_EOL;
			              $class .= "\t */" . PHP_EOL;
			              $class .= "\tpublic function getModel() { " . PHP_EOL . PHP_EOL . "\t\treturn " . '$this->model;' . PHP_EOL . "\t}" . PHP_EOL;
			              $class .= PHP_EOL . "\t/**" . PHP_EOL;
			              $class .= "\t * (non-PHPdoc)" . PHP_EOL;
			              $class .= "\t * @see AgilePHP/mvc/BaseController#index()" . PHP_EOL;
			              $class .= "\t */" . PHP_EOL;
			              $class .= "\tpublic function index() { " . PHP_EOL . PHP_EOL . "\t\tparent::index();" . PHP_EOL . "\t}" . PHP_EOL;
			      	      $class .= "}";

			      	 	  $file = $projectPath . DIRECTORY_SEPARATOR . 'control' . DIRECTORY_SEPARATOR . $name . 'Controller.php';
			      	      $h = fopen( $file, 'w' );
			      	      fwrite( $h, '<?php' . PHP_EOL . PHP_EOL . '/** AgilePHP generated controller */' . PHP_EOL . PHP_EOL . $class  . PHP_EOL . '?>' );
			      	      fclose( $h );

			      	      if( !file_exists( $file ) )
			      	          throw new FrameworkException( 'Failed to create new controller' );     
	      			}
	      	  }
       }

       private function updateNavigation( $projectPath, $models ) {

       		   $file = $projectPath . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'admin_navigation.phtml';

       		   if( !file_exists( $file ) ) return;

       		   $h = fopen( $file, 'r' );
      		   $data = '';
      		   while( !feof( $h ) )
      		  		  $data .= fgets( $h, 4096 );
      		   fclose( $h );

      		   $h = fopen( $file, 'w' );

      		   $nav = '';
       		   foreach( $models as $model ) {

	      			foreach( $model as $name => $properties ) {

	      				  $nav .= '<td style="padding-left: 10px;">' . PHP_EOL . "\t\t\t";
	      				  $nav .= '<a href="<?php echo AgilePHP::getFramework()->getRequestBase() ?>/' . $name . 'Controller">' . $name . '</a>' . PHP_EOL . "\t\t";
	      				  $nav .= '</td>' . PHP_EOL . PHP_EOL . "\t\t";
			      	}
	      	  }

	      	  fwrite( $h, str_replace( '<!-- #@AgilePHPGen -->', $nav . '<!-- #@AgilePHPGen -->', $data ) );
			  fclose( $h );

			  if( !file_exists( $file ) )
			      throw new FrameworkException( 'Failed to update admin_navigation' );
       }
}
?>