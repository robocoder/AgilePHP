AgilePHP.IDE.Properties = function() {

	var id = 'ide-properties';
	var selectedComponent = null;

	return new Ext.Panel({

		id: 'ide-properties',
		region: 'east',
        title: 'Toolbar',
        collapsible: true,
        split: true,
        width: 225,
        minSize: 175,
        maxSize: 400,
        margins: '3 3 3 0',
	    cmargins: '3 3 3 0',
        layout: 'vbox',
        items: [
        	new Ext.Panel({
        		id: 'ide-properties-components',
        		region: 'center',
        		title: 'Components',
        		width: '100%',
        		frame: false,
        		layout: 'fit',
        		flex: 1,
        		autoScroll: true,
        		items: [
						new Ext.tree.TreePanel({
							id: id + '-components-treepanel',
							useArrows: true,
						    autoScroll: true,
						    animate: true,
						    enableDD: true,
						    containerScroll: true,
						    border: false,
						    dataUrl: AgilePHP.getRequestBase() + '/FileExplorerController/getComponents/' + AgilePHP.IDE.FileExplorer.workspace + '/' + AgilePHP.IDE.FileExplorer.projectName,
						    disableCaching: false,
						    rootVisible: false,
						    root: {
						        nodeType: 'async',
						        text: 'Components',
						        draggable: false,
						        id: id + '-components-treepanel-root',
						        iconCls: 'mime-folder'
						        
						    },
						    listeners: {
				        		
			        			click: function( node, e ) {

						    		selectedComponent = node.id;

						    		var store = {};
						    		var properties = node.attributes.component.properties;
						    		var types = node.attributes.component.types;
						    		var value = null;
						    		for( var property in properties ) {

						    			switch( types[property] ) {
						    			
					    			 		case 'date':
					    			 			value = new Date(Date.parse( properties[property] ) );
					    			 			break;

					    			 		case 'boolean':
					    			 			value = (properties[property] == true) ? true : false;
					    			 			break;

					    			 		case 'int':
					    			 			value = parseInt(properties[property]);
					    			 			break;

					    			 		default:
						    					value = properties[property];
						    			}
				    			 		store[property] = value;
					    			}
			                        Ext.getCmp( id + '-grid' ).setSource( store );
			        			}
			        		}
						})
        		]
        	}),

        	new Ext.Panel({

        		id: 'ide-properties-properties',
        		region: 'south',
        		title: 'Properties',
        		width: '100%',
        		layout: 'fit',
        		frame: false,
        		flex: 1,
        		autoScroll: true,
        		items: new Ext.grid.PropertyGrid({
        			id: id + '-grid',
				    source: {},
				    listeners: {
				    
				    	afteredit: function( e ) {

				    		var componentsRemote = new ComponentsRemote();
				    			componentsRemote.setCallback( function( response ) {

				    				if( !response ) {

				    					AgilePHP.IDE.error( 'Error saving configuration. The server did not reply' );
				    					return false;
				    				}
				    				if( response._class == 'AgilePHP_RemotingException' )
				    					AgilePHP.IDE.error( response.message );
				    			});
				    			componentsRemote.setProperty( selectedComponent, e.record.data.name, e.record.data.value );
				    	}
				    }
        		})
        	})
        ]
	});
};