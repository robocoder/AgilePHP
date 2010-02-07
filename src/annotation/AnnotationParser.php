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
 * @package com.makeabyte.agilephp.annotation
 */

/**
 * Responsible for parsing and returning annotation details about PHP classes.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.annotation
 * @version 0.2a
 * @static
 */
class AnnotationParser {

	  private static $instance;
	  private static $classes = array();
	  private static $properties = array();
	  private static $methods = array();
	  private static $sources = array();

	  private static $class;
	  private static $filename;

	  private function __construct() { }
	  private function __clone() { }

	  /**
	   * Returns a Singletom instance of AnnotationParser.
	   * 
	   * @return AnnotationParser Singleton instance of AnnotationParser
	   * @static
	   */
	  public static function getInstance() {

	  		 if( self::$instance == null )
	  		 	 self::$instance = new self();

	  		 return self::$instance;
	  }

	  /**
	   * Breaks the class file into PHP tokens and extracts all interface,
	   * class, method, and property level annotations.
	   * 
	   * @param String $class The name of the class to parse
	   * @return void
	   */
	  public static function parse( $class ) {

	  		 self::$class = $class;
	  		 self::$filename = $class . '.php';

		     if( in_array( self::$filename, self::$sources ) )
		         return;

	  		 array_push( self::$sources, self::$filename );

	  		 $comments = array();
	  		 $tokens = token_get_all( self::getSourceCode() );

	  		 for( $i=0; $i<count( $tokens ); $i++ ) {

	  			  $token = $tokens[$i];
				  if( is_array( $token ) ) {

					  list( $code, $value ) = $token;

					  switch( $code ) {

							  case T_COMMENT:

							 	   array_push( $comments, $value );
								   break;

							  case T_CLASS:

								   if( count( $comments ) ) {

									   self::$classes[$class] = self::parseAnnotations( implode( PHP_EOL, $comments ) );
									   $comments = array();
								   }
								   break;

							  case T_VARIABLE:

								   if( count( $comments ) ) {

								   	   $key = str_replace( '$', '', $token[1] );
									   self::$properties[$class][ $key ] = self::parseAnnotations( implode( PHP_EOL, $comments ) );
									   $comments = array();
								   }
								   break;

							  case T_FUNCTION:

								   if( count( $comments ) ) {

									   for( $j=$i; $j<count( $tokens ); $j++ ) {
										    if( is_array( $tokens[$j] ) ) {
										 	    if( $tokens[$j][0] == T_STRING ) {
										 	 	    self::$methods[$class][$tokens[$j][1]] = self::parseAnnotations( implode( PHP_EOL, $comments ) );
										 	 	    $comments = array();
										 	 	    break;
										 	    }
										    }
									   }
								   }
							 	   break;

							 	   /*
							 	    * @todo Support annotated interfaces
							case T_INTERFACE:

								   if( count( $comments ) ) {

									   $this->annotations['interface'] = $this->parseAnnotations( implode( "\n", $comments ) );
									   $comments = array();
								   }
								   break;
								   */

							case T_DOC_COMMENT;
							case T_WHITESPACE: 
							case T_PUBLIC: 
							case T_PROTECTED: 
							case T_PRIVATE: 
							case T_ABSTRACT: 
							case T_FINAL: 
							case T_VAR: 
								break;

							default:
								$comments = array();
								break;
						}
					}
					else {

						$comments = array();
					}
				}
	  }

	  /**
	   * Returns an array of class level annotations or false if no class
	   * level annotations are present.
	   * 
	   * @param AnnotatedClass $class An instance of the annotated class to inspect.
	   * @return Array of class level annotations or false if no annotations
	   * 		 are present.
	   */
	  public static function getClassAnnotations( AnnotatedClass $class ) {

	  		 return isset( self::$classes[$class->getName()] ) ? self::$classes[$class->getName()] : false; 
	  }

	  /**
	   * Returns an array of property level annotations or false if no annotations
	   * are present for the specified property.
	   * 
	   * @param AnnotatedProperty $property The AnnotatedProperty instance to inspect.
	   * @return Array of property level annotations
	   */
	  public static function getPropertyAnnotations( AnnotatedProperty $property ) {

  		 	 $class = $property->getDeclaringClass()->getName();
  		 	 
  		 	 if( isset( self::$properties[$class] ) ) {

		  		 foreach( self::$properties[$class] as $name => $value )
		  		  		if( $name == $property->getName() )
		  		 			return $value;
  		 	 }

  		 	 return false;
	  }

	  /**
	   * Returns an array of method level annotations or false if no annotations
	   * are found for the specified method.
	   * 
	   * @param AnnotatedMethod $method The AnnotatedMethod instance to inspect.
	   * @return Array of method level annotations or false if no annotations are present.
	   */
	  public static function getMethodAnnotations( AnnotatedMethod $method ) {

	  	     $class = $method->getDeclaringClass()->getName();
	  	     if( isset( self::$methods[$class] ) ) {
			     
	  	     	 foreach( self::$methods[$class] as $name => $value )
			  		 	if( $name == $method->getName() )
			  		 		return $value;
	  	     }

		  	 return false;
	  }

	  /**
	   * Returns an array of class level annotations
	   * 
	   * @param String $class The name of the class to inspect
	   * @return Array of class level annotations, void otherwise
	   */
	  public static function getClassAnnotationsAsArray( $class ) {

	  		 if( array_key_exists( $class, self::$classes ) )
	  		 	 return self::$classes[$class];
	  }

	  /**
	   * Returns an array of method level annotations for the specified class
	   * 
	   * @param String $class The name of the class to inspect
	   * @param String $method The name of the method to inspect
	   * @return Array of method level annotations, void otherwise
	   */
	  public static function getMethodAnnotationsAsArray( $class ) {

	  		 if( array_key_exists( $class, self::$methods ) )
	  		 	 return self::$methods[$class];
	  }

	  /**
	   * Returns an array of property level annotations for the specified class.
	   * 
	   * @param String $class The name of the class to inspect
	   * @return Array of property level annotations, void otherwise
	   */
	  public static function getPropertyAnnotationsAsArray( $class ) {

	  		 if( array_key_exists( $class, self::$properties ) )
	  		 	 return self::$properties[$class];
	  }

	  /**
	   * Parses code extracted from the tokenized PHP file for the presence of
	   * AgilePHP annotations.
	   * 
	   * @param String $text The text/code string to parse
	   * @return void
	   */
	  private static function parseAnnotations( $text ) {

	  		  $annotations = array();

			  // Extract the annotation string including the name and property/value declaration
	  		  preg_match_all( '/^\\s*#@(.*)/', $text, $annotes );

			  if( !count( $annotes ) )
	  		  	  return;

	  		  foreach( $annotes[1] as $annote ) {

	  		  		   // Extract the annotation name
	  		  		   preg_match( '/\w+/', $annote, $className );

	  		  		   // Create instance of the annotation class or create a new instance of stdClass
	  		  		   // if the annotation class could not be parsed
	  		  		   $oAnnotation = new $className[0]();

	  		  		   // Extract name/value pair portion of the annotation
	  		  		   preg_match_all( '/\((.*=.*\(?\)?)\)/', $annote, $props );

					   // Extract arrays
					   if( count( $props ) && count( $props[1] ) )
					   	   preg_match_all( '/[_a-zA-Z]+[0-9_]?\s?=\s?{+?.*?}+\s?,?/', $props[1][0], $arrays );

					   // Extract other annotations
					   // @todo Support child annotations
					   //preg_match_all( '/@(.*)?,?/', $props[1][0], $childAnnotes );

					   // Add arrays to annotation instance and remove it from the properties
	  		  		   if( isset( $arrays ) ) {

	  		  		   	   $result = self::parseKeyArrayValuePairs( $oAnnotation, $arrays[0], $props[1][0] );
	  		  		   	   $oAnnotation = $result->annotation;
	  		  		   	   $props[1][0] = $result->properties;
					   }

					   // Add strings and PHP literals to annotation instance
					   if( count( $props ) && count( $props[1] ) )
					   	   $oAnnotation = self::parseKeyValuePairs( $oAnnotation, $props[1][0] );

					   // Push the annotation instance onto the stack
	  		  		   array_push( $annotations, $oAnnotation );
	  		  }

	  		  return $annotations;
	  }
	  
	  /**
	   * Parses an annotations property assignments which contain one or more array values. The
	   * array is added to the annotation instance according to its property name and the array
	   * is removed from the properties string.
	   * 
	   * @param Object $oAnnotation An instance of the annotation object
	   * @param String $arrays The string value containing each of the property assignments
	   * @param String $properties The annotations properties as they were parsed from the code
	   * @return stdClass instance containing the annotation instance and truncated properties string
	   */
	  private static function parseKeyArrayValuePairs( $oAnnotation, $arrays, $properties ) {

	  		 foreach( $arrays as $array ) {

		  		 	// Remove arrays from the parsed annotation property/value assignments
					$properties = preg_replace( '/' . $array . '/', '', $properties ) . PHP_EOL;

			   		// Split the array into key/value
			   		preg_match( '/(.*)={1}\s?\{(.*)\},?/', $array, $matches );
			   		$property = trim( $matches[1] );
			   		$elements = explode( ',', trim( $matches[2] ) );

			   		// Place each of the annotations array elements into a PHP array
			 		$value = array();
			   		foreach( $elements as $element ) {
	
			   				$pos = strpos( $element, '=' );
			   				if( $pos !== false ) {
	
			   					// Associative array element
			   					$pieces = explode( '=', $element );
			   					//$value[ trim( $pieces[0] ) ] = trim( $pieces[1] );
			   					$value[ trim( $pieces[0] ) ] = self::getQuotedStringValue( $pieces[1] );
			   				}
			   				else

			   					// Indexed array element
			   					array_push( $value, self::getQuotedStringValue( $element ) );
			   		}

			   		// Set the annotation instance property with the PHP array
			   		$oAnnotation->$property = $value;
			   }

			   $stdClass = new stdClass();
			   $stdClass->annotation = $oAnnotation;
			   $stdClass->properties = $properties;

			   return $stdClass;
	  }

	  /**
	   * Parses quoted strings from annotations property VALUE definitons.
	   * 
	   * @param String $value The value to parse
	   * @return void
	   */
	  private static function getQuotedStringValue( $value ) {

	  		  // Single quoted value
  	  		  $pos = strpos( $value, '\'' );
  	  		  if( $pos !== false ) {

  	  		      preg_match( '/\s?\'+(.*)\'+/', $value, $matches );
  	  		   	  if( count( $matches ) == 2 )
  	  		   	      return $matches[1];
  	  		   }

  	  		   // Double quoted value
			   $pos = strpos( $value, '"' );
  	  		   if( $pos !== false ) {

  	  		       preg_match( '/\s?"+(.*)"+/', $value, $matches );
  	  		   	   if( count( $matches ) == 2 )
  	  		   	 	   return $matches[1];
  	  		   }

  	  		   // Treat unquoted values as objects
			   $o = str_replace( ' ', '', $value );
  	  		   return new $o;
	  }

	  /**
	   * Parses strings and PHP literals from annotation property definition(s).
	   * 
	   * @param Object $oAnnotation An instance of the annotation object
	   * @param String $properties String representation of the annotations property definition(s).
	   * @return The annotation instance populated according to its definition(s).
	   */
	  private static function parseKeyValuePairs( $oAnnotation, $properties ) {

  		  	 $keyValuePairItems = explode( ',', $properties );

  	  		 foreach( $keyValuePairItems as $kv ) {

  	  		   		  $pieces = explode( '=', $kv );

  	  		   		  preg_match( '/(.*)=(.*)/', $kv, $pieces );

  	  		   		  if( count( $pieces ) < 2 ) continue;

  	  		   		  $property = trim( $pieces[1] );
  	  		   		  $value = trim( $pieces[2] );

  	  		   		  // Single quoted value
  	  		   		  $pos = strpos( $value, '\'' );
  	  		   		  if( $pos !== false ) {

  	  		   			  preg_match( '/^\'(.*)\'/', $value, $matches );
  	  		   			  if( count( $matches ) == 2 ) {

  	  		   				  $oAnnotation->$property = $matches[1];
  	  		   				  continue;
  	  		   			  }
  	  		   		  }

  	  		   		  // Double quoted value
					  $pos = strpos( $value, '"' );
  	  		   		  if( $pos !== false ) {

  	  		   			  preg_match( '/^"(.*)"/', $value, $matches );
  	  		   			  if( count( $matches ) == 2 ) {

  	  		   				  $oAnnotation->$property = $matches[1];
  	  		   				  continue;
  	  		   			  }
  	  		   		  }

  	  		   		  // Treat values which are not quoted as PHP literals
  	  		   		  if( $property && $value )
  	  		   			  $oAnnotation->$property = eval( 'return ' . $value . ';' );
  	  		   }

  	  		   return $oAnnotation;
	  }

	  /**
	   * Returns the PHP file content to be parsed.
	   * 
	   * @return PHP code
	   */
	  public static function getSourceCode() {

	  		 if( $code = self::search( AgilePHP::getFramework()->getFrameworkRoot() ) )
	  		     return $code;

	  		 if( $code = self::search( AgilePHP::getFramework()->getWebRoot() ) )
	  		     return $code;

	  		 throw new AgilePHP_AnnotationException( 'Failed to load source code for class \'' . $this->class . '\'.' );
	  }

	  /**
	   * Recursively scan the specified directory in an effort to find $this->class to load its
	   * source code.
	   * 
	   * @param String $directory The directory to inspect. 
	   * @return File contents for $this->class or void if the file contents could not be located
	   */
	  private static function search( $directory ) {

	  	 $it = new RecursiveDirectoryIterator( $directory );
		 foreach( new RecursiveIteratorIterator( $it ) as $file ) {

		   	      if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..'  &&
		   	      	  substr( $file, -4 ) != 'view' ) {

			 		  if( array_pop( explode( DIRECTORY_SEPARATOR, $file ) ) == self::$filename )
		     	 			  return file_get_contents( $file );
			      }
		 }
	  }
}
?>