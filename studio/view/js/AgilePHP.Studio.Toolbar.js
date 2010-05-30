/**
 * Creates a toolbar / menubar for the workspace.
 * 
 * @return Ext.Toolbar
 */
AgilePHP.Studio.Toolbar = function() {

	return new Ext.Toolbar({

			id: 'studio-toolbar',
			region: 'north',
			height: 25,
			width: document.documentElement.clientWidth,
			style: 'margins: 0 0 0 0',
		    items: [ '-', {

				xtype: 'tbbutton',
		    	id: 'fileMenu',
				text: 'File',
				menu: [{

					  id: 'toolbar-btn-newproject',
					  text: 'New Project ...',
					  iconCls: 'fileNewProject',
					  tooltip: {text: 'Create New AgilePHP Project', title: 'New Project', autoHide: true},
					  handler: function() {

							  new AgilePHP.Studio.Window.File.NewProject().show();
					  }
				}, '-', {
					  id: 'btnImport',
					  text: 'Import',
					  iconCls: 'fileImport',
					  tooltip: {text: 'Import data from CSV', title: 'Import', autoHide: true},
					  handler: function() {

						  if( !Ext.WindowMgr.get( 'fileImportWindow' ) ) {

							  var win = new AgilePHP.Studio.Window.File.Import();
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

							  var win = new AgilePHP.Studio.Window.File.Export();
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
							AgilePHP.Studio.logout();
					  },
					  tooltip: {text: 'Log out of the application', title: 'Logout', autoHide: true}
					}]
		    }, {
				xtype: 'tbbutton',
		    	id: 'toolsMenu',
				text: 'Tools',
				menu: [{
				   text: 'Database Manager',
				   iconCls: 'databaseManager',
				   handler: function() {

						if( !Ext.WindowMgr.get( 'databaseManagerWindow' ) ) {

							var win = new AgilePHP.Studio.Window.Tools.DatabaseManager();
								win.show();
						}
						else {

							Ext.WindowMgr.get( 'databaseManagerWindow' ).show();
						}
				   }
				}, '-', {
					   id: 'btnToolsSettings',
					   text: 'Settings',
					   iconCls: 'toolsSettings',
					   handler: function() {

							if( !Ext.WindowMgr.get( 'toolsSettingsWindow' ) ) {

								var win = new AgilePHP.Studio.Window.Tools.Settings();
									win.show();
							}
							else {

								Ext.WindowMgr.get( 'toolsSettingsWindow' ).show();
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
				   text: 'About ' + AgilePHP.Studio.appName + ' ' + AgilePHP.Studio.version,
				   iconCls: 'helpAbout',
				   handler: function() {

						AgilePHP.Studio.Window.Help.About.show();
				   },
				}]
			}]
	});
};