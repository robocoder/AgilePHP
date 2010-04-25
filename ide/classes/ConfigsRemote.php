<?php

class ConfigsRemote {

	  #@RemoteMethod
 	  public function get( $name ) {

 	  		 $config = new Config();
 	  		 $config->setName( $name );

 	  		 $o = new stdClass;
 	  		 $o->value = $config->getValue();

 	  		 return $o;
	  }

	  #@RemoteMethod
 	  public function getConfigs() {

 	  		 $results = array();

 	  		 $pm = new PersistenceManager();
 	  		 $pm->setMaxResults( 50 );

 	  		 $configs = $pm->find( new Config() );

 	  		 foreach( $configs as $config ) {
 	  		 	
 	  		 	$o = new stdClass;
 	  		 	$o->name = $config->getName();
 	  		 	$o->value = $config->getValue();

 	  		 	array_push( $results, $o );
 	  		 }

 	  		 return $results;
	  }
}
?>