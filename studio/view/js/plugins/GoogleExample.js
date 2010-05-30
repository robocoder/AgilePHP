AgilePHP.Studio.Plugins.GoogleExample = function() {

		AgilePHP.Studio.Desktop.addTab({
				id: 'googleExamplePlugin',
				title: 'Google',
				html: '<iframe src="http://www.google.com" width="100%" height="100%" frameborder="0"/>'
		});
}
var GoogleExamplePlugin = new AgilePHP.Studio.Plugins.GoogleExample();