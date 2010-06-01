AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/menubar/window/file/Import.js' );
AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/menubar/window/file/Export.js' );
AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/menubar/window/tools/DatabaseManager.js' );
AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/menubar/window/tools/DatabaseManager.Compare.js' );
AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/menubar/window/tools/Settings.js' );
AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/menubar/window/help/About.js' );
AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/menubar/wizard/file/NewProject.js' );

/**
 * Creates a toolbar / menubar for the workspace.
 * 
 * @return Ext.Toolbar
 */
AgilePHP.Studio.Menubar = {};

AgilePHP.Studio.Menubar.file = {

    	id: 'studio-menubar-file',
    	xtype: 'tbbutton',
		text: 'File',
		menu: [{

			  id: 'studio-menubar-btn-newproject',
			  text: 'New Project ...',
			  iconCls: 'fileNewProject',
			  tooltip: {text: 'Create New AgilePHP Project', title: 'New Project', autoHide: true},
			  handler: function() {

				  	new AgilePHP.Studio.Menubar.file.NewProject().show();
			  }
		}, '-', {
			  id: 'studio-menubar-file-import',
			  text: 'Import',
			  iconCls: 'fileImport',
			  tooltip: {text: 'Import data from CSV', title: 'Import', autoHide: true},
			  handler: function() {

				  if( !Ext.WindowMgr.get( 'fileImportWindow' ) ) {

					  var win = new AgilePHP.Studio.Menubar.file.Import();
				  		  win.show();
				  }
				  else {

					  Ext.WindowMgr.get( 'fileImportWindow' ).show();
				  }
			  }
			},{
			  id: 'studio-menubar-file-export',
			  text: 'Export',
			  iconCls: 'fileExport',
			  tooltip: {text: 'Export data to CSV', title: 'Export', autoHide: true},
			  handler: function() {

				  if( !Ext.WindowMgr.get( 'fileExportWindow' ) ) {

					  var win = new AgilePHP.Studio.Menubar.file.Export();
				  		  win.show();
				  }
				  else {

					  Ext.WindowMgr.get( 'fileImportWindow' ).show();
				  }
			  }					  
			}, '-', {
			  id: 'studio-menubar-file-logout',
			  text: 'Logout',
			  iconCls: 'fileLogout',
			  handler: function() {
					AgilePHP.Studio.logout();
			  },
			  tooltip: {text: 'Log out of the application', title: 'Logout', autoHide: true}
			}]
};

AgilePHP.Studio.Menubar.tools = {

		id: 'studio-menubar-tools',
		xtype: 'tbbutton',
		text: 'Tools',
		menu: [{
		   id: 'studio-menubar-tools-databasemanager',
		   text: 'Database Manager',
		   iconCls: 'databaseManager',
		   handler: function() {
	
				if( !Ext.WindowMgr.get( 'databaseManagerWindow' ) ) {
	
					var win = new AgilePHP.Studio.Menubar.tools.DatabaseManager();
						win.show();
				}
				else {
	
					Ext.WindowMgr.get( 'databaseManagerWindow' ).show();
				}
		   }
		}, '-', {
			   id: 'studio-menubar-tools-settings',
			   text: 'Settings',
			   iconCls: 'toolsSettings',
			   disabled: true,
			   handler: function() {
	
					if( !Ext.WindowMgr.get( 'toolsSettingsWindow' ) ) {
	
						var win = new AgilePHP.Studio.Menubar.tools.Settings();
							win.show();
					}
					else {
	
						Ext.WindowMgr.get( 'toolsSettingsWindow' ).show();
					}
			   }
		}]
};

AgilePHP.Studio.Menubar.help = {

		id: 'studio-menubar-tools-help',
    	xtype: 'tbbutton',
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

				AgilePHP.Studio.Menubar.help.About.show();
		   },
		}]
};

AgilePHP.Studio.Menubar.create = function() {

	var toolbar = new Ext.Toolbar({

			id: 'studio-menubar',
			region: 'north',
			height: 25,
			width: document.documentElement.clientWidth,
			style: 'margins: 0 0 0 0',
		    items: [ '-', AgilePHP.Studio.Menubar.file, AgilePHP.Studio.Menubar.tools, AgilePHP.Studio.Menubar.help ],
		    getFileMenu : function() {

				return AgilePHP.Studio.Menubar.file;
			},	
			getToolsMenu: function() {

				return AgilePHP.Studio.Menubar.tools;
			},
			getHelpMenu: function() {

				return AgilePHP.Studio.Menubar.help;
			}
	});

	if( AgilePHP.Studio.User.getRole() == 'admin' ) {

		Ext.getCmp( 'studio-menubar-tools-settings' ).setDisabled( false );
	}

	return toolbar;
};