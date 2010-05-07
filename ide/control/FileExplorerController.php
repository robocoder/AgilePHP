<?php

class FileExplorerController extends BaseExtController {

		 private $file;

		 public function __construct() {

		 		parent::__construct();

		 		set_error_handler( 'FileExplorerController::ErrorHandler' );

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

		 		Logger::getInstance()->debug( 'FileExplorerController::save Saving content: \'' . $_POST['content'] . '\'.' );
				Logger::getInstance()->debug( 'FileExplorerController::save Saving file \'' . $this->file . '\'.' );

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
		 		if( $path == '.' ) {

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
	  		   Logger::getInstance()->debug( 'FileExplorerController::delete Deleting treePath \'' . $treePath . '\'.' );

	  		   header( 'content-type: application/json' );

	  		   if( is_dir( $treePath ) ) {

	  		   	   print FileUtils::delete( $treePath ) ? '{success:true}' : '{success:false}';
	  		   	   return;
	  		   }

	  		   print (unlink( str_replace( ':', '/', $treePath ) ) ? '{success:true}' : '{success:false}');
	  	}

	  	/**
	  	 * Event handler for file explorer tree copy. Outputs JSON format for XHR.
	  	 * 
	  	 * @param $treeSrc The source tree node's id/ file system path (colon substituted for /)
	  	 * @param $treeDst The destination tree node's id/file system path (colon substituted for /)
	  	 * @return void
	  	 */
	  	public function copy( $treeSrc, $treeDst ) {

	  		   Logger::getInstance()->debug( 'FileExplorerController::copy $treeSrc = \'' . $treeSrc . '\', $treeDst = \'' . $treeDst . '\'.' );

	  		   $treeSrc = str_replace( ':', '/', $treeSrc );
	  		   $treeDst = str_replace( ':', '/', $treeDst );

	  		   $array = explode( '/', $treeSrc );
	  		   $dstPath = $treeDst . '/' . array_pop( $array );

	  		   Logger::getInstance()->debug( 'FileExplorerController::copy Copying src \'' . $treeSrc . '\' to destination \'' . $dstPath . '\'.' );

	  		   header( 'content-type: application/json' );
	  		   if( is_dir( $treeSrc ) ) {

	  		   	   FileUtils::copy( $treeSrc, $dstPath );
	  		   	   print '{success:true, parent: "' . str_replace( '/', ':', $treeDst ) . '"}';
	  		   	   return;
	  		   }

	  		   copy( $treeSrc, $dstPath );
	  		   print '{success:true, parent: "' . str_replace( '/', ':', $treeDst ) . '"}';
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

	  		   Logger::getInstance()->debug( 'FileExplorerController::copy $treeSrc = \'' . $treeSrc . '\'.' );
	  		   Logger::getInstance()->debug( 'FileExplorerController::copy $treeDst = \'' . $treeDst . '\'.' );

	  		   $src = str_replace( ':', DIRECTORY_SEPARATOR, $treeSrc );
	  		   $dst = str_replace( ':', DIRECTORY_SEPARATOR, $treeDst );
	  		   $array = explode( DIRECTORY_SEPARATOR, $src );
	  		   $dstPath = $dst . DIRECTORY_SEPARATOR . array_pop( $array );

	  		   Logger::getInstance()->debug( 'FileExplorerController::copy Copying src \'' . $src . '\' to destination \'' . $dstPath . '\'.' );

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

	  	public function test() {

	  		   $nmr = new NewModelRemote;
	  		   $columns = $nmr->getDatabaseColumns( 'users' );
	  		   
	  		   $renderer = new AJAXRenderer();
	  		   $renderer->setOutput( 'xml' );
	  		   $renderer->render( $columns );
	  		   exit;
	  	}
	  	
	  	/**
	  	 * Renders a JSON object which contains a list of domain models which are present in the
	  	 * specified project, relative to the workspace configuration.
	  	 * 
	  	 * @param string $projectName The name of the project relative to the workspace root
	  	 * @return void
	  	 */
	    public function getModels( $projectName ) {

	    	   $config = new Config();
	 		   $config->setName( 'workspace' );

	 		   $projectPath = $config->getValue() . DIRECTORY_SEPARATOR .
	 		   		 			Scope::getRequestScope()->sanitize( $projectName );

	  		   $models = array();

			   $i=0;
		  	   $it = new RecursiveDirectoryIterator( $projectPath . DIRECTORY_SEPARATOR . 'model' );
			   foreach( new RecursiveIteratorIterator( $it ) as $file ) {

			   			if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

			 		    	$file = str_replace( '.php', '', $file->getFilename() );

				 		    if( substr( $file, 0, 4 ) == '.svn' ) continue;

				 		    $i++;

				 		    $model = array();
				 		    $model[0] = $i;
				 		    $model[1] = $file;

				 		    array_push( $models, $model );
				        }
			   }

			   $o = new stdClass;
			   $o->models = $models;

			   $this->getRenderer()->render( $o );
	  	}
	  	
		/**
	  	 * Prints a list of view templates in JSON format
	  	 * 
	  	 * @param string $projectName The name of the project relative to the workspace root
	  	 * @return void
	  	 */
	    public function getViewTemplates( $projectName ) {

	    	   $views = array();

	  		   $i=0;
		  	   $it = new RecursiveDirectoryIterator( '.' . DIRECTORY_SEPARATOR . 'templates' .
		  					DIRECTORY_SEPARATOR . 'views' );
			   foreach( new RecursiveIteratorIterator( $it ) as $file ) {

			   	        if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

			   	      	    $file = str_replace( '.' . DIRECTORY_SEPARATOR . 'templates' .
			   	      	  			  DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR, '', $file );

			   	      	  	if( substr( $file, -6 ) != '.phtml' ) continue;
				 		    if( substr( $file, 0, 4 ) == '.svn' ) continue;

				 		    $file = str_replace( '.phtml', '', $file );

				 		    $i++;

				 		    $view = array();
				 		    $view[0] = $i;
				 		    $view[1] = $file;

				 		    array_push( $views, $view );
				        }
			   }
		   
			   $o = new stdClass;
			   $o->views = $views;

			   $this->getRenderer()->render( $o );
	  	}

	  	/**
	  	 * Prints a list of controller templates in JSON format
	  	 * 
	  	 * @return void
	  	 */
	  	public function getControllerTemplates() {

	  		   $controllers = array();

	  		   $i=0;
		  	   $it = new RecursiveDirectoryIterator( '.' . DIRECTORY_SEPARATOR . 'templates' .
		  					DIRECTORY_SEPARATOR . 'controllers' );
			   foreach( new RecursiveIteratorIterator( $it ) as $file ) {

			   	        if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

			   	      	    $file = str_replace( '.' . DIRECTORY_SEPARATOR . 'templates' .
			   	      	  			  DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR, '', $file );

			   	      	  	if( substr( $file, -4 ) != '.php' ) continue;
				 		    if( substr( $file, 0, 4 ) == '.svn' ) continue;

				 		    $file = str_replace( '.php', '', $file );

				 		    $i++;

				 		    $controller = array();
				 		    $controller[0] = $i;
				 		    $controller[1] = $file;

				 		    array_push( $controllers, $controller );
				        }
			   }
		   
			   $o = new stdClass;
			   $o->controllers = $controllers;

			   $this->getRenderer()->render( $o );
	  	}

	  	public function createController( $projectName ) {

	  		   $config = new Config();
	 		   $config->setName( 'workspace' );

	 		   $projectRoot = $config->getValue() . DIRECTORY_SEPARATOR .
	 		   		 			Scope::getRequestScope()->sanitize( $projectName );

	 		   $controlDir = $projectRoot .	DIRECTORY_SEPARATOR . 'control';

	  		   $request = Scope::getRequestScope();
	  		   $type = $request->getSanitized( 'type' );

	  		   switch( $type ) {

	  		   		case 'basic':
	  		   			$name = $request->getSanitized( 'name' );
	  		   			$code = file_get_contents( '.' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
	  		   						'controllers' . DIRECTORY_SEPARATOR . 'BasicController.tpl' );
	  		   			$code = preg_replace( '/#ClassName#/', $name, $code );
	  		   			$code = preg_replace( '/#projectName#/', $projectName, $code );
	  		   			$h = fopen( $controlDir . DIRECTORY_SEPARATOR . ucfirst( $name ) . '.php', 'w' );
	  		   			fwrite( $h, $code );
	  		   			fclose( $h );
	  		   			break;

	  		   		case 'model':
	  		   			$model = $request->getSanitized( 'model' );
	  		   			$name = $model . 'Controller';
	  		   			$code = file_get_contents( '.' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
	  		   						'controllers' . DIRECTORY_SEPARATOR . 'ModelController.tpl' );
	  		   			$code = preg_replace( '/#ClassName#/', $name, $code );
	  		   			$code = preg_replace( '/#projectName#/', $projectName, $code );
	  		   			$code = preg_replace( '/#model#/', $model, $code );
	  		   			$h = fopen( $controlDir . DIRECTORY_SEPARATOR . $name . '.php', 'w' );
	  		   			fwrite( $h, $code );
	  		   			fclose( $h );
	  		   			break;

	  		   		case 'custom':

	  		   			$controller = $request->getSanitized( 'controller' ) . '.php';

				 	    copy( '.' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller,
				 	    	  $controlDir . DIRECTORY_SEPARATOR . $controller );

		 	 			$this->fixLineBreaks( $controlDir . DIRECTORY_SEPARATOR . $controller );
	  		   			break;

	  		   		default:
	  		   			throw new AgilePHP_Exception( 'Unsupported controller type \'' . $type . '\'.' );
	  		   }

	  		   $o = new stdClass;
	  		   $o->success = true;
	  		   $o->nodeId = str_replace( DIRECTORY_SEPARATOR, ':', $projectRoot ) . ':control';

	  		   $this->getRenderer()->render( $o );
	  	}

		public function createView( $projectName ) {

	  		   $config = new Config();
	 		   $config->setName( 'workspace' );

	 		   $projectRoot = $config->getValue() . DIRECTORY_SEPARATOR .
	 		   		 			Scope::getRequestScope()->sanitize( $projectName );

	 		   $viewDir = $projectRoot .	DIRECTORY_SEPARATOR . 'view';

	  		   $request = Scope::getRequestScope();
	  		   $type = $request->getSanitized( 'type' );

	  		   switch( $type ) {

	  		   		case 'basic':
	  		   			$name = $request->getSanitized( 'name' );
	  		   			$code = file_get_contents( '.' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
	  		   						'views' . DIRECTORY_SEPARATOR . 'basic-phtml.tpl' );
	  		   			$h = fopen( $viewDir . DIRECTORY_SEPARATOR .  $name . '.phtml', 'w' );
	  		   			fwrite( $h, $code );
	  		   			fclose( $h );
	  		   			break;

	  		   		case 'custom':

	  		   			$view = $request->getSanitized( 'view' ) . '.phtml';

				 	    copy( '.' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view,
				 	    	  $viewDir . DIRECTORY_SEPARATOR . $view );

		 	 			$this->fixLineBreaks( $viewDir . DIRECTORY_SEPARATOR . $view );
	  		   			break;

	  		   		default:
	  		   			throw new AgilePHP_Exception( 'Unsupported controller type \'' . $type . '\'.' );
	  		   }

	  		   $o = new stdClass;
	  		   $o->success = true;
	  		   $o->nodeId = str_replace( DIRECTORY_SEPARATOR, ':', $projectRoot ) . ':view';

	  		   $this->getRenderer()->render( $o );
	  	}

        /**
	     * Utility method to replace *nix line breaks with windows line breaks if building on windows.
	     * 
	     * @param String $file The fully qualified file path
	     * @return void
	     */
	    private function fixLineBreaks( $file ) {

	  		    if( substr( getcwd(), 0, 1 ) != '/' ) {

	       		    $h = fopen( $file, 'r' );
	      		    $data = '';
	      		    while( !feof( $h ) )
	      		 		   $data .= fgets( $h, 4096 );
	      		    fclose( $h );

	      		    $data = str_replace( "\n", PHP_EOL, $data );

             	    $h = fopen( $file, 'w' );
			  	    fwrite( $h, $data );
			  	    fclose( $h );
	  		    }
	    } 

	    public function __destruct() {

	  	 	   restore_error_handler();
	    }
}
?>