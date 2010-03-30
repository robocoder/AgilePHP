// ExamplePlugin constructor
function ExamplePlugin() {

		// Add a new tab to the workspace
		AgilePHP.IDE.Workspace.addTab({
				id: 'tabExamplePlugin',
				title: 'Example Plugin',
				iconCls: 'cdsLogo',
				html: '<div style="padding: 25px 25px 25px 25px; font-weight: bolder;">ExamplePlugin works!</div>' +
					  '<div style="padding: 25px 25px 25px 25px;"><input type="button" onclick="ExamplePlugin.testMethod()" value="ExamplePlugin.testMethod()"/></div>'
		});

		// Set the active workspace tab to this plugin
		//AgilePHP.IDE.Workspace.activateTab( 'tabExamplePlugin' );

		// Add a new button to the toolbar
		AgilePHP.IDE.Workspace.addToolbarButton({
			id : 'btnExamplePlugin',
			text: 'Example Plugin',
			menu: [{
				   text: 'ExamplePlugin.testMethod()',
				   iconCls: 'cdsLogo',
				   handler: function() { 
						ExamplePlugin.testMethod();
				   }
			}]
		});
}

// Add a method to the ExamplePlugin object that creates a new window when clicked
ExamplePlugin.prototype.testMethod = function() {

	  var ExamplePluginWindow = new AgilePHP.IDE.Window( 'examplePlugin', 'fileExport', 'Example Plugin' );
	  	  ExamplePluginWindow.setHTML( 'ExamplePlugin works!' );
		  ExamplePluginWindow.show();

	  new AgilePHP.IDE.Notification( 'Example Plugin', 'ExamplePlugin works!' );
}

// Create the new instance of ExamplePlugin
var ExamplePlugin = new ExamplePlugin();