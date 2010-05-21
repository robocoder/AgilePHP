Ext.QuickTips.init();
AgilePHP.IDE = {

	author: 'Jeremy Hahn',
	copyright: 'Make A Byte, inc.',
	version: '0.1a',
	licence: 'GNU General Public License v3',
	package: 'com.makeabyte.agilephp.ide',
	appName: 'AgilePHP Framework IDE',

	setDebug: function( val ) {

		AgilePHP.IDE.debug = (val) ? true : false;
		AgilePHP.setDebug( AgilePHP.IDE.debug );
	},

	// not being used at the moment due to async loading causing issues with readiness in the app
	bootstrap: function() {

		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.Window.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.Menubar.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.TabPanel.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.Properties.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.Taskbar.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.Debugger.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.Desktop.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.Login.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.Plugins.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.Notification.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.FileExplorer.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.IDE.Editor.js' );
	},

	logout: function() {

		// Destroy AgilePHP session
		var xhr = new AgilePHP.XHR();
			xhr.request( AgilePHP.getRequestBase() + '/ExtLoginController/logout' );

		// Destroy the workspace and load the login form
		AgilePHP.IDE.Workspace.destroy();
		setTimeout( 'AgilePHP.IDE.Login.show()', 500 );

		// Destroy all window instances
		Ext.WindowMgr.getBy( function( window ) {
			window.destroy();
			return true;
		}, this );
	},

	error: function( message ) {

		Ext.Msg.show({
		   minWidth: 200,
		   title: 'Error',
		   msg: message,
		   buttons: Ext.Msg.OK,
		   icon: Ext.MessageBox.ERROR
		});
	},
	
	warn: function( message ) {

		Ext.Msg.show({
		   minWidth: 200,
		   title: 'Warning',
		   msg: message,
		   buttons: Ext.Msg.OK,
		   icon: Ext.MessageBox.WARNING
		});
	},

	info: function( message ) {

		Ext.Msg.show({
		   minWidth: 200,
		   title: 'Information',
		   msg: message,
		   buttons: Ext.Msg.OK,
		   icon: Ext.MessageBox.INFO
		});
	},
	
	dateRenderer : function( value, metaData, record, rowIndex, colIndex, store ) {

		var month = value.substring( 0, 2 );
		var date = value.substring( 2, 4 );
		var year = value.substring( 4, 8 );
		var hour = value.substring( 8, 10 );
		var mins = value.substring( 10, 12 );
		var secs = value.substring( 12, 14 );

		var retval = month + '/' + date + '/' + year + ' ' + hour + ':' + mins + ':' + secs;

		return retval;
	}
};

AgilePHP.IDE.Remoting = {

		classes: [],

		isLoaded: function( clazz ) {

			return AgilePHP.IDE.Remoting.classes[clazz] == true;
		},

		load: function( clazz ) {

			if( !AgilePHP.IDE.Remoting.classes[clazz] ) {

				AgilePHP.IDE.Remoting.classes[clazz] = true;
				AgilePHP.loadScript( AgilePHP.getRequestBase() + '/RemotingController/load/' + clazz );
			}
		}
};