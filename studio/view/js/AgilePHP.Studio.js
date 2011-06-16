/**
 * Base AgilePHP Studio JavaScript object
 * 
 * @static
 */
AgilePHP.Studio = {

	author : 'Jeremy Hahn',
	copyright : 'Make A Byte, inc.',
	licence : 'GNU General Public License v3',
	version: '0.1a',
	package : 'com.makeabyte.agilephp.studio',
	appName : 'AgilePHP Studio',

	/**
	 * Puts the Studio and AgilePHP framework into debug mode
	 * 
	 * @param {Boolean}
	 *            val True to enable debug mode, false otherwise. Defaults to
	 *            false.
	 * @return void
	 */
	setDebug : function(val) {
		AgilePHP.Studio.debug = (val) ? true : false;
		AgilePHP.setDebug(AgilePHP.Studio.debug);
	},

	/**
	 * Asynchronously loads required JavaScript libraries.
	 * 
	 * @return void
	 */
	bootstrap : function() {

		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.Window.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.Menubar.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.TabPanel.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.Properties.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.Taskbar.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.Debugger.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.Desktop.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.Plugins.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.Notification.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.FileExplorer.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.Editor.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'view/js/AgilePHP.Studio.PagedGridPanel.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'components/ext/ux/form/FileUploadField.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'components/ext/ux/grid/CheckColumn.js');
		AgilePHP.loadScript(AgilePHP.getDocumentRoot()
				+ 'components/ext/ux/PagingMemoryProxy.js');

		// AgilePHP.loadScript( AgilePHP.getDocumentRoot() +
		// 'view/js/RemotingProxy.js' );
	},

	/**
	 * Logs a user out of the Studio by killing the server side session using
	 * the AgilePHP Identity component, destorying all user interface
	 * components, and loads the login window.
	 * 
	 * @return void
	 */
	logout : function() {

		// Destroy AgilePHP session
		var xhr = new AgilePHP.XHR();
		xhr.request(AgilePHP.getRequestBase() + '/ExtLoginController/logout');

		// Destroy the workspace and load the login form
		AgilePHP.Studio.Desktop.destroy();
		setTimeout('new AgilePHP.Studio.LoginWindow()', 500);
	},

	/**
	 * Displays an error dialog.
	 * 
	 * @return void
	 */
	error : function(message) {

		Ext.Msg.show({
			minWidth : 200,
			title : 'Error',
			msg : message,
			buttons : Ext.Msg.OK,
			icon : Ext.MessageBox.ERROR
		});
	},

	/**
	 * Displays a warning dialog.
	 * 
	 * @return void
	 */
	warn : function(message) {

		Ext.Msg.show({
			minWidth : 200,
			title : 'Warning',
			msg : message,
			buttons : Ext.Msg.OK,
			icon : Ext.MessageBox.WARNING
		});
	},

	/**
	 * Displays an information dialog.
	 * 
	 * @return void
	 */
	info : function(message) {

		Ext.Msg.show({
			minWidth : 200,
			title : 'Information',
			msg : message,
			buttons : Ext.Msg.OK,
			icon : Ext.MessageBox.INFO
		});
	},

	/**
	 * Custom Ext date formatter
	 * 
	 * @return void
	 */
	dateRenderer : function(value, metaData, record, rowIndex, colIndex, store) {

		var month = value.substring(0, 2);
		var date = value.substring(2, 4);
		var year = value.substring(4, 8);
		var hour = value.substring(8, 10);
		var mins = value.substring(10, 12);
		var secs = value.substring(12, 14);

		var retval = month + '/' + date + '/' + year + ' ' + hour + ':' + mins
				+ ':' + secs;

		return retval;
	}
};

/**
 * Stores the current logged in user
 * 
 * @static
 */
AgilePHP.Studio.User = {

	username : null,
	role : null,

	/**
	 * Sets the username of the current logged in user
	 * 
	 * @param {String}
	 *            username The username of the current logged in user
	 * @return void
	 */
	setUsername : function(username) {
		AgilePHP.Studio.User.username = username;
	},

	/**
	 * Gets the username of the current logged in user
	 * 
	 * @return {String} The username of the current logged in user
	 */
	getUsername : function() {
		return AgilePHP.Studio.User.username;
	},

	/**
	 * Sets the role which the current logged in user is a member
	 * 
	 * @param {String}
	 *            role The role which the current logged in user is a member of
	 * @return void
	 */
	setRole : function(role) {
		AgilePHP.Studio.User.role = role;
	},

	/**
	 * Returns the name of the role which the current logged in user belongs
	 * 
	 * @return {String} The role which the current logged in user belongs
	 */
	getRole : function() {
		return AgilePHP.Studio.User.role;
	}
};