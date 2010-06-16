AgilePHP.Studio.FileExplorer.NewModel = function() {

	var id = 'fe-new-model';

	var mode = null; // 1 = user defined, 2 = from database

	var gridSelectedIndex, selectedTable = 0;

	var newModelRemote = new NewModelRemote();

	var win = new AgilePHP.Studio.Window( id, 'btn-new-model', 'New Model', false, 330 );

	var workspace = AgilePHP.Studio.FileExplorer.workspace;
		workspace = (workspace.indexOf( '|' ) === 0) ? workspace.replace( /\|/g, '/' ) : workspace.replace( /\|/g, '\\' );
	
	var store = new Ext.data.Store({
	        proxy: new Ext.data.MemoryProxy( [] ),
	        reader: new Ext.data.ArrayReader({}, [
		               {name: id + '-editorgridpanel-property'},
		               {name: id + '-editorgridpanel-column'},
		               {name: id + '-editorgridpanel-displayname'},
		               {name: id + '-editorgridpanel-type'},
		               {name: id + '-editorgridpanel-length', type: 'float'},
		               {name: id + '-editorgridpanel-default'},
		               {name: id + '-editorgridpanel-visible'},
		               {name: id + '-editorgridpanel-required'},
		               {name: id + '-editorgridpanel-index'},
		               {name: id + '-editorgridpanel-pkey'},
		               {name: id + '-editorgridpanel-autoincrement'},
		               {name: id + '-editorgridpanel-sortable'},
		               {name: id + '-editorgridpanel-selectable'},
		               {name: id + '-editorgridpanel-sanitize'}
		          ])
	});

	var visibleColumn = new Ext.ux.grid.CheckColumn({
   		id: id + '-editorgridpanel-visible',
        header: 'Visible',
        dataIndex: id + '-editorgridpanel-visible',
        width: 45
    });
	
	var requiredColumn = new Ext.ux.grid.CheckColumn({
   		id: id + '-editorgridpanel-required',
        header: 'Required',
        dataIndex: id + '-editorgridpanel-required',
        width: 60
    });
	
	var indexColumn = new Ext.ux.grid.CheckColumn({
   		id: id + '-editorgridpanel-index',
        header: 'Index',
        dataIndex: id + '-editorgridpanel-index',
        width: 50
    });

	var pkeyColumn = new Ext.ux.grid.CheckColumn({
   		id: id + '-editorgridpanel-pkey',
        header: 'Primary Key',
        dataIndex: id + '-editorgridpanel-pkey',
        width: 75
    });
	
	var autoIncrementColumn = new Ext.ux.grid.CheckColumn({
   		id: id + '-editorgridpanel-autoincrement',
        header: 'Auto Increment',
        dataIndex: id + '-editorgridpanel-autoincrement',
        width: 87
    });
	
	var sortableColumn = new Ext.ux.grid.CheckColumn({
   		id: id + '-editorgridpanel-sortable',
        header: 'Sortable',
        dataIndex: id + '-editorgridpanel-sortable',
        width: 60
    });
	
	var selectableColumn = new Ext.ux.grid.CheckColumn({
   		id: id + '-editorgridpanel-selectable',
        header: 'Selectable',
        dataIndex: id + '-editorgridpanel-selectable',
        width: 60
    });

	var sanitizeColumn = new Ext.ux.grid.CheckColumn({
   		id: id + '-editorgridpanel-sanitize',
        header: 'Sanitize',
        dataIndex: id + '-editorgridpanel-sanitize',
        width: 60
    });

	var grid = new Ext.grid.EditorGridPanel({

		id: id + '-editorgridpanel',
	    store: store,
	    autoHeight: true,
	    stripeRows: true,
	    tbar: new Ext.Toolbar({
	    	width: '100%',
	    	items:[{
		            iconCls: 'btn-list-add',
		            handler : function() {
		                var mapping = grid.getStore().recordType;
		                var m = new mapping({});
		                grid.stopEditing();
		                store.insert( grid.getStore().getModifiedRecords().length, m );
		                grid.startEditing( grid.getStore().getModifiedRecords().length, 0 );
		            }
			    }, {
		            iconCls: 'btn-list-remove',
		            handler : function() {
		                grid.getStore().removeAt( gridSelectedIndex );
		        	}
		        }, {
		        	iconCls: 'btn-refresh',
		        	handler: function() {
		
		        		var data = newModelRemote.getTableColumnsMeta( workspace, AgilePHP.Studio.FileExplorer.getSelectedProject(), selectedTable );
				  	  	store.removeAll();
				  	  	store.loadData( data );
		        	}
		        }]
	    }),
	    stateId: 'grid',
	    plugins: [visibleColumn, requiredColumn, indexColumn, pkeyColumn, autoIncrementColumn, sortableColumn, selectableColumn, sanitizeColumn],
	    columns: [{
	    		id: id + '-editorgridpanel-property',
	        	header: 'Property',
	        	width: 100,
	        	name: 'property',
	        	sortable: true,
	        	editor: new Ext.form.TextField({
                  allowBlank: false
	        	}),
	        	dataIndex: id + '-editorgridpanel-property'
	       	}, {
	       		id: id + '-editorgridpanel-column',
	       		header: 'Column',
	       		sortable: true,
	       		width: 100,
	       		editor: new Ext.form.ComboBox({
      	       		id: id + '-editorgridpanel-column-combo',
      	       		name: 'column',
      	       		mode: 'local',
      	       		editable: true,
      			    emptyText: '(Choose Column)',
      			    store: new Ext.data.ArrayStore({
      			        id: id + '-editorgridpanel-column-combo-store',
      			        fields: [
      			            {name: 'name'}
      			        ]
      			    }),
      			    displayField: 'name',
      		        triggerAction: 'all',
      		        allowBlank: false
  	            }),
	       		dataIndex: id + '-editorgridpanel-column'
	       	}, {
	       		id: id + '-editorgridpanel-displayname',
	       		header: 'Display',
	       		name: 'display',
	       		width: 100,
	       		sortable: true,
	       		editor: new Ext.form.TextField(),
	       		dataIndex: id + '-editorgridpanel-displayname'
	       	}, {
	       		id: id + '-editorgridpanel-type',
	       		header: 'Type',
	       		sortable: true,
	       		width: 100,
	       		editor: new Ext.form.ComboBox({
	       			id: id + '-editorgridpanel-type-combo',
	       		    typeAhead: true,
	       		    triggerAction: 'all',
	       		    forceSelection: true,
	       		    lazyRender: true,
	       		    mode: 'local',
	       		    name: 'type',
	       		    store: new Ext.data.ArrayStore({
	       		        id: id + '-editorgridpanel-type-combo-store',
	       		        fields: [
	       		            'name'
	       		        ]
	       		    }),
	       		    displayField: 'name'
	       		}),
	       		dataIndex: id + '-editorgridpanel-type'
	       	}, {
	       		id: id + '-editorgridpanel-length',
	       		header: 'Length',
	       		name: 'length',
	       		width: 100,
	       		sortable: true,
	       		editor: new Ext.form.NumberField({
                  allowBlank: false,
                  allowNegative: false,
                  maxValue: 100000
                }),
	       		dataIndex: id + '-editorgridpanel-length'
	       	}, {
	       		id: id + '-editorgridpanel-default',
	       		header: 'Default',
	       		name: 'default',
	       		width: 100,
	       		sortable: true,
	       		editor: new Ext.form.TextField(),
	       		dataIndex: id + '-editorgridpanel-default'
	       	}, visibleColumn,
	       	   requiredColumn,
	       	   indexColumn,
	       	   pkeyColumn,
	       	   autoIncrementColumn,
	       	   sortableColumn,
	       	   selectableColumn,
	       	   sanitizeColumn
	       	],
	    listeners: {
			rowclick: function( grid, rowIndex, e ) {

				gridSelectedIndex = rowIndex;
			}, 
			rowdblclick: function( grid, rowIndex, e ) {

			},
			contextmenu : function( e ) {
	
				e.preventDefault();
			},
			rowcontextmenu: function( grid, rowIndex, e ) {

				e.preventDefault();				
	        }
		}
	});
	
	var wizard = win.wizard([{
	  		id: 'step-0',
	  		xtype: 'form',
	  		labelWidth: 1,
	  		defaults: {
	  	        anchor: '100%',
	  	        allowBlank: false,
	  	        msgTarget: 'side'
	  	    },
	  	   	items: [{
	  	   		xtype: 'label',
	  	   		html: '<div class="window-header">Choose how you want to create the new model. "New" allows a defined name and properties. "Database" creates your model by reverse engineering a table in your database.</div>'
	  	   	}, {
	  	           xtype: 'fieldset',
	  	           title: 'Source',
	  	           defaults: {width: '95%'},
	  	           defaultType: 'textfield',
	  	           items: [{
	      	 			id: id + '-form-type-manual',
	      				xtype: 'radio',
	      				inputValue: 'new',
	      				boxLabel: 'New',
	      				name: 'type',
	      				checked: true,
	      				listeners: {
	  	            		check: function( radio, checked ) {
	  	            			Ext.getCmp( id + '-form-name' ).setDisabled( checked == false );
	  	            			Ext.getCmp( id  + '-form-createtable' ).setDisabled( checked == false );
	  	            		}
	  	            	}
	  	            }, {
          	       		id: id + '-form-name',
          	   	        xtype: 'textfield',
          	   	        name: 'name',
          	   	        emptyText: '(Model name)',
          	   	        allowBlank: false
          	       	}, {
	      	 			id: id + '-form-type-database',
	      				xtype: 'radio',
	      				name: 'type',
	      				boxLabel: 'Database',
	      				inputValue: 'database',
	      				listeners: {
	  	            		check: function( radio, checked ) {
	 	            			Ext.getCmp( id + '-form-database-table' ).setDisabled( checked == false );
	  	            		}
	  	            	}
	  	            }, new Ext.form.ComboBox({
	      	       		id: id + '-form-database-table',
	      	       		name: 'controller',
	      	       		mode: 'local',
	      			    emptyText: '(Choose Database Table)',
	      			    store: new Ext.data.ArrayStore({
	      			        id: id + '-form-database-store',
	      			        fields: [
	      			            {name: 'name'}
	      			        ]
	      			    }),
	      			    displayField: 'name',
	      			    disabled: true,
	      		        forceSelection: true,
	      		        triggerAction: 'all',
	      		        allowBlank: false
	  	            }), {
	  	            	id: id  + '-form-orm',
	  	            	xtype: 'checkbox',
	  	            	name: 'orm',
	  	            	inputValue: true,
	  	            	boxLabel: 'Update orm.xml',
	  	            	checked: true
	  	            }, {
	  	            	id: id  + '-form-createtable',
	  	            	xtype: 'checkbox',
	  	            	name: 'createtable',
	  	            	inputValue: true,
	  	            	boxLabel: 'Create Database Table',
	  	            	checked: true
	  	            }]
		   	}],
		   	label: 'Type',
            handler: function() {

	  	    	mode = 1; // user defined model
	  	    	selectedTable = Ext.getCmp( id + '-form-database-table' ).getValue();

	  	    	if( Ext.getCmp( id + '-form-type-database' ).getValue() ) {

	  	    		mode = 2; // create from database table

	  	    		newModelRemote.setCallback( function( response ) {

	  	    			if( response.RemotingException._class == 'RemotingException' ) {

			  	  			AgilePHP.Studio.error( response.RemotingException.message );
			  	  			return false;
			  	  		}

	  	    			store.removeAll();
				  	  	store.loadData( response );
	  	    		});
	  	    		newModelRemote.getTableColumnsMeta( workspace, AgilePHP.Studio.FileExplorer.getSelectedProject(), selectedTable );

			  	  	newModelRemote.setCallback( function( response ) {

				  	  	if( response.RemotingException._class == 'RemotingException' ) {
	
			  	  			AgilePHP.Studio.error( response.RemotingException.message );
			  	  			return false;
			  	  		}

			  	  		if( response ) {

			  	  			Ext.getCmp( id + '-editorgridpanel-column-combo' ).getStore().loadData( response );
			  	  			return true;
			  	  		}
			  	  	});
			  	  	newModelRemote.getTableColumns( workspace, AgilePHP.Studio.FileExplorer.getSelectedProject(), selectedTable );
	  	    	}

            	return true;
            }
		}, {
      	    id: 'step-1',
      	    label: 'Configure',
      		xtype: 'container',
      		layout: 'fit',
      		autoScroll: true,
      		defaults: {
      	        anchor: '95%',
      	        allowBlank: false,
      	        msgTarget: 'side'
      	    },
      	    items: [{
      	   		xtype: 'label',
      	   		html: '<div class="window-header">Configure each of the model property to database column mappings and then click finish.</p>'
      	   	}, grid ],
      	   	handler: function() {

      	    	var properties = [];
      	    	var tableName = (selectedTable) ? selectedTable : Ext.getCmp( id + '-form-name' ).getValue();
      	    	var createTableEl = Ext.getCmp( id  + '-form-createtable' );
      	    	var createTableFlag = (createTableEl.disabled == false && createTableEl.getValue()) ? true : false;

      	    	store.each( function( record ) {

      	    		if( mode == 1 ) {

	      	    		properties.push([ record.data[ id + '-editorgridpanel-property'],
		      	    		              record.data[ id + '-editorgridpanel-column'],
		      	    		              record.data[ id + '-editorgridpanel-displayname'],
		      	    		              record.data[ id + '-editorgridpanel-type'],
		      	    		              record.data[ id + '-editorgridpanel-length'],
		      	    		              record.data[ id + '-editorgridpanel-default'],
		      	    		              record.data[ id + '-editorgridpanel-visible'],
		      	    		              record.data[ id + '-editorgridpanel-required'],
		      	    		              record.data[ id + '-editorgridpanel-index'],
		      	    		              record.data[ id + '-editorgridpanel-pkey'],
		      	    		              record.data[ id + '-editorgridpanel-autoincrement'],
		      	    		              record.data[ id + '-editorgridpanel-sortable'],
		      	    		              record.data[ id + '-editorgridpanel-selectable'],
		      	    		              record.data[ id + '-editorgridpanel-sanitize']
		      	    	]);
      	    		}
      	    		else if( mode ==2 )
      	    			properties.push( record.json );
      	    	});

      	    	newModelRemote.setCallback( function( response ) {

      	    		if( response.RemotingException._class == 'RemotingException' ) {

		  	  			AgilePHP.Studio.error( response.RemotingException.message );
		  	  			win.show();
		  	  			return false;
		  	  		}
      	    		AgilePHP.Studio.FileExplorer.highlightedNode.reload();
      	    		win.close();
      	    	});
      	    	newModelRemote.create( tableName,
			 				AgilePHP.Studio.FileExplorer.workspace,
			 				AgilePHP.Studio.FileExplorer.getSelectedProject(),
			 				properties,
			 				Ext.getCmp( id + '-form-orm' ).getValue(),
			 				createTableFlag );

      	    	Ext.getCmp( 'fe-new-model' ).hide();
      	    }
    }]);
	
	newModelRemote.setCallback( function( tables ) {

		Ext.getCmp( id + '-form-database-table' ).getStore().loadData( tables );
	});
	newModelRemote.getDatabaseTables( workspace, AgilePHP.Studio.FileExplorer.getSelectedProject() );

	newModelRemote.setCallback( function( types ) {
	
		Ext.getCmp( id + '-editorgridpanel-type-combo' ).getStore().loadData( types );
	});
	newModelRemote.getSQLDataTypes();

	return wizard;
};