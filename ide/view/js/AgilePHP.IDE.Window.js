/**
 * Singleton object responsible for creating a new window instance inside
 * the EventManager workspace. This window handles creation of a taskbar
 * button as well as state relationship between the button and the window.
 * 
 * @param {mixed} id The globally unique id for the window
 * @param {string} iconCls CSS class name used to assign the icon to the window and taskbar button
 * @param {string} title The title of the window
 * @param {integer} width The window width
 * @param {integer} height The window height
 * @return AgilePHP.IDE.Window
 */
AgilePHP.IDE.Window = function( id, iconCls, title, width, height ) {

		if( Ext.WindowMgr.get( id + 'Window' ) ) {

			var win = Ext.getCmp( id + 'Window' );
		   	    win.setActive( true );
		   	    win.setVisible( true );
		   	    return win.instance;
		}

		this.id = id;
		this.title = title;
		this.width = width ? width : 500,
		this.height = height ? height : 300;
		this.icon = iconCls;

		this.window = null;
		this.trayBtn = null;

		this.createWindow = function() {

			this.window = new Ext.Window({

				id: this.id + 'Window',
				renderTo: Ext.getBody(),
				title: this.title,
		        width: this.width,
		        height: this.height,
		        closeable: true,
		        minimizable: true,
				maximizable: true,
		        plain: true,
		        iconCls: this.icon,
		        listeners: {
						minimize: function( window ) {

							var trayBtnId = window.getId().replace( 'Window', '' ) + 'TrayButton';
							var btn = Ext.getCmp( trayBtnId );
								btn.toggle();
							window.hide();
						},
						close: function( panel ) {

							var trayBtnId = panel.getId().replace( 'Window', '' ) + 'TrayButton';
							var btn = Ext.getCmp( trayBtnId );
								btn.destroy();

							var win = Ext.getCmp( panel.getId() );
							if( win ) win.destroy();
						},
						activate: function( window ) {

							var trayBtnId = window.getId().replace( 'Window', '' ) + 'TrayButton';
							var btn = Ext.getCmp( trayBtnId );
								btn.toggle( true, true );
						},
						deactivate: function( window ) {

							var trayBtnId = window.getId().replace( 'Window', '' ) + 'TrayButton';
							var btn = Ext.getCmp( trayBtnId );
								btn.toggle( false, true );
						}
				}
			});

			this.window.instance = this;
		};

		this.createTrayButton = function() {

			this.trayBtn = new Ext.Button({

					id: this.id + 'TrayButton',
				    cls: 'x-btn-text-icon',
					iconCls: this.icon,
					pressed: true,
					enableToggle: true,
					text: this.title,
					listeners: {
							toggle: function( btn, pressed ) {
									var winId = btn.getId().replace( 'TrayButton', 'Window' );
									var win = Ext.getCmp( winId );
									pressed ? win.show() : win.hide();
							}
					}
			});

			AgilePHP.IDE.Workspace.taskbar.add( this.trayBtn );
			AgilePHP.IDE.Workspace.taskbar.doLayout();
		};

		this.show = function() {

			if( !this.trayBtn )	this.createTrayButton();
			if( !this.window ) this.createWindow();

			this.window.show();
		};

		this.close = function() {

			this.window.close();
		};

		this.minimize = function() {

			this.window.minimize();
		};

		this.setHTML = function( content ) {

			this.window.body.dom.innerHTML = content;
		};

		this.add = function( o ) {

			this.window.add( o );
		};

		this.createWindow();
};

AgilePHP.IDE.Window.File = {};
AgilePHP.IDE.Window.Tools = {};
AgilePHP.IDE.Window.Help = {};

AgilePHP.loadScript( 'view/js/windows/File.Import.js' );
AgilePHP.loadScript( 'view/js/windows/File.Export.js' );

AgilePHP.loadScript( 'view/js/windows/Tools.DatabaseManager.js' );
AgilePHP.loadScript( 'view/js/windows/Tools.DatabaseManager.Compare.js' );
AgilePHP.loadScript( 'view/js/windows/Tools.Settings.js' );

AgilePHP.loadScript( 'view/js/windows/Help.About.js' );