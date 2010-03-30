AgilePHP.IDE.Window.Help.About = {

	window: new Ext.Window({

			id: 'ide-about-window',
    		renderTo: Ext.getBody(),
    		title: 'About',
            width: 500,
            height: 300,
            closeAction: 'hide',
            plain: true,
            iconCls: 'appIcon',
            modal: true,
            animateTarget: 'btnAbout',

            html: '<div id="aboutWindow" style="background-color: #000000; height: 300px;">' +
            		'<img src="view/images/logo.png" style="float: left; padding: 10px 10px 20px 10px;">' +
            		'<div style="float: left; padding-left: 290px; padding-right: 25px; color: #FFFFFF">' + 
            			'<div style="font-weight: bolder;">' + AgilePHP.IDE.appName + '</div>' +
            			'<div style="font-weight: bolder;">Version: ' + AgilePHP.IDE.version + '</div>' +
            			'<div>&copy; 2010 <a target="_blank" href="http://www.makeabyte.com" style="text-decoration: none; color: #FFFFFF;">Make A Byte, inc.</a></div>' +
            	  '</div>',
            buttons: [{
                text: 'Close',
                handler: function(){
            		AgilePHP.IDE.Window.Help.About.window.hide();
                }
            }]
     }),

     show: function() {

			AgilePHP.IDE.Window.Help.About.window.show( this );
	 }
};