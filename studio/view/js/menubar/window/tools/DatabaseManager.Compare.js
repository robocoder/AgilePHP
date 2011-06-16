AgilePHP.Remoting.load('DatabaseManagerRemote');

AgilePHP.Studio.Menubar.tools.DatabaseManager.Compare = function() {

	// Get list of database servers if this is the first time the window is being created.
	//if(!AgilePHP.Studio.Window.Tools.DatabaseManager.databases.length) {

		var stub = AgilePHP.Remoting.getStub('DatabaseManagerRemote');
		var dbmr = new DatabaseManagerRemote();
			dbmr.getServers(AgilePHP.Studio.Menubar.tools.DatabaseManager.Compare.remoteDatabasesHandler);
	//}

	this.window = new AgilePHP.Studio.Window('databaseManagerCompare', 'databaseCompare', 'Database Compare', 600); 

	this.panel = new Ext.FormPanel({

		//url: 'save-form.php',
		labelWidth: 75,	
	    frame: true,
	    bodyStyle: 'padding: 5px 5px 0',
	    width: '100%',
	    renderTo: document.body,
	    layout:'column', // arrange items in columns
	    defaults: {      // defaults applied to items
	        layout: 'form',
	        border: true,
	        bodyStyle: 'padding: 4px'
	    },
	    items: [{
	        xtype:'fieldset',
	        columnWidth: 0.5,
	        title: 'Source Database',
	        collapsible: true,
	        autoHeight: true,
	        width: '50%',
	        style: 'padding-left: 10px;',
	        defaults: {
	            anchor: '-20' // leave room for error icon	            
	        },
	        defaultType: 'textfield',
	        items :[new Ext.form.ComboBox({
				id: 'db-manager-compare-window-source-combo',
			    mode: 'local',
			    emptyText: '(Choose Server)',
			    editable: true,
			    store: new Ext.data.ArrayStore({
			        id: 'db-manager-compare-window-source-combo-store',
			        fields: [
			            {name: 'id'},
			            {name: 'name'}
			        ]
			    }),
			    valueField: 'id',
			    displayField: 'name',
			    fieldLabel: 'Server',
			    triggerAction: 'all'
	        }), {
		        	id: 'srcUsername',
		        	name: 'srcUsername',
	                fieldLabel: 'Username'
	            }, {
	            	id: 'srcPassword',
	            	name: 'srcPassword',
	                fieldLabel: 'Password',
	                inputType: 'password'
	            }
	        ]
	    }, {
	    	id: 'db-manager-compare-window-target-fieldset',
	        xtype:'fieldset',
	        columnWidth: 0.5,
	        title: 'Target Database',
	        collapsible: true,
	        autoHeight: true,
	        width: '50%',
	        style: 'margin-left: 5px; padding-left: 10px;',
	        defaults: {
	            anchor: '-20' // leave room for error icon
	        },
	        defaultType: 'textfield',
	        items :[new Ext.form.ComboBox({
				id: 'db-manager-compare-window-target-combo',
			    mode: 'local',
			    emptyText: '(Choose Server)',
			    editable: true,
			    store: new Ext.data.ArrayStore({
			        id: 'db-manager-compare-window-target-combo-store',
			        fields: [
			            {name: 'id'},
			            {name: 'name'}
			        ]
			    }),
			    valueField: 'id',
			    displayField: 'name',
			    fieldLabel: 'Server',
			    triggerAction: 'all'
	        }), {
	        	id: 'tgtUsername',
	        	name: 'tgtUsername',
                fieldLabel: 'Username'
            }, {
            	id: 'tgtPassword',
            	name: 'tgtPassword',
                fieldLabel: 'Password',
                inputType: 'password'
            }
	        ]
	    }]
	});

	AgilePHP.debug(this.window);
	AgilePHP.debug(this.panel);

	this.window.add(this.panel);
	this.window.window.on('maximize', function() {

		this.window.window.relayEvents(Ext.getCmp('db-manager-compare-window-target-fieldset'), ['resize']);

	}, this);

	return this.window;
};

AgilePHP.Studio.Menubar.tools.DatabaseManager.Compare.databases = [];

AgilePHP.Studio.Menubar.tools.DatabaseManager.Compare.remoteDatabasesHandler = function(data) {

	 if(data.RemotingException) {

		 AgilePHP.Studio.error(data.message);
		 return false;
	 }

	 AgilePHP.Studio.Menubar.tools.DatabaseManager.Compare.databases = data.servers;

	 var srcCombo = Ext.getCmp('db-manager-compare-window-source-combo');
	 	 srcCombo.getStore().loadData(data.servers);

	 var tgtCombo = Ext.getCmp('db-manager-compare-window-target-combo');
	 	 tgtCombo.getStore().loadData(data.servers);
};