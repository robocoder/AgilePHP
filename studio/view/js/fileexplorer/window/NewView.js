AgilePHP.Studio.FileExplorer.NewView = function() {

	var id = 'fe-new-view';
	var viewsUrl = AgilePHP.getRequestBase() + '/FileExplorerController/getViewTemplates/' + AgilePHP.Studio.FileExplorer.getSelectedProject();

	new AgilePHP.XHR().request( viewsUrl, function( response ) {

		var combo = Ext.getCmp( id + '-form-view' );
			combo.getStore().loadData( response.views );
	});

	var win = new AgilePHP.Studio.Window( id, 'btn-new-view', 'New View' );
		win.add( new Ext.FormPanel({
	  		id: id + '-form',
	  		url: AgilePHP.getRequestBase() + '/FileExplorerController/createView/' + AgilePHP.Studio.FileExplorer.getSelectedProject(), 
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
	  	   		html: '<div class="window-header" style="padding-top: 10px;">Choose the view you want and then click finish.</div>'
	  	   	}, {
	  	           xtype: 'fieldset',
	  	           title: 'View Type',
	  	           defaults: {width: '95%'},
	  	           defaultType: 'textfield',
	  	           items: [{
	      	 			id: id + '-form-type-basic',
	      				xtype: 'radio',
	      				inputValue: 'basic',
	      				boxLabel: 'PHTML',
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
          	   	        emptyText: '(View name)',
          	   	        allowBlank: false
          	       	}, {
	      	 			id: id + '-form-type-template',
	      				xtype: 'radio',
	      				name: 'type',
	      				boxLabel: 'Custom Template',
	      				inputValue: 'custom',
	      				listeners: {
	  	            		check: function( radio, checked ) {
	 	            			Ext.getCmp( id + '-form-view' ).setDisabled( checked == false );
	  	            		}
	  	            	}
	  	            }, new Ext.form.ComboBox({
	      	       		id: id + '-form-view',
	      	       		name: 'view',
	      	       		mode: 'local',
	      			    emptyText: '(Choose View)',
	      			    store: new Ext.data.ArrayStore({
	      			        id: id + '-form-view-store',
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
			                waitTitle: 'Working', 
		                    waitMsg: 'Creating view...',
			                success: function( form, action ) {

								Ext.getCmp( id ).close();
								AgilePHP.Studio.FileExplorer.tree.getNodeById( action.result.nodeId ).reload();
							},
			                failure: function( form, action ) {

								Ext.getCmp( id + '-form' ).getForm().reset();

								if( !action ) AgilePHP.Studio.error( 'No reply from server' );
								if( action.result.errors.reason ) {
									
									AgilePHP.Studio.error( action.result.errors.reason );
									return;
								}			                	 
			                } 
			      	   	}); 
	            }
		   	}]
		}));

	return win;
};