AgilePHP.Studio.TabPanel = function() {

	//var tabPanel = new Ext.TabPanel({

	return new Ext.TabPanel({

		id: 'studio-tabpanel',
		region: 'center',
		//deferredRender: false,
		//height: document.documentElement.clientHeight - 90,
		//height: 200,
	    activeTab: 0,
	    autoScroll: true,
	    enableTabScroll: true,
	    margins: '3 0 3 0',
	    cmargins: '3 0 3 0',
	    items:[{
	    	id: 'studio-tabpanel-start',
	    	title: 'Start',
	    	iconCls: 'appIcon',
	    	closable: true,
	    	//autoScroll: true,
	    	html: 'This is where the general start page will be.'
	    }],
	    listeners: {

			resize: function( tabPanel ) {

				//tabPanel.setHeight( document.documentElement.clientHeight - 90 )
			}
		}
	});

	/*
	return new Ext.Panel({

		id: 'studio-viewport-center-panel',
		region: 'center',
		bodyCfg: {
			cls: 'extBlue'
		},
		style: 'margins: 0 0 0 0',
		items: tabPanel
	 });
	 */
};