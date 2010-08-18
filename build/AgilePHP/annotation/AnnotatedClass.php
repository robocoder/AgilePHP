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
 * Extends the PHP ReflectionClass to provide details about class
 * level AgilePHP annotations.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.annotation
 */
class AnnotatedClass extends ReflectionClass {

      private $annotations = array();

      /**
       * Inializes the AnnotatedClass by parsing the passed class file for
       * AgilePHP annotations. Uses AgilePHP CacheProvider if enabled.
       *
       * @param mixed $class The name or instance of the class to inspect
       * @return AnnotatedClass
       * @throws AnnotationException
       */
      public function __construct($class) {

             try {
                   parent::__construct($class);

                   if($cacher = AgilePHP::getCacher()) {

                      $cacheKey = 'AGILEPHP_ANNOTATEDCLASS_' . parent::getName();
                      if($cacher->exists($cacheKey)) {

                         $this->annotations = $cacher->get($cacheKey);
                         return;
                      }
                   }

                   if(!$this->annotations = AnnotationParser::getClassAnnotations($this)) {

                      AnnotationParser::parse(parent::getName());
                      $this->annotations = AnnotationParser::getClassAnnotations($this);
                   }

                   if(isset($cacher)) $cacher->set($cacheKey, $this->annotations);
               }
               catch(ReflectionException $e) {

                     throw new AnnotationException($e->getMessage(), $e->getCode());
               }
      }

      /**
       * Returns boolean indicator based on whether or not there are any annotations present.
       *
       * @return True if the class has any class level annotations. False if not.
       */
      public function isAnnotated() {

             return count($this->annotations) && isset($this->annotations[0]) ? true : false;
      }

      /**
       * Checks the class for the presence of a class level annotation.
       *
       * @param String $annotation The name of the annotation to confirm.
       * @return True if the annotation is present, false otherwise.
       */
      public function hasAnnotation($annotation) {

             if(!$this->isAnnotated()) return false;

             foreach($this->annotations as $annote) {

                 $class = new parent($annote);
                 if($class->getName() == $annotation)
                    return true;
             }

             return false;
      }

      /**
       * Returns all class level annotations. If a name is specified
       * only annotations which match the specified name will be returned,
       * otherwise all annotations are returned.
       *
       * @param String $name Optional annotation name to filter out. Default is return all
       *               annotations.
       * @return An array of class level annotations or false of no annotations could
       *          be found.
       */
      public function getAnnotations($name = null) {

             if($name != null) {

                $annotations = array();
                foreach($this->annotations as $annote) {

                    $class = new parent($annote);
                    if($class->getName() == $annotation)
                       array_push($annotations, $annote);
                }

                if(!count($annotations)) return false;

                return $annotations;
             }

             return $this->annotations;
      }

      /**
       * Gets an annotation instance by name. If the named annotation is found more
       * than once, an array of annotations are returned.
       *
       * @param String $name The name of the annotation
       * @return The annotation instance or false if the annotation was not found
       */
      public function getAnnotation($annotation) {

             $annotations = array();

             foreach($this->annotations as $annote) {

                 $class = new parent($annote);
                 if($class->getName() == $annotation)
                    array_push($annotations, $annote);
             }

             if(!count($annotations)) return false;

             return (count($annotations) > 1) ? $annotations : $annotations[0];
      }

      /**
       * Returns an AnnotatedMethod instance for the specified method name.
       *
       * @param String $name The method name
       * @return AnnotatedMethod
       */
      public function getMethod($name) {

               return new AnnotatedMethod(parent::getName(), $name);
      }

      /**
       * Returns an array of AnnotatedMethod objects, one for each method in the
       * class which contains an annotation.
       *
       * @param String $filter The filter
       * @return Array of AnnotatedMethod objects.
       * @see http://php.net/manual/en/reflectionclass.getmethods.php
       */
      public function getMethods($filter = null) {

             if(!$filter)
                $filter = ReflectionMethod::IS_PUBLIC + ReflectionMethod::IS_PROTECTED + ReflectionMethod::IS_PRIVATE;

             $methods = array();
             foreach(parent::getMethods($filter) as $method) {

                 $m = new AnnotatedMethod(parent::getName(), $method->name);
                 if($m->isAnnotated())
                    array_push($methods, $m);
             }

             return $methods;
      }

      /**
       * Returns an AnnotatedProperty instance for the specified property name.
       *
       * @param String $name The name of the property
       * @return AnnotatedProperty
       */
      public function getProperty($name) {

             return new AnnotatedProperty(parent::getName(), $name);
      }

      /**
       * Returns an array of AnnotatedProperty objects; one for each property
       * in the class which contains an annotation.
       *
       * @param String $filter The optional filter
       * @return Array of AnnotatedProperty objects.
       * @see http://www.php.net/manual/en/reflectionclass.getproperties.php
       */
      public function getProperties($filter = null) {

             if(!$filter)
                $filter = ReflectionProperty::IS_PUBLIC + ReflectionProperty::IS_PROTECTED + ReflectionProperty::IS_PRIVATE;

             $properties = array();
             foreach(parent::getProperties($filter) as $property) {

                 $p = new AnnotatedProperty(parent::getName(), $property->name);
                 if($p->isAnnotated())
                    array_push($properties, $p);
             }

             return $properties;
      }
}
?>