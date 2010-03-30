AgilePHP.IDE.Plugins = {

	load: function() {

		var stub = AgilePHP.Remoting.getStub( 'PluginsRemote' );
			stub.setCallback( AgilePHP.IDE.Plugins.loadHandler );

		var pr = new PluginsRemote();
		    pr.getPlugins();
	},

	loadHandler: function( o ) {

		if( typeof o == 'AgilePHP_RemotingException' ) {

			AgilePHP.IDE.error( o.message );
			return false;
		}

		for( var i=0; i<o.length; i++ )
			AgilePHP.loadScript( o[i].path );
	}
};