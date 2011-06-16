AgilePHP.Studio.LoginWindow = function() {

		var mask = new Ext.LoadMask(Ext.getBody(), { msg: 'Authenticating...'});

		var formpanel = new Ext.FormPanel({

				labelWidth: 80,
				url: AgilePHP.getRequestBase() + '/ExtLoginController/login',
				baseCls: 'x-plain',
				width: 475,
				height: 100,
				defaultType: 'textfield',
				monitorValid: true,
				html: '<div class="window-heading">Welcome to AgilePHP Studio! Please enter your username and password and click the login button to continue.',
				items: [{
	                xtype:'fieldset',
	                title: 'Credentials',
	                defaults: {width: 290},
	                defaultType: 'textfield',
	                height: 90,
	                width: 400,
	                collapsible: true,
	                style: {
	                	position: 'absolute',
	                	marginTop: '80px',
	            		marginLeft: '40px'
	                },
	                items: [{
	                	id: 'Username',
	                	name: 'username',
						value: 'admin',
	                    fieldLabel: 'Username',
						allowBlank: false
	                }, {
	                	id: 'Password',
	                	name: 'password',
						value: 'test',
	                    fieldLabel: 'Password',
	                    inputType: 'password',
						allowBlank: false
	                }]
	            }]
		});

		var win = new Ext.Window({

		    id: 'ext-login-window',
            renderTo: Ext.getBody(),
			title: 'AgilePHP Studio Login',
            width: 500,
            height: 300,
            resizable: false,
			closable: false,
			draggable: false,
			iconCls: 'appIcon',
			html: '<div class="window-copyright">Copyright (c) 2010 <a style="color: #000000;" href="http://www.makeabyte.com" target="_blank">Make A Byte, inc</a></div>',
            items: [formpanel],
            buttons: [{
                text: 'Login',
				handler: function() {

            		mask.show();

            		if(!Ext.get('Username').getValue()) {

						AgilePHP.Studio.error('Username required!');
						return false;
					}								
					if(!Ext.get('Password').getValue()) {

						AgilePHP.Studio.error('Password required!');
						return false;
					}

					formpanel.getForm().submit({

						method: 'POST',
						success: function(btn, event) {

							 mask.hide();

							 AgilePHP.Studio.User.setUsername(event.result.data.username.toLowerCase());
							 AgilePHP.Studio.User.setRole(event.result.data.role.toLowerCase());

							 //Ext.get('ext-login-window').fadeOut({ duration: 1});
							 //Ext.getCmp('ext-login-window').el.shadow.el.hide();
	                		 //setTimeout('Ext.getCmp("ext-login-window").destroy()', 1000);
	                		 //setTimeout('AgilePHP.Studio.Desktop.load()', 1000);
							 Ext.getCmp("ext-login-window").destroy()
	                		 AgilePHP.Studio.Desktop.load();
						},
						failure: function(form, action) {

							 mask.hide();
							 var response = Ext.util.JSON.decode(action.response.responseText);

							 if(!response)
								 AgilePHP.Studio.error('No response from server');

							 else if(response.success == false)
								 (action.failureType == 'server') ?
			                    			Ext.get('ext-login-window').shake() :
			                    			AgilePHP.Studio.error('Server unreachable: ' + action.response.responseText); 

							 formpanel.getForm().reset();
						}
					});
           		}
            }]
		});

		win.show();
		Ext.get('ext-login-window').fadeIn({ duration: 1});
};