/*
# AgilePHP Framework :: The Rapid "for developers" PHP5 framework
# Copyright (C) 2009 Make A Byte, inc

# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * AgilePHP client side JavaScript library
 */
var AgilePHP = {

	author: 'Jeremy Hahn',
	copyright: 'Make A Byte, inc.',
	licence: 'GNU General Public License v3',
	package: 'com.makeabyte.agilephp',

	requestBase: null,
	documentRoot: null,
	debugMode: false,

	/**
	 * Sets the path relative to DocumentRoot which handles application
	 * requests. Defaults to /index.php.
	 * 
	 * @param {String} path The request base path
	 * @return void
	 */
	setRequestBase: function(path) {
		this.requestBase = path;
	},

	/**
	 * Returns the base path which handles application requests.
	 * 
	 * @return The AgilePHP request base / bootstrap. (the page
	 *         that handles dispatching application requests)
	 */
	getRequestBase: function() {

		if(!AgilePHP.requestBase) {

			var pos = location.pathname.indexOf('.php') + 4;
			AgilePHP.requestBase = location.pathname.substring(0, pos);
		}

		return AgilePHP.requestBase;
	},

	/**
	 * Returns the document root path where the application lives on the server.
	 * 
	 * @return {string} The document root where the application lives.
	 */
	getDocumentRoot: function() {

		if(!AgilePHP.documentRoot) {

			var pieces = AgilePHP.getRequestBase().split('/');
			AgilePHP.documentRoot = pieces.slice(0, (pieces.length - 1)).join('/')	+ '/';
		}

		return AgilePHP.documentRoot;
	},

	/**
	 * When set to true, this causes the AgilePHP client JavaScript routines to
	 * output debugging information. This also enables the use of
	 * AgilePHP.debug.
	 * 
	 * @param boolean
	 *            {Boolean} True to enable debugging, false to disable
	 * @return void
	 */
	setDebug: function(boolean) {
		AgilePHP.debugMode = boolean;
	},

	/**
	 * Tells whether or not the AgilePHP client side JavaScript is in debug mode
	 * 
	 * @return {Boolean} True if AgilePHP client side JavaScript is in debugging
	 *         mode, false otherwise
	 */
	isInDebugMode: function() {
		return AgilePHP.debugMode == true;
	},

	/**
	 * Performs an HTTP GET request (using location.href) for the specified url
	 * 
	 * @param url
	 *            {String} The url to load
	 * @return void
	 */
	go: function(url) {
		location.href = url;
	},

	/**
	 * Adds a new <script></script> tag to the head of the HTML document and
	 * adds the specified file to the src attribute value.
	 * 
	 * @param {String} file The file to set as the value to the script src attribute
	 * @return void
	 */
	loadScript: function(file) {
		var head = document.getElementsByTagName('head')[0];
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = file;
		head.appendChild(script);
	},

	/**
	 * Writes debugging messages to firebug console
	 * 
	 * @param {Mixed} msg Write the specified message to the firebug console
	 * @return void
	 */
	debug: function(msg) {

		try {
			if(this.isInDebugMode())
				console.log(msg);
		}
		catch (e) {}
	},

	/**
	 * Handles client side JavaScript events in regards to ORM framework
	 */
	ORM: {

		/**
		 * This is fired when a user clicks a delete action link on a database
		 * record when rendered using the XSLT controller.
		 */
		confirmDelete: function(requestBase, value, page, controller, action) {
			var decision = confirm('Are you sure you want to delete this record?');
			if(decision === true)
				location.href = requestBase + '/' + controller + '/' + action
						+ '/' + value + '/' + page;
		},

		/**
		 * Used to highlight a database table row on mouseover
		 */
		setStyle: function(el, style) {
			el.setAttribute('class', style);
		},

		/**
		 * Performs a search action for XSLT generated pages
		 */
		search: function(isComponent) {
			var pos = location.pathname.indexOf('.php') + 5;
			var mvcQuery = location.pathname.substring(pos);
			var mvcArgs = mvcQuery.split('/');
			var controller = mvcArgs[0] + ((isComponent) ? '/' + mvcArgs[1] : '');
			var keyword = document.getElementById('agilephpSearchText').value;
			var field = document.getElementById('agilephpSearchField').value;
			var view = document.getElementById('view').value;
			var page = document.getElementById('page').value;
			var url = location.protocol + '//' + location.host
					+ AgilePHP.getRequestBase() + '/' + controller + '/search/'
					+ page + '/' + view + '/' + field + '/' + keyword;

			location.href = url;
		}
	},

	/**
	 * AgilePHP XMLHttpRequest object, basis for AJAX calls...
	 */
	XHR: function(async, requestToken) {

		this.isAsync = (async === false) ? false : true;
		this.requestToken = requestToken;

		this.MS_PROGIDS = new Array("MSXML2.XMLHTTP.12.0",
				"MSXML2.XMLHTTP.11.0", "MSXML2.XMLHTTP.10.0",
				"MSXML2.XMLHTTP.9.0", "MSXML2.XMLHTTP.8.0",
				"MSXML2.XMLHTTP.7.0", "MSXML2.XMLHTTP.6.0",
				"MSXML2.XMLHTTP.5.0", "MSXML2.XMLHTTP.4.0",
				"MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP");

		/**
		 * Creates a new XMLHttpRequest object
		 * 
		 * @return {XMLHttpRequest} The XHR instance
		 */
		this.createRequest = function() {

			var xhr = null;

		     if(window.XMLHttpRequest != null)
		    	 xhr = new window.XMLHttpRequest();

		     else if(window.ActiveXObject != null) {

		    	  for(var i=0; i<this.MS_PROGIDS.length && !obj; i++) {

		    		   try {
		    			   xhr = new ActiveXObject(this.MS_PROGIDS[i]);
		    		   }
		    		   catch(ex) {}
		    	  }
		     }

		     if(xhr == null) {
		    		
		    	 var msg = 'Could not create XHR object.';
		    	 AgilePHP.debug(msg);
		    	 throw msg;
		     }

		     return xhr;
		},

		/**
		 * Sets the XMLHttpRequest to asynchronous communication mode.
		 * This is the default (since AJAX is AsynchronousJAX...).
		 */
		this.setAsynchronous = function() {
			this.isAsync = true;
		},

		/**
		 * Sets the XMLHttpRequest to synchronous communication mode.
		 * You should not use this unless you really have no other
		 * choice since it leaves your clients browser waiting on the
		 * reply.
		 */
		this.setSynchronous = function() {
			this.isAsync = false;
		},

		/**
		 * Sets the AgilePHP RequestScope token that guards against CSFR
		 * attacks
		 * 
		 * @param {string} token The anti-CSFR token
		 * @return void
		 */
		this.setRequestToken = function(token) {
			this.requestToken = token;
		},

		/**
		 * Returns the AgilePHP RequestScope token used to guard against
		 * CSFR attacks
		 * 
		 * @return AgilePHP RequestScope anti-CSFR token
		 */
		this.getRequestToken = function() {
			return this.requestToken;
		},

		/**
		 * Evaluates an XMLHttpRequest response object and returns the
		 * result
		 * 
		 * @param {XMLHttpRequest} xhr The XMLHttpRequest object to evaluate
		 * @return The eval result
		 */
		this.eval = function(xhr) {
			return eval('(' + xhr.responseText + ')');
		},

		/**
		 * Creates an XMLHttpRequest GET request to the server
		 * 
		 * @param {String} url The url to send the request
		 * @param {function} callback The callback function to execute
		 * @return The XMLHttpRequest response object if
		 *         XHR.setSynchronous was called. Otherwise the callback
		 *         is executed when the request has completed.
		 */
		this.request = function(url, callback) {

			var xhr = this.createRequest();

			xhr.open('GET', url, this.isAsync);
			xhr.setRequestHeader('X-Powered-By', 'AgilePHP Framework');
			xhr.send(null);

			if(this.isAsync) {

				xhr.onreadystatechange = function() {

					if (xhr.readyState == 4) {

						var data = (xhr.responseText.length) ? eval('('+ xhr.responseText + ')') : null;
						AgilePHP.debug(data);
						if(callback) callback(data);
					}
				}
			}
			else {

				var data = (xhr.responseText.length) ? eval('(' + xhr.responseText + ')') : null;
				AgilePHP.debug(data);
				return data;
			}
		},

		/**
		 * Creates an XMLHttpRequest POST request to the server
		 * 
		 * @param {String} url The url to send the request
		 * @param {String} data The data to send to the server (ex: var1=val1&var2=val2...)
		 * @param {function} callback The callback function to execute when the call has completed
		 * @return The XMLHttpRequest response object if
		 *         XHR.setSynchronous was called. Otherwise the callback
		 *         is executed when the request has completed.
		 */
		this.post = function(url, data, callback) {

			// Add AgilePHP RequestScope anti-CSFR token to POST requests if present
			if (this.getRequestToken())
				data = 'AGILEPHP_REQUEST_TOKEN=' + this.getRequestToken() + '&' + data;

			var xhr = this.createRequest();

			xhr.open('POST', url, this.isAsync);
			xhr.setRequestHeader('X-Powered-By', 'AgilePHP Framework');
			xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			xhr.setRequestHeader('Content-length', data.length);
			xhr.setRequestHeader('Connection', 'close');
			xhr.send(data);

			if (this.isAsync) {

				xhr.onreadystatechange = function() {

					if (xhr.readyState == 4) {
						var data = (xhr.responseText.length) ? eval('(' + xhr.responseText + ')') : null;
						AgilePHP.debug(data);
						if(callback) callback(data);
					}
				}
			} else {

				var data = (xhr.responseText.length) ? eval('(' + xhr.responseText + ')') : null;
				AgilePHP.debug(data);

				return data;
			}
		},

		/**
		 * Creates an XMLHttpRequest GET request to the server and sets
		 * the specified el.innerHTML with the responseText of the
		 * request.
		 * 
		 * @param {String} url The url to send the request
		 * @param {Object} el The HTML element to set the .innerHTML
		 *        value with the XMLHttpRequest responseText
		 * @return void
		 */
		this.updater = function(url, el) {

			var xhr = this.createRequest();
			xhr.open('GET', url, this.isAsync);
			xhr.setRequestHeader('X-Powered-By', 'AgilePHP Framework');
			xhr.send(null);

			if(this.isAsync) {

				xhr.onreadystatechange = function() {

					if (xhr.readyState == 4) {

						AgilePHP.debug(xhr);
						AgilePHP.debug(el);
						new AgilePHP.XHR().updaterHandler(xhr, el);
					}
				}
			} else {

				AgilePHP.debug(xhr);
				AgilePHP.debug(el);
				new AgilePHP.XHR().updaterHandler(xhr, el);
			}
		},

		/**
		 * Performs an el.innerHTML update with XHR responseText
		 * response
		 * 
		 * @param {Object} o The XMLHttpRequest response object which
		 *        contains the responseText
		 * @param {Element} el The HTML element which gets its
		 *        .innerHTML attribute set with the responseText
		 * @return void
		 * @throws If the responseText attribute does not exist
		 */
		this.updaterHandler = function(o, el) {

			try {
				document.getElementById(el).innerHTML = o.responseText;
			} catch (e) {
				AgilePHP.debug(e);
				throw e;
			}
		},

		/**
		 * Performs an AJAX form submit. The following HTML elements
		 * contained in the specified form will be captured: 1) input
		 * type="text" 2) input type="password" 3) input type="checkbox"
		 * 4) input type="radio" 5) select
		 * 
		 * @param {string} url The url the form is posted to
		 * @param {Element} el The HTML form element to submit
		 * @param {function} callback The callback function or object to
		 *        invoke with the XHR result
		 * @return void
		 */
		this.formSubmit = function(url, form, callback) {

			var data = '';

			// Standard input form elements
			for(var i = 0; i < form.getElementsByTagName('input').length; i++) {

				if(form.getElementsByTagName('input')[i].type == 'text')
					data += form.getElementsByTagName('input')[i].name + '='
							+ form.getElementsByTagName('input')[i].value + '&';

				if(form.getElementsByTagName('input')[i].type == 'password')
					data += form.getElementsByTagName('input')[i].name + '='
							+ form.getElementsByTagName("input")[i].value + '&';

				if(form.getElementsByTagName('input')[i].type == 'checkbox') {

					if (form.getElementsByTagName('input')[i].checked)
						data += form.getElementsByTagName('input')[i].name + '='
								+ form.getElementsByTagName('input')[i].value + '&';
					else
						data += form.getElementsByTagName('input')[i].name + '=&';
				}
				if(form.getElementsByTagName('input')[i].type == 'radio') {

				   if(form.getElementsByTagName('input')[i].checked) {

					  data += form.getElementsByTagName('input')[i].name + '='
								+ form.getElementsByTagName('input')[i].value + '&';
					}
				}

				// Set AgilePHP RequestScope anti-CSFR token if present
				if(form.getElementsByTagName('input')[i].type == 'hidden')
					if(form.getElementsByTagName('input')[i].name == 'AGILEPHP_REQUEST_TOKEN')
						this.setRequestToken(form.getElementsByTagName('input')[i].value);
			}

			// select elements
			for(var i = 0; i < form.getElementsByTagName('select').length; i++) {

				var index = form.getElementsByTagName('select')[i].selectedIndex;
				data += form.getElementsByTagName('select')[i].name + '='
						+ form.getElementsByTagName('select')[i].options[index].value + '&';
			}

			// textarea elements
			for(var i = 0; i < form.getElementsByTagName('textarea').length; i++)
				data += form.getElementsByTagName('textarea')[i].name + '='
						+ form.getElementsByTagName('textarea')[i].value + '&';

			data = data.substring(0, data.length - 1);
			if (callback == undefined || callback == null) {

				this.setSynchronous(true);
				return this.post(url, data);
			}

			this.post(url, data, callback);
		};
	},

	/**
	 * Allows easy interaction with the AgilePHP MVC system using AJAX.
	 */
	MVC: {

		controller: 'IndexController',
		action: 'index',
		parameters: [],

		/**
		 * Sets the name of the MVC controller to send the request
		 * 
		 * @param {String} controller The name of the controller to send the request
		 * @return void
		 */
		setController: function(controller) {
			AgilePHP.MVC.controller = controller;
		},

		/**
		 * Returns the name of the MVC controller handling the request
		 * 
		 * @return {String} The MVC controller currently handling requests
		 */
		getController: function() {
			return AgilePHP.MVC.controller;
		},

		/**
		 * Sets the name of the controllers action method to invoke
		 * 
		 * @param {String} action The name of the controllers action method to invoke
		 * @return void
		 */
		setAction: function(action) {
			AgilePHP.MVC.action = action;
		},

		/**
		 * Returns the name of the controller action method currently being invoked
		 * 
		 * @return {String} The action name
		 */
		getAction: function() {

			return AgilePHP.MVC.action;
		},

		/**
		 * Sets the parameters which are passed into the invoked method
		 * 
		 * @param {Array} params An array of parameters to pass into the controller
		 *            action method
		 * @return void
		 */
		setParameters: function(params) {

			if (typeof params == 'Array') {

				AgilePHP.MVC.parameters = params.join('/');
				return;
			}

			AgilePHP.MVC.parameters = params;
		},

		/**
		 * Executes the AJAX request to the controller/method/parameters which
		 * are presently set within the AgilePHP.MVC object.
		 * 
		 * @param {function} callback The callback function to execute after the XHR
		 *        request has completed
		 * @return If no callback function is supplied, the call is treated
		 *         synchronously and the result is returned.
		 */
		processRequest: function(callback) {

			var url = AgilePHP.getRequestBase() + '/' + this.getController()
					+ '/' + this.getAction();

			if (this.parameters.length)
				url += '/' + this.parameters.join('/');

			if (callback != undefined)
				new AgilePHP.XHR().request(url, callback)

			else {

				var xhr = new AgilePHP.XHR();
				xhr.setSynchronous(true);

				return xhr.request(url);
			}
		}
	},

	/**
	 * Allows remoting PHP objects from the client
	 */
	Remoting: {

		classes: [],
		controller: null,
		transport: 'xhr',
		endpoint: 'localhost:4020/agilephp',

		/**
		 * Sets the name of the remoting controller
		 * 
		 * @param {String} controller The name of the remoting controller
		 * @return void
		 */
		setController: function(controller) {
			AgilePHP.Remoting.controller = controller;
		},

		/**
		 * Returns the name of the remoting controller
		 * 
		 * @return The controller name
		 */
		getController: function() {
			return AgilePHP.Remoting.controller;
		},

		/**
		 * Sets the transport used to communicate with the Remote service
		 * 
		 * @param {String} transport The transport mechanism used for communication
		 *        (XHR|WebSocket)
		 * @return void
		 */
		setTransport: function(transport) {
			AgilePHP.Remoting.transport = transport.toLowerCase();
		},

		/**
		 * Gets the transport mechanism used for communication
		 * 
		 * @return {String} The transport mechanism used for communication
		 *         (xhr|websocket)
		 */
		getTransport: function() {
			return AgilePHP.Remoting.transport.toLowerCase();
		},

		/**
		 * Invokes a server side PHP object.
		 * 
		 * @param {object} stub The client side Stub instance respresenting a
		 *        remote PHP object.
		 * @param {string} method The name of the remote method to invoke
		 * @param {array} parameters An array containing the arguments/parameters to
		 *         pass into
		 * @return mixed Void if asynchronous (call will be executed), otherwise
		 *         the eval'd response from the service
		 */
		invoke: function(stub, method, parameters) {

			AgilePHP.debug('AgilePHP.Remoting.invoke');
			AgilePHP.debug(stub);
			AgilePHP.debug(method);
			AgilePHP.debug(parameters);

			var parameters = Array.prototype.slice.call(parameters);
			var exceptionHandler = null;
			var callback = null;
			var clazz = stub._class
			var constructorArgs = [];

			// Capture constructor arguments from the service stub
			for(var field in stub)
				if(stub.hasOwnProperty(field))
				   constructorArgs.push(stub[field]);

			// Exception handler is defined
			if(typeof parameters[parameters.length - 1] == 'function'
					&& ((parameters.length >= 2) && typeof parameters[parameters.length - 2] == 'function')) {

				exceptionHandler = parameters.pop();
				callback = parameters.pop();
			}
			// Just the callback is defined
			else if(typeof parameters[parameters.length - 1] == 'function') {

				callback = parameters.pop();
			}

			// Server doesnt care about this - only needed to keep track of stubs
			delete stub._class;

			// Create the HTTP data string for submission to the server-side Remoting component
			var data = 'class=' + clazz + '&method=' + method + '&constructorArgs=' + escape(JSON.stringify(constructorArgs));

			// Marshall parameters - Set a _class field on all function object parameters / models
			//if(parameters) data += '&parameters=' + escape(JSON.stringify(AgilePHP.Remoting.marshall(parameters)));
			if(parameters) data += '&parameters=' + encodeURIComponent(JSON.stringify(parameters));

			// Send the data to the server
			return AgilePHP.Remoting._send(data, callback, exceptionHandler);
		},

		/**
		 * Sends the remoting request to the server using one of the following
		 * supported transports: 1) XHR - Asynchronous XMLHttpRequest 2)
		 * WebSocket - "HTML 5" WebSocket API
		 * 
		 * @param {String}
		 *            data JSON serializes data to submit to the server
		 * @param {function}
		 *            callback Response callback handler
		 * @return mixed void if asynchronous (no callback defined), otherwise
		 *         the eval'd server response.
		 */
		_send: function(data, callback, exceptionHandler) {

			// WebSocket Transport
			if(AgilePHP.Remoting.getTransport() == 'websocket') {

				if('WebSocket' in window == false) {

					var error = 'WebSocket API not supported!';
					(exceptionHandler != null) ? exceptionHandler(error) : AgilePHP.debug(error);
					return false;
				}
				if(callback == undefined) {

					var error = 'WebSocket transport requires a defined callback. Aborting...';
					(exceptionHandler != null) ? exceptionHandler(error)
							: AgilePHP.debug(error);
					return false;
				}
				var ws = new WebSocket('ws://' + AgilePHP.Remoting.endpoint);
				ws.onopen = function() {
					ws.send(data);
				};
				ws.onmessage = function(response) {
					callback(AgilePHP.Remoting.unmarshall(response));
				};
				ws.onclose = function() {
					AgilePHP.debug('WebSocket Closed');
				};
				return;
			}

			// XHR Transport
			var url = (AgilePHP.Remoting.controller == null) ? AgilePHP.Remoting.endpoint :
							AgilePHP.getRequestBase() + '/'	+ AgilePHP.Remoting.controller + '/invoke';

			var async = typeof callback == 'function';
			var xhr = new AgilePHP.XHR(async).createRequest();
			xhr.open('POST', url, async);
			xhr.setRequestHeader('X-Powered-By', 'AgilePHP Remoting Framework');
			xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			xhr.setRequestHeader('Content-length', data.length);
			xhr.setRequestHeader('Connection', 'close');
			xhr.send(data);

			if (async) {

				xhr.onreadystatechange = function() {

					if(xhr.readyState == 4) {

						var response = (xhr.responseText.length) ? eval('(' + xhr.responseText + ')') : null;
						AgilePHP.debug(response);

						// Catch exceptions
						for(var property in response) {
							if(property == '_class' && response._class.indexOf('Exception') !== -1) {
								if(exceptionHandler) exceptionHandler(response);
								   return;
							}
						}
						if(callback) callback(AgilePHP.Remoting.unmarshall(response));
					}
				}
			} else {

				var response = (xhr.responseText.length) ? eval('(' + xhr.responseText + ')') : null;
				AgilePHP.debug(response);

				return AgilePHP.Remoting.unmarshall(response);
			}
		},

		/**
		 * Checks to see if the specified class already has a stub loaded
		 * 
		 * @param {String}
		 *            clazz The remote class name
		 * @return Boolean True if the stub has already been loaded, false
		 *         otherwise
		 */
		isLoaded: function(clazz) {
			return AgilePHP.Remoting.classes[clazz] == true;
		},

		/**
		 * Loads a remoting stub for the specified class
		 * 
		 * @param {String}
		 *            The remote class name
		 * @return void
		 */
		load: function(clazz) {

			if(!AgilePHP.Remoting.classes[clazz]) {

				AgilePHP.Remoting.classes[clazz] = true;
				AgilePHP.loadScript(AgilePHP.getRequestBase() + '/' + AgilePHP.Remoting.getController() + '/index/' + clazz);
			}
		},

		/**
		 * Merges a config object with the specified model. This allows the JS
		 * developer to override constructor property initialization as well as
		 * define their own custom properties.
		 * 
		 * @param {Object} model The model to merge with the config
		 * @param {Object} config The config to merge onto the model
		 * @return {Object} The merged model
		 */
		merge: function(model, config) {

			for(var prop in config)
				if(config[prop] !== null)
				   model[prop] = config[prop];

			return model;
		},

		/**
		 * Recursively adds a _class field to any function objects / models.
		 * The function name is used as the model name value.
		 * 
		 * @param {array} parameters An array of parameters to marshall
		 * @return {array} The marshalled parameters
		 *
		marshall: function(parameters) {

			// Store changes locally so references are not effected
			var newParams = [];

			// Loop over all of the parameters, adding add a _class field
			// to models so they can be unmarshalled to a native object on the
			// server.
			for(var i = 0; i < parameters.length; i++) {

				var param = parameters[i];
				if(typeof param == 'object') {

					// array or actual object?
					if(AgilePHP.Remoting.Parser.isArray(param)) {

						// array - perform recursive marshall operation on the
						// parameter
						newParams[i] = AgilePHP.Remoting.marshall(param);
					}
					else { // object - recursively set the _class field

						if(!newParams[i]) newParams[i] = {};

						for(var field in param)
							if(param.hasOwnProperty(field))
							   newParams[i][field] = (param[field] !== null && param[field] !== undefined) ?
									AgilePHP.Remoting.marshall([ param[field] ])[0] : null;

						newParams[i]._class = AgilePHP.Remoting.Parser.getModelName(param);
					}
				}
				else
					// Leave the parameter as is
					newParams[i] = param;
			}

			return newParams;
		},*/

		/**
		 * Unmarshalls the server repsonse into a native JavaScript object / model
		 * 
		 * @param {Object} response The server response
		 * @return mixed The unmarshalled response
		 */
		unmarshall : function(response) {

			if (typeof response == 'object') {

				// array or actual object?
				if (AgilePHP.Remoting.Parser.isArray(response)) {

					response = AgilePHP.Remoting.unmarshallArray(response);
				}
				else { // object - unwrap to native JS model if possible

					for(var field in response) {

						if(field == '_class') {

						   if(response._class in window !== false) {

							  var model = new window[response._class];

							  delete response._class;
							  response = AgilePHP.Remoting.merge(model, response);
							}
						}
					}
				}
			}
			// else leave the response as is

			return response;
		},

		/**
		 * Unmarshalls an array
		 *  
		 * @param {array} theArray The array to unmarshall
		 * @return {mixed} The unmarshalled response
		 */
		unmarshallArray: function(theArray) {

			var retval = [];

			for(var i=0; i<theArray.length; i++) {

				if(typeof theArray[i] == 'object') {

					if(AgilePHP.Remoting.Parser.isArray(theArray[i])) {

					   retval.push(AgilePHP.Remoting.unmarshallArray(theArray[i]));
					}
				    else
				    	retval.push(AgilePHP.Remoting.unmarshall(theArray[i]));
				}
				else
					retval.push(theArray[i]);
			}

			return retval;
		},

		/**
		 * Handles JS model parsing
		 */
		Parser: {

			/**
			 * Retrieves the model name from the specified function object by
			 * parsing the function name.
			 * 
			 * @param {Object} The function object to parse
			 * @return {string} The model / function name
			 */
			getModelName: function(o) {

				var regex = /function (.{1,})\(/;
				var results = (regex).exec((o).constructor.toString());
				return (results && results.length > 1) ? results[1] : null;
			},
			
			isArray: function(o) {

				return o.constructor.toString().match(/array/i) !== null;
			}
		}
	}
}