<?php

class PageController extends BaseController {

		 private $file;

		 public function __construct() {

		 		parent::__construct();
		 }

		 public function index() { 

		 		throw new AgilePHP_Exception( 'Malformed request' );
		 }

		 /**
	      * Provides the TinyMCE WYSIWYG editor 'template_external_list_url' configuration.
	      * 
	      * @return void
	      */
	  	 public function templateList() {

	  	 	    $templates = $this->getFiles( 'templates' );
			  	$js =  'var tinyMCETemplateList = [ ';

	  		    		for( $i=0; $i<count( $templates ); $i++ ) {

	  		    				 $js .= '["Template ' . ($i+1) . '", "' . $templates[$i] . '"]';
	  		    				 $js .= ( ($i+1) < count( $templates ) ? ', ' : '];' ); 
	  		    		}
				print $js;
	  	 }

	  	 /**
	      * Provides the TinyMCE WYSIWYG editor 'external_link_list_url' configuration.
	      * 
	      * @return void
	      */
	  	 public function linkList() {

	  	 		$js =  'var tinyMCELinkList = new Array( ';

	  	 		$links = $this->getFiles( 'links' );
	  		    for( $i=0; $i<count( $links ); $i++ ) {

	  		    	 $js .= '["' . $links[$i] . '", "' . $links[$i] . '"]';
	  		    	 $js .= ( ($i+1) < count( $links ) ? ', ' : ');' ); 
	  		    }
				print $js;
	     }

	     /**
	      * Provides the TinyMCE WYSIWYG editor 'external_image_list_url' configuration.
	      * 
	      * @return void
	      */
	     public function imageList() {

	     		$js =  'var tinyMCEImageList = new Array( ';
	    		$images = $this->getFiles( 'images' );
	    		for( $i=0; $i<count( $images ); $i++ ) {

	   				 $js .= '["' . $images[$i] . '", "' . $images[$i] . '"]';
	   				 $js .= ( ($i+1) < count( $images ) ? ', ' : ');' ); 
	    		}
				print $js;
	     }

	     /**
	      * Provides the TinyMCE WYSIWYG editor 'media_external_list_url' configuration.
	      * 
	      * @return void
	      */
	     public function mediaList() {

	  		    $js =  'var tinyMCEMediaList = [';
	    		$videos = $this->getFiles( 'videos' );
	    		for( $i=0; $i<count( $videos ); $i++ ) {
	  		    		
	   				 $js .= '["' . $videos[$i] . '", "' . $videos[$i] . '"]';
	   				 $js .= ( ($i+1) < count( $videos ) ? ', ' : '];' ); 
	    		}
				print $js;
	     }

		 /**
	      * Provides the TinyMCE WYSIWYG editor 'content_css' configuration.
	      * 
	      * @return void
	      */
	     public function cssList() {

	  		    $js =  '';
	    		$css = $this->getFiles( 'css' );
	    		for( $i=0; $i<count( $css ); $i++ ) {

	   				 $js .= $css[$i];
	   				 $js .= ( ($i+1) < count( $css ) ? ',' : '' ); 
	    		}
				return $js;
	     }

	     /**
	      * Returns the file contents for the specified file.
	      * 
	      * @param $file The file path to load.
	      * @return The contents of the specified file.
	      */
	     public function getContents() {

	     		if( !file_exists( $this->file ) )
		 		    throw new AgilePHP_Exception( 'Error loading file \'' . $this->file . '\'. File does not exist.' );

		 		$content = '';
		 		$h = fopen( $this->file, 'r' );
		 		while( !feof( $h ) )
		 		 	  $content .= fgets( $h, 2048 );
		 		$h->close;

		 		return $content;
	     }

	     /**
	      * Sets the specified file and calls 'loadPage' to load the
	      * specified file into an editor instance.
	      * 
	      * @param $file The file path to edit.
	      * @return void
	      */
		 public function edit( $file ) {

		 	 	$this->file = str_replace( ':', '/', $file );
		  	    $this->loadPage();
		 }

		 /**
		  * Loads a TinyMCE editor instance.
		  * 
		  * @return void
		  */
		 public function loadPage() {

		 		$content = $this->getContents();

	 		    $this->getRenderer()->set( 'tinymce_content', $content );
		 		$this->getRenderer()->set( 'tinymce_file', str_replace( '/', ':', $this->file ) );
		 		$this->getRenderer()->set( 'css_content', $this->cssList() );
		 		$this->getRenderer()->set( 'fullpagetoggle_bit', ($this->getRenderer()->get( 'fullpagetoggle_bit' ) ? 1 : 0 ) );
		 		$this->getRenderer()->set( 'fullpagetoggle_requestbase', AgilePHP::getFramework()->getRequestBase() .
		 					 '/' . MVC::getInstance()->getController() . '/fullpageToggle/' . str_replace( '/', ':', $this->file ) );

		 		$this->getRenderer()->renderComponent( 'TinyMCE', 'tinymce' );
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

		 		Logger::getInstance()->debug( 'BaseTinyMCEController::save Content = \'' . $_POST['content'] . '\'.' );
				Logger::getInstance()->debug( 'BaseTinyMCEController::save Saving file \'' . $this->file . '\'.' );

				$content = stripslashes( $_POST['content'] );
				//$content = html_entity_decode( stripslashes( $_POST['content'] ) );
				//Logger::getInstance()->debug( 'decoded content = ' . $content );
				
				$content = str_replace( '<div class="php-code">&lt?php', '<?php', $content );
				$content = str_replace( '<div class="php-code">&lt?=', '<?=', $content );
				//$content = str_replace( '<div class=">', '', $content );
				$content = str_replace( '?&gt</div>', '?>', $content );
				if( strpos( $content, '<script' ) === false ) {

					$content = str_replace( '</head>', '<script type="text/javascript" src="/AgilePHP/AgilePHP.js"></script>
<script type="text/javascript" src="/components/BannerRotator/view/js/BannerRotator.js"></script>
<script type="text/javascript" src="/view/js/parkcitiesnews.js"></script></head>', $content );
				}

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

		 		$request = Scope::getInstance()->getRequestScope();

		 		$node = $request->get( 'node' );
		 		$path = preg_replace( '/:/', '/', $node );

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

				$renderer = MVC::getInstance()->createRenderer( 'AJAXRenderer' );
				$renderer->render( $o->filesystem );
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
	  		   Logger::getInstance()->debug( 'BaseTinyMCEController::delete Deleting treePath \'' . $treePath . '\'.' );

	  		   header( 'content-type: application/json' );

	  		   if( is_dir( $treePath ) ) {

	  		   	   print $this->recursiveDelete( $treePath ) ? '{result:true}' : '{result:false}';
	  		   	   return;
	  		   }

	  		   print (unlink( str_replace( ':', '/', $treePath ) ) ? '{result:true}' : '{result:false}');
	  	}

	  	/**
	  	 * Performs a recursive delete on a directory.
	  	 * 
	  	 * @param $src The tree source path to delete (colons substitutes for /)
	  	 * @return void
	  	 */
	  	public function recursiveDelete( $src ) {

	  		   Logger::getInstance()->debug( 'BaseTinyMCEController::recursiveDelete Deleting source \'' . $src . '\'.' );

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

	  		   Logger::getInstance()->debug( 'BaseTinyMCEController::copy $treeSrc = \'' . $treeSrc . '\', $treeDst = \'' . $treeDst . '\'.' );

	  		   $treeSrc = str_replace( ':', '/', $treeSrc );
	  		   $treeDst = str_replace( ':', '/', $treeDst );

	  		   $dstPath = $treeDst . '/' . array_pop( explode( '/', $treeSrc ) );

	  		   Logger::getInstance()->debug( 'BaseTinyMCEController::copy Copying src \'' . $treeSrc . '\' to destination \'' . $dstPath . '\'.' );

	  		   header( 'content-type: application/json' );
	  		   if( is_dir( $treeSrc ) ) {

	  		   	   $this->recursiveCopy( $treeSrc, $dstPath );
	  		   	   print '{result:true, parent: "' . str_replace( '/', ':', $treeDst ) . '"}';
	  		   	   return;
	  		   }

	  		   copy( $treeSrc, $dstPath );
	  		   print '{result:true, parent: "' . str_replace( '/', ':', $treeDst ) . '"}';
	  	}

	  	/**
	  	 * Performs a recursive file copy from $src to $dst. 
	  	 * 
	  	 * @param $src The source directory to copy
	  	 * @param $dst The destination path to copy into
	  	 * @return void
	  	 */
	  	function recursiveCopy( $src, $dst ) {

	  			 Logger::getInstance()->debug( 'BaseTinyMCEController::copyRecursive Copy src \'' . $src . '\' to destination \'' . $dst . '\'.' );

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

	  		   Logger::getInstance()->debug( 'BaseTinyMCEController::copy $treeSrc = \'' . $treeSrc . '\'.' );
	  		   Logger::getInstance()->debug( 'BaseTinyMCEController::copy $treeDst = \'' . $treeDst . '\'.' );

	  		   $treeSrc = str_replace( ':', '/', $treeSrc );
	  		   $treeDst = str_replace( ':', '/', $treeDst );
	  		   $dstPath = $treeDst . '/' . array_pop( explode( '/', $treeSrc ) );

	  		   Logger::getInstance()->debug( 'BaseTinyMCEController::copy Copying src \'' . $treeSrc . '\' to destination \'' . $dstPath . '\'.' );

	  		   header( 'content-type: application/json' );
	  		   print rename( $treeSrc, $dstPath ) ? '{result:true, parent: "' . str_replace( '/', ':', $treeDst ) . '"}' : '{result:false}';
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

	  		   		  Logger::getInstance()->debug( 'BaseTinyMCEController::upload Failed to upload file. Error code: ' . $e->getCode() . ', Message: ' . $e->getMessage() );
	  		   		  
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

	  		   Logger::getInstance()->debug( 'BaseTinyMCEController::createDirectory Creating new file \'' . $filename . '\'.' );
	  		   
	  		   $o = new stdClass();
	  		   $o->result = mkdir( $filename ) ? true : false;

	  		   $renderer = MVC::getInstance()->createRenderer( 'AJAXRenderer' );
	  		   $renderer->render( $o );
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
	  		   $o->result = touch( $filename ) ? true : false;

	  		   $renderer = MVC::getInstance()->createRenderer( 'AJAXRenderer' );
	  		   $renderer->render( $o );
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
}
?>