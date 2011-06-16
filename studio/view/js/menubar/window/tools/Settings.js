AgilePHP.Remoting.load('PearPeclRemote');

AgilePHP.Studio.Menubar.tools.Settings = function() {

	var id = 'menubar-tools-settings';
	var win = new AgilePHP.Studio.Window(id, 'toolsSettings', 'Settings', 550, 500);

	var pearPagingMemoryProxy = new Ext.ux.data.PagingMemoryProxy([]);
	var peclPagingMemoryProxy = new Ext.ux.data.PagingMemoryProxy([]);

	// Remote PHP class 
	var pearPeclRemote = new PearPeclRemote();

	 	// Get installed PEAR extensions
		pearPeclRemote.getInstalledExtensions('pear', function(response) {

			if(!response) return false; // no packages installed

			if(response.RemotingException) {

				AgilePHP.Studio.error(response.message);
				return false;
			}

			var data = [];
			for(var i=0; i<response.exts.length; i++) {

				 data.push([ 
				             response.exts[i][0],
				             response.exts[i][1],
				             response.exts[i][2]
				 ]);
			}
			pearPagingMemoryProxy.data = data;
			Ext.getCmp(id + '-pear-pagingtoolbar').doRefresh();
		});

		// Get installed PECL extensions
		pearPeclRemote.getInstalledExtensions('pecl', function(response) {

			if(!response) return false; // no packages installed

			if(response.RemotingException) {

				AgilePHP.Studio.error(response.message);
				return false;
			}

			var data = [];
			for(var i=0; i<response.exts.length; i++) {

				 data.push([ 
				             response.exts[i][0],
				             response.exts[i][1],
				             response.exts[i][2]
				 ]);
			}
			peclPagingMemoryProxy.data = data;
			Ext.getCmp(id + '-pecl-pagingtoolbar').doRefresh();
		});

	var pearStore = new Ext.data.Store({
        proxy: pearPagingMemoryProxy,
        reader: new Ext.data.ArrayReader({}, [
                   {name: id + '-pear-package'},
	               {name: id + '-pear-version'},
	               {name: id + '-pear-state'}
	          ]),
	    remoteSort: true
	});
	pearStore.load({params:{start:0, limit:5}});
	
	var peclStore = new Ext.data.Store({
        proxy: peclPagingMemoryProxy,
        reader: new Ext.data.ArrayReader({}, [
                   {name: id + '-pecl-package'},
	               {name: id + '-pecl-version'},
	               {name: id + '-pecl-state'}
	          ]),
	    remoteSort: true
	});

	var checkbox = new Ext.grid.CheckboxSelectionModel();

	var pearModel = new Ext.grid.ColumnModel(
		    [
			  checkbox,
			  {
		        header: 'Package',
		        readOnly: true,
		        dataIndex: id + '-pear-package',
		        width: 200,
		        sortable: true
		      },{
		        header: 'Version',
		        dataIndex: id + '-pear-version',
		        width: 50,
		        sortable: true
		      },{
		        header: 'State',
		        dataIndex: id + '-pear-state',
		        width: 100,
		        sortable: true
		      }]
		   );
		pearModel.defaultSortable = true;

	var peclModel = new Ext.grid.ColumnModel(
			    [
				  checkbox,
				  {
			        header: 'Package',
			        readOnly: true,
			        dataIndex: id + '-pecl-package',
			        width: 200,
			        sortable: true
			      },{
			        header: 'Version',
			        dataIndex: id + '-pecl-version',
			        width: 50,
			        sortable: true
			      },{
			        header: 'State',
			        dataIndex: id + '-pecl-state',
			        width: 100,
			        sortable: true
			      }]
			   );
	peclModel.defaultSortable = true;

	var pearGrid =  new AgilePHP.Studio.PagedGridPanel({
	  		id: id + '-grid-pear',
	        store: pearStore,
	        viewConfig: {
	            forceFit: true
			},
			cm: pearModel,
	        stripeRows: true,
	        stateId: id + '-grid-pear-state',
	        tbar: new Ext.Toolbar({
	        	id: id + '-grid-pear-toolbar',
	        	items: [{
					id: id + '-grid-pear-toolbar-install',
					text: 'Uninstall',
					iconCls: 'btn-list-remove',
					disabled: true,
					handler: function() {
		        		pearPeclRemote.uninstall('pear', pearGrid.getSelectionModel().getSelected().json[0], function(response) {

		        			if(!response) return false; // no packages installed

		        			if(response.RemotingException) {

		        				AgilePHP.Studio.error(response.message);
		        				return false;
		        			}

		        			var data = [];
		        			for(var i=0; i<response.exts.length; i++) {
	
		        				 data.push([ 
		        				             response.exts[i][0],
		        				             response.exts[i][1],
		        				             response.exts[i][2]
		        				 ]);
		        			}
		        			pearPagingMemoryProxy.data = data;
		        			Ext.getCmp(id + '-pear-pagingtoolbar').doRefresh();
		        		});
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
	        bbar: {
	        	 id: id + '-pear-pagingtoolbar',
	        	 xtype: 'paging',
	             pageSize: 5,
	             displayInfo: true,
	             displayMsg: 'Displaying data {0} - {1} of {2}',
	             emptyMsg: 'No data to display'
	        },
	        listeners: {
				
				rowclick: function(grid, rowIndex, e) {

					 Ext.getCmp(id + '-grid-pear-toolbar').setDisabled(false);
				}
			}
	});
		
	var peclGrid =  new AgilePHP.Studio.PagedGridPanel({
	  		id: id + '-grid-pecl',
	        store: peclStore,
	        viewConfig: {
	            forceFit: true
			},
			cm: peclModel,
	        stripeRows: true,
	        stateId: 'grid',
	        tbar: new Ext.Toolbar({
	        	id: id + '-grid-pecl-toolbar',
	        	items: [{
					id: id + '-grid-pecl-toolbar-install',
					text: 'Uninstall',
					iconCls: 'btn-list-remove',
					disabled: true,
					handler: function() {
		        		pearPeclRemote.uninstall('pecl', peclGrid.getSelectionModel().getSelected().json[0], function(response) {
		        			
		        			if(!response) return false; // no packages installed
	
		        			if(response.RemotingException) {
	
		        				AgilePHP.Studio.error(response.message);
		        				return false;
		        			}
	
		        			var data = [];
		        			for(var i=0; i<response.exts.length; i++) {
	
		        				 data.push([ 
		        				             response.exts[i][0],
		        				             response.exts[i][1],
		        				             response.exts[i][2]
		        				 ]);
		        			}
		        			peclPagingMemoryProxy.data = data;
		        			Ext.getCmp(id + '-pecl-pagingtoolbar').doRefresh();
		        		});
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
	        bbar: {
	        	 id: id + '-pecl-pagingtoolbar',
	        	 xtype: 'paging',
	             pageSize: 5,
	             displayInfo: true,
	             displayMsg: 'Displaying data {0} - {1} of {2}',
	             emptyMsg: 'No data to display'
	        },
	        listeners: {
				
				rowclick: function(grid, rowIndex, e) {

					 Ext.getCmp(id + '-grid-pecl-toolbar').setDisabled(false);
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
	    }]
	});
	
	win.add(tabpanel);
	
	return win;
};