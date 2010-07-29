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

      private static $class;
      private static $filename;
      private static $cacher;

      private function __construct() { }
      private function __clone() { }

      /**
       * Adds parsed class level annotations to the stack
       *
       * @param string $class The class name
       * @param array $annotations An array of parsed class level annotations
       * @return void
       */
      private static function addClass($class, array $annotations) {

              if(self::$cacher) {

                 $key = 'AGILEPHP_ANNOTATION_' . $class;
                 if(self::$cacher->exists($key)) return;

                 self::$classes[$class] = $annotations;
                 self::$cacher->set($key, self::$classes);
                 return;
              }

              self::$classes[$class] = $annotations;
      }

      /**
       * Retrieves annotations parsed from the specified class
       *
       * @param string $class The class name
       * @return mixed An array of annotations if any were parsed, void otherwise
       */
      private static function getClass($class) {

              if(self::$cacher) {

                 $key = 'AGILEPHP_ANNOTATION_' . $class;
                 if($value = self::$cacher->get($key)) return $value;
              }

              return self::$classes;
      }

      /**
       * Adds parsed method level annotations to the stack
       *
       * @param string $class The class name
       * @param string $method The method name
       * @param array $annotations An array of parsed method level annotations
       * @return void
       */
      private static function addMethod($class, $method, array $annotations) {

              if(self::$cacher) {

                 $key = 'AGILEPHP_ANNOTATION_' . $class . '_M';
                 if(self::$cacher->exists($key)) return;

                 self::$methods[$class][$method] = $annotations;
                 self::$cacher->set($key, self::$methods);
                 return;
              }

              self::$methods[$class][$method] = $annotations;
      }

      /**
       * Retrieves a list of method level annotations for the specified class
       *
       * @param string $class The class name
       * @return mixed An array of parsed method level annotations if any were parsed, void otherwise
       */
      private static function getMethods($class) {

              if(self::$cacher) {

                 $key = 'AGILEPHP_ANNOTATION_' . $class . '_M';
                 if($value = self::$cacher->get($key)) return $value;
              }

              return self::$methods;
      }

      /**
       * Adds a property level annotation to the stack
       *
       * @param string $class The class name
       * @param string $property The property/field name
       * @param array $annotations An array of parsed property level annotations
       * @return void
       */
      private static function addProperty($class, $property, array $annotations) {

              if(self::$cacher) {

                 $key = 'AGILEPHP_ANNOTATION_' . $class . '_P';
                 if(self::$cacher->exists($key)) return;

                 self::$properties[$class][$property] = $annotations;
                 self::$cacher->set($key, self::$properties);
                 return;
              }

              self::$properties[$class][$property] = $annotations;
      }

      /**
       * Retrieves a list of parsed property annotations for the specified class
       *
       * @param string $class The name of the class to retrieve the properties for
       * @return array
       */
      private static function getProperties($class) {

              if(self::$cacher) {

                 $key = 'AGILEPHP_ANNOTATION_' . $class . '_P';
                 if($value = self::$cacher->get($key)) return $value;
              }

              return self::$properties;
      }

      /**
       * Breaks the class file into PHP tokens and extracts all interface,
       * class, method, and property level annotations.
       *
       * @param String $class The name of the class to parse
       * @return void
       * @static
       */
      public static function parse($class) {

             self::$cacher = AgilePHP::getCacher();
             self::$class = $class;
             self::$filename = $class . '.php';

             // @todo continue optimizations
             //if(!self::$cacher) {

                 if(in_array(self::$filename, self::$sources)) return;
                 array_push(self::$sources, self::$filename);
             //}

             $comments = array();
             $tokens = token_get_all(self::getSourceCode());

             for($i=0; $i<count($tokens); $i++) {

                 $token = $tokens[$i];
                 if(is_array($token)) {

                    list($code, $value) = $token;

                    switch($code) {

                           case T_COMMENT:

                                  if(preg_match('/^\\s*#@/', $value))
                                     array_push($comments, $value);
                                break;

                           case T_CLASS:

                                 if(count($comments)) {
                                    self::addClass($class, self::parseAnnotations($comments));
                                    $comments = array();
                                 }
                                 break;

                           case T_VARIABLE:

                                if(count($comments)) {
                                   $key = str_replace('$', '', $token[1]);
                                   self::addProperty($class, $key, self::parseAnnotations($comments));
                                   $comments = array();
                                }
                                break;

                           case T_FUNCTION:

                                if(count($comments)) {
                                   for($j=$i; $j<count($tokens); $j++) {
                                       if(is_array($tokens[$j])) {
                                          if($tokens[$j][0] == T_STRING) {
                                              self::addMethod($class, $tokens[$j][1], self::parseAnnotations($comments));
                                              $comments = array();
                                              break;
                                          }
                                       }
                                   }
                                }
                                 break;

                            /** @todo Support annotated interfaces */
                            // case T_INTERFACE:

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
       *          are present.
       * @static
       */
      public static function getClassAnnotations(AnnotatedClass $class) {

             $classes = self::getClass($class->getName());
             return isset($classes[$class->getName()]) ? $classes[$class->getName()] : false;
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
             $properties = self::getProperties($class);

             if(isset($properties[$class])) {

                foreach($properties[$class] as $name => $value)
                    if($name == $property->getName())
                       return $value;
             }
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
             $methods = self::getMethods($class);

             if(isset($methods[$class])) {

                foreach($methods[$class] as $name => $value)
                    if($name == $method->getName())
                       return $value;
             }
      }

      /**
       * Returns an array of class level annotations
       *
       * @param String $class The name of the class to inspect
       * @return Array of class level annotations, void otherwise
       * @static
       */
      public static function getClassAnnotationsAsArray($class) {

             $classes = self::getClass($class);
             if(array_key_exists($class, $classes))
                  return $classes[$class];
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

             $methods = self::getMethods($class);
             if(array_key_exists($class, $methods))
                return $methods[$class];
      }

      /**
       * Returns an array of property level annotations for the specified class.
       *
       * @param String $class The name of the class to inspect
       * @return Array of property level annotations, void otherwise
       * @static
       */
      public static function getPropertyAnnotationsAsArray($class) {

             $properties = self::getProperties($class);
             if(array_key_exists($class, $properties))
                return $properties[$class];
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
                                 $oAnnotation = $result->annotation;
                                 $props[1][0] = $result->properties;
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
                      $properties = preg_replace('/' . $array . '/', '', $properties) . PHP_EOL;

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

              $keyValuePairItems = explode(',', $properties);

              foreach($keyValuePairItems as $kv) {

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
       * @return String The raw PHP source code for the file being parsed
       * @static
       * @throws AnnotationException if the source could not be retrieved
       */
      private static function getSourceCode() {

                try {
                      return AgilePHP::getSource(self::$class);
                }
                catch(FrameworkException $e) {

                       throw new AnnotationException($e->getMessage(), $e->getCode());
                }
      }
}
?>