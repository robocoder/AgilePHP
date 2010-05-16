AgilePHP.IDE.Menubar.file.NewProject = function() {

	var id = 'file-newproject';

	if( Ext.WindowMgr.get( id ) ) {

		var win = Ext.getCmp( id );
	   	    win.setActive( true );
	   	    win.setVisible( true );
	   	    return win.instance;
	}
	var window = new AgilePHP.IDE.Window( id, 'fileNewProject', 'New Project Wizard', 550, 350 );

	AgilePHP.IDE.Remoting.load( 'ProjectRemote' );
	AgilePHP.IDE.Remoting.load( 'DatabaseManagerRemote' );

	var pbar = new Ext.ProgressBar({
        id: id + 'progressbar',
        width: 300,
        text: 'Creating project'
    });

	/**
	 * Generates HTML for wizard summary step.
	 * 
	 * @return {String} The HTML summary code
	 */
	var getSummary = function() {

		var html = '<div class="wizard-summary" style="height: 210px;">';

			// loop over each step to extract label and value
			for( var i=0; i<window.steps.length; i++ ) {

				 var category = window.steps[i].label;

				 // loop over step's fieldset to get form field label and value
				 if( window.steps[i].items && window.steps[i].items.length >= 2 ) {

					 html += '<div class="wizard-summary-category">' + category + '</div>';
					 html += '<div class="wizard-summary-block">';

					 var fields = window.steps[i].items[1].items;
					 for( var j=0; j<fields.length; j++ ) {

						 	  var el = Ext.getCmp( fields[j].id );
						 	  var label = fields[j].fieldLabel;
						 	  if( !label ) continue;

						 	  // Display results user friendly
						 	  var value;
						 	  if( el.getValue() === true )
						 		  value = 'YES';
						 	  else if( el.getValue() === false )
						 		  value = 'NO';
						 	  else
						 		 value = (el.getValue()) ? el.getValue() : '(empty)';

							  html += '<div class="wizard-summary-label">' + label + '</div>';
							  html += '<div class="wizard-summary-item">' + value + '</div>';
					 }
					 html += '</div>';
				 }
			}
		html += '</div>';

		return html;
	};

	/**
	 * Submits all of the form field elements to the server and updates the last step/card
	 * text accordingly.
	 * 
	 * @return void
	 */
	var submit = function() {

		var params = [];

		// loop over each step to extract label and value
		for( var i=0; i<window.steps.length; i++ ) {

			 // loop over step's fieldset to get form field label and value
			 if( window.steps[i].items && window.steps[i].items.length >= 2 ) {

				 var fields = window.steps[i].items[1].items;
				 for( var j=0; j<fields.length; j++ ) {

					  if( !fields[j].fieldLabel ) continue;
				 	  var el = Ext.getCmp( fields[j].id );

				 	  params.push({label: fields[j].fieldLabel, name: fields[j].name, value: el.getValue()});
				 }
			 }
		}

		// Handler is executed first, then wizard.navigate overwrites :(
		// Ext.getCmp( id + '-card-prev' ).setDisabled( true );

		Ext.getCmp( id + '-card-next' ).setDisabled( true );

		pbar.show();
        pbar.wait({
            interval: 200,
            duration: 30000,
            increment: 15,
            fn: function() {
        		pbar.reset();
                AgilePHP.IDE.error( 'Operation timed out' );
            }
        });
	    Ext.getCmp( 'step-8' ).add( pbar );

	    var projectRemote = new ProjectRemote();
	    	projectRemote.setCallback( function( response ) {

	    		pbar.reset( true );
		    	//Ext.getCmp( id + '-card-prev' ).setDisabled( true );
		    	Ext.getCmp( id + '-card-next' ).setDisabled( false );

	    		if( response._class == 'AgilePHP_RemotingException' ) {

	    			var errHtml = '<div class="wizard-header"><h1>Failed to create project.</h1></div><p style="padding-top: 15px;">' + response.message + '</p>';
	    			if( AgilePHP.IDE.debug ) errHtml += '<p><pre>' + response.trace + '</pre></p>';

	    			Ext.getCmp( id + 'step-8-message' ).el.dom.innerHTML = errHtml; 
	    			window.setLabelStatus( 8, 3 );
	    			return;
	    		}

		    	Ext.getCmp( id + 'step-8-message' ).el.dom.innerHTML = '<div class="wizard-header">Your project was successfully created.</div>';

		    	AgilePHP.IDE.FileExplorer.tree.getRootNode().reload(); // Reload tree root (workspace)
		    });
	        projectRemote.create( params );
	};

	/**
	 * Return the new project wizard
	 */
	return window.wizard([{
              		id: 'step-0',
              		xtype: 'form',
              		labelWidth: 75,
              		defaults: {
              	        anchor: '95%',
              	        allowBlank: false,
              	        msgTarget: 'side'
              	    },
              	   	items: [{   		
              	   		xtype: 'label',
              	   		html: '<div class="window-header">Please supply the name of your project. When you are finished click the next button.</div>'
              	   	}, {
              	           xtype: 'fieldset',
              	           title: 'General',
              	           //collapsible: true,
              	           defaults: {width: '95%'},
              	           defaultType: 'textfield',
              	           items: [{
	              	       		id: id + '-form-workspace',
	              	   	        xtype: 'textfield',
	              	   	        name: 'workspace',
	              	   	        fieldLabel: 'Workspace',
	              	   	        readOnly: true,
	              	   	        allowBlank: false,
	              	   	        value: AgilePHP.IDE.FileExplorer.workspace
	              	       	}, {
	              	       		id: id + '-form-name',
	              	   	        xtype: 'textfield',
	              	   	        name: 'projectName',
	              	   	        fieldLabel: 'Name',
	              	   	        allowBlank: false
	              	       	}]
              	   	}],
              	   	label: 'General',
              	   	handler: function() {
              	
              	    	if( !Ext.getCmp( id + '-form-name' ).getValue() ) {
              	
              	    		AgilePHP.IDE.error( 'Project name is required.' );
              	    		return false;
              	    	}
              	
              	    	return true;
              	    }
              	}, {
              	    id: 'step-1',
              	    label: 'Logging',
              		xtype: 'form',
              		labelWidth: 75,
              		defaults: {
              	        anchor: '95%',
              	        allowBlank: false,
              	        msgTarget: 'side'
              	    },
              	    items: [{   		
              	   		xtype: 'label',
              	   		html: '<div class="window-header">Would you like to enable logging in your project? (recommended)</p>'
              	   	}, {
              	           xtype: 'fieldset',
              	           title: 'Logging',
              	           items: [{
              	       		id: id + '-form-log-enable',
              	       		xtype: 'checkbox',
              	   	        name: 'logEnable',
              	   	        fieldLabel: 'Enable',
              	   	        listeners: {
              	           		check: function( checkbox, checked ) {
              	
              	           			Ext.getCmp( id + '-form-log-level' ).setDisabled( checked == false );
              	           		}
              	           	}
              	       	}, new Ext.form.ComboBox({
              	       		id: id + '-form-log-level',
              	       		name: 'logLevel',
              	       		mode: 'local',
              			    emptyText: '(Logging Level)',
              			    store: new Ext.data.ArrayStore({
              			        id: id + '-loglevel-store',
              			        fields: [
              			            {name: 'id'},
              			            {name: 'name'}
              			        ],
              			        data: [ ['info', 'INFO'],
              			                ['warn', 'WARN'],
              			                ['error', 'ERROR'],
              			                ['debug', 'DEBUG']
              			        ],
              			    }),
              			    valueField: 'id',
              			    displayField: 'name',
              			    fieldLabel: 'Level',
              			    disabled: true,
              			    typeAhead: true,
              		        forceSelection: true,
              		        triggerAction: 'all'
              	       	})]
              	   	}],
              	   	handler: function() {
              	
              			var enabled = Ext.getCmp( id + '-form-log-enable' ).getValue(); 
              			if( !enabled ) return true;
              	
              			if( !Ext.getCmp( id + '-form-log-level' ).getValue() ) {
              	
              				AgilePHP.IDE.error( 'Log level required.' );
              				return false;
              			}
              	
              			return true;
              		}
              	}, {
              	    id: 'step-2',
              	    label: 'Identity',
              		xtype: 'form',
              		labelWidth: 75,
              		defaults: {
              	        anchor: '95%',
              	        allowBlank: false,
              	        msgTarget: 'side'
              	    },
              	    items: [{
              	   		xtype: 'label',
              	   		html: '<div class="window-header">Would you like to use the Identity component to manage users, roles, and sessions?</p>'
              	   	}, {
              	           xtype: 'fieldset',
              	           title: 'Identity',
              	           defaultType: 'textfield',
              	           items: [{
              	       		id: id + '-form-identity-enable',
              	       		xtype: 'checkbox',
              	   	        name: 'identityEnable',
              	   	        fieldLabel: 'Enable',
              	   	        checked: true
              	           }]
              	       }]
              	}, {
              	    id: 'step-3',
              	    label: 'Encryption',
              	    xtype: 'form',
              		labelWidth: 75,
              		defaults: {
              	        anchor: '95%',
              	        allowBlank: false,
              	        msgTarget: 'side'
              	    },
              	    items: [{
              	   		xtype: 'label',
              	   		html: '<div class="window-header">Would you like to use the Crypto component to secure private data?</p>'
              	   	}, {
              	           xtype: 'fieldset',
              	           title: 'Encryption',
              	           defaultType: 'textfield',
              	           items: [{
              	       		id: id + '-form-crypto-enable',
              	       		xtype: 'checkbox',
              	   	        name: 'cryptoEnable',
              	   	        fieldLabel: 'Enable',
              	   	        checked: true
              	           }]
              	       }]
              	}, {
              	    id: 'step-4',
              	    label: 'Sessions',
              	    xtype: 'form',
              		labelWidth: 75,
              		defaults: {
              	        anchor: '95%',
              	        allowBlank: false,
              	        msgTarget: 'side'
              	    },
              	    items: [{
              	   		xtype: 'label',
              	   		html: '<div class="window-header">The AgilePHP SessionScope component uses database persisted sessions instead of the standard PHP filesystem based sessions. Would you like to use the SessionScope component?</p>'
              	   	}, {
              	           xtype: 'fieldset',
              	           title: 'Sessions',
              	           defaultType: 'textfield',
              	           items: [{
              	       		id: id + '-form-session-enable',
              	       		xtype: 'checkbox',
              	   	        name: 'sessionEnable',
              	   	        fieldLabel: 'Enable',
              	   	        checked: true
              	           }]
              	       }]
              	}, {
              	    id: 'step-5',
              	    label: 'Database',
              	    xtype: 'form',
              		labelWidth: 75,
              		defaults: {
              	        anchor: '95%',
              	        allowBlank: false,
              	        msgTarget: 'side'
              	    },
              	    items: [{
              	   		xtype: 'label',
              	   		html: '<div class="window-header">Will you be using a database server?</p>'
              	   	}, {
              	           xtype: 'fieldset',
              	           title: 'Database',
              	           defaultType: 'textfield',
              	           items: [{
              	       		id: id + '-form-database-enable',
              	       		xtype: 'checkbox',
              	   	        name: 'databaseEnable',
              	   	        fieldLabel: 'Enable',
              	   	        listeners: {
              	           		check: function( checkbox, checked ) {
              	           			Ext.getCmp( id + '-form-database-type' ).setDisabled( checked == false );
              	           		}
              	           	}
              	           }, new Ext.form.ComboBox({
              	       		id: id + '-form-database-type',
              	       		name: 'databaseType',
              	       		mode: 'local',
              			    emptyText: '(Database Servers)',
              			    store: new Ext.data.ArrayStore({
              			        id: id + '-database-store',
              			        fields: [
              			            {name: 'id'},
              			            {name: 'name'}
              			        ],
              			        data: [ ['mysql', 'MySQL'],
              			                ['sqlite', 'SQLite'],
              			                ['pgsql', 'PostgreSQL'],
              			                ['mssql', 'MSSQL (PDO ODBC Driver)'],
              			                ['sqlsrv', 'MSSQL (SQLSRV Driver)']
              			        ],
              			    }),
              			    valueField: 'id',
              			    displayField: 'name',
              			    fieldLabel: 'Type',
              			    disabled: true,
              			    typeAhead: true,
              		        forceSelection: true,
              		        triggerAction: 'all',
              		        listeners: {

              	           		//change: function( e, newValue, oldValue ) {
              	        	    select: function( el, record, index ) {

              	           			if( el.getValue() == 'sqlite' ) {

              	           				Ext.getCmp( id + '-form-database-name' ).setDisabled( false );
              	           				Ext.getCmp( id + '-form-database-hostname' ).setDisabled( true );
              	           				Ext.getCmp( id + '-form-database-username' ).setDisabled( true );
              	           				Ext.getCmp( id + '-form-database-password' ).setDisabled( true );

              	           				Ext.getCmp( id + '-form-database-test' ).setDisabled( Ext.getCmp( id + '-form-database-name' ).getValue() == false );
              	           			}
              	           			else {

	              	           			Ext.getCmp( id + '-form-database-name' ).setDisabled( false );
		              	           		Ext.getCmp( id + '-form-database-hostname' ).setDisabled( false );
	          	           				Ext.getCmp( id + '-form-database-username' ).setDisabled( false );
	          	           				Ext.getCmp( id + '-form-database-password' ).setDisabled( false );

	          	           				if( el.getValue() == 'pgsql' )
             	           				    Ext.getCmp( id + '-form-database-test' ).setDisabled( Ext.getCmp( id + '-form-database-name' ).getValue() == false );
	          	           				else
	          	           					Ext.getCmp( id + '-form-database-test' ).setDisabled( false );
              	           			}
              	           		}
              	           	}
              	       	}), {
              	       		id: id + '-form-database-name',
              	   	        xtype: 'textfield',
              	   	        name: 'databaseName',
              	   	        fieldLabel: 'Name',
              	   	        allowBlank: false,
              	   	        disabled: true,
              	   	        enableKeyEvents: true,
              	   	        listeners: {
              	        	   keyup: function( el, e ) {

              	        	   		if( Ext.getCmp( id + '-form-database-type' ).getValue() == 'sqlite' ||
              	        	   			Ext.getCmp( id + '-form-database-type' ).getValue() == 'pgsql' ) {

            	        	   			Ext.getCmp( id + '-form-database-test' ).setDisabled( el.getValue() == false );
              	        	   		}
              	        	   		else
           	        	   				Ext.getCmp( id + '-form-database-test' ).setDisabled( el.getValue() == true );
              	           	   }
              	           	}
              	       	}, {
              	       		id: id + '-form-database-hostname',
              	   	        xtype: 'textfield',
              	   	        name: 'databaseHostname',
              	   	        fieldLabel: 'Hostname',
              	   	        allowBlank: false,
              	   	        disabled: true
              	   	        /*
              	   	        enableKeyEvents: true,
              	   	        listeners: {
              	       			keyup: function( el, e ) {

			              	       	validIp = /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/;
			  	       				validHostname = /^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$/;

			  	       				if( !el.getValue().match( validIp ) && !el.getValue().match( validHostname ) ) {

			  	       					AgilePHP.IDE.error( 'Invalid hostname' );
	              	       				Ext.getCmp( id + '-form-database-test' ).setDisabled( false );
	              	       				return;
			  	       				}

              	       				Ext.getCmp( id + '-form-database-test' ).setDisabled( true );
              	       			}
              	       		}
              	       		*/
              	       	}, {
              	       		id: id + '-form-database-username',
              	   	        xtype: 'textfield',
              	   	        name: 'databaseUsername',
              	   	        fieldLabel: 'Username',
              	   	        allowBlank: false,
              	   	        disabled: true
              	       	}, {
              	       		id: id + '-form-database-password',
              	   	        xtype: 'textfield',
              	   	        name: 'databasePassword',
              	   	        fieldLabel: 'Password',
              	   	        allowBlank: false,
              	   	        inputType: 'password',
              	   	        disabled: true
              	       	}, {
              	       		id: id + '-form-database-test',
              	       		xtype: 'button',
              	       		text: 'Test Connection',
              	       		style: 'float: right;',
              	       		disabled: true,
              	       		listeners: {

              	       			click: function( button, e ) {

              	       				var workspace = Ext.getCmp( id + '-form-workspace' ).getValue();
              	       				var project = Ext.getCmp( id + '-form-name' ).getValue();

              	       				var database = {};
              	       					database.type = Ext.getCmp( id + '-form-database-type' ).getValue();
              	       					database.name = Ext.getCmp( id + '-form-database-name' ).getValue()
              	       					database.hostname = Ext.getCmp( id + '-form-database-hostname' ).getValue()
              	       					database.username = Ext.getCmp( id + '-form-database-username' ).getValue()
              	       					database.password = Ext.getCmp( id + '-form-database-password' ).getValue()

              	       				button.setDisabled( true );
              	       				var dbManagerRemote = new DatabaseManagerRemote();
              	       					dbManagerRemote.setCallback( function( response ) {

              	       						button.setDisabled( false );
              	       						if( response._class == 'AgilePHP_RemotingException' ) {

              	       							var errHtml = '<p>' + response.message + '</p>';
              	       							if( AgilePHP.IDE.debug ) errHtml += '<p><pre>' + response.trace + '</pre></p>';
              	       							AgilePHP.IDE.error( errHtml );
              	       							return false;
              	       						}

              	       						Ext.getCmp( id + '-form-database-test' ).setDisabled( false );
              	       						switch( response ) {

              	       							case -1:
              	       								AgilePHP.IDE.error( 'Connection failed.' );
              	       							break;

              	       							case 0:
              	       								AgilePHP.IDE.warn( 'The connection to the database engine was successful but the database could not be found.' );
              	       							break;

              	       							case 1:
              	       							case true:
              	       								AgilePHP.IDE.info( 'Connection successful!' );
              	       							break;

              	       							default:
              	       								AgilePHP.IDE.error( 'Unexpected response from server.' );
              	       						}
              	       				});
              	       				dbManagerRemote.testConnection( database );
              	       			}
              	       		}
              	       	}]
              	       }],
              	       handler: function() {

              	    	var enabled = Ext.getCmp( id + '-form-database-enable' ).getValue(); 
              	    	if( !enabled ) return true;

              	    	if( !Ext.getCmp( id + '-form-database-type' ).getValue() ) {

              	    		AgilePHP.IDE.error( 'Database type required.' );
              	    		return false;
              	    	}

              	    	if( Ext.getCmp( id + '-form-database-type' ).getValue() == 'sqlite' ) {

              	    		if( !Ext.getCmp( id + '-form-database-name' ).getValue() ) {

              	    			AgilePHP.IDE.error( 'Database name required.' );
              	    			return false;
              	    		}
              	    		return true;
              	    	}

              	    	if( !Ext.getCmp( id + '-form-database-name' ).getValue() ) {
              		    	
              	    		AgilePHP.IDE.error( 'Database name required.' );
              	    		return false;
              	    	}
              	    	if( !Ext.getCmp( id + '-form-database-hostname' ).getValue() ) {
              	    	
              	    		AgilePHP.IDE.error( 'Database hostname required.' );
              	    		return false;
              	    	}
              	    	if( !Ext.getCmp( id + '-form-database-username' ).getValue() ) {
              		    	
              	    		AgilePHP.IDE.error( 'Database username required.' );
              	    		return false;
              	    	}
              	    	if( !Ext.getCmp( id + '-form-database-password' ).getValue() ) {
              		    	
              	    		AgilePHP.IDE.error( 'Database password required.' );
              	    		return false;
              	    	}
              	    	return true;
              		}
              	}, {
              	    id: 'step-6',
              	    label: 'IDE Support',
              	    xtype: 'form',
              		labelWidth: 75,
              		defaults: {
              	        anchor: '95%',
              	        allowBlank: false,
              	        msgTarget: 'side'
              	    },
              	    items: [{   		
              	   		xtype: 'label',
              	   		html: '<div class="window-header">If you would like to enable external IDE support, please choose from the following platforms below.</p>'
              	   	}, {
              	           xtype: 'fieldset',
              	           title: 'Enable',
              	           items: [{
              	       		id: id + '-form-ide-enable',
              	       		xtype: 'checkbox',
              	   	        name: 'ideEnable',
              	   	        fieldLabel: 'Enable',
              	   	        listeners: {
              	           		check: function( checkbox, checked ) {
              	
              	           			Ext.getCmp( id + '-form-ide-platform' ).setDisabled( checked == false );
              	           		}
              	           	}
              	       	}, new Ext.form.ComboBox({
              	       		id: id + '-form-ide-platform',
              	       		name: 'idePlatform',
              	       		mode: 'local',
              			    emptyText: '(IDE Platform)',
              			    store: new Ext.data.ArrayStore({
              			        id: id + '-combo-store',
              			        fields: [
              			            {name: 'id'},
              			            {name: 'name'}
              			        ],
              			        data: [ ['eclipse', 'Eclipse'],
              			                ['netbeans', 'Netbeans']
              			        ],
              			    }),
              			    valueField: 'id',
              			    displayField: 'name',
              			    fieldLabel: 'IDE',
              			    disabled: true,
              			    typeAhead: true,
              		        forceSelection: true,
              		        triggerAction: 'all'
              	       	})]
              	   	}],
              	   	handler: function() {

              	    	// Display summary on completion page
              	    	Ext.getCmp( 'step-7' ).el.dom.innerHTML = '<p style="padding-bottom: 10px;">Your new project will be created using the following configuration. Click next to begin.</p>' +
            	    	  		getSummary();

              			var enabled = Ext.getCmp( id + '-form-ide-enable' ).getValue(); 
              			if( !enabled ) return true;
              	
              			if( !Ext.getCmp( id + '-form-ide-platform' ).getValue() ) {
              	
              				AgilePHP.IDE.error( 'IDE platform required.' );
              				return false;
              			}

              			return true;
              		}
              	},  {
              	    id: 'step-7',
              	    label: 'Summary',
              	    handler: function() {

              			submit();
              			return true;
              	 	}
              	}, {
              		id: 'step-8',
              		label: 'Create',
              		items: [{

              			id: id + 'step-8-message',
              			html: '<div class="wizard-header">Please be patient while your project is being created.</div>'
              		}],
              		handler: function() {

              			Ext.getCmp( id ).close();
              		}
              	}
      ]);
};