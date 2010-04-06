<?php
class DatabaseManagerRemote {

	  #@RemoteMethod
	  public function getServers() {

	  		 $boxes = array();

	  		 try {
		  		   $pm = new PersistenceManager();
		  		   $servers = $pm->find( new Server() );

		  		   foreach( $servers as $server ) {

		  		   		$box = array();
		  		 		$box[0] = $server->getId();
		  		 		$box[1] = $server->getIp() . ' (' . $server->getProfile() . ')';

			  		 	array_push( $boxes, $box );
		  		 }

		  		 $o = new stdClass;
		  		 $o->servers = $boxes;

		  		 return $o;
	  		 }
	  		 catch( Exception $e ) {

	  		 	 throw new AgilePHP_RemotingException( $e );
	  		 }
	  }
}
?>