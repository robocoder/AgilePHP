<?php 

class Inventory {

	  private $id;
	  private $name;
	  private $description;
	  private $price;
	  private $category;
	  private $image;
	  private $video;

	  /**
	   * Inventory entity class
	   * 
	   * @return void
	   */
	  public function Inventory() { }

	  /**
	   * Sets the inventory id
	   * 
	   * @param $id The inventory id
	   * @return void
	   */
	  public function setId( $id ) {

	  	     $this->id = $id;
	  }

	  public function getId() {

	  	     return $this->id;
	  }

	  public function setName( $name ) {

	  	     $this->name = $name;
	  }

	  public function getName() {

	  	     return $this->name;
	  }

	  public function setDescription( $description ) {
	  	
	  	     $this->description = $description;
	  }

	  public function getDescription() {

	  	     return $this->description;
	  }

	  public function setPrice( $price ) {

	  	     $this->price = $price;	  	     
	  }

	  public function getPrice() {

	  	     return $this->price;
	  }
	  
	  public function getCategory() {
	  	
	  		 return $this->category;
	  }
	  
	  public function setCategory( $category ) {
	  	
	  		 $this->category = $category;
	  }

	  public function setImage( $path ) {
	  	
	  	     $this->image = $path;
	  }
	  
	  public function getImage() {
	  	
	  	     return $this->image;
	  }
	  
	  public function setVideo( $path ) {

	  	     $this->video = $path;
	  }

	  public function getVideo() {
	  	
	  	     return $this->video;
	  }
}
?>