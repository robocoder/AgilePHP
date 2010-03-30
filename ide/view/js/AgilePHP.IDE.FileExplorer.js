AgilePHP.IDE.FileExplorer = {
	
		panel: null,
		pasteMode: null,
		selectedNode: null,
		tree: null,
		window: null,

		/**
		 * Creates a new tab/page in the content editing area.
		 * 
		 * @param title The title displayed on the tab
		 * @param id The id of the new tab element
		 * @param name The name of the new tab element
		 * @return void
		 */
		newPage: function( title, id, name ) {

				/*
				var iframe = {
						id: id,
						name: name,
						title: title,
						closable: true,
						width: "100%",
						height: "100%",
						xtype: "iframepanel",
						animCollapse: true,
						defaultSrc: AgilePHP.getRequestBase() + '/' + AgilePHP.MVC.getController() + '/edit/' + id
				};
				*/

				var editor = new Ext.form.HtmlEditor({
						id: id,
						name: name,
						title: title,
						closable: true,
						//width: 500, // AgilePHP.IDE.Workspace.getTabPanel().getInnerWidth() - 200
						listeners: {
					
							render: function( component ) {
					
								component.add({

								  id: 'btnTestButton',
								  text: 'test'
								});
								component.doLayout();
							}
						}
				});

				var tp = AgilePHP.IDE.Workspace.getTabPanel();
					tp.add( editor );
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
			            text: '/',
			            draggable: false,
			            id: '.:',
			            iconCls: 'mime-folder'
			        },
			        contextMenu: new Ext.menu.Menu({
			            items: [{ id: 'new',
			            		  text: 'New',
			            		  menu: {
			            				items: [{
			                    			id: 'new-folder',
			                    			text: 'New Folder',
			                    			iconCls: 'btn-folder-new'
			                    		 },
			                    		 {
			                    			id: 'new-file',
			                    			text: 'New File',
			                    			iconCls: 'btn-document-new'
			                    		 }],
			                    		 
			                    		 listeners: {
		
							                itemclick: function( item ) {
		
							                    switch( item.id ) {
		
							                        case 'new-folder':
							                        	Ext.Msg.prompt( 'New Folder Name', 'Enter the folder name:', function( btn, text ) {
		
							                        		if( btn == 'ok' ) {
		
							                        			AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.parentMenu.contextNode;
							                        			var url = AgilePHP.getRequestBase() + '/' + AgilePHP.MVC.getController() + 
							                        						'/createDirectory/' + item.parentMenu.parentMenu.contextNode.id + '/' + text;
		
							                        			new AgilePHP.XHR().request( url, AgilePHP.IDE.FileExplorer.createDirectoryHandler );
							                        	    }
							                        	});
							                            break;
		
							                        case 'new-file':
							                        	Ext.Msg.prompt( 'New File Name', 'Enter the file name:', function( btn, text ) {
		
							                        		if( btn == 'ok' ) {
		
							                        			AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.parentMenu.contextNode;
							                        			var url = AgilePHP.getRequestBase() + '/' + AgilePHP.MVC.getController() + 
							                        						'/createfile/' + item.parentMenu.parentMenu.contextNode.id + '/' + text;
		
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
					                id: 'edit-copy',
					                text: 'Copy',
					                iconCls: 'btn-edit-copy'
					            }, {
					                id: 'edit-cut',
					                text: 'Cut',
					                iconCls: 'btn-edit-cut'
					            }, {
					                id: 'edit-delete',
					                text: 'Delete',
					                iconCls: 'btn-edit-delete'
					            }, {
					                id: 'edit-paste',
					                text: 'Paste',
					                iconCls: 'btn-edit-paste',
					                disabled: true
					            }, '-', {
					            	id: 'upload',
					                text: 'Upload',
					                iconCls: 'btn-upload'
					            }
					    ],
			            listeners: {
			                itemclick: function(item) {
		
			                    switch (item.id) {
		
			                        case 'edit-copy':
		
			                            item.parentMenu.items.get( 'edit-paste' ).removeClass( 'btn-greyed' );
			                            item.parentMenu.items.get( 'edit-paste' ).enable();
		
			                            AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.contextNode;
			                            AgilePHP.IDE.FileExplorer.pasteMode = 'copy';
			                            break;
		
			                        case 'edit-cut':
		
			                            item.parentMenu.items.get( 'edit-paste' ).removeClass( 'btn-greyed' );
			                            item.parentMenu.items.get( 'edit-paste' ).enable();
		
			                            AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.contextNode;
			                            AgilePHP.IDE.FileExplorer.pasteMode = 'move';
			                            break;
		
			                        case 'edit-delete':
		
			                        	Ext.Msg.confirm( 'Confirmation', 'Are you sure you want to delete "' + item.parentMenu.contextNode.text + '"?', function( btn ) {
		
			                        		if( btn == 'yes' ) {
			                        			
			                        			AgilePHP.IDE.FileExplorer.selectedNode = item.parentMenu.contextNode;
			                        			AgilePHP.IDE.FileExplorer._delete();
			                        		}
			                        	});
			                            break;
		
			                        case 'edit-paste':
		
			                        	item.parentMenu.items.get( 'edit-paste' ).addClass( 'btn-greyed' );
			                            item.parentMenu.items.get( 'edit-paste' ).disable();
		
			                            if( AgilePHP.IDE.FileExplorer.pasteMode == 'copy' )
			                            	AgilePHP.IDE.FileExplorer.copy( AgilePHP.IDE.FileExplorer.selectedNode, item.parentMenu.contextNode );
		
			                            else if( this.pasteMode == 'move' )
			                            	AgilePHP.IDE.FileExplorer.move( AgilePHP.IDE.FileExplorer.selectedNode, item.parentMenu.contextNode );
		
			                        	break;
		
			                        case 'upload':
		
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
		
			        		// Register the context node with the menu so that a Menu Item's handler function can access
			        		// it via its parentMenu property.
			                node.select();
			                var c = node.getOwnerTree().contextMenu;
			                c.contextNode = node;
			                c.showAt( e.getXY() );
		
			                var paste = c.items.get( 'edit-paste' );
		
			                if( node.isLeaf() ) {
		
			                	c.items.get( 'new' ).disabled = true;
			                	c.items.get( 'new' ).addClass( 'btn-greyed' );
		
			                	c.items.get( 'upload' ).disabled = true;
			                	c.items.get( 'upload' ).addClass( 'btn-greyed' );
		
			                	if( AgilePHP.IDE.FileExplorer.selectedNode != null && paste.disabled == false ) {
		
			                		paste.addClass( 'btn-greyed' );
			                		paste.disabled = true;
			                	}
			                }
			                else {
		
			                	c.items.get( 'new' ).disabled = false;
			                	c.items.get( 'new' ).removeClass( 'btn-greyed' );
		
			                	c.items.get( 'upload' ).disabled = false;
			                	c.items.get( 'upload' ).removeClass( 'btn-greyed' );
		
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
				    	AgilePHP.IDE.FileExplorer.newPage( node.text, node.id, node.text );
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
		copyHandler : function( o ) {

			if( o !== undefined || o !== null ) {

				try {
						if( o.result == true )
							AgilePHP.IDE.FileExplorer.tree.getNodeById( response.parent ).reload();
				}
				catch( e ) {

					AgilePHP.debug( e );
					AgilePHP.IDE.error( 'Error performing copy: ' + o.responseText );
				}
			}
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
		moveHandler : function( o ) {

			if( o !== undefined || o !== null ) {

				try {
					  if( o.result == true )
							AgilePHP.IDE.FileExplorer.tree.getNodeById( response.parent ).reload();
						else
							AgilePHP.IDE.FileExplorer.error( 'Unexpected error: ' + o );
				}
				catch( e ) {

					AgilePHP.debug( e );
					this.showError( 'Error moving folder: ' + o );
				}
			}
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
		deleteHandler : function( o ) {

			if( o !== undefined || o !== null ) {

				try {
					  if( o.result == true )
						  AgilePHP.IDE.FileExplorer.selectedNode.remove();
					  else
						  this.showError( 'Unexpected error: ' + o );
				}
				catch( e ) {

					AgilePHP.debug( e );
					AgilePHP.IDE.FileExplorer.showError( 'Error deleting folder.', o );
				}
			}
		},

		/**
		 * Callback handler for right click context menu 'newFolder' prompt.
		 * 
		 * @param o XHR object
		 * @return void
		 */
		createDirectoryHandler : function( o ) {

			if( o != undefined || o != null ) {

				try {
					  var response = eval( '(' + o.responseText + ')' );
					  if( response.result == true )
						  AgilePHP.IDE.FileExplorer.selectedNode.reload();
					  else
						  AgilePHP.IDE.FileExplorer.showError( 'Unexpected error', o );
				}
				catch( e ) {

					AgilePHP.debug( e );
					AgilePHP.IDE.FileExplorer.showError( 'Error creating new folder.', o );
				}
			}
		},

		/**
		 * Callback handler for right click context menu 'newFile' prompt.
		 * 
		 * @param o XHR object
		 * @return void
		 */
		createFileHandler : function( o ) {
			
			if( o != undefined || o != null ) {

				try {
					  var response = eval( '(' + o.responseText + ')' );
					  if( response.result == true )
						  AgilePHP.IDE.FileExplorer.selectedNode.reload();
					  else
						  this.showError( 'Unexpected error', o );
				}
				catch( e ) {

					AgilePHP.debug( e );
					AgilePHP.IDE.FileExplorer.showError( 'Error creating new file.', o );
				}
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
		            id: 'form-file',
		            emptyText: 'Select a file',
		            fieldLabel: 'File',
		            name: 'upload',
		            buttonText: '',
		            buttonCfg: {
		                iconCls: 'btn-upload'
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

		    /*
		    win = new Ext.Window({

		        title: 'Agile<em>PHP</em> :: Upload Form',
		        closable: true,
		        width: 500,
		        height: 145,
		        plain: true,
		        modal: true,
		        layout: 'fit',

		        items: [ fp ]
		    });

			win.show( this );
			*/
		}
};