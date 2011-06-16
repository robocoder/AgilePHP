AgilePHP.Studio.Plugins = {

	load: function() {

		var stub = AgilePHP.Remoting.getStub('PluginsRemote');
		var pr = new PluginsRemote();
		    pr.getPlugins(AgilePHP.Studio.Plugins.loadHandler, function(ex) {
		    	AgilePHP.Studio.error(ex.message);
		    });
	},

	loadHandler: function(o) {

		for(var i=0; i<o.length; i++)
			AgilePHP.loadScript(o[i].path);
	}
};