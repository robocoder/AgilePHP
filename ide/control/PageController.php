<?php

class PageController extends BaseController {

		 private $file;

		 public function __construct() {

		 		set_error_handler( 'PageController::ErrorHandler' );

		 		parent::__construct();

		 		$this->createRenderer( 'AJAXRenderer' );
		 }

		 public function index() { 

		 		throw new AgilePHP_Exception( 'Malformed request' );
		 }

	     /**
	      * Returns the file contents for the specified file.
	      * 
	      * @param $file The file path to load.
	      * @return The contents of the specified file.
	      */
	     private function getContents( $file ) {

	     		 if( !file_exists( $file ) )
		 		     throw new AgilePHP_Exception( 'Error loading file \'' . $file . '\'. File does not exist.' );
	 		    
		 		 $content = '';
		 		 $h = fopen( $file, 'r' );
		 		 while( !feof( $h ) )
		 		 	   $content .= fgets( $h, 2048 );
		 		 fclose( $h );

		 		 return trim( $content );
	     }

		 /**
		  * Loads an editor instance.
		  * 
		  * @return void
		  */
		 public function load( $type, $id ) {

		 		//$pieces = explode( '.', $id );
		 		//$extension = strtolower( $pieces[count($pieces)-1] );

		 		$code = $this->getContents( str_replace( ':', '/', $id ) );

		 		Logger::getInstance()->info( $code );
		 		Logger::getInstance()->info( $type );
		 		Logger::getInstance()->info( $id );
	 		
		 		switch( $type ) {

		 			case 'code':

		 				 //if( $extension == 'phtml' ) {

			 				 $renderer = new PHTMLRenderer();
			 				 $renderer->set( 'code', htmlentities( $code ) );
			 				 $renderer->render( 'editor-code' );

			 				 Logger::getInstance()->error( $renderer );
		 				 //}
		 				 break;

		 			case 'design':

		 				 $o = new stdClass;
		 				 $o->id = $id; 
		 				 $o->code = $code;

		 				 $this->getRenderer()->render( $o );
		 				 break;

		 			default:
		 				throw new Exception( 'Invalid editor type' );
		 		}
		 }

		 /**
		  * Performs a save operation on behalf of TinyMCE. This method has been modified for
		  * the parkcitiesnews.com site so that it saves required javascript to the published page.
		  * 
		  * @param $file The file path where the passed content gets written
		  * @return void
		  */
		 public function save( $file ) {

		 		$this->file = str_replace( ':', '/', $file );

		 		Logger::getInstance()->debug( 'PageController::save Saving content: \'' . $_POST['content'] . '\'.' );
				Logger::getInstance()->debug( 'PageController::save Saving file \'' . $this->file . '\'.' );

				$content = stripslashes( $_POST['content'] );
				
		 		$h = fopen( $this->file, 'w' );
		 		$result = fwrite( $h, $content );
		 		fclose( $h );

		 		if( $result === false )
		 		    throw new AgilePHP_Exception( 'Failed to save view.' );

	 		    $this->loadPage();
		 }

		 /**
		  * Provides a single-level directory/file listing in JSON format to the file explorer. This is
		  * called everytime a node is expanded.
		  * 
		  * @return void
		  */
		 public function getTree() {

		 		function isProject( $path ) {

		 				 return file_exists( $path . DIRECTORY_SEPARATOR . 'agilephp.xml' );
		 		};

		 		$request = Scope::getInstance()->getRequestScope();

		 		$node = $request->getSanitized( 'node' );
		 		$path = preg_replace( '/:/', DIRECTORY_SEPARATOR, $node );

		 		// root tree node / workspace directory
		 		if( $path == '.' . DIRECTORY_SEPARATOR ) {

		 			$config = new Config();
		 			$config->setName( 'workspace' );
		 			$path = $config->getValue();
		 			$isWorkspace = true;
		 		}

		 		$o = new stdClass();
		 		$o->directories = array();
		 		$o->files = array();
		 		$o->filesystem = array();

				foreach( new DirectoryIterator( $path ) as $fileInfo ) {

				    	if( $fileInfo->isDot() ) continue;

				    	if( !$fileInfo->isDir() ) {

				    		preg_match( '/.*\.(.*)/s', $fileInfo->getFilename(), $matches );
				    		$extension = ( (!count( $matches )) ? '' : $matches[1]);
				    	}

				    	$serialized_node = preg_replace( '/\//', ':', $fileInfo->getPathname() );

				    	$stdClass = new stdClass();
				    	$stdClass->id = $serialized_node;
				    	$stdClass->text = $fileInfo->getFilename();
				    	$stdClass->iconCls = 'mime-' . (($fileInfo->isDir()) ? 'folder' : $extension);

				    	if( isset( $isWorkspace ) ) {

				    		// Load only folders containing agilephp.xml file
				    		//
				    		// if( !isProject( $path . DIRECTORY_SEPARATOR . $stdClass->text ) ) continue;
				    		// $stdClass->iconCls = 'app-icon';

				    		if( isProject( $path . DIRECTORY_SEPARATOR . $stdClass->text ) )
				    			$stdClass->iconCls = 'project';
				    	}

				    	if( $fileInfo->isDir() )
				     		array_push( $o->directories, $stdClass );
				     	else {

				     		$stdClass->leaf = true;
				    		array_push( $o->files, $stdClass );
				     	}
				}

				sort( $o->directories );
				sort( $o->files );

				foreach( $o->directories as $stdClass )
						 array_push( $o->filesystem, $stdClass );

				foreach( $o->files as $stdClass )
						 array_push( $o->filesystem, $stdClass );

				$this->getRenderer()->render( $o->filesystem );
		 }

		/**
		 * Deletes a node or branch from the file explorer tree. If the specified $treePath is a directory
		 * the path is handed off to BaseTinyMCEController::recursiveDelete. 
		 *  
		 * @param $treePath The path to delete.
		 * @return void
		 */
	  	public function delete( $treePath ) {

	  		   $treePath = str_replace( ':', '/', $treePath );
	  		   Logger::getInstance()->debug( 'PageController::delete Deleting treePath \'' . $treePath . '\'.' );

	  		   header( 'content-type: application/json' );

	  		   if( is_dir( $treePath ) ) {

	  		   	   print $this->recursiveDelete( $treePath ) ? '{success:true}' : '{success:false}';
	  		   	   return;
	  		   }

	  		   print (unlink( str_replace( ':', '/', $treePath ) ) ? '{success:true}' : '{success:false}');
	  	}

	  	/**
	  	 * Performs a recursive delete on a directory.
	  	 * 
	  	 * @param $src The tree source path to delete (colons substitutes for /)
	  	 * @return void
	  	 */
	  	public function recursiveDelete( $src ) {

	  		   Logger::getInstance()->debug( 'PageController::recursiveDelete Deleting source \'' . $src . '\'.' );

	  		   $dir = opendir( $src );
			   while( false !== ( $file = readdir( $dir ) ) ) {

			     	if( $file != '.' && $file != '..') {

			            if ( is_dir( $src . '/' . $file) )
			                $this->recursiveDelete( $src . '/' . $file );
			            else {
			                
			            	if( !unlink( $src . '/' . $file ) ) {
			            		
			            		Logger::getInstance()->debug( 'Failed to delete file ' . $src . '/' . $file );
			                	throw new AgilePHP_Exception( 'Could not delete file ' . $src . '/' . $file );
			            	}
			            }
			        }
			    }
			    return rmdir( $src );
	  	}

	  	/**
	  	 * Event handler for file explorer tree copy. Outputs JSON format for XHR.
	  	 * 
	  	 * @param $treeSrc The source tree node's id/ file system path (colon substituted for /)
	  	 * @param $treeDst The destination tree node's id/file system path (colon substituted for /)
	  	 * @return void
	  	 */
	  	public function copy( $treeSrc, $treeDst ) {

	  		   Logger::getInstance()->debug( 'PageController::copy $treeSrc = \'' . $treeSrc . '\', $treeDst = \'' . $treeDst . '\'.' );

	  		   $treeSrc = str_replace( ':', '/', $treeSrc );
	  		   $treeDst = str_replace( ':', '/', $treeDst );

	  		   $dstPath = $treeDst . '/' . array_pop( explode( '/', $treeSrc ) );

	  		   Logger::getInstance()->debug( 'PageController::copy Copying src \'' . $treeSrc . '\' to destination \'' . $dstPath . '\'.' );

	  		   header( 'content-type: application/json' );
	  		   if( is_dir( $treeSrc ) ) {

	  		   	   $this->recursiveCopy( $treeSrc, $dstPath );
	  		   	   print '{success:true, parent: "' . str_replace( '/', ':', $treeDst ) . '"}';
	  		   	   return;
	  		   }

	  		   copy( $treeSrc, $dstPath );
	  		   print '{success:true, parent: "' . str_replace( '/', ':', $treeDst ) . '"}';
	  	}

	  	/**
	  	 * Performs a recursive file copy from $src to $dst. 
	  	 * 
	  	 * @param $src The source directory to copy
	  	 * @param $dst The destination path to copy into
	  	 * @return void
	  	 */
	  	function recursiveCopy( $src, $dst ) {

	  			 Logger::getInstance()->debug( 'PageController::copyRecursive Copy src \'' . $src . '\' to destination \'' . $dst . '\'.' );

	  		     $dir = opendir( $src );
			     mkdir( $dst );
			     while( false !== ( $file = readdir( $dir ) ) ) {

			     	if (( $file != '.' ) && ( $file != '..' )) {

			            if ( is_dir( $src . '/' . $file) )
			                $this->recursiveCopy( $src . '/' . $file, $dst . '/' . $file );
			            else
			                copy( $src . '/' . $file, $dst . '/' . $file );
			        }
			    }
			    closedir( $dir );
		}

		/**
		 * Event handler for file explorer tree move. Performs a file rename which moves the file from $treeSrc
		 * to $treeDst.
		 * 
		 * @param $treeSrc The tree source node / file system path (colons substituted for /)
		 * @param $treeDst The tree destination node / file system path (colons substituted for /)
		 * @return void
		 */
	  	public function move( $treeSrc, $treeDst ) {

	  		   Logger::getInstance()->debug( 'PageController::copy $treeSrc = \'' . $treeSrc . '\'.' );
	  		   Logger::getInstance()->debug( 'PageController::copy $treeDst = \'' . $treeDst . '\'.' );

	  		   $src = str_replace( ':', DIRECTORY_SEPARATOR, $treeSrc );
	  		   $dst = str_replace( ':', DIRECTORY_SEPARATOR, $treeDst );
	  		   $dstPath = $dst . DIRECTORY_SEPARATOR . array_pop( explode( DIRECTORY_SEPARATOR, $src ) );

	  		   Logger::getInstance()->debug( 'PageController::copy Copying src \'' . $src . '\' to destination \'' . $dstPath . '\'.' );

	  		   $o = new stdClass;

	  		   if( rename( $src, $dstPath ) ) {

	  		   	   $o->success = true;
	  		   	   $o->srcId = $treeSrc;
	  		   	   $o->newParentId = $treeDst;

	  		   	   $this->getRenderer()->render( $o );
	  		   }

	  		   $o->success = false;
	  		   $this->getRenderer()->render( $o );
	  	}

	  	/**
	  	 * Event handler for file uploads. Saves the chosen file to the specified
	  	 * $treePath destination.
	  	 * 
	  	 * @param $treePath The destination path to save the uploaded content.
	  	 * @return void
	  	 */
		public function upload( $treePath ) {

			   $renderer = MVC::getInstance()->createRenderer( 'AJAXRenderer' );
			   $o = new stdClass();

	  		   try {
		    		   $upload = new Upload();
		  	    	   $upload->setName( 'upload' );
		  	    	   $upload->setDirectory( str_replace( ':', '/', $treePath ) );
		  	    	   $path = $upload->save();
	  		   }
	  		   catch( AgilePHP_Exception $e ) {

	  		   		  Logger::getInstance()->debug( 'PageController::upload Failed to upload file. Error code: ' . $e->getCode() . ', Message: ' . $e->getMessage() );
	  		   		  
	  		   		  $o->success = false;
	  		   		  $o->file = null;

	  		   		  $renderer->renderNoHeader( $o );
	  		   }

	  		   $o->success = true;
	  		   $o->file = str_replace( '/', ':', $path );

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

	  		   Logger::getInstance()->debug( 'PageController::createDirectory Creating new file \'' . $filename . '\'.' );
	  		   
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

        /**
	     * Retrieves a list of files from of the web application view directory.
	     * 
	     * @param $type Type of files being requested. This equates to the directory
	     * 				name inside of the view folder.
	     * @return An array of files
	     */
	    private function getFiles( $type = 'images' ) {

	     		$files = array(); 
	  		    $it = new RecursiveDirectoryIterator( AgilePHP::getFramework()->getWebRoot() . '/view/' . $type . '/' );
			    foreach( new RecursiveIteratorIterator( $it ) as $file ) {

			    	     if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

			 		  	     preg_match( '/view[\/|\\\]' . $type . '[\/|\\\].*/s', $file, $matches );
			 		  	     array_push( $files, AgilePHP::getFramework()->getDocumentRoot() . '/' . $matches[0] );
			    	     }
			    }

			    return $files;
	  	}

	  /**
	   * Custom PHP error handling function which throws an AgilePHP_Exception instead of reporting
	   * a PHP warning.
	   * 
	   * @param Integer $errno Error number
	   * @param String $errmsg Error message
	   * @param String $errfile The name of the file that caused the error
	   * @param Integer $errline The line number that caused the error
	   * @return false
	   * @throws AgilePHP_Exception
	   */
 	  public static function ErrorHandler( $errno, $errmsg, $errfile, $errline ) {

 	  	     if( $errno == E_WARNING )
	    	     throw new AgilePHP_Exception( $errmsg, $errno );
	  }

	  public function __destruct() {

	  	 	  restore_error_handler();
	  }
}
?>