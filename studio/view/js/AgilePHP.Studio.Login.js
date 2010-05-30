AgilePHP.Studio.Login = {

	form: null,

	init: function() {

		AgilePHP.Studio.Login.form = new Ext.FormPanel({

				id: 'studio-login-form',
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
		
			    		AgilePHP.Studio.Login.form.getForm().submit({
			                    method: 'POST', 
			                    waitTitle: 'Logging in', 
			                    waitMsg: 'Authenticating...',
			                    success: function() {

			    					AgilePHP.Studio.Login.destroy();
			    					setTimeout( 'AgilePHP.Studio.Desktop.load()', 1000 );
								},
			                    failure: function( form, action ) {
		
			                    	(action.failureType == 'server' ) ?
			                    			Ext.get( 'studio-login-form' ).shake() :
			                    			Ext.Msg.alert( 'Warning!', 'Server unreachable: ' + action.response.responseText ); 

			                    	AgilePHP.Studio.Login.form.getForm().reset(); 
			                    } 
			             }); 
			       } 
		        }] 
		    });
	},

	show : function() {

		AgilePHP.Studio.Login.init();

		var appPanel = Ext.getCmp( 'studio-viewport-panel' );
			appPanel.add( AgilePHP.Studio.Login.form );
			appPanel.doLayout();

		Ext.get( 'studio-login-form' ).fadeIn({ duration: 3});
	},

	destroy : function() {

		Ext.get( 'studio-login-form' ).fadeOut({ duration: 1});
		setTimeout( 'AgilePHP.Studio.Login.form.destroy()', 1000 );
	}
};