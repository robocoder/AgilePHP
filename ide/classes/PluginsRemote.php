<?php

class PluginsRemote {

	  #@RemoteMethod
 	  public function getPlugins() {

	  		 $plugins = array();
	  		 $path = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'view' .
	  		 		 DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'plugins';

	  		 $h = opendir( $path );
			 while( $file = readdir( $h ) ) {

			 	if( $file != '.' && $file != '..' ) {

			 		$pieces = explode( '.', $file );
			 		if( strtolower( array_pop( $pieces ) ) == 'js' ) {

			 			$o = new stdClass;
			 			$o->path = AgilePHP::getFramework()->getDocumentRoot() . '/view/js/plugins/' . $file;
			 			array_push( $plugins, $o );
			 		}
			 	}
			 }
			 closedir( $h );

			 return $plugins;
	  }
}
?>