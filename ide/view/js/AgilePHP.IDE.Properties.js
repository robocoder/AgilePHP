AgilePHP.IDE.Properties = function() {

	return new Ext.Panel({

		id: 'ide-properties',
		region: 'east',
        title: 'Toolbar',
        collapsible: true,
        split: true,
        width: 225,
        minSize: 175,
        maxSize: 400,
        margins: '3 3 3 0',
	    cmargins: '3 3 3 0',
        layout: 'vbox',
        items: [

        	new Ext.Panel({

        		id: 'ide-properties-components',
        		region: 'center',
        		title: 'Components',
        		width: '100%',
        		frame: false,
        		layout: 'fit',
        		flex: 1,
        		autoScroll: true,
        		items: [
        		        
						new Ext.tree.TreePanel({
						
							useArrows: true,
						    autoScroll: true,
						    animate: true,
						    enableDD: true,
						    containerScroll: true,
						    border: false,
						    dataUrl: AgilePHP.getRequestBase() + '/' + AgilePHP.MVC.getController() + '/getTree',
						    disableCaching: false,
						    root: {
						        nodeType: 'async',
						        text: '/',
						        draggable: false,
						        id: '.:',
						        iconCls: 'mime-folder'
						    }
						})
        		]
        	}),

        	new Ext.Panel({

        		id: 'ide-properties-properties',
        		region: 'south',
        		title: 'Properties',
        		width: '100%',
        		layout: 'fit',
        		frame: false,
        		flex: 1,
        		autoScroll: true,
        		items: new Ext.grid.PropertyGrid({
				    source: {
				        "(name)": "Properties Grid",
				        "grouping": false,
				        "autoFitColumns": true,
				        "productionQuality": false,
				        "created": new Date(Date.parse('10/15/2006')),
				        "tested": false,
				        "version": 0.01,
				        "borderWidth": 1
				    }
        		})
        	})
        ]
	});
};