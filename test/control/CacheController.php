<?php 

class CacheController {

	  public function testExpires() {

	  		 $c = new MockCacher();
	  		 $c->expires();
	  }
	  
	  public function testNeverExpires() {

	  		 $c = new MockCacher();
	  		 $c->neverExpires();
	  }
}
?>