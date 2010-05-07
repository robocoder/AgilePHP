AgilePHP.IDE.FileExplorer.NewModel = function() {

	var stub = AgilePHP.Remoting.getStub( 'NewModelRemote' );
		stub.setCallback( function( tables ) {

			var combo = Ext.getCmp( id + '-form-database' );
				combo.getStore().loadData( tables );
		});

	var nmr = new NewModelRemote();
		nmr.getDatabaseTables();

	var win = new AgilePHP.IDE.Window( id, 'btn-new-model', 'New Model' );

	/*
		win.add( new Ext.FormPanel({
	  		id: id + '-form',
	  		url: AgilePHP.getRequestBase() + '/FileExplorerController/createModel/' + AgilePHP.IDE.FileExplorer.projectName, 
	  		frame: true,
	  		monitorValid: true,
	  		labelWidth: 1,
	  		defaults: {
	  	        anchor: '100%',
	  	        allowBlank: false,
	  	        msgTarget: 'side'
	  	    },
	  	   	items: [{
	  	   		xtype: 'label',
	  	   		html: '<div class="window-header" style="padding-top: 10px;">Enter the name of the model with each of its attributes and map them to the corresponding database table.</div>'
	  	   	}, {
	  	           xtype: 'fieldset',
	  	           title: 'Model Type',
	  	           defaults: {width: '95%'},
	  	           defaultType: 'textfield',
	  	           items: [{
	      	 			id: id + '-form-type-basic',
	      				xtype: 'radio',
	      				inputValue: 'basic',
	      				boxLabel: 'Basic',
	      				name: 'type',
	      				checked: true,
	      				listeners: {
	  	            		check: function( radio, checked ) {
	  	            			Ext.getCmp( id + '-form-basic-name' ).setDisabled( checked == false );
	  	            		}
	  	            	}
	  	            }, {
          	       		id: id + '-form-basic-name',
          	   	        xtype: 'textfield',
          	   	        name: 'name',
          	   	        emptyText: '(Model name)',
          	   	        allowBlank: false
          	       	}, {
          	       		id: id + '-form-basic-addbutton',
          	   	        xtype: 'tbbutton',
          	   	        text: 'Add',
          	   	        width: 75
          	       	}, {
	      	 			id: id + '-form-type-database',
	      				xtype: 'radio',
	      				name: 'type',
	      				boxLabel: 'Database',
	      				inputValue: 'database',
	      				listeners: {
	  	            		check: function( radio, checked ) {
	 	            			Ext.getCmp( id + '-form-database' ).setDisabled( checked == false );
	  	            		}
	  	            	}
	  	            }, new Ext.form.ComboBox({
	      	       		id: id + '-form-database',
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
	  	            })]
	  	   	}],
	  	   	buttons: [{
	  	   		id: id + 'btn-finish',
		   		text: 'Finish',
		   		formBind: true,
	            handler: function() {

			      	   	Ext.getCmp( id + '-form' ).getForm().submit({
			                method: 'POST', 
			                success: function( form, action ) {

								Ext.getCmp( id ).close();
								AgilePHP.IDE.FileExplorer.tree.getNodeById( action.result.nodeId ).reload();
							},
			                failure: function( form, action ) {

								Ext.getCmp( id + '-form' ).getForm().reset();

								if( !action ) AgilePHP.IDE.error( 'No reply from server' );
								if( action.result.errors.reason ) {
									
									AgilePHP.IDE.error( action.result.errors.reason );
									return;
								}			                	 
			                } 
			      	   	}); 
	            }
		   	}]
		}));
	*/



	
	return win.wizard([{
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
	  	   		html: '<div class="window-header">Choose how you want to create your new domain model. Manual allows you to define the name and properties. Database table automatically creates your model by reverse engineering a table in your database.</div>'
	  	   	}, {
	  	           xtype: 'fieldset',
	  	           title: 'Configuration',
	  	           defaults: {width: '95%'},
	  	           defaultType: 'textfield',
	  	           items: [{
	      	 			id: id + '-form-type-manual',
	      				xtype: 'radio',
	      				inputValue: 'manual',
	      				boxLabel: 'Manual',
	      				name: 'type',
	      				checked: true,
	      				listeners: {
	  	            		check: function( radio, checked ) {
	  	            			Ext.getCmp( id + '-form-manual-name' ).setDisabled( checked == false );
	  	            		}
	  	            	}
	  	            }, {
          	       		id: id + '-form-manual-name',
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
	 	            			Ext.getCmp( id + '-form-database' ).setDisabled( checked == false );
	  	            		}
	  	            	}
	  	            }, new Ext.form.ComboBox({
	      	       		id: id + '-form-database',
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
	  	            	id: id  + '-form-persistence',
	  	            	xtype: 'checkbox',
	  	            	name: 'persistence',
	  	            	inputValue: true,
	  	            	boxLabel: 'Update persistence.xml'
	  	            }]
		   	}],
		   	label: 'Type',
            handler: function() {

            	return true;
            }
		}, {
      	    id: 'step-1',
      	    label: 'Columns',
      		xtype: 'form',
      		labelWidth: 75,
      		defaults: {
      	        anchor: '95%',
      	        allowBlank: false,
      	        msgTarget: 'side'
      	    },
      	    items: [{
      	   		xtype: 'label',
      	   		html: '<div class="window-header">Configure each of the model property to database column mappings and then click finish.</p>'
      	   	} ]
      	}]);
};