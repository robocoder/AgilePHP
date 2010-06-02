AgilePHP.Studio.FileExplorer.NewComponent = function() {

	var id = 'fe-new-component';

	var pagingMemoryProxy = new Ext.ux.data.PagingMemoryProxy( [] );

	var componentsRemote = new ComponentsRemote();
		componentsRemote.setCallback( function( response ) {

			if( response._class == 'AgilePHP_RemotingException' ) {

				AgilePHP.Studio.error( response.message );
				return false;
			}

			var data = [];
			for( var i=0; i<response.apps.length; i++ ) {

				 data.push([ 
				             response.apps[i].id,
				             response.apps[i].appId,
				             response.apps[i].name,
				             response.apps[i].description,
				             response.apps[i].appType.name,
				             response.apps[i].currency.symbol + response.apps[i].cost,
				             response.apps[i].size
				 ]);
			}
			pagingMemoryProxy.data = data;
			Ext.getCmp( id + '-new-component-pagingtoolbar' ).doRefresh();
		});
		componentsRemote.getApps();

	var store = new Ext.data.Store({
        proxy: pagingMemoryProxy,
        reader: new Ext.data.ArrayReader({}, [
                   {name: id + '-app-id'},
	               {name: id + '-app-appId'},
	               {name: id + '-app-name'},
	               {name: id + '-app-description'},
	               {name: id + '-app-type'},
	               {name: id + '-app-cost'},
	               {name: id + '-app-size'}
	          ])
	});

	var checkbox = new Ext.grid.CheckboxSelectionModel();

	var colModel = new Ext.grid.ColumnModel(
		    [
			  checkbox,
			  {
		        header: 'Id',
		        readOnly: true,
		        dataIndex: id + '-app-id',
		        hidden: true
		      },{
		        header: 'AppId',
		        dataIndex: id + '-app-appId',
		        width: 100
		      },{
		        header: 'Name',
		        dataIndex: id + '-app-name',
		        width: 150
		      },{
				header: 'Description',
		        dataIndex: id + '-app-description',
		        width: 200
			  }, {
				header: 'Type',
			    dataIndex: id + '-app-type',
			    width: 100
			  }, {
				header: 'Cost',
			    dataIndex: id + '-app-cost',
			    width: 100
			  }, {
				header: 'Size',
			    dataIndex: id + '-app-size',
			    width: 150
			  }]
		    );
		colModel.defaultSortable= true;

	var grid = new AgilePHP.Studio.PagedGridPanel({
  		id: id + '-new-component-grid',
        store: store,
        viewConfig: {
            forceFit: true
		},
		cm: colModel,
        stripeRows: true,
        stateId: id + '-grid-state',
        tbar: new Ext.Toolbar({
        	id: id + '-new-component-grid-toolbar',
        	items: [{
				id: id + '-new-component-grid-toolbar-install',
				text: 'Install',
				iconCls: 'btn-list-add',
				disabled: true,
				handler: function() {

					var grid = Ext.getCmp( id + '-new-component-grid' );
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
        	}]
		}),
        bbar: {
        	 id: id + '-new-component-pagingtoolbar',
        	 xtype: 'paging',
             pageSize: 10,
             displayInfo: true,
             displayMsg: 'Displaying data {0} - {1} of {2}',
             emptyMsg: 'No data to display'
        },
        listeners: {

			rowclick: function( grid, rowIndex, e ) {

				 Ext.getCmp( id + '-new-component-grid-toolbar-install' ).setDisabled( false );
			}
		}
	});
		
	var win = new AgilePHP.Studio.Window( id, 'btn-new-component', 'New Component', 550 );
		win.add( grid );

	return win;
};