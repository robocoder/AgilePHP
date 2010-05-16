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
 * @package com.makeabyte.agilephp.ide.control
 */

/**
 * Controller responsible for server side processing of the FileExplorer
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.ide.control
 */
class FileExplorerController extends BaseExtController {

		 private $file;

		 public function __construct() {

		 		parent::__construct();
		 		
		 		set_error_handler( 'FileExplorerController::ErrorHandler' );
		 		$this->createRenderer( 'AJAXRenderer' );
		 }

		 public function index() { 

		 		throw new AgilePHP_Exception( 'Invalid request' );
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

		 		$code = $this->getContents( preg_replace( '/\|/', DIRECTORY_SEPARATOR, $id ) );

		 		switch( $type ) {

		 			case 'code':

		 				 //if( $extension == 'phtml' ) {

			 				 $renderer = new PHTMLRenderer();
			 				 $renderer->set( 'id', $id );
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
		 public function save() {

		 		$request = Scope::getRequestScope();

		 		$id = $request->get( 'id' );

		 		$file = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $id );
		 		$code = stripslashes( html_entity_decode( $_POST['code'] ) );

		 		Logger::getInstance()->debug( $code );
		 		
		 		$h = fopen( $file, 'w' );
		 		$result = fwrite( $h, $code );
		 		fclose( $h );

		 		if( $result === false )
		 		    throw new AgilePHP_Exception( 'Failed to save code' );

		 		$o = new stdClass;
		 		$o->success = true;

		 		$this->getRenderer()->render( $o );
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
		 		$path = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $node );

		 		if( !$path || $path == '.' ) {

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

				    	$serialized_node = preg_replace( '/\\' . DIRECTORY_SEPARATOR . '/', '|', $fileInfo->getPathname() );

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
		 #@RemoteMethod
	  	public function delete( $treePath ) {

	  		   $treePath = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $treePath );

	  		   Logger::getInstance()->debug( 'FileExplorerController::delete Deleting treePath \'' . $treePath . '\'.' );

	  		   header( 'content-type: application/json' );

	  		   print FileUtils::delete( $treePath ) ? '{success:true}' : '{success:false}';
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

	  		   $srcId = $treeSrc;
	  		   $dstId = $treeDst;

	  		   $treeSrc = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $treeSrc );
	  		   $treeDst = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $treeDst );

	  		   $array = explode( DIRECTORY_SEPARATOR, $treeSrc );
	  		   $dstPath = $treeDst . DIRECTORY_SEPARATOR . array_pop( $array );

	  		   Logger::getInstance()->debug( 'FileExplorerController::copy Copying src \'' . $treeSrc . '\' to destination \'' . $dstPath . '\'.' );

	  		   header( 'content-type: application/json' );
  		   	   FileUtils::copy( $treeSrc, $dstPath );
  		   	   print '{success:true, parent: "' . $dstId . '"}';
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

	  		   Logger::getInstance()->debug( 'FileExplorerController::move $treeSrc = \'' . $treeSrc . '\'.' );
	  		   Logger::getInstance()->debug( 'FileExplorerController::move $treeDst = \'' . $treeDst . '\'.' );

	  		   $src = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $treeSrc );
	  		   $dst = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $treeDst );

	  		   $array = explode( DIRECTORY_SEPARATOR, $src );
	  		   $dstPath = $dst . DIRECTORY_SEPARATOR . array_pop( $array );

	  		   Logger::getInstance()->debug( 'FileExplorerController::move Moving src \'' . $src . '\' to destination \'' . $dstPath . '\'.' );

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
		 * Event handler for file explorer rename context menu item.
		 * 
		 * @param $treeSrc The tree source node / file system path (colons substituted for /)
		 * @param $dst The tree destination node / file system path (colons substituted for /)
		 * @return void
		 */
	  	public function rename( $treeSrc, $dst ) {

	  		   Logger::getInstance()->debug( 'FileExplorerController::rename $treeSrc = \'' . $treeSrc . '\'.' );
	  		   Logger::getInstance()->debug( 'FileExplorerController::rename $dst = \'' . $dst . '\'.' );

	  		   $src = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $treeSrc );

	  		   $array = explode( DIRECTORY_SEPARATOR, $src );
	  		   array_pop( $array );

	  		   $parent = implode( DIRECTORY_SEPARATOR, $array );
	  		   $dstPath = $parent . DIRECTORY_SEPARATOR . $dst; 

	  		   Logger::getInstance()->debug( 'FileExplorerController::rename Renaming src \'' . $src . '\' to destination \'' . $dstPath . '\'.' );

	  		   $o = new stdClass;

	  		   if( rename( $src, $dstPath ) ) {

	  		   	   $o->success = true;
	  		   	   $o->parentId = preg_replace( '/\\' . DIRECTORY_SEPARATOR . '/', '|', $parent );

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

			   $request = Scope::getRequestScope();
			   $name = $request->get( 'name' );

			   $filename = ($name) ? $name : null;

			   $path = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $treePath );
			   $renderer = MVC::getInstance()->createRenderer( 'AJAXRenderer' );
			   $o = new stdClass();

	  		   try {
		    		   $upload = new Upload();
		  	    	   $upload->setName( 'upload' );
		  	    	   $upload->setDirectory( $path );
		  	    	   $path = $upload->save( $filename );
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

	  		   $filename = (str_replace( '|', DIRECTORY_SEPARATOR, $path ) . DIRECTORY_SEPARATOR . $name);
	  		   if( $filename == '.' ) $filename = '.' . DIRECTORY_SEPARATOR;

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

	  		   $filename = str_replace( '|', DIRECTORY_SEPARATOR, $path ) . DIRECTORY_SEPARATOR . $name;
	  		   if( $filename == '.' ) $filename = '.' . DIRECTORY_SEPARATOR;

	  		   $o = new stdClass();
	  		   $o->success = touch( $filename ) ? true : false;

	  		   $this->getRenderer()->render( $o );
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

	  	/**
	  	 * Creates a new controller in the specified project
	  	 * 
	  	 * @param String $projectName The name of the project in the workspace to create the controller for
	  	 * @return void
	  	 * @throws AgilePHP_Exception
	  	 */
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
	  		   $o->nodeId = str_replace( DIRECTORY_SEPARATOR, '|', $projectRoot ) . '|control';

	  		   $this->getRenderer()->render( $o );
	  	}

	  	/**
	  	 * Creates a new view in the specified project
	  	 * 
	  	 * @param String $projectName The name of the project in the workspace to create the view for
	  	 * @return void
	  	 * @throws AgilePHP_Exception
	  	 */
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
	  		   $o->nodeId = str_replace( DIRECTORY_SEPARATOR, '|', $projectRoot ) . '|view';

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