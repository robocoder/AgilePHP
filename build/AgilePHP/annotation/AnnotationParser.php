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
 */
class AnnotationParser {

      private static $classes = array();
      private static $properties = array();
      private static $methods = array();
      private static $sources = array();

      private static $filename;
      private static $cacher;

      private function __construct() { }
      private function __clone() { }

      /**
       * Breaks the class file into PHP tokens and extracts all interface,
       * class, method, and property level annotations. Uses AgilePHP
       * CacheProvider if enabled.
       *
       * @param String $class The name of the class to parse
       * @return void
       * @static
       */
      public static function parse($class) {

             self::$cacher = AgilePHP::getCacher();
             self::$filename = $class . '.php';

             // First level cache - non-persistent
             if(in_array(self::$filename, self::$sources)) return;
             array_push(self::$sources, self::$filename);

             // Second level cache - persistent
             if(self::$cacher) {

                $cacheKey = 'AGILEPHP_ANNOTATIONPARSER_PARSE_' . $class;
                if(self::$cacher->exists($cacheKey)) {

                   $annotes = self::$cacher->get($cacheKey);

                   self::$classes[$class] = $annotes->classes;
                   self::$methods[$class] = $annotes->methods;
                   self::$properties[$class] = $annotes->properties;
                   return;
                }
             }

             $comments = array();
             $tokens = token_get_all(self::getSourceCode($class));
             $tcount = count($tokens);

             for($i=0; $i<$tcount; $i++) {

                 if(is_array($tokens[$i])) {

                    $ccount = count($comments);

                    switch($tokens[$i][0]) {

                           case T_COMMENT:

                               if(strpos($tokens[$i][1], '#@') === 0)
                                  array_push($comments, $tokens[$i][1]);
                               break;

                           case T_CLASS:

                                 if($ccount) {
                                    self::$classes[$class] = self::parseAnnotations($comments);
                                    $comments = array();
                                 }
                                 break;

                           case T_VARIABLE:

                                if($ccount) {
                                   $key = str_replace('$', '', $tokens[$i][1]);
                                   self::$properties[$class][ $key ] = self::parseAnnotations($comments);
                                   $comments = array();
                                }
                                break;

                           case T_FUNCTION:

                                if($ccount) {
                                   for($j=$i; $j<$tcount; $j++) {
                                       if(is_array($tokens[$j])) {
                                          if($tokens[$j][0] == T_STRING) {
                                              self::$methods[$class][$tokens[$j][1]] = self::parseAnnotations($comments);
                                              $comments = array();
                                              break;
                                          }
                                       }
                                   }
                                }
                                break;

                           case T_STATIC:

                               if(isset($comments[0])) {

                                  // Static methods can be defined using the following:
                                  // "context" static function name()
                                  // static "context" function name()
                                  // static function name()
                                  // The following for loop makes sure all possibilities are accounted for
                                  // and the function/method name is parsed.
                                  //
                                  // Properties/fields are also parsed
                                  $count = 0;
                                  for($j=$i; $j<$tcount; $j++) {
                                      $count++;
                                      if(is_array($tokens[$i])) {
                                         if($tokens[$i+$count][0] == T_FUNCTION) {
                                            self::$methods[$class][$tokens[$i+$count+2][1]] = self::parseAnnotations($comments);
                                            $comments = array();
                                            break;
                                         }
                                         elseif(isset($tokens[$i+$count][1]) && strpos($tokens[$i+$count][1], '$') === 0) {

                                             $key = str_replace('$', '', $tokens[$i+$count][1]);
                                             self::$properties[$class][$key] = self::parseAnnotations($comments);
                                             $comments = array();
                                             break;
                                         }
                                      }
                                  }
                               }
                               break;

                            /** @todo Support annotated interfaces */
                            // case T_INTERFACE:

                            case T_DOC_COMMENT:
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

                if(self::$cacher) {

                   $annotes = new stdClass;
                   $annotes->classes = isset(self::$classes[$class]) ? self::$classes[$class] : array();
                   $annotes->methods = isset(self::$methods[$class]) ? self::$methods[$class] : array();
                   $annotes->properties = isset(self::$properties[$class]) ? self::$properties[$class] : array();

                   self::$cacher->set($cacheKey, $annotes);
             }
      }

      /**
       * Returns an array of class level annotations or false if no class
       * level annotations are present.
       *
       * @param AnnotatedClass $class An instance of the annotated class to inspect.
       * @return Array of class level annotations or false if no annotations
       *          are present.
       * @static
       */
      public static function getClassAnnotations(AnnotatedClass $class) {

             return isset(self::$classes[$class->getName()]) ? self::$classes[$class->getName()] : false;
      }

      /**
       * Returns an array of property level annotations or false if no annotations
       * are present for the specified property.
       *
       * @param AnnotatedProperty $property The AnnotatedProperty instance to inspect.
       * @return Array of property level annotations
       * @static
       */
      public static function getPropertyAnnotations(AnnotatedProperty $property) {

             $class = $property->getDeclaringClass()->getName();

  		 	 if(isset(self::$properties[$class])) {

		  		foreach(self::$properties[$class] as $name => $value)
		  		  		if($name == $property->getName())
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
       * @static
       */
      public static function getMethodAnnotations(AnnotatedMethod $method) {

             $class = $method->getDeclaringClass()->getName();
	  	     if(isset(self::$methods[$class])) {

	  	     	foreach(self::$methods[$class] as $name => $value)
			  		    if($name == $method->getName())
			  		       return $value;
	  	     }

		  	 return false;
      }

      /**
       * Returns an array of class level annotations
       *
       * @param String $class The name of the class to inspect
       * @return Array of class level annotations, void otherwise
       * @static
       */
      public static function getClassAnnotationsAsArray($class) {

             if(array_key_exists($class, self::$classes))
	  		 	return self::$classes[$class];
      }

      /**
       * Returns an array of method level annotations for the specified class
       *
       * @param String $class The name of the class to inspect
       * @param String $method The name of the method to inspect
       * @return Array of method level annotations, void otherwise
       * @static
       */
      public static function getMethodAnnotationsAsArray($class) {

             if(array_key_exists($class, self::$methods))
                return self::$methods[$class];
      }

      /**
       * Returns an array of property level annotations for the specified class.
       *
       * @param String $class The name of the class to inspect
       * @return Array of property level annotations, void otherwise
       * @static
       */
      public static function getPropertyAnnotationsAsArray($class) {

             if(array_key_exists($class, self::$properties))
	  		    return self::$properties[$class];
      }

      /**
       * Parses code extracted from the tokenized PHP file for the presence of
       * AgilePHP annotations.
       *
       * @param String $text The text/code string to parse
       * @return void
       * @static
       */
      private static function parseAnnotations(array $items) {

              $annotations = array();

              foreach($items as $text) {

                  // Extract the annotation string including the name and property/value declaration
                  preg_match_all('/^\\s*#@(.*)/', $text, $annotes);

                  if(!count($annotes)) return;

                     foreach($annotes[1] as $annote) {

                             // Extract the annotation name
                             preg_match('/\w+/', $annote, $className);

                             // Create instance of the annotation class or create a new instance of stdClass
                             // if the annotation class could not be parsed
                             $oAnnotation = new $className[0]();

                             // Extract name/value pair portion of the annotation
                             preg_match_all('/\((.*=.*\(?\)?)\)/', $annote, $props);

                             // Extract arrays
                             if(count($props) && count($props[1]))
                                preg_match_all('/[_a-zA-Z]+[0-9_]?\\s?=\s?{+?.*?}+\\s?,?/', $props[1][0], $arrays);

                              // Extract other annotations
                              // @todo Support child annotations
                              //preg_match_all('/@(.*)?,?/', $props[1][0], $childAnnotes);

                              // Add arrays to annotation instance and remove it from the properties
                              if(isset($arrays[0]) && $arrays[0] != null) {

                                 $result = self::parseKeyArrayValuePairs($oAnnotation, $arrays[0], $props[1][0]);
                                 $oAnnotation = $result['annotation'];
                                 $props[1][0] = $result['properties'];
                              }

                              // Add strings and PHP literals to annotation instance
                              if(count($props) && count($props[1]))
                                 $oAnnotation = self::parseKeyValuePairs($oAnnotation, $props[1][0]);

                              // Push the annotation instance onto the stack
                              array_push($annotations, $oAnnotation);
                      }
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
       * @static
       */
      private static function parseKeyArrayValuePairs($oAnnotation, $arrays, $properties) {

              foreach($arrays as $array) {

                      // Remove arrays from the parsed annotation property/value assignments
                      $properties = str_replace($array, '', $properties) . PHP_EOL;

                      // Split the array into key/value
                      preg_match('/(.*)=\s*?\{(.*)\},?/', $array, $matches);
                      $property = trim($matches[1]);
                      $elements = explode(',', trim($matches[2]));

                      // Place each of the annotations array elements into a PHP array
                      $value = array();
                      foreach($elements as $element) {

                          $pos = strpos($element, '=');
                          if($pos !== false) {

                             // Associative array element
                             $pieces = explode('=', $element);
                             $value[trim($pieces[0])] = self::getQuotedStringValue($pieces[1]);
                          }
                          else

                             // Indexed array element
                             array_push($value, self::getQuotedStringValue($element));
                       }

                       // Set the annotation instance property with the PHP array
                       $oAnnotation->$property = $value;
               }

               return array('annotation' => $oAnnotation, 'properties' => $properties);
      }

      /**
       * Parses quoted strings from annotations property VALUE definitons.
       *
       * @param String $value The value to parse
       * @return void
       * @static
       */
      private static function getQuotedStringValue($value) {

              // Single quoted value
              $pos = strpos($value, '\'');
              if($pos !== false) {

                 preg_match('/\s?\'+(.*)\'+/', $value, $matches);
                 if(count($matches) == 2)
                    return $matches[1];
              }

              // Double quoted value
              $pos = strpos($value, '"');
              if($pos !== false) {

                 preg_match('/\s?"+(.*)"+/', $value, $matches);
                 if(count($matches) == 2)
                    return $matches[1];
              }

              // Treat unquoted values as objects
              // @todo This needs to be examined deeper
              //$o = str_replace(' ', '', $value);
              $o = preg_replace('/new/i', '', $value);
              $o = preg_replace('/\(\)/i', '', trim($o));

              if($o) return new $o;
      }

      /**
       * Parses strings and PHP literals from annotation property definition(s).
       *
       * @param Object $oAnnotation An instance of the annotation object
       * @param String $properties String representation of the annotations property definition(s).
       * @return The annotation instance populated according to its definition(s).
       * @static
       */
      private static function parseKeyValuePairs($oAnnotation, $properties) {

              $kvpair = explode(',', $properties);

              foreach($kvpair as $kv) {

                  $pieces = explode('=', $kv);
                  preg_match('/(.*)=(.*)/', $kv, $pieces);

                  if(count($pieces) < 2) continue;

                     $property = trim($pieces[1]);
                     $value = trim($pieces[2]);

                     // Single quoted value
                     $pos = strpos($value, '\'');
                     if($pos !== false) {

                        preg_match('/^\'(.*)\'/', $value, $matches);
                        if(count($matches) == 2) {

                           $oAnnotation->$property = $matches[1];
                           continue;
                        }
                     }

                     // Double quoted value
                     $pos = strpos($value, '"');
                     if($pos !== false) {

                        preg_match('/^"(.*)"/', $value, $matches);
                        if(count($matches) == 2) {

                           $oAnnotation->$property = $matches[1];
                           continue;
                        }
                     }

                     // Treat values which are not quoted as PHP literals
                     if($property && $value)
                        $oAnnotation->$property = eval('return ' . $value . ';');
               }

               return $oAnnotation;
      }

      /**
       * Retrieves the raw PHP source code for the file being parsed.
       *
       * @param string $class The class name to return the source code for.
       * @return string The raw PHP source code for the file being parsed
       * @throws AnnotationException if the source could not be retrieved
       * @static 
       */
      private static function getSourceCode($class) {

                try {
                      return AgilePHP::getSource($class);
                }
                catch(FrameworkException $e) {

                       throw new AnnotationException($e->getMessage(), $e->getCode());
                }
      }
}
?>