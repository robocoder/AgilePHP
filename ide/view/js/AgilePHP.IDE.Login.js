AgilePHP.IDE.Login = {

	form: null,

	init: function() {

		AgilePHP.IDE.Login.form = new Ext.FormPanel({

				id: 'ide-login-form',
		        url: AgilePHP.getRequestBase() + '/ExtLoginController/login', 
		        frame: true,
		        width: '210px',
		        title: 'Please Login',
		        labelWidth: 57,
		        defaultType: 'textfield',
				monitorValid:true,
				style: 'margin: 100px auto 0px auto;',
		        items:[{ 
		                fieldLabel: 'Username', 
		                name: 'username', 
		                allowBlank: false 
		            },{ 
		                fieldLabel: 'Password', 
		                name: 'password', 
		                inputType: 'password', 
		                allowBlank: false 
		        }],
			    buttons:[{ 
		            text: 'Login',
		            formBind: true,	 
		            handler: function() {
		
			    		AgilePHP.IDE.Login.form.getForm().submit({
			                    method: 'POST', 
			                    waitTitle: 'Logging in', 
			                    waitMsg: 'Authenticating...',
			                    success: function() {

			    					AgilePHP.IDE.Login.destroy();
			    					setTimeout( 'AgilePHP.IDE.Desktop.load()', 1000 );
								},
			                    failure: function( form, action ) {
		
			                    	(action.failureType == 'server' ) ?
			                    			Ext.get( 'ide-login-form' ).shake() :
			                    			Ext.Msg.alert( 'Warning!', 'Server unreachable: ' + action.response.responseText ); 

			                    	AgilePHP.IDE.Login.form.getForm().reset(); 
			                    } 
			             }); 
			       } 
		        }] 
		    });
	},

	show : function() {

		AgilePHP.IDE.Login.init();

		var appPanel = Ext.getCmp( 'ide-viewport-panel' );
			appPanel.add( AgilePHP.IDE.Login.form );
			appPanel.doLayout();

		Ext.get( 'ide-login-form' ).fadeIn({ duration: 3});
	},

	destroy : function() {

		Ext.get( 'ide-login-form' ).fadeOut({ duration: 1});
		setTimeout( 'AgilePHP.IDE.Login.form.destroy()', 1000 );
	}
};