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
 * Extends the PHP ReflectionProperty to provide details about property
 * level AgilePHP annotations.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.annotation
 */
class AnnotatedProperty extends ReflectionProperty {

      private $annotations;

      /**
       * Creates a new instance of AnnotatedProperty.
       *
       * @param mixed type $class The name or instance of a class to inspect.
       * @param string $property The name of the property to inspect.
       * @return void
       * @throws AnnotationException
       */
      public function __construct($class, $property) {

             try {
                    parent::__construct($class, $property);

                    if($cacher = AgilePHP::getCacher()) {

                         $cacheKey = 'AGILEPHP_ANNOTATEDPROPERTY_' . parent::getDeclaringClass()->getName() . $property;
                         if($cacher->exists($cacheKey)) {
    
                            $this->annotations = $cacher->get($cacheKey);
                            return;
                         }
                    }

                    $this->annotations = AnnotationParser::getPropertyAnnotations($this);
                    if(isset($cacher)) $cacher->set($cacheKey, $this->annotations);  
             }
             catch(ReflectionException $e) {

                   throw new AnnotationException($e->getMessage(), $e->getCode());
             }
      }

      /**
       * Returns boolean indicator based on the presence of any annotations.
       *
       * @return True if this property has any annotations, false otherwise.
       */
      public function isAnnotated() {

              return count($this->annotations) && isset($this->annotations[0]) ? true : false;
      }

      /**
       * Checks the property for the presence of the specified annotation.
       *
       * @param String $annotation The name of the annotation.
       * @return True if the annotation is present, false otherwise.
       */
      public function hasAnnotation($annotation) {

             foreach($this->annotations as $annote) {

                 $class = new ReflectionClass($annote);
                 if($class->getName() == $annotation)
                    return true;
             }

             return false;
      }

      /**
       * Returns all property annotations. If a name is specified
       * only annotations which match the specified name will be returned,
       * otherwise all annotations are returned.
       *
       * @param String $name Optional name of the annotation to filter on. Default is return
       *                      all annotations.
       * @return An array of property level annotations or false of no annotations could
       *          be found.
       */
      public function getAnnotations($name = null) {

             if($name != null) {

                $annotations = array();
                foreach($this->annotations as $annote) {

                    if($annote instanceof $name)
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

                 $class = new ReflectionClass($annote);
                 if($class->getName() == $annotation)
                    array_push($annotations, $annote);
             }

             if(!count($annotations)) return false;

             return (count($annotations) > 1) ? $annotations : $annotations[0];
      }

      /**
       * Gets the parent class as an AnnotatedClass
       *
       * @return AnnotatedClass
       */
      public function getDeclaringClass() {

             $class = parent::getDeclaringClass();
             return new AnnotatedClass($class->getName());
      }
}
?>