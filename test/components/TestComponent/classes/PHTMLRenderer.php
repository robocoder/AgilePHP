<?php

namespace TestComponent;

class PHTMLRenderer extends \BaseRenderer {

      public static function isWorking() {

	     return true;
      }

      public function render( $view ) {

	     $path = 'components/TestComponent/view/' . $view . '.phtml';

 	     if( !file_exists( $path ) )
      	 	 throw new \AgilePHP_Exception( 'Error rendering component view. Path does not exist ' . $path );
  
 	     foreach( $this->getStore() as $key => $value )
	              $$key = $value;

	     require_once $path;
      }
}
?>
