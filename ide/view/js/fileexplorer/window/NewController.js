AgilePHP.IDE.FileExplorer.NewController = function() {

	var id = 'fe-new-controller';
	var controllersUrl = AgilePHP.getRequestBase() + '/FileExplorerController/getControllerTemplates';
	var modelsUrl = AgilePHP.getRequestBase() + '/FileExplorerController/getModels/' + AgilePHP.IDE.FileExplorer.projectName;

	new AgilePHP.XHR().request( controllersUrl, function( response ) {

		var combo = Ext.getCmp( id + '-form-controller' );
			combo.getStore().loadData( response.controllers );
	});

	new AgilePHP.XHR().request( modelsUrl, function( response ) {

		var combo = Ext.getCmp( id + '-form-model-name' );
			combo.getStore().loadData( response.models );
	});

	var win = new AgilePHP.IDE.Window( id, 'btn-new-controller', 'New Controller' );
		win.add( new Ext.FormPanel({
	  		id: id + '-form',
	  		url: AgilePHP.getRequestBase() + '/FileExplorerController/createController/' + AgilePHP.IDE.FileExplorer.projectName, 
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
	  	   		html: '<div class="window-header" style="padding-top: 10px;">Choose the type of controller you would like to create and then click finish.</div>'
	  	   	}, {
	  	           xtype: 'fieldset',
	  	           title: 'Controller Type',
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
          	   	        emptyText: '(Controller name)',
          	   	        allowBlank: false
          	       	}, {
	      	 			id: id + '-form-type-model',
	      				xtype: 'radio',
	      				name: 'type',
	      				boxLabel: 'Domain Model',
	      				inputValue: 'model',
	      				listeners: {
	  	            		check: function( radio, checked ) {
	  	            			Ext.getCmp( id + '-form-model-name' ).setDisabled( checked == false );
	  	            		}
	  	            	}
	  	            }, new Ext.form.ComboBox({
	      	       		id: id + '-form-model-name',
	      	       		name: 'model',
	      	       		mode: 'local',
	      	       		editable: true,
	      			    emptyText: '(Choose Model)',
	      			    store: new Ext.data.ArrayStore({
	      			        id: id + '-form-controller-model-store',
	      			        fields: [
	      			            {name: 'id'},
	      			            {name: 'name'}
	      			        ]
	      			    }),
	      			    valueField: 'id',
	      			    displayField: 'name',
	      			    disabled: true,
	      		        triggerAction: 'all',
	      		        allowBlank: false
	  	            }), {
	      	 			id: id + '-form-type-template',
	      				xtype: 'radio',
	      				name: 'type',
	      				boxLabel: 'Custom',
	      				inputValue: 'custom',
	      				listeners: {
	  	            		check: function( radio, checked ) {
	 	            			Ext.getCmp( id + '-form-controller' ).setDisabled( checked == false );
	  	            		}
	  	            	}
	  	            }, new Ext.form.ComboBox({
	      	       		id: id + '-form-controller',
	      	       		name: 'controller',
	      	       		mode: 'local',
	      			    emptyText: '(Choose Controller)',
	      			    store: new Ext.data.ArrayStore({
	      			        id: id + '-form-controller-store',
	      			        fields: [
	      			            {name: 'id'},
	      			            {name: 'name'}
	      			        ]
	      			    }),
	      			    valueField: 'id',
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

	return win;
};