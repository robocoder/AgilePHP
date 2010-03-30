AgilePHP.IDE.Window.Database.Synchronize = function() {

	this.window = new AgilePHP.IDE.Window( 'databaseSynchronize', 'databaseSynchronize', 'Synchronize Databases' ); 

	this.panel = new Ext.Panel({

		id: 'database-sync-window-panel',
		items: [new Ext.form.ComboBox({
			id: 'database-sync-window-source-combo',
		    //typeAhead: true,
		    //triggerAction: 'all',
		    //lazyRender: true,
		    mode: 'local',
		    store: new Ext.data.ArrayStore({
		        id: 'database-sync-window-source-combo-store',
		        fields: [
		            'myId',
		            'displayText'
		        ],
		        data: [[1, 'item1'], [2, 'item2']]
		    }),
		    valueField: 'myId',
		    displayField: 'displayText'
		})]			        
	});

	this.window.add( this.panel );

	return this.window;
};