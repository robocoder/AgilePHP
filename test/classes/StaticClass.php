<?php
class StaticClass {

      #@Logger
      public static $logger;
      
      #@In(class = IdentityManagerFactory::getManager())
      public static $identityManager;

      #@TestInterceptor
      static public function test($value) {

             self::$logger->debug('StaticClass::test ' . $value);
             return $value;
      }

      public static function test2() {

             self::$logger->debug('StaticClass::test2');
             return 'test2';
      }
      
      static public function getLogger() {
          
             return self::$logger;
      }
}
?>