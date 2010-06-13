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
 * @package com.makeabyte.agilephp.test.control
 */

/**
 * Responsible for all processing and rendering tasks in regards to the inventory
 * module.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 */
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

	  /**
	   * Adds a new inventory item.
	   * 
	   * @return void
	   */
	  public function persist() {

	  	     $request = Scope::getRequestScope();

	  	     $image = null;
	  	     $video = null;

	  	     if( $_FILES['image']['size'] )
	  	     	 $image = $this->upload( 'image' );

	  	     if( $_FILES['video']['size'] )
	  	     	 $video = $this->upload( 'video' );

	  		 $i = new Inventory();
	  		 $i->setName( $request->get( 'name' ) );
	  		 $i->setDescription( $request->get( 'description' ) );
	  		 $i->setPrice( floatval( $request->get( 'price' ) ) );
	  		 $i->setCategory( $request->get( 'category' ) );
	  		 if( $image ) $i->setImage( $image );
	  		 if( $video ) $i->setVideo( $video );

	  		 try {
	  	 	 		ORM::persist( $i );
	  		 }
	  		 catch( ORMException $e ) {

	  		 		if( file_exists( AgilePHP::getFramework()->getWebRoot() . $image ) )
	  		 			@unlink( AgilePHP::getFramework()->getWebRoot() . $image );

	  		 		if( file_exists( AgilePHP::getFramework()->getWebRoot() . $video ) )
	  		 			@unlink( AgilePHP::getFramework()->getWebRoot() . $video );

	  	     	    throw new ORMException( $e->getMessage(), $e->getCode() );
	  		 }

	  		 $this->clear();
	  	 	 parent::index( $this->getPage() );
	  }

	  /**
	   * Performs the actual update operation for inventory items.
	   * 
	   * @return void
	   */
	  public function merge( $id, $page = 1 ) {

	  		 $request = Scope::getRequestScope();

			 if( $_FILES['image']['size'] )
			 	 $image = $this->upload( 'image' );

			 if( $_FILES['video']['size'] )
			 	$video = $this->upload( 'video' );

			 $this->getModel()->setId( $id );
			 $i = new Inventory();
			 $i->setId( $id );

	  		 if( isset( $image ) && $image != $i->getImage() ) {

	  		 	 $img = realpath( '../' . $i->getImage() );
	  		 	 if( file_exists( $img ) ) unlink( $img );

  		 	 	 $i->setImage( $image );
	  		 }

	  		 //if( isset( $image ) && $image == $i->getImage() || !isset( $image ) && $i->getImage() )
	  		 	// $i->setImage( $i->getImage() );

	  		 if( isset( $video ) && $video != $i->getVideo() ) {

	  		 	 $vid = realpath( '../' . $i->getVideo() );
	  		 	 if( file_exists( $vid ) ) unlink( $vid );

	  		 	 $i->setVideo( $video );
	  		 }

	  		 $i->setName( $request->get( 'name' ) );
	  		 $i->setDescription( $request->get( 'description' ) );
	  		 $i->setCategory( $request->get( 'category' ) );
	  		 $i->setPrice( floatval( $request->get( 'price' ) ) );

	  		 try {
	  	 	 		ORM::merge( $i );
	  		 }
	  		 catch( ORMException $e ) {

	  		 		$img = realpath( '../' . $i->getImage() );
	  		 		$vid = realpath( '../' . $i->geVideo() );

	  		 		if( $i->getImage() && file_exists( $mg ) ) unlink( $img );
	  		 		if( $i->getVideo() && file_exists( $vid ) ) unlink( $vid );

	  	     	    throw new ORMException( $e->getMessage(), $e->getCode() );
	  		 }

	  		 $this->clear();
	  	 	 parent::index( $this->getPage() );
	  }

	  /**
	   * Performs an inventory delete operation
	   * 
	   * @param $id The id of the inventory item to delete
	   * @return void
	   */
	  public function delete( $id ) {

	 		 $this->getModel()->setId( $id );

	 		 $img = realpath( '../' . $this->getModel()->getImage() );
	 		 $vid = realpath( '../' . $this->getModel()->getVideo() );

	  	     if( $this->getModel()->getImage() && file_exists( $img ) ) unlink( $img );
	  	     if( $this->getModel()->getVideo() && file_exists( $vid ) ) unlink( $vid );

	  		 ORM::delete( $this->getModel() );

	  		 $this->clear();
  		     parent::index( $this->getPage() );
	  }

	  /**
	   * Uploads images and videos to the server and files them into the appropriate
	   * folder based on the passed type.
	   * 
	   * @param $type The type of file to upload (image|video).
	   * @return void
	   */
	  public function upload( $type ) {

	  	     // If php.ini post_max_size is set to a size less than the data being posted,
		  	 // the PHP $_POST array will be empty (regardless if POST data is present.
			 $maxSize = (integer)ini_get( 'post_max_size' ) * 1024 * 1024; 
			 $contentLength = (integer)$_SERVER['CONTENT_LENGTH'];
			 if( $contentLength > $maxSize )
			 	 throw new FrameworkException( 'HTTP Content-Length greater than PHP configuration directive \'post_max_size\' (results in empty $_POST array). Content-Length = \'' . $contentLength . '\', post_max_size = \'' . $maxSize . '\'' );

			 $target = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR
			 		  . $type . DIRECTORY_SEPARATOR . $_FILES[$type]['name'];

			 $upload = new Upload();
			 $upload->setName( $type );
			 $upload->setDirectory( AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'uploads' .
			 						DIRECTORY_SEPARATOR . $type );
			 $upload->save();

			 return str_replace( AgilePHP::getFramework()->getWebRoot(), AgilePHP::getFramework()->getDocumentRoot(), $target );
	  }
}