AgilePHP.IDE.FileExplorer = {
	
		panel: null,
		pasteMode: null,
		selectedNode: null,
		tree: null,
		window: null,

		getTree: function() {

			return AgilePHP.IDE.FileExplorer.tree;
		},
		
		/**
		 * Creates a new tab/page in the content editing area.
		 * 
		 * @param id The id of the tree node which was double clicked
		 * @param text The text of the tree node which was double clicked
		 * @return void
		 */
		newPage: function( id, text ) {

				var editors = [];
				var designViews = [ 'html', 'xhtml' ];
				var codeViews = [ 'xml', 'xsl', 'css', 'html', 'xhtml', 'php', 'phtml', 'js', 'sql' ];
				var pieces = text.split( '.' );
				var extension = pieces[pieces.length-1];

				if( codeViews.indexOf( extension ) === -1 && designViews.indexOf( extension ) === -1 ) {

					AgilePHP.IDE.error( 'Unsupported file type "' + extension + '".' );
					return;
				}

				if( codeViews.indexOf( extension ) !== -1 )
					editors.push( new AgilePHP.IDE.Editor( id, 'code' ) );

				if( designViews.indexOf( extension ) !== -1 ) {
					editors.push( new AgilePHP.IDE.Editor( id, 'design' ) );
					editors.push({
							    	id: 'previewer-' + id,
							        title: 'Preview'
					});
				}

				var tabs = new Ext.TabPanel({

					id: id,
					title: text,
					closable: true,
				    activeTab: 0,
				    tabPosition: 'bottom',
				    items: editors
				});

				var tp = AgilePHP.IDE.Workspace.getTabPanel();
					tp.add( tabs );
					tp.setActiveTab( id );
		},

		Panel: function() {

			AgilePHP.IDE.FileExplorer.panel = new Ext.Panel({

					id: 'ide-fileexplorer-panel',
					region: 'west',
					title: 'File Explorer',
					collapsible: true,
					split: true,
					width: 225,
			        minSize: 175,
			        maxSize: 400,
				    margins: '3 0 3 3',
				    cmargins: '3 0 3 3',
				    autoScroll: true				
			});

			AgilePHP.IDE.FileExplorer.tree = new Ext.tree.TreePanel({

			    	useArrows: true,
			        autoScroll: true,
			        animate: true,
			        enableDD: true,
			        containerScroll: true,
			        border: false,
			        dataUrl: AgilePHP.getRequestBase() + '/PageController/getTree',
			        disableCaching: false,
			        root: {
			            nodeType: 'async',
			            text: 'Workspace',
			            draggable: false,
			            id: '.:',
			            iconCls: 'mime-folder'
			        },
			        contextMenu: new Ext.menu.Menu({
			        	id: 'file-explorer-contextmenu',
			            items: [{ id: 'file-explorer-contextmenu-new',
			            		  text: 'New',
			            		  menu: {
			            				items: [{
			                    			id: 'file-explorer-contextmenu-new-folder',
			                    			text: 'Folder',
			                    			iconCls: 'btn-new-folder'
			                    		 },
			                    		 {
			                    			id: 'file-explorer-contextmenu-new-file',
			                    			text: 'File',
			                    			iconCls: 'btn-new-file'
			                    		 }],
			                    		 
			                    		 listeners: {
		
							                itemclick: function( item ) {
		
							                    switch( item.id ) {
		
							                        case 'file-explorer-contextmenu-new-folder':
							                        	Ext.Msg.prompt( 'New Folder Name', 'Enter the folder name:', function( btn, text ) {
		
							                        		if( btn == 'ok' ) {
		
							                        			AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.parentMenu.contextNode;
							                        			var url = AgilePHP.getRequestBase() + '/PageController/createDirectory/' +
							                        						item.parentMenu.parentMenu.contextNode.id + '/' + text;
		
							                        			new AgilePHP.XHR().request( url, AgilePHP.IDE.FileExplorer.createDirectoryHandler );
							                        	    }
							                        	});
							                            break;
		
							                        case 'file-explorer-contextmenu-new-file':
							                        	Ext.Msg.prompt( 'New File Name', 'Enter the file name:', function( btn, text ) {
		
							                        		if( btn == 'ok' ) {
		
							                        			AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.parentMenu.contextNode;
							                        			var url = AgilePHP.getRequestBase() + '/PageController/createfile/' + 
							                        						item.parentMenu.parentMenu.contextNode.id + '/' + text;
		
							                        			new AgilePHP.XHR().request( url, AgilePHP.IDE.FileExplorer.createFileHandler );
							                        	    }
							                        	});
							                        	break;
							                    }
							                }
						          		 },
						          		showSeparator: true
			            		  }

					            }, '-', {
					                id: 'file-explorer-contextmenu-edit-copy',
					                text: 'Copy',
					                iconCls: 'btn-edit-copy'
					            }, {
					                id: 'file-explorer-contextmenu-edit-cut',
					                text: 'Cut',
					                iconCls: 'btn-edit-cut'
					            }, {
					                id: 'file-explorer-contextmenu-edit-delete',
					                text: 'Delete',
					                iconCls: 'btn-edit-delete'
					            }, {
					                id: 'file-explorer-contextmenu-edit-paste',
					                text: 'Paste',
					                iconCls: 'btn-edit-paste',
					                disabled: true
					            }, '-', {
					            	id: 'file-explorer-contextmenu-upload',
					                text: 'Upload',
					                iconCls: 'btn-upload'
					            }
					    ],
			            listeners: {

			        		// Handles context menu item clicks
			                itemclick: function( item ) {

			                    switch( item.id ) {

			                        case 'file-explorer-contextmenu-edit-copy':
		
			                            item.parentMenu.items.get( 'file-explorer-contextmenu-edit-paste' ).removeClass( 'btn-greyed' );
			                            item.parentMenu.items.get( 'file-explorer-contextmenu-edit-paste' ).enable();
		
			                            AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.contextNode;
			                            AgilePHP.IDE.FileExplorer.pasteMode = 'copy';
			                            break;
		
			                        case 'file-explorer-contextmenu-edit-cut':
		
			                            item.parentMenu.items.get( 'file-explorer-contextmenu-edit-paste' ).removeClass( 'btn-greyed' );
			                            item.parentMenu.items.get( 'file-explorer-contextmenu-edit-paste' ).enable();
		
			                            AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.contextNode;
			                            AgilePHP.IDE.FileExplorer.pasteMode = 'move';
			                            break;
		
			                        case 'file-explorer-contextmenu-edit-delete':
		
			                        	Ext.Msg.confirm( 'Confirmation', 'Are you sure you want to delete "' + item.parentMenu.contextNode.text + '"?', function( btn ) {
		
			                        		if( btn == 'yes' ) {
			                        			
			                        			AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.contextNode;
			                        			AgilePHP.IDE.FileExplorer._delete();
			                        		}
			                        	});
			                            break;
		
			                        case 'file-explorer-contextmenu-edit-paste':

			                        	item.parentMenu.items.get( 'file-explorer-contextmenu-edit-paste' ).addClass( 'btn-greyed' );
			                            item.parentMenu.items.get( 'file-explorer-contextmenu-edit-paste' ).disable();

			                            if( AgilePHP.IDE.FileExplorer.pasteMode == 'copy' )
			                            	AgilePHP.IDE.FileExplorer.copy( AgilePHP.IDE.FileExplorer.selectedNode, item.parentMenu.contextNode );
		
			                            else if( AgilePHP.IDE.FileExplorer.pasteMode == 'move' )
			                            	AgilePHP.IDE.FileExplorer.move( AgilePHP.IDE.FileExplorer.selectedNode, item.parentMenu.contextNode );
		
			                        	break;
		
			                        case 'file-explorer-contextmenu-upload':
		
			                        	AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.contextNode;
			                        	AgilePHP.IDE.FileExplorer.showUploadForm();
			                        	break;
		
			                        default:
			                        	return false;
			                    }
			                }
			            }
			        }),
			        listeners: {

			            contextmenu: function( node, e ) {

				        	// Remove conditional menus/items before contextmenu is shown
			                var el = Ext.getCmp( 'file-explorer-contextmenu-database' );
			                if( el ) el.destroy();
			                
			                el = Ext.getCmp( 'file-explorer-contextmenu-database-separator' );
			                if( el ) el.destroy();

			                
			                
			        		// Register the context node with the menu so that a Menu Item's handler function can access
			        		// it via its parentMenu property.
			                node.select();
			                var c = node.getOwnerTree().contextMenu;
			                c.contextNode = node;
			                c.showAt( e.getXY() );

			                var paste = c.items.get( 'file-explorer-contextmenu-edit-paste' );

			                if( node.isLeaf() ) {

			                	c.items.get( 'file-explorer-contextmenu-new' ).disabled = true;
			                	c.items.get( 'file-explorer-contextmenu-new' ).addClass( 'btn-greyed' );
		
			                	c.items.get( 'file-explorer-contextmenu-upload' ).disabled = true;
			                	c.items.get( 'file-explorer-contextmenu-upload' ).addClass( 'btn-greyed' );
		
			                	if( AgilePHP.IDE.FileExplorer.selectedNode != null && paste.disabled == false ) {
		
			                		paste.addClass( 'btn-greyed' );
			                		paste.disabled = true;
			                	}

			                	// Conditionally show database menu for persistence.xml
			                	if( node.id.match( /:persistence.xml$/i ) ) {

			                		if( !Ext.get( 'file-explorer-contextmenu-database' ) ) {

			                			Ext.getCmp( 'file-explorer-contextmenu' ).add({
	
			                				id: 'file-explorer-contextmenu-database-separator',
			                				xtype: 'menuseparator'
			                			}, {
				                			id: 'file-explorer-contextmenu-database',
				                			text: 'Database',
				                			//iconCls: 'databaseManager',
					                			menu: {
						                			items: [{
						                			
						                				id: 'file-explorer-contextmenu-database-create',
						                				text: 'Create'
						                			}, {
						                			
						                				id: 'file-explorer-contextmenu-database-drop',
						                				text: 'Drop'
						                			}, {
						                			
						                				id: 'file-explorer-contextmenu-database-reverseengineer',
						                				text: 'Reverse Engineer'
						                			}]
				                				}
				                			
				                		});
			                		}
			                	}
			                }
			                else {

			                	// Conditionally display "New -> New Model" if "model" directory was chosen
			                	if( node.text.toLowerCase() == 'model' ) {

			                		c.items.get( 'file-explorer-contextmenu-new' ).menu.add({
		                    			id: 'file-explorer-contextmenu-new-model',
		                    			text: 'Model',
		                    			iconCls: 'btn-new-model'
		                    		 });
			                	}
			                	else {

			                		if( Ext.getCmp( 'file-explorer-contextmenu-new-model' ) )
			                			Ext.getCmp( 'file-explorer-contextmenu-new-model' ).destroy();
			                	}

			                	// Conditionally display "New -> New View" if "view" directory was chosen
			                	if( node.text.toLowerCase() == 'view' ) {

			                		c.items.get( 'file-explorer-contextmenu-new' ).menu.add({
		                    			id: 'file-explorer-contextmenu-new-view',
		                    			text: 'View',
		                    			iconCls: 'btn-new-view'
		                    		 });
			                	}
			                	else {

			                		if( Ext.getCmp( 'file-explorer-contextmenu-new-view' ) )
			                			Ext.getCmp( 'file-explorer-contextmenu-new-view' ).destroy();
			                	}

			                	// Conditionally display "New -> New Controller" if "control" directory was chosen
			                	if( node.text.toLowerCase() == 'control' ) {

			                		c.items.get( 'file-explorer-contextmenu-new' ).menu.add({
		                    			id: 'file-explorer-contextmenu-new-controller',
		                    			text: 'Controller',
		                    			iconCls: 'btn-new-controller'
		                    		 });
			                	}
			                	else {
			                		
			                		if( Ext.getCmp( 'file-explorer-contextmenu-new-controller' ) )
			                			Ext.getCmp( 'file-explorer-contextmenu-new-controller' ).destroy();
			                	}

			                	// Conditionally display "New -> New Component" if "components" directory was chosen
			                	if( node.text == 'components' ) {

			                		c.items.get( 'file-explorer-contextmenu-new' ).menu.add({
		                    			id: 'file-explorer-contextmenu-new-component',
		                    			text: 'Component',
		                    			iconCls: 'btn-new-component'
		                    		 });
			                	}
			                	else {
			                		
			                		if( Ext.getCmp( 'file-explorer-contextmenu-new-component' ) )
			                			Ext.getCmp( 'file-explorer-contextmenu-new-component' ).destroy();
			                	}

			                	c.items.get( 'file-explorer-contextmenu-new' ).disabled = false;
			                	c.items.get( 'file-explorer-contextmenu-new' ).removeClass( 'btn-greyed' );
		
			                	c.items.get( 'file-explorer-contextmenu-upload' ).disabled = false;
			                	c.items.get( 'file-explorer-contextmenu-upload' ).removeClass( 'btn-greyed' );
		
			                	if( AgilePHP.IDE.FileExplorer.selectedNode != null && paste.disabled == true ) {
		
			                		paste.removeClass( 'btn-greyed' );
			                		paste.disabled = false;
			                	}
			                }
			            }
			        }
			    }),
	
			    AgilePHP.IDE.FileExplorer.tree.on( 'dblclick', function( node, e ) {
	
					if( node.attributes.iconCls != 'mime-folder' ) {
	
						var pieces = node.id.split( ':' );
				    	var id = pieces.join( '/' );
				    	var title = pieces[ pieces.length -1 ];
				    	AgilePHP.IDE.FileExplorer.newPage( node.id, node.text );
					}
				});
	
				AgilePHP.IDE.FileExplorer.tree.on( 'nodedrop', function( e ) {
		
						AgilePHP.debug( 'nodedrop event fired' );
						AgilePHP.debug( e );
		
						var url = AgilePHP.getRequestBase() + '/PageController/move/' + e.dropNode.id + '/' + e.target.id;
						var response = new AgilePHP.XHR().request( url, AgilePHP.IDE.FileExplorer.moveHandler );
				});
	
				AgilePHP.IDE.FileExplorer.panel.add( AgilePHP.IDE.FileExplorer.tree );
				AgilePHP.IDE.FileExplorer.tree.getRootNode().expand();
	
				return AgilePHP.IDE.FileExplorer.panel;
		},
		
		/**
		 * Performs AJAX request to copy the selected node.
		 * 
		 * @param fromItem The tree node id being copied from
		 * @param toItem The tree node id being copied to
		 * @return void
		 */
		copy : function( fromItem, toItem ) {

			var url = AgilePHP.getRequestBase() + '/PageController/copy/' + fromItem.id + '/' + toItem.id;
			new AgilePHP.XHR().request( url, this.copyHandler );
		},

		/**
		 * Callback handler for copy. Reloads the parent node if the copy was successful.
		 * 
		 * @param o The XHR object
		 * @return void
		 */
		copyHandler : function( response ) {

				if( response.success == true ) {

					AgilePHP.IDE.FileExplorer.tree.getNodeById( response.parent ).reload();
					return;
				}

				AgilePHP.debug( response );
				AgilePHP.IDE.error( "Error performing copy.\n" + response.errors.reason );
		},

		/**
		 * Performs AJAX request to move/rename the selected node.
		 * 
		 * @param fromItem The source tree node id
		 * @param toItem The destination tree node id
		 * @return void
		 */
		move : function( fromItem, toItem ) {

			var url = AgilePHP.getRequestBase() + '/PageController/move/' + fromItem.id + '/' + toItem.id;
			new AgilePHP.XHR().request( url, this.moveHandler );
		},

		/**
		 * Callback handler for move.
		 */
		moveHandler : function( response ) {

			 if( response.success == true ) {

				 AgilePHP.IDE.FileExplorer.tree.getNodeById( response.srcId ).destroy();
				 AgilePHP.IDE.FileExplorer.tree.getNodeById( response.newParentId ).reload();
				 return;
			 }

			 AgilePHP.debug( response );
			 AgilePHP.IDE.error( "Error moving folder.\n" + response.errors.reason );
		},

		/**
		 * Performs AJAX request to delete the selected node.
		 */
		_delete : function() {

			if( this.selectedNode ) {

				var url = AgilePHP.getRequestBase() + '/PageController/delete/' + AgilePHP.IDE.FileExplorer.selectedNode.id;
				new AgilePHP.XHR().request( url, this.deleteHandler );
			}
		},

		/**
		 * Callback handler for delete
		 */
		deleteHandler : function( response ) {

				if( response.success == true ) {

					AgilePHP.IDE.FileExplorer.selectedNode.remove();
					return;
				}

				AgilePHP.debug( response );
				AgilePHP.IDE.error( "Error deleting folder.\n" + response.errors.reason );
		},

		/**
		 * Callback handler for right click context menu 'newFolder' prompt.
		 * 
		 * @param o XHR object
		 * @return void
		 */
		createDirectoryHandler : function( response ) {

			  	if( response.success == true ) {

				    AgilePHP.IDE.FileExplorer.selectedNode.reload();
				    return;
			  	}

				AgilePHP.debug( response );
				AgilePHP.IDE.error( "Error creating new folder.\n" + response.errors.reason );
		},

		/**
		 * Callback handler for right click context menu 'newFile' prompt.
		 * 
		 * @param o XHR object
		 * @return void
		 */
		createFileHandler : function( response ) {
			
				if( response.success == true ) {

					 AgilePHP.IDE.FileExplorer.selectedNode.reload();
					 return;
				}

				AgilePHP.debug( response );
				AgilePHP.IDE.error( "Error creating new file.\n" + response.errors.reason );
		},

		/**
		 * Displays the file upload form
		 * 
		 * @return void
		 */
		showUploadForm : function() {

			Ext.QuickTips.init();

			var win;

		    var fp = new Ext.FormPanel({
		    	id: 'file-explorer-uploadform',
		        fileUpload: true,
		        width: 500,
		        frame: true,
		        autoHeight: true,
		        bodyStyle: 'padding: 10px 10px 0 10px;',
		        labelWidth: 50,
		        defaults: {
		            anchor: '95%',
		            allowBlank: false,
		            msgTarget: 'side'
		        },
		        items: [{
		            xtype: 'textfield',
		            fieldLabel: 'Name'
		        },{
		            xtype: 'fileuploadfield',
		            emptyText: 'Select a file',
		            fieldLabel: 'File',
		            name: 'upload',
		            buttonText: '',
		            buttonCfg: {
		                iconCls: 'btn-new-file'
		            }
		        }],
		        buttons: [{
		            text: 'Save',
		            handler: function() {

		        		if( fp.getForm().isValid() ) {

		        			fp.getForm().submit({

			                    url: AgilePHP.getRequestBase() + '/PageController/upload/' + AgilePHP.IDE.FileExplorer.selectedNode.id,
			                    waitMsg: 'Uploading your file...',
			                    success: function( fp, o ){

			                        win.destroy();
			                        fp.destroy();

			                        AgilePHP.IDE.FileExplorer.tree.getRootNode().reload();
			                    }
			                });
		                }
		            }
		        },{
		            text: 'Reset',
		            handler: function(){
		                fp.getForm().reset();
		            }
		        }]
		    });

		    win = new Ext.Window({

		        title: 'Upload Form',
		        closable: true,
		        width: 500,
		        height: 145,
		        plain: true,
		        modal: true,
		        layout: 'fit',
		        iconCls: 'btn-upload',
		        items: [ fp ]
		    });

			win.show( this );
		}
};