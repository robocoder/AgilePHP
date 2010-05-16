AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/fileexplorer/window/NewModel.js' );
AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/fileexplorer/window/NewView.js' );
AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/fileexplorer/window/NewController.js' );

AgilePHP.IDE.Remoting.load( 'NewModelRemote' );

AgilePHP.IDE.FileExplorer = {

		projectName: null,
		workspace: null,
		panel: null,
		pasteMode: null,
		selectedNode: null,
		tree: null,
		window: null,
		highlightedNode: null,

		getProjectName: function() {

			return AgilePHP.IDE.FileExplorer.projectName;
		},

		getTree: function() {

			return AgilePHP.IDE.FileExplorer.tree;
		},

		setProjectNameFromNode: function( nodeId ) {

            var pieces = nodeId.split( /\|/ );
                  pieces.pop(); // model directory

            AgilePHP.IDE.FileExplorer.projectName = pieces.pop();
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
				var codeViews = [ 'xml', 'xsl', 'css', 'html', 'xhtml', 'php', 'phtml', 'js', 'sql', 'htaccess' ];
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

				var tp = AgilePHP.IDE.Desktop.getTabPanel();
					tp.add( tabs );
					tp.setActiveTab( id );
		},

		Panel: function() {

			var configs = new ConfigsRemote();
				configs.setCallback( function( response ) {

					AgilePHP.IDE.FileExplorer.workspace = response.value;
				});
				configs.get( 'workspace' );

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
			        dataUrl: AgilePHP.getRequestBase() + '/FileExplorerController/getTree',
			        disableCaching: false,
			        root: {
			            nodeType: 'async',
			            text: 'Workspace',
			            draggable: false,
			            id: '.',
			            iconCls: 'mime-folder'
			        },
			        rootVisible: false,
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
							                        			var url = AgilePHP.getRequestBase() + '/FileExplorerController/createDirectory/' +
							                        						item.parentMenu.parentMenu.contextNode.id + '/' + text;
		
							                        			new AgilePHP.XHR().request( url, function( response ) {

							                        			  	if( response.success == true ) {

							                        				    AgilePHP.IDE.FileExplorer.selectedNode.reload();
							                        				    return;
							                        			  	}

							                        				AgilePHP.debug( response );
							                        				AgilePHP.IDE.error( "Error creating new folder.\n" + response.errors.reason );
							                        			});
							                        	    }
							                        	});
							                            break;
		
							                        case 'file-explorer-contextmenu-new-file':
							                        	Ext.Msg.prompt( 'New File Name', 'Enter the file name:', function( btn, text ) {
		
							                        		if( btn == 'ok' ) {
		
							                        			AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.parentMenu.contextNode;
							                        			var url = AgilePHP.getRequestBase() + '/FileExplorerController/createfile/' + 
							                        						item.parentMenu.parentMenu.contextNode.id + '/' + text;
		
							                        			new AgilePHP.XHR().request( url, function( response ) {
							                        				
							                        				if( response.success == true ) {

							                        					 AgilePHP.IDE.FileExplorer.selectedNode.reload();
							                        					 return;
							                        				}

							                        				AgilePHP.debug( response );
							                        				AgilePHP.IDE.error( "Error creating new file.\n" + response.errors.reason );
							                        			});
							                        	    }
							                        	});
							                        	break;
							                        	
							                        case 'file-explorer-contextmenu-new-model':
							                        	new AgilePHP.IDE.FileExplorer.NewModel().show();
							                        break;

							                        case 'file-explorer-contextmenu-new-view':
							                        	new AgilePHP.IDE.FileExplorer.NewView().show();
							                        break;

							                        case 'file-explorer-contextmenu-new-controller':
							                        	new AgilePHP.IDE.FileExplorer.NewController().show();
							                        break;
							                    }
							                }
						          		 },
						          		showSeparator: true
			            		  }

					            }, '-', {
					                id: 'file-explorer-contextmenu-edit-refresh',
					                text: 'Refresh',
					                iconCls: 'btn-edit-refresh'
					            }, {
					                id: 'file-explorer-contextmenu-edit-rename',
					                text: 'Rename',
					                iconCls: 'btn-edit-rename'
					            }, {
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

				                    case 'file-explorer-contextmenu-edit-refresh':
				                		
			                            AgilePHP.IDE.FileExplorer.highlightedNode.reload();
		                            break;

				                    case 'file-explorer-contextmenu-edit-rename':

				                    	Ext.Msg.prompt( 'Rename', 'Enter the new name', function( btn, text ) {

				                    		if( btn == 'ok' ) {

				                    			var src = AgilePHP.IDE.FileExplorer.highlightedNode.id;

				                    			var url = AgilePHP.getRequestBase() + '/FileExplorerController/rename/' + src + '/' + text;
				                    			new AgilePHP.XHR().request( url, function( response ) {

				                    				 if( response.success == true ) {

				                    					 AgilePHP.IDE.FileExplorer.tree.getNodeById( response.parentId ).reload();
				                    					 return;
				                    				 }

				                    				 AgilePHP.IDE.error( "Error performing rename operation" );
				                    			});
				                    	    }
				                    	});
			                            break;

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
			                        			AgilePHP.IDE.FileExplorer.delete();
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

			        		AgilePHP.IDE.FileExplorer.setProjectNameFromNode( node.id );
			        		AgilePHP.IDE.FileExplorer.highlightedNode = node;

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
			                	if( node.id.match( /\|persistence.xml$/i ) ) {

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
						                				text: 'Create',
						                				iconCls: 'btn-new-database',
						                				handler: function() {

						                					var dbManagerRemote = new DatabaseManagerRemote();
					                							dbManagerRemote.setCallback( function( response ) {
					                								
					                								if( response._class == 'AgilePHP_RemotingException' ) {

					                									AgilePHP.IDE.error( response.message )
					                									return;
					                								}

					                								AgilePHP.IDE.info( 'Database successfully created' );
					                							});
					                							dbManagerRemote.create( AgilePHP.IDE.FileExplorer.workspace, AgilePHP.IDE.FileExplorer.projectName );
						                				}
						                			}, {
						                			
						                				id: 'file-explorer-contextmenu-database-drop',
						                				text: 'Drop',
						                				iconCls: 'btn-trash',
						                				handler: function() {

							                				var dbManagerRemote = new DatabaseManagerRemote();
				                								dbManagerRemote.setCallback( function( response ) {
				                								
				                								if( response._class == 'AgilePHP_RemotingException' ) {
	
				                									AgilePHP.IDE.error( response.message )
				                									return;
				                								}
	
				                								AgilePHP.IDE.info( 'Database successfully dropped' );
				                							});
				                							dbManagerRemote.drop( AgilePHP.IDE.FileExplorer.workspace, AgilePHP.IDE.FileExplorer.projectName );
						                				}
						                			}, {
						                			
						                				id: 'file-explorer-contextmenu-database-reverseengineer',
						                				text: 'Reverse Engineer',
						                				iconCls: 'btn-reverse-engineer-database',
						                				handler: function() {
						                				
							                				var dbManagerRemote = new DatabaseManagerRemote();
				                								dbManagerRemote.setCallback( function( response ) {
				                								
				                								if( response._class == 'AgilePHP_RemotingException' ) {
		
				                									AgilePHP.IDE.error( response.message )
				                									return;
				                								}

				                								AgilePHP.IDE.info( 'persistence.xml successfully configured' );
				                							});
				                							dbManagerRemote.reverseEngineer( AgilePHP.IDE.FileExplorer.workspace, AgilePHP.IDE.FileExplorer.projectName );
						                				}
						                			}]
				                				}
				                			
				                		});
			                		}
			                	}
			                }
			                else {

			                	if( Ext.get( 'file-explorer-contextmenu-new-model' ) )
		                			Ext.getCmp( 'file-explorer-contextmenu-new-model' ).destroy();

			                	if( Ext.get( 'file-explorer-contextmenu-new-view' ) )
		                			Ext.getCmp( 'file-explorer-contextmenu-new-view' ).destroy();

			                	if( Ext.get( 'file-explorer-contextmenu-new-controller' ) )
		                			Ext.getCmp( 'file-explorer-contextmenu-new-controller' ).destroy();

			                	if( Ext.get( 'file-explorer-contextmenu-new-component' ) )
		                			Ext.getCmp( 'file-explorer-contextmenu-new-component' ).destroy();

			                	if( node.text.toLowerCase() == 'model' ) {

			                		c.items.get( 'file-explorer-contextmenu-new' ).menu.add({
		                    			id: 'file-explorer-contextmenu-new-model',
		                    			text: 'Model',
		                    			iconCls: 'btn-new-model'
		                    		 });
			                	}

			                	if( node.text.toLowerCase() == 'view' ) {

			                		c.items.get( 'file-explorer-contextmenu-new' ).menu.add({
		                    			id: 'file-explorer-contextmenu-new-view',
		                    			text: 'View',
		                    			iconCls: 'btn-new-view'
		                    		 });
			                	}

			                	if( node.text.toLowerCase() == 'control' ) {

			                		c.items.get( 'file-explorer-contextmenu-new' ).menu.add({
		                    			id: 'file-explorer-contextmenu-new-controller',
		                    			text: 'Controller',
		                    			iconCls: 'btn-new-controller'
		                    		 });
			                	}

			                	if( node.text == 'components' ) {

			                		c.items.get( 'file-explorer-contextmenu-new' ).menu.add({
		                    			id: 'file-explorer-contextmenu-new-component',
		                    			text: 'Component',
		                    			iconCls: 'btn-new-component'
		                    		 });
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
	
					if( node.isLeaf() && node.attributes.iconCls != 'mime-folder' ) {
	
						var pieces = node.id.split( '|' );
				    	var id = pieces.join( '/' );
				    	var title = pieces[ pieces.length -1 ];
				    	AgilePHP.IDE.FileExplorer.newPage( node.id, node.text );
					}
				});
	
				AgilePHP.IDE.FileExplorer.tree.on( 'nodedrop', function( e ) {
		
						AgilePHP.debug( 'nodedrop event fired' );
						AgilePHP.debug( e );
		
						var url = AgilePHP.getRequestBase() + '/FileExplorerController/move/' + e.dropNode.id + '/' + e.target.id;
						var response = new AgilePHP.XHR().request( url, AgilePHP.IDE.FileExplorer.moveHandler );
				});

				AgilePHP.IDE.FileExplorer.tree.on( 'click', function( node, e ) {

						 
				});
	
				AgilePHP.IDE.FileExplorer.panel.add( AgilePHP.IDE.FileExplorer.tree );
				//AgilePHP.IDE.FileExplorer.tree.getRootNode().expand();
	
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

			var url = AgilePHP.getRequestBase() + '/FileExplorerController/copy/' + fromItem.id + '/' + toItem.id;
			new AgilePHP.XHR().request( url, function( response ) {

				if( response.success == true ) {

					AgilePHP.IDE.FileExplorer.tree.getNodeById( response.parent ).reload();
					return;
				}

				AgilePHP.debug( response );
				AgilePHP.IDE.error( "Error performing copy.\n" + response.errors.reason );
			});
		},

		/**
		 * Performs AJAX request to move/rename the selected node.
		 * 
		 * @param fromItem The source tree node id
		 * @param toItem The destination tree node id
		 * @return void
		 */
		move : function( fromItem, toItem ) {

			var url = AgilePHP.getRequestBase() + '/FileExplorerController/move/' + fromItem.id + '/' + toItem.id;
			new AgilePHP.XHR().request( url, function( response ) {

				 if( response.success == true ) {

					 AgilePHP.IDE.FileExplorer.tree.getNodeById( response.srcId ).destroy();
					 AgilePHP.IDE.FileExplorer.tree.getNodeById( response.newParentId ).reload();
					 return;
				 }

				 AgilePHP.debug( response );
				 AgilePHP.IDE.error( "Error moving folder.\n" + response.errors.reason );
			});
		},

		/**
		 * Performs AJAX request to delete the selected node.
		 */
		delete : function() {

			if( this.selectedNode ) {

				var url = AgilePHP.getRequestBase() + '/FileExplorerController/delete/' + AgilePHP.IDE.FileExplorer.selectedNode.id;
				new AgilePHP.XHR().request( url, function( response ) {

					if( response.success == true ) {

						AgilePHP.IDE.FileExplorer.selectedNode.remove();
						return;
					}

					AgilePHP.debug( response );
					AgilePHP.IDE.error( "Error deleting folder.\n" + response.errors.reason );
				});
			}
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
		            msgTarget: 'side'
		        },
		        items: [{
		            xtype: 'textfield',
		            fieldLabel: 'Name',
		            name: 'name'
		        },{
		            xtype: 'fileuploadfield',
		            emptyText: 'Select a file',
		            fieldLabel: 'File',
		            name: 'upload',
		            allowBlank: false,
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

			                    url: AgilePHP.getRequestBase() + '/FileExplorerController/upload/' + AgilePHP.IDE.FileExplorer.selectedNode.id,
			                    waitMsg: 'Uploading your file...',
			                    success: function( fp, o ) {

			                        win.destroy();
			                        fp.destroy();

			                        AgilePHP.IDE.FileExplorer.highlightedNode.reload();
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