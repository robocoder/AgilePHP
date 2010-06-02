AgilePHP.Studio.PagedGridPanel = Ext.extend(Ext.grid.GridPanel, {
   
	constructor: function( cfg ) {

        if( cfg && cfg.store && cfg.bbar && cfg.bbar.xtype == 'paging' && 
        		!(cfg.bbar instanceof Ext.PagingToolbar && !this.bbar.store) ) {

            if( cfg.store.xtype && ! (cfg.store instanceof Ext.data.Store) )
                cfg.store = Ext.ComponentMgr.create(cfg.store);

            cfg.bbar.store = cfg.store;
        }   
        AgilePHP.Studio.PagedGridPanel.superclass.constructor.call( this, cfg );
    }   
});