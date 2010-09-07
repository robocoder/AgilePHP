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
 * @package com.makeabyte.agilephp
 */

require 'FrameworkException.php';
require 'MVC.php';

/**
 * AgilePHP core framework
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @static
 */
final class AgilePHP {

      private static $displayPhpErrors = true;
      private static $webroot;                    // The full system path to the web application
      private static $frameworkRoot;              // The full system path to the location of the AgilePHP framework
      private static $documentRoot;               // The relative path to the web app from the server's document root.
      private static $requestBase;                // The base request URL used to communicate with MVC component (bootstrap)
      private static $debugMode = false;          // Whether or not this component is running in debug mode
      private static $xml;                        // AgilePHP configuration - agilephp.xml
      private static $appName;                    // Name of the AgilePHP application
      private static $interceptions = array();    // An array of interceptions which have occurred during __autoload
      private static $startTime;                  // Used with startClock and stopClock methods
      private static $cacher;                     // Stores a CacheProvider instance if configured in agilephp.xml

      private function __construct() { }
      private function __clone() { }

      /**
       * Initalize the AgilePHP framework. Sets the following defaults \n
       * $webroot = current working directory (of the script that instantiated the framework)
       * $requestBase = name of the script that instantiated the framework
       * $frameworkRoot = $webroot/AgilePHP
       * $appName = The HTTP HOST header value
       *
       * @return void
       * @static
       */
      public static function init($agilephpDotXml = null) {

             self::$webroot = getcwd();
             self::$requestBase = $_SERVER['SCRIPT_NAME'];

             if(!self::$frameworkRoot)
                self::$frameworkRoot = self::$webroot . DIRECTORY_SEPARATOR . 'AgilePHP';

             self::parseXml($agilephpDotXml);
      }

     /**
       * Parses AgilePHP configuration file (agilephp.xml) and initalizes the
       * framework according to the specified configuration.
       *
       * @param string $agilephpDotXml Optional file path to agilephp.xml configuration file
       * @return void
       * @static
       */
      private static function parseXml($agilephpDotXml = null) {

              $agilephp_xml = ($agilephpDotXml) ? $agilephpDotXml : self::$webroot . DIRECTORY_SEPARATOR . 'agilephp.xml';
              if(self::$cacher) {

                 $key = 'AGILEPHP_CONFIG';
                 if(self::$cacher->exists('AGILEPHP_CONFIG'))
                    return self::$cacher->get('AGILEPHP_CONFIG');
              }

              if(!file_exists($agilephp_xml)) {

                  spl_autoload_register('AgilePHP::autoloadNoAnnotations', true, true);
                  return;
              }

              $dom = new DOMDocument();
              $dom->Load($agilephp_xml);
              if(!$dom->validate())
                  throw new FrameworkException('agilephp.xml Document Object Model validation failed. Validate your document using AgilePHP/agilephp.dtd');

              self::$xml = simplexml_load_file($agilephp_xml);
              if(isset($key)) self::$cacher->set($key, self::$xml);

              if(self::$xml->mvc) {

                 if($requestBase = self::$xml->mvc->attributes()->requestBase)
                    self::$requestBase = $requestBase;

                  MVC::init((string)self::$xml->mvc->attributes()->controller,
                            (string)self::$xml->mvc->attributes()->action,
                            (string)self::$xml->mvc->attributes()->renderer,
                            (string)self::$xml->mvc->attributes()->sanitize,
                            self::$xml->mvc->cache);
              }

              if(self::$xml->annotations) {

                 require_once 'Annotation.php';
                 require_once 'Interception.php';
                 spl_autoload_register('AgilePHP::autoload', true, true);
              }
              else
                 spl_autoload_register('AgilePHP::autoloadNoAnnotations', true, true);

              if(self::$xml->caching) {

                 require 'cache/CacheException.php';

                 $provider = (string)self::$xml->caching->attributes()->provider;
                 if($provider) self::$cacher = new $provider;
              }
      }

      /**
       * Returns a CacheProvider implementation if one has been configured in agilephp.xml
       *
       * @return CacheProvider A new CacheProvider implementation
       * @static
       */
      public static function getCacher() {

             return self::$cacher;
      }

      /**
       * Sets the fully qualified path to the base directory of
       * the web application.
       *
       * @param $path The fully qualified path to the web application
       * @return void
       * @static
       */
      public static function setWebRoot($path) {

             self::$webroot = $path;
      }

      /**
       * Returns the fully qualified path to the base directory of
       * the web application.
       *
       * @return The fully qualified path to the web application
       * @static
       */
      public static function getWebRoot() {

             return self::$webroot;
      }

      /**
       * Sets the full system path to the location of the AgilePHP framework. The given path is
       * appended to the current php.ini include_path configuration.
       *
       * @param $path The full system path to the location where AgilePHP framework resides.
       * @return void
       * @static
       */
      public static function setFrameworkRoot($path) {

             self::$frameworkRoot = $path;

             $include_path = ini_get('include_path');

             if(strpos($include_path, ':' . $path) === false)
                ini_set('include_path', $include_path . PATH_SEPARATOR . ':' . $path);

             //Log::debug('Initalizing framework with php include_path: ' . ini_get('include_path'));
      }

      /**
       * Gets the full system path to the location where AgilePHP resides.
       *
       * @return The full system path to the location of the AgilePHP framework.
       * @static
       */
      public static function getFrameworkRoot() {

             return self::$frameworkRoot;
      }

      /**
       * Sets the relative path to the web application from the server's document root.
       *
       * @param String $path The document root path
       * @return void
       * @static
       */
      public static function setDocumentRoot($path) {

              self::$documentRoot = $path;
      }

      /**
       * Returns the relative path to the web application from the server's document root.
       *
       * @return The web applications relative path from the server's document root
       * @static
       */
      public static function getDocumentRoot() {

             if(!self::$documentRoot) {

                $pieces = explode('.php', $_SERVER['SCRIPT_NAME']);
                array_pop($pieces);
                $docRootPieces = array();
                $newPieces = explode('/', implode('/', $pieces));
                for($i=0; $i<(count($newPieces) - 1); $i++)
                    $docRootPieces[$i] = $newPieces[$i];

                self::$documentRoot = implode('/', $docRootPieces);
             }

             return self::$documentRoot;
      }

      /**
       * Returns the base action url used to communicate with the
       * AgilePHP MVC component. Defaults to the name of the script
       * which initalizes the framework.
       *
       * @return The base action url used to communicate with the AgilePHP MVC component.
       * @static
       */
      public static function getRequestBase() {

             return self::$requestBase;
      }

      /**
       * Sets the base action url used to communicate with the AgilePHP MVC component.
       *
       * @param $url The base url to be used to communicate with the AgilePHP MVC component.
       * @return void
       * @static
       */
      public static function setRequestBase($url) {

             self::$requestBase = $url;
      }

      /**
       * Sets the name of the AgilePHP web application.
       *
       * @param String $name The name of the AgilePHP application
       * @return void
       * @static
       */
      public static function setAppName($name) {

             self::$appName = $name;
      }

      /**
       * Returns the name of the AgilePHP web application.
       *
       * @return AgilePHP web application name
       * @static
       */
      public static function getAppName() {

              if(!self::$appName)
                  self::$appName = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : 'localhost';

              return self::$appName;
      }

      /**
       * Loads a class from the web application 'classes' or 'components' directory using a
       * package dot type notation. First the classes directory is searched, then components.
       *
       * @param String $classpath The dot notation classpath (my.package.ClassName)
       * @return void
       * @throws FrameworkException If an error occurred loading the specified classpath
       * @static
       */
      public static function import($classpath) {

             Log::debug('AgilePHP::import ' . $classpath);

             $file = preg_replace('/\./', DIRECTORY_SEPARATOR, $classpath);

             if(file_exists('classes' . DIRECTORY_SEPARATOR . $file . '.php'))
                require_once('classes' . DIRECTORY_SEPARATOR . $file . '.php');

             else if(file_exists('components' . DIRECTORY_SEPARATOR . $file . '.php'))
                require_once('components' . DIRECTORY_SEPARATOR . $file . '.php');

             else
                throw new FrameworkException('Failed to import source from \'' . $classpath . '\'.');
      }

      /**
       * By default PHP hides errors on production servers. Setting this to true enables PHP
       * 'display_errors', sets 'error_reporting' to 'E_ALL'.
       *
       * @param bool $bool True to turn on error reporting on (E_ALL)
       * @return void
       * @static
       */
      public static function setDisplayPhpErrors($bool = true) {

             self::$displayPhpErrors = $bool;

             if(self::$displayPhpErrors) {

                ini_set('display_errors', '1');
                error_reporting(E_ALL);
             }
      }

      /**
       * Enables or disables AgilePHP framework debug mode.
       *
       * @param bool $boolean True for debug mode, false for production mode. Default is production.
       * @return void
       * @static
       */
      public static function setDebugMode($boolean) {

             self::$debugMode = $boolean;
             if(self::$debugMode) self::setDisplayPhpErrors(true);
      }

      /**
       * Whether or not AgilePHP framework is running in debug mode.
       *
       * @return void
       * @static
       */
      public static function isInDebugMode() {

             return self::$debugMode;
      }

      /**
       * Calls PHP date_default_timezone_set function to set the current timezone.
       *
       * @param String $timezone The timezone to use as default.
       * @return void
       * @static
       * <code>
       * AgilePHP::setDefaultTimezone('America/New_York');
       * </code>
       */
      public static function setDefaultTimezone($timezone) {

             date_default_timezone_set($timezone);
      }

      /**
       * Adds an Interception to the interceptions stack
       *
       * @param Interception $interception The interception instance to add to the stack
       * @return void
       * @static
       */
      public static function addInterception(Interception $interception) {

             if($interception->getMethod()) {

                $level = 'method';
                $from = $interception->getClass() . '::' . $interception->getMethod();
             }
             elseif($interception->getProperty()) {

                $level = 'property';
                $from = $interception->getClass() . '::' . $interception->getProperty();
             }
             else {

                 $level = 'class';
                 $from = $interception->getClass();
             }

             Log::debug('AgilePHP::addInterception Adding ' . $level . ' level #@' . get_class($interception->getInterceptor()) .
             		' interceptor for \'' . $from . '\'.');

             array_push(self::$interceptions, $interception);
      }

      /**
       * Returns an array of Interceptions which have been loaded into the framework
       *
       * @return Array of Interception instances
       * @static
       */
      public static function getInterceptions() {

             return self::$interceptions;
      }

      /**
       * Returns the agilephp.xml file as a SimpleXMLElement.
       *
       * @return SimpleXMLElement agilephp.xml configuration
       * @static
       */
      public static function getConfiguration() {

             return self::$xml;
      }

      /**
       * Defines the error handler responsible for handling framework and application wide errors.
       *
       * @param mixed $function A standard PHP function or static method responsible for error handling
       * @return void
       * @static
       */
      public static function setErrorHandler($function) {

             set_error_handler($function);
      }

      /**
       * Handles PHP E_NOTICE, E_WARNING, E
       *
       * @return void
       * @static
       */
      public static function handleErrors() {

             set_error_handler('AgilePHP::ErrorHandler');
      }

      /**
       * Custom PHP error handling function which writes error to log instead of echoing it out.
       *
       * @param Integer $errno Error number
       * @param String $errmsg Error message
       * @param String $errfile The name of the file that caused the error
       * @param Integer $errline The line number that caused the error
       * @return false
       * @throws FrameworkException
       * @static
       */
       public static function ErrorHandler($errno, $errmsg, $errfile, $errline) {

                $entry = PHP_EOL . 'Number: ' . $errno . PHP_EOL . 'Message: ' . $errmsg .
                          PHP_EOL . 'File: ' . $errfile . PHP_EOL . 'Line: ' . $errline;

                switch($errno) {

                    case E_NOTICE:
                    case E_USER_NOTICE:

                         Log::info($entry);
                         break;

                    case E_WARNING:
                    case E_USER_WARNING:

                         Log::warn($entry);
                         break;

                    case E_ERROR:
                    case E_USER_ERROR:
                    case E_RECOVERABLE_ERROR:

                         Log::error($entry);
                         break;

                    default:
                         Log::debug($entry);
                }

      }

      /**
       * Starts a timer. Useful for measuring how long a particular
       * operation takes.
       *
       * @return void
       * @static
       */
      public static function startClock() {

             $mtime = microtime();
             $mtime = explode(' ', $mtime);
             $mtime = $mtime[1] + $mtime[0];
             self::$startTime = $mtime;
      }

      /**
       * Stops the timer and returns the elapsed time between startClock()
       * and stopClock().
       *
       * @return Time elapsed time between startClock() and endClock()
       * @static
       */
      public static function stopClock() {

             $mtime = microtime();
             $mtime = explode(' ', $mtime);
             $mtime = $mtime[1] + $mtime[0];
             $endtime = $mtime;
             $difference = ($endtime - self::$startTime);

             return $difference;
      }

      /**
       * Retrieves the raw PHP source code for the specified class. The search
       * algorithm assumes the file is named after the class.
       *
       * @param String $class The class name
       * @return String The raw PHP source code for the specified class
       * @throws FrameworkException if the requested class could not be found
       * @static
       */
      public static function getSource($class) {

             // Serve from cache if enabled and present
             if(self::$cacher) {

                $key = 'AGILEPHP_SOURCE_' . $class;
                if($source = self::$cacher->get($key))
                   return $source;
             }

             // Search classmap
             if(isset(self::$classmap[$class])) {

                // Allow the application to override framework class paths
                if(file_exists(self::$webroot . self::$classmap[$class])) {

                   $source = file_get_contents(self::$webroot . self::$classmap[$class]);
                   if(self::$cacher) self::$cacher->set($key, $source);
                   return $source;
                }
                else {

                    $source = file_get_contents(self::$frameworkRoot . self::$classmap[$class]);
                    if(self::$cacher) self::$cacher->set($key, $source);
                    return $source;
                }
             }

             // PHP namespace support
             $namespace = explode('\\', $class);
             $className = array_pop($namespace);
             $namespace = implode(DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;

             // PHAR support
             if(strpos($class, 'phar://') !== false) {

                $source = file_get_contents($class);
                if(self::$cacher) self::$cacher->set($key, $source);
                return $source;
             }

             // Search web application (one level using namespace as directory delimiter)
             $directories = glob(self::$webroot . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
             foreach($directories as $directory) {

                 $path = $directory . DIRECTORY_SEPARATOR . $namespace . $className . '.php';
                 if(file_exists($path)) {

                    $source = file_get_contents($path);
                    if(self::$cacher) self::$cacher->set($key, $source);
                    return $source;
                 }
             }

             // Search web application (recursively - no namespace by class name)
		  	 $it = new RecursiveDirectoryIterator(self::$webroot);
			 foreach(new RecursiveIteratorIterator($it) as $file) {

  			   	     $pieces = explode(DIRECTORY_SEPARATOR, $file);
				 	 if(array_pop($pieces) == $className . '.php') {

    			   	    $source = file_get_contents($file);
    				    if(self::$cacher) self::$cacher->set($key, $source);
    			        return $source;
    			   	 }
			 }

             throw new FrameworkException('Failed to retrieve source code for class \'' . $class . '\'.');
      }

      /**
       * Lazy loads standard framework and web application classes. Parses each class
       * source file for the presense of AgilePHP interceptors.
       *
       * @param String $class The name of the class being loaded by __autoload
       * @param boolean $bypassInterceptors Flag used to enable/disable InterceptorFilter logic
       * @return void
       * @static
       */
      public static function autoload($class, $bypassInterceptors = false) {

             // Parse class for AgilePHP interceptors if enabled
             if(!$bypassInterceptors) {

	            // Filter for interceptors
                new InterceptorFilter($class);

                // Intercepted classes are loaded by the filter
                if(class_exists($class, false)) return;
             }

             // Use caching if enabled
             if(self::$cacher) {

                $key = 'AGILEPHP_AUTOLOAD_' . $class;
                if($clazz = self::$cacher->get($key)) {

                   require $clazz;
                   return;
                }
             }

             // Search classmap
             if(isset(self::$classmap[$class])) {

                // Allow the application to override framework class paths
                if(file_exists(self::$webroot . self::$classmap[$class])) {

                    if(self::$cacher) self::$cacher->set($key, self::$webroot . self::$classmap[$class]);
                    require self::$webroot . self::$classmap[$class];
                    return;
                }
                else {

                    if(self::$cacher) self::$cacher->set($key, self::$frameworkRoot . self::$classmap[$class]);
                    require self::$frameworkRoot . self::$classmap[$class];
                    return;
                }
             }

             // PHP namespace support
             $namespace = explode('\\', $class);
             $className = array_pop($namespace);
             $namespace = implode(DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;

             // Search web application (one level)
             $directories = glob(self::$webroot . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
             foreach($directories as $directory) {

                 $path = $directory . $namespace . $className . '.php';
                 if(file_exists($path)) {

                    if(self::$cacher) self::$cacher->set($key, $path);
                    require $path;
                    return;
                 }
             }

             // Search web application (recursively - as last resort effort)
		  	 $it = new RecursiveDirectoryIterator(self::$webroot);
			 foreach(new RecursiveIteratorIterator($it) as $file) {

		   	      	  $pieces = explode(DIRECTORY_SEPARATOR, $file);
			 		  if(array_pop($pieces) == $className . '.php') {

			 		     if(self::$cacher) self::$cacher->set($key, $file->getPathname());
		     	 		 require $file;
		     	 		 return;
			 		  }
			 }

             throw new FrameworkException('The requested class \'' . $class . '\' could not be auto loaded.');
      }

      /**
       * Lazy loads standard framework and web application classes.
       *
       * @param String $class The name of the class being loaded by __autoload
       * @return void
       * @static
       */
      public static function autoloadNoAnnotations($class) {

             self::autoload($class, true);
      }

      /**
       * Static classmap responsible for storing relationships between classes
       * and their physical location on disk.
       *
       * @var array An associative array of class name keys with their physical disk location as the value.
       * @static
       */
      public static $classmap = array(
          'Annotation' => '/Annotation.php',
          'Cache' => '/Cache.php',
          'Component' => '/Component.php',
          'Crypto' => '/Crypto.php',
          'Form' => '/Form.php',
          'FrameworkException' => '/FrameworkException.php',
          'i18n' => '/i18n.php',
          'Identity' => '/Identity.php',
          'Interception' => '/Interception.php',
          'Log' => '/Log.php',
          'Mailer' => '/Mailer.php',
          'MVC' => '/MVC.php',
          'ORM' => '/ORM.php',
          'Remoting' => '/Remoting.php',
          'Scope' => '/Scope.php',
          'Upload' => '/Upload.php',
          'Validator' => '/Validator.php',
          'AnnotatedClass' => '/annotation/AnnotatedClass.php',
          'AnnotatedMethod' => '/annotation/AnnotatedMethod.php',
          'AnnotatedProperty' => '/annotation/AnnotatedProperty.php',
          'AnnotationException' => '/annotation/AnnotationException.php',
          'AnnotationParser' => '/annotation/AnnotationParser.php',
          'ApcCacheProvider' => '/cache/ApcCacheProvider.php',
          'CacheException' => '/cache/CacheException.php',
          'CacheProvider' => '/cache/CacheProvider.php',
          'FileCacheProvider' => '/cache/FileCacheProvider.php',
          'XCacheProvider' => '/cache/XCacheProvider.php',
          'DataRenderer' => '/data/renderer/DataRenderer.php',
          'JsonRenderer' => '/data/renderer/JsonRenderer.php',
          'XmlRenderer' => '/data/renderer/XmlRenderer.php',
          'YamlRenderer' => '/data/renderer/YamlRenderer.php',
          'YesNoRenderer' => '/data/renderer/YesNoRenderer.php',
          'DataTransformer' => '/data/transformer/DataTransformer.php',
          'JsonToModel' => '/data/transformer/JsonToModel.php',
          'XmlToModel' => '/data/transformer/XmlToModel.php',
          'YamlToModel' => '/data/transformer/YamlToModel.php',
      	  'YesNoToBoolean' => '/data/transformer/YesNoToBoolean.php',
          'RequestParam' => '/form/RequestParam.php',
          'AccessDeniedException' => '/identity/AccessDeniedException.php',
          'Authentication' => '/identity/Authentication.php',
          'BasicAuthentication' => '/identity/BasicAuthentication.php',
          'BasicForgotPasswdMailer' => '/identity/BasicForgotPasswdMailer.php',
          'BasicRegistrationMailer' => '/identity/BasicRegistrationMailer.php',
          'BasicResetPasswdMailer' => '/identity/BasicResetPasswdMailer.php',
          'DefaultAuthenticator' => '/identity/DefaultAuthenticator.php',
          'IdentityManager' => '/identity/IdentityManager.php',
          'IdentityManagerFactory' => '/identity/IdentityManagerFactory.php',
          'IdentityManagerImpl' => '/identity/IdentityManagerImpl.php',
          'IdentityModel' => '/identity/IdentityModel.php',
          'IdentityUtils' => '/identity/IdentityUtils.php',
          'LoggedIn' => '/identity/LoggedIn.php',
          'NotLoggedInException' => '/identity/NotLoggedInException.php',
          'Password' => '/identity/Password.php',
          'Restrict' => '/identity/Restrict.php',
          'Role' => '/identity/Role.php',
          'User' => '/identity/User.php',
          'AfterInvoke' => '/interception/AfterInvoke.php',
          'AroundInvoke' => '/interception/AroundInvoke.php',
          'In' => '/interception/In.php',
          'InterceptionException', '/interception/InterceptionException.php',
          'Interceptor' => '/interception/Interceptor.php',
          'InterceptorFilter' => '/interception/InterceptorFilter.php',
          'InterceptorProxy' => '/interception/InterceptorProxy.php',
          'InvocationContext' => '/interception/InvocationContext.php',
          'Audit' => '/logger/Audit.php',
          'FileLogger' => '/logger/FileLogger.php',
          'LogFactory' => '/logger/LogFactory.php',
          'Logger' => '/logger/Logger.php',
          'LogProvider' => '/logger/LogProvider.php',
          'SysLogger' => '/logger/SysLogger.php',
          'AJAXRenderer' => '/mvc/AJAXRenderer.php',
          'BaseController' => '/mvc/BaseController.php',
          'BaseModelActionController' => '/mvc/BaseModelActionController.php',
          'BaseModelController' => '/mvc/BaseModelController.php',
          'BaseModelXmlController' => '/mvc/BaseModelXmlController.php',
          'BaseModelXslController' => '/mvc/BaseModelXslController.php',
          'BaseRenderer' => '/mvc/BaseRenderer.php',
      	  'ComponentModelActionController' => '/mvc/ComponentModelActionController.php',
          'ExtFormRenderer' => '/mvc/ExtFormRenderer.php',
          'PHTMLRenderer' => '/mvc/PHTMLRenderer.php',
          'XSLTRenderer' => '/mvc/XSLTRenderer.php',
          'BaseDialect' => '/orm/dialect/BaseDialect.php',
          'MSSQLDialect' => '/orm/dialect/MSSQLDialect.php',
          'MySQLDialect' => '/orm/dialect/MySQLDialect.php',
          'PGSQLDialect' => '/orm/dialect/PGSQLDialect.php',
          'SQLDialect' => '/orm/dialect/SQLDialect.php',
          'SQLiteDialect' => '/orm/dialect/SQLiteDialect.php',
      	  'Transactional' => '/orm/Transactional.php',
          'Column' => '/orm/Column.php',
          'Database' => '/orm/Database.php',
      	  'DomainModel' => '/orm/DomainModel.php',
          'ForeignKey' => '/orm/ForeignKey.php',
          'Id' => '/orm/Id.php',
          'IdentityMap' => '/orm/IdentityMap.php',
          'ORMException' => '/orm/ORMException.php',
          'ORMFactory' => '/orm/ORMFactory.php',
          'Procedure' => '/orm/Procedure.php',
          'ProcedureParam' =>'/orm/ProcedureParam.php',
          'StoredProcedure' =>'/orm/StoredProcedure.php',
          'Table' => '/orm/Table.php',
          'ApplicationScope' => '/scope/ApplicationScope.php',
          'OrmSessionProvider' => '/scope/OrmSessionProvider.php',
          'PhpSessionProvider' => '/scope/PhpSessionProvider.php',
          'RequestScope' => '/scope/RequestScope.php',
          'Session' => '/scope/Session.php',
          'SessionProvider' => '/scope/SessionProvider.php',
          'SessionScope' => '/scope/SessionScope.php',
          'Stateful' => '/scope/Stateful.php',
          'ArrayValidator' => '/validator/ArrayValidator.php',
          'BitValidator' => '/validator/BitValidator.php',
          'BooleanValidator' => '/validator/BooleanValidator.php',
          'DateValidator' => '/validator/DateValidator.php',
          'EmailValidator' => '/validator/EmailValidator.php',
          'FloatValidator' => '/validator/FloatValidator.php',
          'IPv4Validator' => '/validator/IPv4Validator.php',
          'IPv6Validator' => '/validator/IPv6Validator.php',
          'LengthValidator' => '/validator/LengthValidator.php',
          'NumberValidator' => '/validator/NumberValidator.php',
          'ObjectValidator' => '/validator/ObjectValidator.php',
          'PasswordValidator' => '/validator/PasswordValidator.php',
          'StringValidator' => '/validator/StringValidator.php',
          'StrongPasswordValidator' => '/validator/StrongPasswordValidator.php',
          'RemoteMethod' => '/webservice/remoting/RemoteMethod.php',
          'RemotingException' => '/webservice/remoting/RemotingException.php',
          'ConsumeMime' => '/webservice/rest/ConsumeMime.php',
          'DELETE' => '/webservice/rest/DELETE.php',
          'GET' => '/webservice/rest/GET.php',
          'Path' => '/webservice/rest/Path.php',
          'POST' => '/webservice/rest/POST.php',
          'ProduceMime' => '/webservice/rest/ProduceMime.php',
          'PUT' => '/webservice/rest/PUT.php',
          'RestClient' => '/webservice/rest/RestClient.php',
          'RestClientException' => '/webservice/rest/RestClientException.php',
          'RestService' => '/webservice/rest/RestService.php',
          'RestServiceException' => '/webservice/rest/RestServiceException.php',
          'RestUtil' => '/webservice/rest/RestUtil.php',
          'SOAPBinding' => '/webservice/soap/SOAPBinding.php',
          'SOAPService' => '/webservice/soap/SOAPService.php',
          'WebMethod' => '/webservice/soap/WebMethod.php',
          'WebService' => '/webservice/soap/WebService.php',
          'WSDL' => '/webservice/soap/WSDL.php'
     );
}
?>