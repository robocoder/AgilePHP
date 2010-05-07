AgilePHP.IDE.Debugger = function() {

	return new Ext.Panel({

			id: 'ide-debugger',
			region: 'south',
			collapsible: true,
			autoScroll: true,
			split: true,
			height: 200,
			minSize: 100,
			style: 'margins: 0 0 0 0',
		    items: [
					new Ext.TabPanel({

						id: 'ide-south-tabpanel',
					    activeTab: 0,
					    autoScroll: true,
					    enableTabScroll: true,
					    margins: '3 0 3 0',
					    cmargins: '3 0 3 0',
					    items:[{
					    	id: 'ide-south-tabpanel-log',
					    	title: 'Log',
					    	iconCls: 'tabLog',
					    	height: 123,
					    	html: 'This is the console / live log view...'
					    }, {
					    	id: 'ide-south-tabpanel-debugger',
					    	title: 'Debugger',
					    	iconCls: 'tabDebugger',
					    	html: 'This is the console / live log view...'
					    }],
					    listeners: {

							resize: function( tabPanel ) {

								//tabPanel.setHeight( document.documentElement.clientHeight - 90 )
							}
						}
					})
		    ],
		    bbar: AgilePHP.IDE.Desktop.taskbar
	});
};