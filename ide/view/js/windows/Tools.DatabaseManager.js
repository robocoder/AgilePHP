AgilePHP.IDE.Window.Tools.DatabaseManager = function() {

	var toolbar = new Ext.Toolbar({
		id: 'db-manager-toolbar',
		buttons: [{

			id: 'db-manager-toolbar-compare',
			text: 'Compare',
			iconCls: 'databaseCompare',
			handler: function() {

				if( !Ext.WindowMgr.get( 'databaseManagerCompareWindow' ) ) {

					var compareWin = new AgilePHP.IDE.Window.Tools.DatabaseManager.Compare();
						compareWin.show();
				}
				else {

					Ext.WindowMgr.get( 'databaseManagerCompareWindow' ).show();
				}
			}
		}]
	});

	var win = new AgilePHP.IDE.Window( 'databaseManager', 'databaseManager', 'Database Manager' );
		win.add( toolbar );

	return win;
};