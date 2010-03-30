AgilePHP.IDE.Workspace = {

		panel: null,
		toolbar: null,
		explorer: null,
		tabPanel: null,
		properties: null,
		taskbar: null,
		debugger: null,

		load: function() {

			AgilePHP.IDE.Workspace.toolbar = new AgilePHP.IDE.Toolbar();
			AgilePHP.IDE.Workspace.explorer = new AgilePHP.IDE.FileExplorer.Panel();
			AgilePHP.IDE.Workspace.tabPanel = new AgilePHP.IDE.TabPanel();
			AgilePHP.IDE.Workspace.properties = new AgilePHP.IDE.Properties();
			AgilePHP.IDE.Workspace.taskbar = new AgilePHP.IDE.Taskbar();
			AgilePHP.IDE.Workspace.debugger = new AgilePHP.IDE.Debugger();

		    var viewport = new Ext.Viewport({

				 id: 'ide-viewport',
				 title: AgilePHP.IDE.appName,
				 layout: 'border',
				 items: [
				          AgilePHP.IDE.Workspace.toolbar,
				          AgilePHP.IDE.Workspace.explorer,
				          AgilePHP.IDE.Workspace.tabPanel,
				          AgilePHP.IDE.Workspace.properties,
				          AgilePHP.IDE.Workspace.debugger
				 ]
			});

			AgilePHP.IDE.Workspace.panel = Ext.getCmp( 'ide-viewport' );
			//AgilePHP.IDE.Plugins.load();
		},

		add: function( o ) {

			AgilePHP.IDE.Workspace.panel.add( o );
			AgilePHP.IDE.Workspace.panel.doLayout();
		},

		clear: function() {

			AgilePHP.IDE.Workspace.panel.removeAll();
			AgilePHP.IDE.Workspace.panel.doLayout();
			AgilePHP.IDE.Workspace.panel = null;
			AgilePHP.IDE.Workspace.tabPanel = null;
		},

		destroy: function() {

			//Ext.get( 'ide-workspace' ).fadeOut({ duration: .5});
			Ext.get( 'ide-toolbar' ).fadeOut({ duration: .5});
			Ext.get( 'ide-taskbar' ).fadeOut({ duration: .5});
			setTimeout( 'Ext.getCmp( "ide-workspace" ).destroy();' +
						'Ext.getCmp( "ide-toolbar" ).destroy();' +
						'Ext.getCmp( "ide-taskbar" ).destroy();', 500 );
		},

		getPanel: function() {

			return AgilePHP.IDE.Workspace.panel;
		},

		getToolbar: function() {

			return AgilePHP.IDE.Workspace.toolbar;
		},

		addToolbarButton: function( button ) {

			AgilePHP.IDE.Workspace.toolbar.addButton( button );
			AgilePHP.IDE.Workspace.toolbar.doLayout();
		},

		getTabPanel: function() {

			return AgilePHP.IDE.Workspace.tabPanel;
		},
		
		addTab: function( tab ) {

			AgilePHP.IDE.Workspace.tabPanel.add( tab );
			AgilePHP.IDE.Workspace.tabPanel.doLayout();
		},

		activateTab: function( tabId ) {

			AgilePHP.IDE.Workspace.tabPanel.activate( tabId );
		}
};