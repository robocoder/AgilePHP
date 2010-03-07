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
 * @package com.makeabyte.agilephp.generator
 */

/**
 * Creates a new domain model based on user input. After the name and columns
 * have been collected, persistence.xml can be automatically updated with a
 * stub table.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.generator
 * @version 0.1a
 */
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

      	      $class = "class " . $this->modelName . ' {' . PHP_EOL . PHP_EOL;
              foreach( $this->properties as $key => $value ) {

                       if( is_numeric( $key ) )
                           $class .= "\tprivate \$$value;" . PHP_EOL;
                       else
                           $class .= "\tprivate \$$key = $value;" . PHP_EOL;
              }
              $class .= PHP_EOL . "\tpublic function __construct() { }" . PHP_EOL . PHP_EOL;
              foreach( $this->properties as $key => $value ) {

                       if( is_numeric( $key ) )
                           $class .= "\tpublic function set" . ucfirst( $value ) . "( \$value ) {" . PHP_EOL . PHP_EOL . "\t\t \$this->$value = \$value;" . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL;
                       else
                           $class .= "\tpublic function set" . ucfirst( $key ) . "( \$value ) {" . PHP_EOL . PHP_EOL . "\t\t \$this->$key = \$value;" . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL;
              }
              foreach( $this->properties as $key => $value ) {

                       if( is_numeric( $key ) )
                           $class .= "\tpublic function get" . ucfirst( $value ) . "() {" . PHP_EOL . PHP_EOL . "\t\t return \$this->$value;" . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL;
                       else
                           $class .= "\tpublic function get" . ucfirst( $key ) . "() {" . PHP_EOL . PHP_EOL . "\t\t return \$this->$key;" . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL;
			  }
      	      $class .= "}";

      	      $file = $this->getCache()->getProjectRoot() . '/model/' . ucfirst( $this->modelName ) . '.php';
      	      $h = fopen( $file, 'w' );
      	      fwrite( $h, '<?php' . PHP_EOL . PHP_EOL . '/** AgilePHP generated domain model */' . PHP_EOL . PHP_EOL . $class . PHP_EOL . '?>' );
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

      		  $xml = "\t<table name=\"" . strtolower( $this->modelName ) . "\" model=\"" . $this->modelName . "\" display=\"\" description=\"\">" . PHP_EOL;
      		  $clazz = new ReflectionClass( $this->instance );
      		  foreach( $clazz->getProperties() as $property )
      		  		   $xml .= "\t\t\t<column name=\"" . $property->name . "\" type=\"\" length=\"\"/>" . PHP_EOL;

      		  $xml .= "\t\t</table>" . PHP_EOL . "\t</database>" . PHP_EOL;

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