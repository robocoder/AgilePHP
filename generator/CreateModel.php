<?php

require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';

class CreateModel extends AgilePHPGen {

	  private $modelName;
	  private $properties = array();
	  private $class;
	  private $xml;
	  private $instance;

      public function __construct() {

      		 parent::__construct();
      		 
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
      public function testCreateModel() {

      	     $this->modelName = ucfirst( $this->prompt( 'Enter the name of the new model.' ) );

      	     echo "Enter each of the column names. Enter a blank column name when done.\n";
      	     $this->getAnswers();

      	     $this->createModel();
      }

      /**
       * Collects each of the property names
       * 
       * @return True when finished
       */
      private function getAnswers() {

      		 echo "AgilePHP> ";
      		 $answer = strtolower( trim( fgets( STDIN ) ) );

      		 if( $answer != '' ) {

      		 	 array_push( $this->properties, $answer );
      		 	 $this->getAnswers();
      		 }

      		 return true;
      }

      /**
       * Creates domain model object
       * 
       * @return void
       */
      private function createModel() {

      	      $class = "class " . $this->modelName . " {\n\n";
              foreach( $this->properties as $key => $value ) {

                       if( is_numeric( $key ) )
                           $class .= "\tprivate \$$value;\n";
                       else
                           $class .= "\tprivate \$$key = $value;\n";
              }
              $class .= "\n\tpublic function __construct() { }\n\n";
              foreach( $this->properties as $key => $value ) {

                       if( is_numeric( $key ) )
                           $class .= "\tpublic function set" . ucfirst( $value ) . "( \$value ) {\n\n\t\t \$this->$value = \$value;\n\t}\n\n";
                       else
                           $class .= "\tpublic function set" . ucfirst( $key ) . "( \$value ) {\n\n\t\t \$this->$key = \$value;\n\t}\n\n";
              }
              foreach( $this->properties as $key => $value ) {

                       if( is_numeric( $key ) )
                           $class .= "\tpublic function get" . ucfirst( $value ) . "() {\n\n\t\t return \$this->$value;\n\t}\n\n";
                       else
                           $class .= "\tpublic function get" . ucfirst( $key ) . "() {\n\n\t\t return \$this->$key;\n\t}\n\n";
			  }
      	      $class .= "}";

      	      $file = $this->getCache()->getProjectRoot() . '/model/' . ucfirst( $this->modelName ) . '.php';
      	      $h = fopen( $file, 'w' );
      	      fwrite( $h, "<?php\n\n/** AgilePHP generated domain model */\n\n" . $class  . "?>" );
      	      fclose( $h );

      	      if( !file_exists( $file ) )
      	     	  PHPUnit_Framework_Assert::fail( 'Failed to create new domain model' );

      	      eval( $class );
      	      $this->instance = new $this->modelName;

      	      $this->updatePersistenceXml();
      }

      /**
       * Update persistence.xml file if desired.
       * 
       * @return void
       */
      private function updatePersistenceXml() {

       		  $answer = $this->prompt( 'Update persistence.xml with a template stub for this table? (Y/N)' );
      		  if( strtolower( $answer ) != 'y' )
      		  	  return;

      		  foreach( $this->xml->database->table as $tableXml ) {

      		  		   if( (string)$tableXml->attributes()->model == $this->modelName )
      		  		   	   PHPUnit_Framework_Assert::Fail( 'Failed to update persistence.xml. Table element already exists.' );
      		  }
      		  /*
      		  $table = $this->xml->database->addChild( 'table' );
      		  $table->addAttribute( 'name', strtolower( $this->modelName ) );
      		  $table->addAttribute( 'model', $this->modelName );

      		  $clazz = new ReflectionClass( $this->instance );
      		  foreach( $clazz->getProperties() as $property ) {

      		  		   $column = $table->addChild( 'column' );
      		  		   $column->addAttribute( 'name', $property->name );
      		  		   $column->addAttribute( 'type', '' );
      		  		   $column->addAttribute( 'length', '255' );
      		  }

      	      //$this->xml->asXML( $this->getCache()->getProjectRoot() . '/persistence.xml' );
      		   */

      		  $xml = "\t<table name=\"" . strtolower( $this->modelName ) . "\" model=\"" . $this->modelName . "\" display=\"\" description=\"\">\n";
      		  $clazz = new ReflectionClass( $this->instance );
      		  foreach( $clazz->getProperties() as $property )
      		  		   $xml .= "\t\t\t<column name=\"" . $property->name . "\" type=\"\" length=\"\"/>\n";

      		  $xml .= "\t\t</table>\n\t</database>\n";

      		  $h = fopen( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'persistence.xml', 'r' );
      		  $data = '';
      		  while( !feof( $h ) )
      		  		 $data .= fgets( $h, 4096 );
      		  fclose( $h );

      		  $h = fopen( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'persistence.xml', 'w' );
      		  fwrite( $h, str_replace( '</database>', $xml, $data ) );
      		  fclose( $h );
      }
}
?>