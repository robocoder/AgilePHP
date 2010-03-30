<?php
class DatabaseCompareRemote {

	  #@RemoteMethod
	  public function getDatabases() {

	  		 $boxes = array();

	  		 try {
		  		   $pm = new PersistenceManager();
		  		   $servers = $pm->find( new Server() );

		  		   foreach( $servers as $server ) {

		  		   	/*
		  		 		$s = new stdClass();
		  		 		$s->id = $server->getId();
		  		 		$s->name = $server->getIp() . ' (' . $server->getProfile() . ')';
					*/

		  		   		$box = array();
		  		 		$box[0] = $server->getId();
		  		 		$box[1] = $server->getIp() . ' (' . $server->getProfile() . ')';

		  		 		/*
		  		 		$s->ip = $server->getIp();
			  		 	$s->hostname = $server->getHostname();
			  		 	$s->profile = $server->getProfile();

			  		 	$st = new stdClass();
			  		 	$st->id = $server->getServerType()->getId();
			  		 	$st->type = $server->getServerType()->getType();
			  		 	$st->name = $server->getServerType()->getName();
			  		 	$s->ServerType = $st;
			  		 	*/

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