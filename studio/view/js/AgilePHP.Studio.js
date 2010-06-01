Ext.QuickTips.init();
AgilePHP.Studio = {

	author: 'Jeremy Hahn',
	copyright: 'Make A Byte, inc.',
	version: '0.1a',
	licence: 'GNU General Public License v3',
	package: 'com.makeabyte.agilephp.studio',
	appName: 'AgilePHP Framework IDE',

	setDebug: function( val ) {

		AgilePHP.Studio.debug = (val) ? true : false;
		AgilePHP.setDebug( AgilePHP.Studio.debug );
	},

	// not being used at the moment due to async loading causing issues with readiness in the app
	bootstrap: function() {

		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.Window.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.Menubar.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.TabPanel.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.Properties.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.Taskbar.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.Debugger.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.Desktop.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.Login.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.Plugins.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.Notification.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.FileExplorer.js' );
		AgilePHP.loadScript( AgilePHP.getDocumentRoot() + 'view/js/AgilePHP.Studio.Editor.js' );
	},

	logout: function() {

		// Destroy AgilePHP session
		var xhr = new AgilePHP.XHR();
			xhr.request( AgilePHP.getRequestBase() + '/ExtLoginController/logout' );

		// Destroy the workspace and load the login form
		AgilePHP.Studio.Desktop.destroy();
		setTimeout( 'new AgilePHP.Studio.LoginWindow()', 500 );

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

AgilePHP.Studio.User = {

		username: null,
		role: null,

		setUsername: function( username ) {

				AgilePHP.Studio.User.username = username;
		},

		getUsername: function() {

				return AgilePHP.Studio.User.username;
		},

		setRole: function( role ) {

				AgilePHP.Studio.User.role = role;
		},

		getRole: function() {

				return AgilePHP.Studio.User.role;
		}
};