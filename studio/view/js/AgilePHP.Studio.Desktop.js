AgilePHP.Studio.Desktop = {

		panel: null,
		menubar: null,
		explorer: null,
		tabPanel: null,
		properties: null,
		taskbar: null,
		debugger: null,

		load: function() {

			AgilePHP.Studio.Desktop.menubar = AgilePHP.Studio.Menubar.create();
			AgilePHP.Studio.Desktop.explorer = new AgilePHP.Studio.FileExplorer.Panel();
			AgilePHP.Studio.Desktop.tabPanel = new AgilePHP.Studio.TabPanel();
			AgilePHP.Studio.Desktop.properties = new AgilePHP.Studio.Properties();
			AgilePHP.Studio.Desktop.taskbar = new AgilePHP.Studio.Taskbar();
			AgilePHP.Studio.Desktop.debugger = new AgilePHP.Studio.Debugger();

		    var viewport = new Ext.Viewport({

				 id: 'studio-viewport',
				 title: AgilePHP.Studio.appName,
				 layout: 'border',
				 items: [
				          AgilePHP.Studio.Desktop.menubar,
				          AgilePHP.Studio.Desktop.explorer,
				          AgilePHP.Studio.Desktop.tabPanel,
				          AgilePHP.Studio.Desktop.properties,
				          AgilePHP.Studio.Desktop.debugger
				 ]
			});

			AgilePHP.Studio.Desktop.panel = Ext.getCmp( 'studio-viewport' );
			//AgilePHP.Studio.Plugins.load();
		},

		add: function( o ) {

			AgilePHP.Studio.Desktop.panel.add( o );
			AgilePHP.Studio.Desktop.panel.doLayout();
		},

		clear: function() {

			AgilePHP.Studio.Desktop.panel.removeAll();
			AgilePHP.Studio.Desktop.panel.doLayout();
			AgilePHP.Studio.Desktop.panel = null;
			AgilePHP.Studio.Desktop.tabPanel = null;
		},

		destroy: function() {

			//Ext.getCmp( 'studio-viewport' ).getEl().fadeOut({ easing: 'easeOut', duration: 1});
			//setTimeout( 'Ext.getCmp( "studio-viewport" ).destroy();', 500 );

			// Destroy all window instances
			Ext.WindowMgr.getBy( function( window ) {

				window.destroy();
				return true;
			}, this );

			Ext.getCmp( 'studio-viewport' ).destroy();
			Ext.getCmp( 'file-explorer-contextmenu' ).destroy();
		},

		getPanel: function() {

			return AgilePHP.Studio.Desktop.panel;
		},

		getMenubar: function() {

			return AgilePHP.Studio.Desktop.menubar;
		},

		addMenubarButton: function( button ) {

			AgilePHP.Studio.Desktop.menubar.addButton( button );
			AgilePHP.Studio.Desktop.menubar.doLayout();
		},

		getTabPanel: function() {

			return AgilePHP.Studio.Desktop.tabPanel;
		},

		addTab: function( tab ) {

			AgilePHP.Studio.Desktop.tabPanel.add( tab );
			AgilePHP.Studio.Desktop.tabPanel.doLayout();
		},

		activateTab: function( tabId ) {

			AgilePHP.Studio.Desktop.tabPanel.activate( tabId );
		}
};