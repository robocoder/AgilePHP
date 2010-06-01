AgilePHP.Remoting.load( 'PearPeclRemote' );

AgilePHP.Studio.Menubar.tools.Settings = function() {

	var id = 'menubar-tools-settings';
	var win = new AgilePHP.Studio.Window( id, 'toolsSettings', 'Settings', 550 );
	
	var pearPeclRemote = new PearPeclRemote();
		pearPeclRemote.setCallback( function( response ) {

			if( !response ) return false; // no packages installed

			var data = [];
			for( var i=0; i<response.exts.length; i++ ) {

				 data.push([ 
				             response.exts[i][0],
				             response.exts[i][1],
				             response.exts[i][2]
				 ]);
			}
			Ext.getCmp( id + '-grid-pear' ).getStore().loadData( data );
		});
		pearPeclRemote.getInstalledPearExts();

	var store = new Ext.data.Store({
        proxy: new Ext.data.MemoryProxy( [] ),
        reader: new Ext.data.ArrayReader({}, [
                   {name: id + '-package'},
	               {name: id + '-version'},
	               {name: id + '-state'}
	          ])
	});

	var checkbox = new Ext.grid.CheckboxSelectionModel();

	var colModel = new Ext.grid.ColumnModel(
		    [
			  checkbox,
			  {
		        header: 'Package',
		        readOnly: true,
		        dataIndex: id + '-package',
		        width: 200
		      },{
		        header: 'Version',
		        dataIndex: id + '-version',
		        width: 50
		      },{
		        header: 'State',
		        dataIndex: id + '-state',
		        width: 100
		      }]
		    );
		colModel.defaultSortable= true;

	var pearGrid =  new Ext.grid.GridPanel({
	  		id: id + '-grid-pear',
	        store: store,
	        viewConfig: {
	            forceFit: true
			},
			cm: colModel,
	        stripeRows: true,
	        stateId: 'grid',
	        tbar: new Ext.Toolbar({
	        	id: id + '-grid-pear-toolbar',
	        	items: [{
					id: id + '-grid-pear-toolbar-install',
					text: 'Install',
					iconCls: 'btn-list-add',
					disabled: true,
					handler: function() {
	
						var grid = Ext.getCmp( id + '-grid-pear' );
						var data = grid.getSelectionModel().getSelected().json;
	
						componentsRemote.setCallback( function( response ) {
	
							if( !response ) {
	
								AgilePHP.Studio.error( 'No reply from server' );
								return false;
							}
							if( response._class == 'AgilePHP_RemotingException' ) {
	
								AgilePHP.Studio.error( response.message );
								return false;
							}
	
							new AgilePHP.Studio.Notification( '<b>Information</b>', 'Component is finished installing.')
				            var t = Ext.getCmp( 'studio-properties-components-treepanel' );
				            	t.getLoader().dataUrl = AgilePHP.getRequestBase() + '/FileExplorerController/getComponents/' + workspace + '/' + project;
				            	t.getRootNode().reload();
	
				            AgilePHP.Studio.FileExplorer.highlightedNode.reload();
						});
						var workspace = AgilePHP.Studio.FileExplorer.getWorkspace();
						var project = AgilePHP.Studio.FileExplorer.getSelectedProject();
						var projectRoot = workspace + '|' + project;
	
						componentsRemote.install( projectRoot, data[0], data[1] );
						win.close();
	        		}
	        	}, {
					id: id + '-grid-pear-toolbar-channels',
					iconCls: 'btn-download',
					text: 'Channels'
      	       	}, '->',
	        		new Ext.form.Label({html:'<em>Search</em>', style: 'padding-right: 5px;'}),
	        		new Ext.form.TextField({
	      	       		id: id + '-grid-pear-toolbar-keyword',
	      	   	        name: 'name',
	      	   	        allowBlank: false
      	       	}), {
					id: id + '-grid-pear-toolbar-search',
					iconCls: 'btn-search',
					handler: function() {
      	       		
      	       			// search handler for pear here
      	       		}
      	       	}]
			}),
	        bbar: new Ext.PagingToolbar({
	        	 id: id + '-pear-pagingtoolbar',
	             pageSize: 6,
	             store: store,
	             displayInfo: true,
	             displayMsg: 'Displaying data {0} - {1} of {2}',
	             emptyMsg: 'No data to display'
	        }),
	        listeners: {
				
				rowclick: function( grid, rowIndex, e ) {

					 Ext.getCmp( id + '-grid-pear-toolbar' ).setDisabled( false );
				}
			}
	});
		
	var peclGrid =  new Ext.grid.GridPanel({
	  		id: id + '-grid-pecl',
	        store: store,
	        viewConfig: {
	            forceFit: true
			},
			cm: colModel,
	        stripeRows: true,
	        stateId: 'grid',
	        tbar: new Ext.Toolbar({
	        	id: id + '-grid-pecl-toolbar',
	        	items: [{
					id: id + '-grid-pecl-toolbar-install',
					text: 'Install',
					iconCls: 'btn-list-add',
					disabled: true,
					handler: function() {
	
						var grid = Ext.getCmp( id + '-grid-pecl' );
						var data = grid.getSelectionModel().getSelected().json;
	
						componentsRemote.setCallback( function( response ) {
	
							if( !response ) {
	
								AgilePHP.Studio.error( 'No reply from server' );
								return false;
							}
							if( response._class == 'AgilePHP_RemotingException' ) {
	
								AgilePHP.Studio.error( response.message );
								return false;
							}
	
							new AgilePHP.Studio.Notification( '<b>Information</b>', 'Component is finished installing.')
				            var t = Ext.getCmp( 'studio-properties-components-treepanel' );
				            	t.getLoader().dataUrl = AgilePHP.getRequestBase() + '/FileExplorerController/getComponents/' + workspace + '/' + project;
				            	t.getRootNode().reload();
	
				            AgilePHP.Studio.FileExplorer.highlightedNode.reload();
						});
						var workspace = AgilePHP.Studio.FileExplorer.getWorkspace();
						var project = AgilePHP.Studio.FileExplorer.getSelectedProject();
						var projectRoot = workspace + '|' + project;
	
						componentsRemote.install( projectRoot, data[0], data[1] );
						win.close();
	        		}
	        	}, {
					id: id + '-grid-pecl-toolbar-channels',
					iconCls: 'btn-download',
					text: 'Channels'
      	       	}, '->',
	        		new Ext.form.Label({html:'<em>Search</em>', style: 'padding-right: 5px;'}),
	        		new Ext.form.TextField({
	      	       		id: id + '-grid-pecl-toolbar-keyword',
	      	   	        name: 'name',
	      	   	        allowBlank: false
      	       	}), {
					id: id + '-grid-pecl-toolbar-search',
					iconCls: 'btn-search'
      	       	}]
			}),
	        bbar: new Ext.PagingToolbar({
	        	 id: id + '-pecl-pagingtoolbar',
	             pageSize: 6,
	             store: store,
	             displayInfo: true,
	             displayMsg: 'Displaying data {0} - {1} of {2}',
	             emptyMsg: 'No data to display'
	        }),
	        listeners: {
				
				rowclick: function( grid, rowIndex, e ) {

					 Ext.getCmp( id + '-grid-pecl-toolbar' ).setDisabled( false );
				}
			}
	});

	var tabpanel = new Ext.TabPanel({

		id: 'studio-tabpanel',
	    activeTab: 0,
	    autoScroll: true,
	    enableTabScroll: true,
	    margins: '3 0 3 0',
	    cmargins: '3 0 3 0',
	    //tabPosition: 'bottom',
	    items:[{
	    	id: id + 'studio-tabpanel-pear',
	    	title: 'PEAR',
	    	iconCls: 'tabPEAR',
	    	layout: 'fit',
	    	items: [pearGrid]
	    }, {
	    	id: id + 'studio-tabpanel-pecl',
	    	title: 'PECL',
	    	iconCls: 'tabPECL',
	    	layout: 'fit',
	    	items: [peclGrid]
	    }],
	    listeners: {

			resize: function( tabPanel ) {

				//tabPanel.setHeight( document.documentElement.clientHeight - 90 )
			}
		}
	});
	
	win.add( tabpanel );
	
	return win;
};