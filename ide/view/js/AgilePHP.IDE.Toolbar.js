/**
 * Creates a toolbar / menubar for the workspace.
 * 
 * @return Ext.Toolbar
 */
AgilePHP.IDE.Toolbar = function() {

	return new Ext.Toolbar({

			id: 'ide-toolbar',
			region: 'north',
			height: 25,
			width: document.documentElement.clientWidth,
			style: 'margins: 0 0 0 0',
		    items: [ '-', {

				xtype: 'tbbutton',
		    	id: 'fileMenu',
				text: 'File',
				menu: [{
					  id: 'btnImport',
					  text: 'Import',
					  iconCls: 'fileImport',
					  tooltip: {text: 'Import data from CSV', title: 'Import', autoHide: true},
					  handler: function() {

						  if( !Ext.WindowMgr.get( 'fileImportWindow' ) ) {

							  var win = new AgilePHP.IDE.Window.File.Import();
						  		  win.show();
						  }
						  else {

							  Ext.WindowMgr.get( 'fileImportWindow' ).show();
						  }
					  }
					},{
					  id: 'btnExport',
					  text: 'Export',
					  iconCls: 'fileExport',
					  tooltip: {text: 'Export data to CSV', title: 'Export', autoHide: true},
					  handler: function() {

						  if( !Ext.WindowMgr.get( 'fileExportWindow' ) ) {

							  var win = new AgilePHP.IDE.Window.File.Export();
						  		  win.show();
						  }
						  else {

							  Ext.WindowMgr.get( 'fileImportWindow' ).show();
						  }
					  }					  
					}, '-', {
					  id: 'btnLogout',
					  text: 'Logout',
					  iconCls: 'fileLogout',
					  handler: function() {
							AgilePHP.IDE.logout();
					  },
					  tooltip: {text: 'Log out of the application', title: 'Logout', autoHide: true}
					}]
		    }, {
				xtype: 'tbbutton',
		    	id: 'databaseMenu',
				text: 'Database',
				menu: [{
				   text: 'Create',
				   iconCls: 'databaseCreate',
				   handler: function() {

						if( !Ext.WindowMgr.get( 'databaseCreateWindow' ) ) {

							var win = new AgilePHP.IDE.Window.Database.Create();
								win.show();
						}
						else {

							Ext.WindowMgr.get( 'databaseCreateWindow' ).show();
						}
				   }
				},{
				   text: 'Compare',
				   iconCls: 'databaseCompare',
				   handler: function() {

						if( !Ext.WindowMgr.get( 'databaseCompareWindow' ) ) {
							var win = new AgilePHP.IDE.Window.Database.Compare();
								win.show();
						}
						else {
						
							Ext.WindowMgr.get( 'databaseCompareWindow' ).show();
						}
				   }
				},{
				   text: 'Synchronize',
				   iconCls: 'databaseSynchronize',
				   handler: function() {

						if( !Ext.WindowMgr.get( 'databaseCompareWindow' ) ) {
							var win = AgilePHP.IDE.Window.Database.Synchronize();
								win.show();
						}
						else {

							Ext.WindowMgr.get( 'databaseCompareWindow' ).show();
						}
				   }
				},{
				   text: 'Delete',
				   iconCls: 'databaseDelete',
				   handler: function() {

						if( !Ext.WindowMgr.get( 'databaseDeleteWindow' ) ) {
							var win = AgilePHP.IDE.Window.Database.Delete();
								win.show();
						}
						else {

							Ext.WindowMgr.get( 'databaseDeleteWindow' ).show();
						}
				   }
			    }]
			}, {
				xtype: 'tbbutton',
		    	id: 'helpMenu',
				text: 'Help',
				menu: [{
				   id: 'btnDocumentation',
				   text: 'Documentation',
				   iconCls: 'helpDocumentation'
				}, '-', {
				   id: 'btnAbout',
				   text: 'About ' + AgilePHP.IDE.appName + ' ' + AgilePHP.IDE.version,
				   iconCls: 'helpAbout',
				   handler: function() {

						AgilePHP.IDE.Window.Help.About.show();
				   },
				}]
			}]
	});
};