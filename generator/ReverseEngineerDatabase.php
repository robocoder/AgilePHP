<?php

ini_set( 'display_errors', '1' );
error_reporting( E_ALL );

require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';
require_once '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'AgilePHP.php';
require_once '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PersistenceManager.php';

class ReverseEngineerDatabase extends AgilePHPGen {

      public function __construct() {

      		 parent::__construct();

      		 $agilephp = AgilePHP::getFramework();
      	     $agilephp->setWebRoot( $this->getCache()->getProjectRoot() );
      	     $agilephp->setFrameworkRoot( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'AgilePHP' );

      		 $persistence_xml = $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'persistence.xml';
	  		 if( !file_exists( $persistence_xml ) )
	  		  	 return;

	  	     $this->xml = simplexml_load_file( $persistence_xml );
	  }

      /**
       * Creates a new domain model object in the web application /model directory.
       * 
       * @return void
       */
      public function testPrompt() {

      		 $message = "This process will reverse engineer the database in persistence.xml to generate domain models and controllers.\n\n" .
      		 			"Press enter to continue...\n";

      	     $this->prompt( $message );

      	     $pm = new PersistenceManager();

      	     $result = $this->format( $pm->reverseEngineer() );

      	     $this->create( $result );
      }

      /**
       * Formats the Database structure for use by this script.
       * 
       * @param $db
       * @return unknown_type
       */
      private function format( Database $db ) {

      		  $models = array();
      		  //$lb = (stristr( php_os, 'WIN' )) ? "\r\n" : "\n";
      		  $lb = "\r\n";
      		  $tableXml = '';

      		  foreach( $db->getTables() as $table ) {

      		  	  $properties = array();

	      		  $tableXml .= "\t\t" . '<table name="' . $table->getName() . '" model="' . $table->getModel() . '" display="' . $table->getModel() . '" description="">';
	      	      $tableXml .= $lb;

	      	      foreach( $table->getColumns() as $column ) {

	      	      		   $columnXml = "\t\t\t";
	      	      		   $columnXml .= '<column name="' . $column->getName() . '" type="' . $column->getType() . '" ';
	      	      		   $columnXml .= ($column->getLength()) ? 'length="' . $column->getLength() . '"' : '';
	      	      		   $columnXml .= ($column->hasForeignKey()) ? ' primaryKey="true"' : '';
	      	      		   $columnXml .= ($column->isAutoIncrement()) ? ' autoIncrement="true"' : '';
	      	      		   $columnXml .= ($column->getDefault()) ? ' default="' . $column->getDefault() . '"' : '';
	      	      		   $columnXml .= ($column->isRequired()) ? ' required="true"' : '';
	      	      		   $columnXml .= '/>' . $lb;

	      	      		   $tableXml .= $columnXml;

	      	      		   if( $column->isPrimaryKey() )
								array_push( $properties, array( 'name' => $column->getName(), 'interceptor' => '#@Id' ) );

							else if( strtolower( $column->getName() ) == 'password' )
      	      		   	   	   array_push( $properties, array( 'name' => $column->getName(), 'interceptor' => '#@Password' ) );

      	      		   	    else
      	      		   	   	   array_push( $properties, array( 'name' => $column->getName(), 'interceptor' => null ) );
	      	      }

	      	      $tableXml .= "\t\t</table>" . $lb;
	      	      array_push( $models, array( $table->getName() => $properties ) );
      		  }

      		  return array( 'models' => $models, 'xml' => $tableXml );
      }

      /**
       * Kicks off each of the generation routines.
       * 
       * @return void
       */
      private function create( $result ) {

      	      $this->createModels( $result['models'] );
      	      $this->writePersistenceXml( $result['xml'] );
      	      $this->createControllers( $result['models'] );
      	      $this->updateNavigation( $result['models'] );
      }

      /**
       * Creates domain model code
       * 
       * @return void
       */
      private function createModels( $models ) {

      	foreach( $models as $model ) {
      		
      			foreach( $model as $name => $properties ) {

      				  $class = "class " . $name . " {\n\n";
		              foreach( $properties as $values ) {

	                           $class .= "\tprivate \$" . $values['name'] . ";\n";
		              }
		              $class .= "\n\tpublic function __construct() { }\n\n";
		              foreach( $properties as $values ) {
		
		              		   if( $values['interceptor'] !== null )
		              			   $class .= "\t" . $values['interceptor'] . "\n";

		              		   $class .= "\tpublic function set" . ucfirst( $values['name'] ) . "( \$value ) {\n\n\t\t \$this->" . $values['name'] . " = \$value;\n\t}\n\n";
		              }
		              foreach( $properties as $key => $values ) {

		              		   $class .= "\tpublic function get" . ucfirst( $values['name'] ) . "() {\n\n\t\t return \$this->" . $values['name'] . ";\n\t}\n\n";
					  }
		      	      $class .= "}";

		      	      $file = $this->getCache()->getProjectRoot() . '/model/' . ucfirst( $name ) . '.php';
		      	      $h = fopen( $file, 'w' );
		      	      fwrite( $h, "<?php\n\n/** AgilePHP generated domain model */\n\n" . $class  . "\n?>" );
		      	      fclose( $h );
		
		      	      if( !file_exists( $file ) )
		      	     	  PHPUnit_Framework_Assert::fail( 'Failed to create new domain model' );
      			}
      	}
      }

      /**
       * Update persistence.xml file if desired.
       * 
       * @return void
       */
      private function writePersistenceXml( $tableXml ) {

       		  $answer = $this->prompt( 'Add model configuration to persistence.xml? (Y/N)' );
      		  if( strtolower( $answer ) != 'y' )
      		  	  return;

      		  $file = $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'persistence.xml';
      		  $pm = new PersistenceManager();

      		  $h = fopen( $file, 'r' );
      		  $data = '';
      		  while( !feof( $h ) )
      		  	  $data .= fgets( $h, 4096 );
      		  fclose( $h );

      		  $tableXml .= "\n\t</database>\n\n</persistence>";

      		  $data = str_replace( '</database>', '', $data );

      		  $h = fopen( $file, 'w' );
      		  fwrite( $h, str_replace( '</persistence>', $tableXml, $data ) );
      		  fclose( $h );
      }

      /**
       * Creates controllers for each of the models.
       * 
       * @param array $models List of models to generate controllers for.
       * @return void
       */
      private function createControllers( $models ) {

      		  $answer = $this->prompt( 'Would you like to generate controllers for each of the generated models? (Y/N)' );
      		  if( strtolower( $answer ) != 'y' )
      		  	  return;

		      foreach( $models as $model ) {

	      			foreach( $model as $name => $properties ) {

	      				  $class = "class " . $name . "Controller extends BaseModelActionController {\n\n";
			              $class .= "\tprivate \$model;\n";
			              $class .= "\n\tpublic function __construct() { \n\n\t\t" . '$this->model = new ' . $name . "();\n\t\tparent::__construct();\n\t}\n"; 
			              $class .= "\n\t/**\n";
			              $class .= "\t * (non-PHPdoc)\n";
			              $class .= "\t * @see AgilePHP/mvc/BaseModelController#getModel()\n";
			              $class .= "\t */\n";
			              $class .= "\tpublic function getModel() { \n\n\t\treturn " . '$this->model;' . "\n\t}\n";
			              $class .= "\n\t/**\n";
			              $class .= "\t * (non-PHPdoc)\n";
			              $class .= "\t * @see AgilePHP/mvc/BaseController#index()\n";
			              $class .= "\t */\n";
			              $class .= "\tpublic function index() { \n\n\t\tparent::modelList();\n\t}\n";
			      	      $class .= "}";

			      	 	  $file = $this->getCache()->getProjectRoot() . '/control/' . $name . 'Controller.php';
			      	      $h = fopen( $file, 'w' );
			      	      fwrite( $h, "<?php\n\n/** AgilePHP generated controller */\n\n" . $class  . "\n?>" );
			      	      fclose( $h );

			      	      if( !file_exists( $file ) )
			      	          PHPUnit_Framework_Assert::fail( 'Failed to create new controller' );     
	      			}
	      	  }
       }

       private function updateNavigation( $models ) {

       		   $answer = $this->prompt( 'Update admin_navigation.phtml navigation? (Y/N)' );
      		   if( strtolower( $answer ) != 'y' )
      		  	  return;

       		   $file = $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'admin_navigation.phtml';
       		   $h = fopen( $file, 'r' );
      		   $data = '';
      		   while( !feof( $h ) )
      		  		  $data .= fgets( $h, 4096 );
      		   fclose( $h );

      		   $h = fopen( $file, 'w' );

      		   $nav = '';
       		   foreach( $models as $model ) {

	      			foreach( $model as $name => $properties ) {

	      				  $nav .= '<td style="padding-left: 10px;">' . "\n\t\t\t";
	      				  $nav .= '<a href="<?php echo AgilePHP::getFramework()->getRequestBase() ?>/' . $name . 'Controller/">' . $name . '</a>' . "\n\t\t";
	      				  $nav .= '</td>' . "\n\n\t\t";
			      	}
	      	  }

	      	  fwrite( $h, str_replace( '<!-- #@AgilePHPGen -->', $nav . '<!-- #@AgilePHPGen -->', $data ) );
			  fclose( $h );

			  if( !file_exists( $file ) )
			      PHPUnit_Framework_Assert::fail( 'Failed to create new controller' );
       }
}
?>