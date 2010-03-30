AgilePHP.IDE.TabPanel.Items = function() {

	var data = [
	  			  ['A', 100.00, 150.00, 200.00, 250.00],
	              ['B', 200.00, 250.00, 300.00, 350.00],
	              ['C', 300.00, 350.00, 400.00, 450.00],
	              ['D', 400.00, 450.00, 500.00, 550.00],
	              ['E', 500.00, 550.00, 600.00, 600.00]
	];

	var store = new Ext.data.Store({
	        proxy: new Ext.data.MemoryProxy( data ),
	        reader: new Ext.data.ArrayReader({}, [
		               {name: 'ide-tab-items-regClass'},
		               {name: 'ide-tab-items-price', type: 'float'},
		               {name: 'ide-tab-items-tier1', type: 'float'},
		               {name: 'ide-tab-items-tier2', type: 'float'},
		               {name: 'ide-tab-items-tier3', type: 'float'}
		          ])
	});
	store.load();

	return new Ext.grid.EditorGridPanel({

		id: 'ide-tab-items',
	    store: store,
	    columns: [{
	    		id: 'ide-tab-items-regClass',
	        	header: 'Registration Class',
	        	width: 150,
	        	sortable: true,
	        	editor: new Ext.form.TextField({
                    allowBlank: false
                }),
	        	dataIndex: 'ide-tab-items-regClass'
	       	}, {
	       		id: 'ide-tab-items-price',
	       		header: 'Price',
	       		width: 100,
	       		sortable: true,
	       		renderer: Ext.util.Format.usMoney,
	       		editor: new Ext.form.NumberField({
                    allowBlank: false,
                    allowNegative: false,
                    maxValue: 10000000
                }),
	       		dataIndex: 'ide-tab-items-price'
	       	}, {
	       		id: 'ide-tab-items-tier1',
	       		header: 'Tier 1',
	       		width: 100,
	       		sortable: true,
	       		renderer: Ext.util.Format.usMoney,
	       		editor: new Ext.form.NumberField({
                    allowBlank: false,
                    allowNegative: false,
                    maxValue: 10000000
                }),
	       		dataIndex: 'ide-tab-items-tier1'
	       	}, {
	       		id: 'ide-tab-items-tier2',
	       		header: 'Tier 2',
	       		width: 100,
	       		sortable: true,
	       		renderer: Ext.util.Format.usMoney,
	       		editor: new Ext.form.NumberField({
                    allowBlank: false,
                    allowNegative: false,
                    maxValue: 10000000
                }),
	       		dataIndex: 'ide-tab-items-tier2'
	       	}, {
	       		id: 'ide-tab-items-tier3',
	       		header: 'Tier 3',
	       		width: 100,
	       		sortable: true,
	       		renderer: Ext.util.Format.usMoney,
	       		editor: new Ext.form.NumberField({
                    allowBlank: false,
                    allowNegative: false,
                    maxValue: 10000000
                }),
	       		dataIndex: 'ide-tab-items-tier3'
	       	}],
	    stripeRows: true,
	    autoWidth: true,
	    autoHeight: true,
	    stateId: 'grid' ,
	    listeners: {
			rowdblclick: function( grid, rowIndex, e ) {

			},
			contextmenu : function( e ) {
	
				e.preventDefault();
			},
			rowcontextmenu: function( grid, rowIndex, e ) {

				e.preventDefault();				
	        }
		}
	});
};