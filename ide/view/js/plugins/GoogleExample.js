AgilePHP.IDE.Plugins.GoogleExample = function() {

		AgilePHP.IDE.Desktop.addTab({
				id: 'googleExamplePlugin',
				title: 'Google',
				html: '<iframe src="http://www.google.com" width="100%" height="100%" frameborder="0"/>'
		});
}
var GoogleExamplePlugin = new AgilePHP.IDE.Plugins.GoogleExample();