<?php

class InventoryController extends BaseModelActionController {

	  protected $model;

	  public function __construct() {

	  	     $this->model = new Inventory();

	  	     parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see control/AdminController#getModel()
	   */
	  public function getModel() {

	  	     return $this->model;
	  }
	  
	  public function index() {

	  		 parent::modelList();
	  }

	  /**
	   * Adds a new inventory item.
	   * 
	   * @return void
	   */
	  public function persist() {

	  	     $request = Scope::getInstance()->getRequestScope();

	  	     $image = $this->upload( 'image' );
	  		 $video = $this->upload( 'video' );

	  		 $i = new Inventory();
	  		 $i->setName( $request->get( 'name' ) );
	  		 $i->setDescription( $request->get( 'description' ) );
	  		 $i->setPrice( floatval( $request->get( 'price' ) ) );
	  		 $i->setCategory( $request->get( 'category' ) );
	  		 if( $image ) $i->setImage( $image );
	  		 if( $video ) $i->setVideo( $video );

	  		 try {
	  	 	 		$this->getPersistenceManager()->persist( $i );
	  		 }
	  		 catch( AgilePHP_PersistenceException $e ) {

	  		 		if( file_exists( AgilePHP::getFramework()->getWebRoot() . $image ) )
	  		 			@unlink( AgilePHP::getFramework()->getWebRoot() . $image );

	  		 		if( file_exists( AgilePHP::getFramework()->getWebRoot() . $video ) )
	  		 			@unlink( AgilePHP::getFramework()->getWebRoot() . $video );

	  	     	    throw new AgilePHP_PersistenceException( $e->getMessage(), $e->getCode() );
	  		 }

	  	 	 parent::modelList( $this->getPage() );
	  }

	  /**
	   * Performs the actual update operation for inventory items.
	   * 
	   * @return void
	   */
	  public function merge() {

		  	 // If php.ini post_max_size is set to a size less than the data being posted,
		  	 // the PHP $_POST array will be empty (regardless if POST data is present.
			 $maxSize = (integer)ini_get( 'post_max_size' ) * 1024 * 1024; 
			 $contentLength = (integer)$_SERVER['CONTENT_LENGTH'];
			 if( $contentLength > $maxSize )
			 	 throw new AgilePHP_Exception( 'HTTP Content-Length greater than PHP configuration directive \'post_max_size\' (results in empty $_POST array). Content-Length = \'' . $contentLength . '\', post_max_size = \'' . $maxSize . '\'' );

	  		 $request = Scope::getInstance()->getRequestScope();

			 foreach( $_FILES as $key => $upload )
			          $this->upload( $key );

			 if( $_FILES['image']['tmp_name'] )
			 	 $image = $this->upload( 'image' );

			 if($_FILES['video']['tmp_name'] )
			 	$video = $this->upload( 'video' );

	  		 $i = new Inventory();
	  		 $i->setId( $request->get( 'id' ) );

	  		 $persisted = $this->getPersistenceManager()->find( $i );

	  		 if( isset( $image ) && $image != $persisted->getImage() ) {

	  		 	 if( file_exists( AgilePHP::getFramework()->getWebRoot() . $persisted->getImage() ) )
	  		 	 	 @unlink( AgilePHP::getFramework()->getWebRoot() . $persisted->getImage() );

  		 	 	 $i->setImage( $image );
	  		 }

	  		 if( isset( $image ) && $image == $persisted->getImage() || !isset( $image ) && $persisted->getImage() )
	  		 	 $i->setImage( $persisted->getImage() );

	  		 if( isset( $video ) && $video != $persisted->getVideo() ) {

	  		 	 if( file_exists( AgilePHP::getFramework()->getWebRoot() . $persisted->getVideo() ) )
	  		 	 	 @unlink( AgilePHP::getFramework()->getWebRoot() . $persisted->getVideo() );

	  		 	 $i->setVideo( $video );
	  		 }

	  		 $i->setName( $request->get( 'name' ) );
	  		 $i->setDescription( $request->get( 'description' ) );
	  		 $i->setCategory( $request->get( 'category' ) );
	  		 $i->setPrice( floatval( $request->get( 'price' ) ) );

	  		 try {
	  	 	 		$this->getPersistenceManager()->merge( $i );
	  		 }
	  		 catch( AgilePHP_PersistenceException $e ) {

	  		 		if( $persisted->getVideo() && file_exists( $persisted->getImage() ) )
	  		 			@unlink( AgilePHP::getFramework()->getWebRoot() . $persisted->getImage() );

	  		 		if( $persisted->getVideo() && file_exists( $persisted->getVideo() ) )
	  		 			@unlink( AgilePHP::getFramework()->getWebRoot() . $persisted->getVideo() );

	  	     	    throw new AgilePHP_PersistenceException( $e->getMessage(), $e->getCode() );
	  		 }

	  	 	  parent::modelList( $this->getPage() );
	  }

	  /**
	   * Performs an inventory delete operation
	   * 
	   * @param $id The id of the inventory item to delete
	   * @return void
	   */
	  public function delete() {

	  	     $persisted = $this->getPersistenceManager()->find( $this->getModel() );

	  	     if( $persisted->getImage() && file_exists( AgilePHP::getFramework()->getWebRoot() . $persisted->getImage() ) )
	  	     	 unlink( AgilePHP::getFramework()->getWebRoot() . $persisted->getImage() );

	  	     if( $persisted->getVideo() && file_exists( AgilePHP::getFramework()->getWebRoot() . $persisted->getVideo() ) )
	  	     	 unlink( AgilePHP::getFramework()->getWebRoot() . $persisted->getVideo() );

	  		 $this->getPersistenceManager()->delete( $persisted );

	  		  parent::modelList( $this->getPage() );
	  }

	  /**
	   * Uploads images and videos to the server and files them into the appropriate
	   * folder based on the passed type.
	   * 
	   * @param $type The type of file to upload (image|video).
	   * @return void
	   */
	  public function upload( $type ) {

	  	     if( $_FILES[$type]['error'] === 4 ) // nothing to upload
	  	     	 return;

			 $target = AgilePHP::getFramework()->getWebRoot() . '/uploads/' . $type . '/' . $_FILES[$type]['name'];

			 if( !move_uploaded_file( $_FILES[$type]['tmp_name'], $target ) ) {
			 	
			 	 switch( $_FILES[$type]['error'] ) {

			 	 	case 1:
			 	 		$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
			 	 		break;
			 	 		
			 	 	case 2:
			 	 		 $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
			 	 		 break;
			 	 		 
			 	 	case 3:
			 	 		 $error = 'The uploaded file was only partially uploaded.';
			 	 		 break;
			 	 		 
			 	 	case 4:
			 	 		 $error = 'No file was uploaded.';
			 	 		 break;
			 	 		 
			 	 	case 5:
			 	 		 $error = 'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.';
			 	 		 break;
			 	 		 
			 	 	case 6:
			 	 		 $error = 'Failed to write file to disk. Introduced in PHP 5.1.0.';
			 	 		 break;
			 	 		 
			 	 	case 7:
			 	 		 $error = 'File upload stopped by extension. Introduced in PHP 5.2.0.';
			 	 		 break;
			 	 }
			 }

			 if( isset( $error ) )
			 	 throw new AgilePHP_Exception( 'There was an error uploading the file! Contact your webmaster for support.' . $error );

			 return str_replace( AgilePHP::getFramework()->getWebRoot(), '', $target );
	  }
}