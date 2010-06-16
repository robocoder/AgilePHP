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
 * @return AgilePHP.Studio.Window
 */
AgilePHP.Studio.Window = function( id, iconCls, title, width, height ) {

		if( Ext.WindowMgr.get( id ) ) {

			var win = Ext.getCmp( id );
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

				id: this.id,
				layout: 'fit',
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

							var trayBtnId = window.getId() + 'TrayButton';
							var btn = Ext.getCmp( trayBtnId );
								btn.toggle();
							window.hide();
						},
						close: function( panel ) {

							var trayBtnId = panel.getId() + 'TrayButton';
							var btn = Ext.getCmp( trayBtnId );
								btn.destroy();

							var win = Ext.getCmp( panel.getId() );
							if( win ) win.destroy();
						},
						activate: function( window ) {

							var trayBtnId = window.getId() + 'TrayButton';
							var btn = Ext.getCmp( trayBtnId );
							if( btn ) btn.toggle( true, true );
						},
						deactivate: function( window ) {

							var trayBtnId = window.getId() + 'TrayButton';
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
									var winId = btn.getId().replace( 'TrayButton', '' );
									var win = Ext.getCmp( winId );
									pressed ? win.show() : win.hide();
							}
					}
			});

			AgilePHP.Studio.Desktop.taskbar.add( this.trayBtn );
			AgilePHP.Studio.Desktop.taskbar.doLayout();
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

/**
 * Creates a "wizard" style window using passed in "step" objects.
 * 
 * @param {Array} An array of "step" objects.
 * @return AgilePHP.Studio.Window
 * @see AgilePHP.Studio.Window.File.NewProject
 */
AgilePHP.Studio.Window.prototype.wizard = function( steps ) {

	this.steps = steps;
	var labels = [];
	var id = this.id;

	// Create the left hand pane which shows a list of labels defined in the steps.
	for( var j=0; j<this.steps.length; j++ ) {

		 var label = (this.steps[j].label) ? this.steps[j].label : 'Step ' + (j+1);
		 labels.push({

			 id: this.id + '-label-' + j,
			 xtype: 'label',
			 html: '<div class="wizard-label"> ' + label + '</div>'
		 });
	}

	/**
	 * Sets the status indicator on a step label to the left of the wizard card.
	 * 
	 * @param {Integer} step The step number to set the indicator for
	 * @param {Integer} status The status used to set the indicator:
	 * 						   1 = Current step
	 * 						   2 = Mark applied
	 * 						   3 = Mark failed
	 * @return void
	 */
	this.setLabelStatus = function( step, status ) {

		if( status === 1 ) // current step (no icon just bolded)
			Ext.getCmp( this.id + '-progress' ).items.get( step ).el.dom.innerHTML = '<div class="wizard-label-selected"><img id="' + this.id + '-label-image-' + step + '" src="' + AgilePHP.getDocumentRoot() + 'view/images/go-next.png"/> ' + this.steps[step].label + '</div>';

		else if( status === 2 ) // mark applied
			Ext.getCmp( this.id + '-progress' ).items.get( step ).el.dom.innerHTML = '<div class="wizard-label-complete"><img id="' + this.id + '-label-image-' + step + '" src="' + AgilePHP.getDocumentRoot() + 'view/images/dialog-apply.png"/> ' + this.steps[step].label + '</div>';

		else if( status === 3 )// mark failed
			Ext.getCmp( this.id + '-progress' ).items.get( step ).el.dom.innerHTML = '<div class="wizard-label-complete"><img id="' + this.id + '-label-image-' + step + '" src="' + AgilePHP.getDocumentRoot() + 'view/images/dialog-cancel.png"/> ' + this.steps[step].label + '</div>';
	}

	/**
	 * Resets a step label to the left of the wizard card to default style
	 * 
	 * @param {Integer} step The step as it relates to the label
	 * @return void
	 */
	this.resetLabel = function( step ) {

		Ext.getCmp( this.id + '-progress' ).items.get( step ).el.dom.innerHTML = '<div class="wizard-label"> ' + this.steps[step].label + '</div>';
	}

	/**
	 * Provides navigation handling for the wizard.
	 * 
	 * @param {Integer} direction -1 to move backwards, 1 to move forward.
	 * @return void
	 */
	this.navigate = function( direction ) {

	    var finish = this.steps[this.steps.length-1].handler;

		var el = Ext.getCmp( this.id + '-deck' );
		if( !el ) return false; // el may not exist once "finish" handler completes

		var l = el.getLayout();
	    var i = l.activeItem.id.split( 'step-' )[1];
	    var step = parseInt( i, 10 );
	    var next = step + direction;

	    if( direction === 1 ) { // Advance to next step/card

	    	// Execute each step's handler if one is defined
	    	var handler = this.steps[step].handler;
	    	if( handler && !handler() )
	    		return false;

	    	// Advance to the previous step/card
	    	l.setActiveItem( next );   	

	    	this.setLabelStatus( step, 2 );
	    	this.setLabelStatus( next, 1 );	

	    	// Toggle finish button text and handler if last step/card.
	    	if( next == (this.steps.length-1) ) {

		    	Ext.getCmp( this.id + '-card-next' ).setText( 'Finish' );
		    	Ext.getCmp( this.id + '-card-next' ).on( 'click', finish );
		    }
	    }
	    else {

	    	// Back track to the previous step/card
	    	l.setActiveItem( next );

	    	// Step/card went back, remove finish text and handler.
	    	Ext.getCmp( this.id + '-card-next' ).setText( 'Next' );
	    	Ext.getCmp( this.id + '-card-next' ).un( 'click', finish );

	    	this.setLabelStatus( next, 1 );
	    	this.resetLabel( step );
	    }

	    // Toggle back button state
		Ext.getCmp( this.id + '-card-prev' ).setDisabled( next == 0 );
	};

	// Add the new cardlayout/wizard steps to the window
	this.add({
			layout: 'hbox',
			frame: true,
			layoutConfig: {
			    align: 'stretch',
			    pack: 'start'
			},
			defaultType: 'container',
			items: [{
				id: this.id + '-progress',
				cls: 'wizard-gradient-x',
				items: labels,
				width: 100
			}, {
				id: this.id + '-deck',
				xtype: 'panel',
				layout: 'card',
				//flex: 1,
				activeItem: 0,
			    items: steps,
			    padding: '10',
			    listeners: {
					render: function( component ) {
						Ext.getCmp( id + '-progress' ).items.get( 0 ).el.dom.innerHTML = '<div class="wizard-label-selected"><img id="' + id + '-label-image-0" src="' + AgilePHP.getDocumentRoot() + 'view/images/go-next.png"/> ' + steps[0].label + '</div>';
					}
				}
			}],
			buttons: [{
		    	id: this.id + '-card-prev',
	            text: 'Back',
	            handler: this.navigate.createDelegate( this, [-1] ),
	            disabled: true
		    }, {
		    	id: this.id + '-card-next',
	            text: 'Next',
	            handler: this.navigate.createDelegate( this, [1] )
	        }]
	});

	return this;
};