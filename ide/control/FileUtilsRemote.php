<?php

class FileUtilsRemote extends Remoting {

	  public function __construct() {

	  		 parent::__construct( $this );
	  }

	  public function index() {

	  		 parent::createStub();
	  }

	  /**
	   * Deletes a node or branch from the file explorer tree. If the specified $treePath is a directory
	   * the path is handed off to BaseTinyMCEController::recursiveDelete. 
	   *  
	   * @param $path The path to delete.
	   * @return True if successful, false otherwise.
	   */
	  #@RemoteMethod
	  public function delete( $path ) {

	  		 Logger::getInstance()->debug( 'FileUtilsRemote::delete Deleting path \'' . $path . '\'.' );

	  		 if( is_dir( $path ) )
	  		   	 return FileUtils::delete( $path);

	  		 return unlink( $path );
	  }

	  /**
	   * Recursively copy the specified source to the specified destination.
	   * 
	   * @param $src The source path
	   * @param $dst The destination path
	   * @return void
	   */
	  #@RemoteMethod
	  public function copy( $src, $dst ) {

	  		 Logger::getInstance()->debug( 'FileUtilsRemote::copy $src = \'' . $src . '\', $dst = \'' . $dst . '\'.' );

	  		 $array = explode( DIRECTORY_SEPARATOR, $src );
	  		 $path = $dst . DIRECTORY_SEPARATOR . array_pop( $array );

	  		 Logger::getInstance()->debug( 'FileUtilsRemote::copy Copying src \'' . $src . '\' to destination \'' . $path . '\'.' );

	  		 $o = new stdClass;
	  		 $o->success = true;
	  		 $o->parent = $dst;

	  		 if( is_dir( $src ) ) {

	  		 	 FileUtils::copy( $src, $path );
	  		 	 return $o;
	  		 }

	  		 if( !copy( $src, $path ) )
	  		 	 throw new AgilePHP_RemotingException( 'Failed to copy \'' . $src . '\' to \'' . $dst . '\'.' );

	  		 return $o;
	 }

	 /**
	  * Event handler for file explorer tree move. Performs a file rename which moves the file from $treeSrc
	  * to $treeDst.
	  * 
	  * @param $treeSrc The tree source node / file system path (colons substituted for /)
	  * @param $treeDst The tree destination node / file system path (colons substituted for /)
	  * @return void
	  */
	 public function move( $src, $dst ) {

	  		   Logger::getInstance()->debug( 'FileUtilsRemote::move $src = \'' . $src . '\'.' );
	  		   Logger::getInstance()->debug( 'FileUtilsRemote::move $dst = \'' . $dst . '\'.' );

	  		   $array = explode( DIRECTORY_SEPARATOR, $src );
	  		   $path = $dst . DIRECTORY_SEPARATOR . array_pop( $array );

	  		   Logger::getInstance()->debug( 'FileUtilsRemote::copy Moving src \'' . $src . '\' to destination \'' . $dst . '\'.' );

	  		   $o = new stdClass;

	  		   if( rename( $src, $path ) ) {

	  		   	   $o->success = true;
	  		   	   $o->srcId = $src;
	  		   	   $o->newParentId = $dst;

	  		   	   return $o;
	  		   }

	  		   $o->success = false;

	  		   return $o;
	  	}

	  	/**
	  	 * Event handler for file uploads. Saves the chosen file to the specified
	  	 * $treePath destination.
	  	 * 
	  	 * @param $treePath The destination path to save the uploaded content.
	  	 * @return void
	  	 */
		public function upload( $treePath ) {

			   $path = str_replace( ':', '/', $treePath );
			   $renderer = MVC::getInstance()->createRenderer( 'AJAXRenderer' );
			   $o = new stdClass();

	  		   try {
		    		   $upload = new Upload();
		  	    	   $upload->setName( 'upload' );
		  	    	   $upload->setDirectory( $path );
		  	    	   $path = $upload->save();
	  		   }
	  		   catch( AgilePHP_Exception $e ) {

	  		   		  Logger::getInstance()->debug( 'FileExplorerController::upload Failed to upload file. Error code: ' . $e->getCode() . ', Message: ' . $e->getMessage() );
	  		   		  
	  		   		  $o->success = false;
	  		   		  $o->file = null;

	  		   		  $renderer->renderNoHeader( $o );
	  		   }

	  		   $o->success = true;
	  		   $o->file = $path;

	  		   $renderer->renderNoHeader( $o );
	  	}

	  	/**
	  	 * Creates a new directory.
	  	 * 
	  	 * @param $path The parent directory path
	  	 * @param $name The name of the new directory
	  	 * @return void
	  	 */
	  	public function createDirectory( $path, $name ) {

	  		   $filename = (str_replace( ':', '/', $path ) . '/' . $name);
	  		   if( $filename == '.' ) $filename = './';

	  		   Logger::getInstance()->debug( 'FileExplorerController::createDirectory Creating new file \'' . $filename . '\'.' );
	  		   
	  		   $o = new stdClass();
	  		   $o->success = mkdir( $filename ) ? true : false;

	  		   $this->getRenderer()->render( $o );
	  	}
	  	
	   /**
	  	 * Creates a new file.
	  	 * 
	  	 * @param $path The parent directory path
	  	 * @param $name The name of the new file
	  	 * @return void
	  	 */
	  	public function createFile( $path, $name ) {

	  		   $filename = str_replace( ':', '/', $path ) . '/' . $name;
	  		   if( $filename == '.' ) $filename = './';

	  		   $o = new stdClass();
	  		   $o->success = touch( $filename ) ? true : false;

	  		   $this->getRenderer()->render( $o );
	  	}
}
?>