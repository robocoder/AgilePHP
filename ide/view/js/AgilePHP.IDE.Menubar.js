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
AgilePHP.IDE.Menubar = {};

AgilePHP.IDE.Menubar.file = {

    	id: 'ide-menubar-file',
    	xtype: 'tbbutton',
		text: 'File',
		menu: [{

			  id: 'ide-menubar-btn-newproject',
			  text: 'New Project ...',
			  iconCls: 'fileNewProject',
			  tooltip: {text: 'Create New AgilePHP Project', title: 'New Project', autoHide: true},
			  handler: function() {

				  	new AgilePHP.IDE.Menubar.file.NewProject().show();
			  }
		}, '-', {
			  id: 'ide-menubar-file-import',
			  text: 'Import',
			  iconCls: 'fileImport',
			  tooltip: {text: 'Import data from CSV', title: 'Import', autoHide: true},
			  handler: function() {

				  if( !Ext.WindowMgr.get( 'fileImportWindow' ) ) {

					  var win = new AgilePHP.IDE.Menubar.file.Import();
				  		  win.show();
				  }
				  else {

					  Ext.WindowMgr.get( 'fileImportWindow' ).show();
				  }
			  }
			},{
			  id: 'ide-menubar-file-export',
			  text: 'Export',
			  iconCls: 'fileExport',
			  tooltip: {text: 'Export data to CSV', title: 'Export', autoHide: true},
			  handler: function() {

				  if( !Ext.WindowMgr.get( 'fileExportWindow' ) ) {

					  var win = new AgilePHP.IDE.Menubar.file.Export();
				  		  win.show();
				  }
				  else {

					  Ext.WindowMgr.get( 'fileImportWindow' ).show();
				  }
			  }					  
			}, '-', {
			  id: 'ide-menubar-file-logout',
			  text: 'Logout',
			  iconCls: 'fileLogout',
			  handler: function() {
					AgilePHP.IDE.logout();
			  },
			  tooltip: {text: 'Log out of the application', title: 'Logout', autoHide: true}
			}]
};

AgilePHP.IDE.Menubar.tools = {

		id: 'ide-menubar-tools',
		xtype: 'tbbutton',
		text: 'Tools',
		menu: [{
		   id: 'ide-menubar-tools-databasemanager',
		   text: 'Database Manager',
		   iconCls: 'databaseManager',
		   handler: function() {
	
				if( !Ext.WindowMgr.get( 'databaseManagerWindow' ) ) {
	
					var win = new AgilePHP.IDE.Menubar.tools.DatabaseManager();
						win.show();
				}
				else {
	
					Ext.WindowMgr.get( 'databaseManagerWindow' ).show();
				}
		   }
		}, '-', {
			   id: 'ide-menubar-tools-settings',
			   text: 'Settings',
			   iconCls: 'toolsSettings',
			   handler: function() {
	
					if( !Ext.WindowMgr.get( 'toolsSettingsWindow' ) ) {
	
						var win = new AgilePHP.IDE.Menubar.tools.Settings();
							win.show();
					}
					else {
	
						Ext.WindowMgr.get( 'toolsSettingsWindow' ).show();
					}
			   }
		}]
};

AgilePHP.IDE.Menubar.help = {

		id: 'ide-menubar-tools-help',
    	xtype: 'tbbutton',
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

				AgilePHP.IDE.Menubar.help.About.show();
		   },
		}]
};

AgilePHP.IDE.Menubar.create = function() {

	return new Ext.Toolbar({

			id: 'ide-menubar',
			region: 'north',
			height: 25,
			width: document.documentElement.clientWidth,
			style: 'margins: 0 0 0 0',
		    items: [ '-', AgilePHP.IDE.Menubar.file, AgilePHP.IDE.Menubar.tools, AgilePHP.IDE.Menubar.help ],
		    getFileMenu : function() {

				return AgilePHP.IDE.Menubar.file;
			},	
			getToolsMenu: function() {

				return AgilePHP.IDE.Menubar.tools;
			},
			getHelpMenu: function() {

				return AgilePHP.IDE.Menubar.help;
			}
	});
};