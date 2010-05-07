AgilePHP.IDE.Desktop = {

		panel: null,
		menubar: null,
		explorer: null,
		tabPanel: null,
		properties: null,
		taskbar: null,
		debugger: null,

		load: function() {

			AgilePHP.IDE.Desktop.menubar = AgilePHP.IDE.Menubar.create();
			AgilePHP.IDE.Desktop.explorer = new AgilePHP.IDE.FileExplorer.Panel();
			AgilePHP.IDE.Desktop.tabPanel = new AgilePHP.IDE.TabPanel();
			AgilePHP.IDE.Desktop.properties = new AgilePHP.IDE.Properties();
			AgilePHP.IDE.Desktop.taskbar = new AgilePHP.IDE.Taskbar();
			AgilePHP.IDE.Desktop.debugger = new AgilePHP.IDE.Debugger();

		    var viewport = new Ext.Viewport({

				 id: 'ide-viewport',
				 title: AgilePHP.IDE.appName,
				 layout: 'border',
				 items: [
				          AgilePHP.IDE.Desktop.menubar,
				          AgilePHP.IDE.Desktop.explorer,
				          AgilePHP.IDE.Desktop.tabPanel,
				          AgilePHP.IDE.Desktop.properties,
				          AgilePHP.IDE.Desktop.debugger
				 ]
			});

			AgilePHP.IDE.Desktop.panel = Ext.getCmp( 'ide-viewport' );
			//AgilePHP.IDE.Plugins.load();
		},

		add: function( o ) {

			AgilePHP.IDE.Desktop.panel.add( o );
			AgilePHP.IDE.Desktop.panel.doLayout();
		},

		clear: function() {

			AgilePHP.IDE.Desktop.panel.removeAll();
			AgilePHP.IDE.Desktop.panel.doLayout();
			AgilePHP.IDE.Desktop.panel = null;
			AgilePHP.IDE.Desktop.tabPanel = null;
		},

		destroy: function() {

			//Ext.get( 'ide-workspace' ).fadeOut({ duration: .5});
			Ext.get( 'ide-menubar' ).fadeOut({ duration: .5});
			Ext.get( 'ide-taskbar' ).fadeOut({ duration: .5});
			setTimeout( 'Ext.getCmp( "ide-workspace" ).destroy();' +
						'Ext.getCmp( "ide-menubar" ).destroy();' +
						'Ext.getCmp( "ide-taskbar" ).destroy();', 500 );
		},

		getPanel: function() {

			return AgilePHP.IDE.Desktop.panel;
		},

		getMenubar: function() {

			return AgilePHP.IDE.Desktop.menubar;
		},

		addMenubarButton: function( button ) {

			AgilePHP.IDE.Desktop.menubar.addButton( button );
			AgilePHP.IDE.Desktop.menubar.doLayout();
		},

		getTabPanel: function() {

			return AgilePHP.IDE.Desktop.tabPanel;
		},

		addTab: function( tab ) {

			AgilePHP.IDE.Desktop.tabPanel.add( tab );
			AgilePHP.IDE.Desktop.tabPanel.doLayout();
		},

		activateTab: function( tabId ) {

			AgilePHP.IDE.Desktop.tabPanel.activate( tabId );
		}
};