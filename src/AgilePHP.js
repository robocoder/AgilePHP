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

		author : 'Jeremy Hahn',
		copyright: 'Make A Byte, inc.',
		version : '0.1a',
		licence : 'GNU General Public License',
		package : 'com.makeabyte.agilephp',

		requestBase : '/index.php',
		debugMode : false,
		
		/**
		 * Sets the path relative to DocumentRoot which handles application
		 * requests. Defaults to /index.php.
		 * 
		 * @param path {String} The request base path
		 * @return void
		 */
		setRequestBase : function( path ) {

			this.requestBase = path;
		},

		/**
		 * Returns the base path which handles application requests.
		 * 
		 * @return The AgilePHP request base (the page that handles application requests)
		 */
		getRequestBase : function() {
			
			return this.requestBase;
		},

		/**
		 * When set to true, this causes the AgilePHP client JavaScript routines to
		 * output debugging information. This also enables the use of AgilePHP.debug.
		 * 
		 * @param boolean {Boolean} True to enable debugging, false to disable
		 * @return void
		 */
		setDebug : function( boolean ) {

			this.debugMode = boolean;
		},

		/**
		 * Tells whether or not the AgilePHP client side JavaScript is in debug mode
		 * 
		 * @return {Boolean} True if AgilePHP client side JavaScript is in debugging mode, false otherwise
		 */
		isInDebugMode : function() {

			return this.debugMode == true;
		},

		/**
		 * Performs an HTTP GET request (using location.href) for the specified url
		 * 
		 * @param url {String} The url to load
		 * @return void
		 */
		go : function( url ) {

			location.href = url;
		},

		/**
		 * Adds a new <script></script> tag to the head of the HTML document
		 * and adds the specified file to the src attribute value.
		 * 
		 * @param file {String} The file to set as the value to the script src attribute
		 * @return void
		 */
		loadScript : function( file )  {

			   var head= document.getElementsByTagName( 'head' )[0];
		       var script= document.createElement( 'script' );
		       script.type= 'text/javascript';
		       script.src= file;
		       head.appendChild( script );
		},

		/**
		 * Writes debugging messages to firebug console
		 * 
		 * @param msg {Mixed} Write the specified message to the firebug console
		 * @return void
		 */
		debug : function( msg ) {

			try {
				  if( this.isInDebugMode() )
					  console.log( msg );
			}
			catch( e ) { }
		},

		/**
		 * Handles client side JavaScript events in regards to Persistence framework
		 */
		Persistence : {

			    /**
			     * This is fired when a user clicks a delete action link on a database record
			     * when rendered using the XSLT controller.
			     */
				confirmDelete : function( requestBase, value, page, controller, action ) {

			         var decision = confirm( 'Are you sure you want to delete this record?' );
			         if( decision === true )
				         location.href = requestBase + '/' + controller + '/' +  action + '/' + value + '/' + page;
				},

				/**
				 * Used to highlight a database table row on mouseover
				 */
				setStyle : function( el, style ) {

						 el.setAttribute( 'class', style );
				}
		},

		/**
		 * AgilePHP XMLHttpRequest object, basis for all AJAX calls...
		 */
		XHR : function() {

			this.instance = null;
			this.isAsync = true;
			this.requestToken = null;

			this.MS_PROGIDS = new Array(
				 "Msxml2.XMLHTTP.7.0",
				 "Msxml2.XMLHTTP.6.0",
				 "Msxml2.XMLHTTP.5.0",
				 "Msxml2.XMLHTTP.4.0",
				 "MSXML2.XMLHTTP.3.0",
				 "MSXML2.XMLHTTP",
				 "Microsoft.XMLHTTP"
			);

			/**
			 * Returns a singleton instance of the XMLHttpRequest object
			 */
			this.getInstance = function() {

				 if( this.instance == null ) {

				     if( window.XMLHttpRequest != null )
				    	 this.instance = new window.XMLHttpRequest();
	
				     else if( window.ActiveXObject != null ) {
	
				    	  for( var i=0; i<this.MS_PROGIDS.length && !obj; i++ ) {
	
				    		   try {
				    			     this.instance = new ActiveXObject( this.MS_PROGIDS[i] );
				    		   }
				    		   catch( ex ) {}
				    	  }
				     }
	
				     if( this.instance == null ) {
	
				    	 var msg = 'Could not create XHR object.';
				    	 AgilePHP.debug( msg );
				    	 throw msg;
				     }
				 }

			     return this.instance;
			},

			/**
			 * Sets the XMLHttpRequest to asynchronous communication mode. This
			 * is the default (since AJAX is AsynchronousJAX...).
			 */
			this.setAsynchronous = function() {
				
				this.isAsync = true;
			},

			/**
			 * Sets the XMLHttpRequest to synchronous communication mode. You should
			 * not use this unless you really have no other choice since it
			 * leaves your clients browser waiting on the reply.
			 */
			this.setSynchronous = function() {

				this.isAsync = false;
			},

			/**
			 * Sets the AgilePHP RequestScope token that guards against CSFR attacks
			 * 
			 * @param token {string} The anti-CSFR token
			 * @return void
			 */
			this.setRequestToken = function( token ) {

				 this.requestToken = token;
			},

			/**
			 * Returns the AgilePHP RequestScope token used to guard against CSFR attacks
			 * 
			 * @return AgilePHP RequestScope anti-CSFR token
			 */
			this.getRequestToken = function() {

				 return this.requestToken;
			},

			/**
			 * Evaluates an XMLHttpRequest response object and returns the result
			 * 
			 * @param xhr {XMLHttpRequest} The XMLHttpRequest object to evaluate
			 * @return The evaluation result
			 */
			this.eval = function( xhr ) {

				return eval( '(' + xhr.responseText + ')' );
			},

			/**
			 * Creates an XMLHttpRequest GET request to the server
			 * 
			 * @param url {String} The url to send the request
			 * @param callback {function} The callback function to execute
			 * @return The XMLHttpRequest response object if XHR.setSynchronous was called. Otherwise
			 * 		   the callback is executed when the request has completed.
			 */
			this.request = function( url, callback ) {

				 var xhr = this.getInstance();

				 xhr.open( 'GET', url, this.isAsync );
				 xhr.setRequestHeader( 'X-Powered-By', 'AgilePHP Framework' );
				 xhr.send( null );

				 if( this.isAsync ) {

					 xhr.onreadystatechange = function() {
	
						 if( xhr.readyState == 4 ) {
	
							 AgilePHP.debug( xhr );
							 callback( xhr );
						 }
					}
				 }
				 else {

					 AgilePHP.debug( xhr );
					 return xhr;
				 }
			},

			/**
			 * Creates an XMLHttpRequest POST request to the server
			 * 
			 * @param url {String} The url to send the request
			 * @param data {String} The data to send to the server (ex: var1=val1&var2=val3...)
			 * @param callback {function} The callback function to execute when the call has completed
			 * @return The XMLHttpRequest response object if XHR.setSynchronous was called. Otherwise
			 * 		   the callback is executed when the request has completed.
			 */
			this.post = function( url, data, callback ) {

				  // Add AgilePHP RequestScope anti-CSFR token to POST requests if present
				  if( this.getRequestToken() )
					  data = 'AGILEPHP_REQUEST_TOKEN=' + this.getRequestToken() + '&' + data;

				  var xhr = this.getInstance();

				  xhr.open( 'POST', url, this.isAsync );
				  xhr.setRequestHeader( 'X-Powered-By', 'AgilePHP Framework' );
				  xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
				  xhr.setRequestHeader( 'Content-length', data.length );
				  xhr.setRequestHeader( 'Connection', 'close' );
				  xhr.send( data );

				  if( this.isAsync ) {

						 xhr.onreadystatechange = function() {
		
							 if( xhr.readyState == 4 ) {

								 AgilePHP.debug( xhr );
								 callback( xhr );
							 }
						}
				  }
				  else {

						 AgilePHP.debug( xhr );
						 return xhr;
				  }
			},

			/**
			 * Creates an XMLHttpRequest GET request to the server and sets the specified el.innerHTML
			 * with the responseText of the request.
			 * 
			 * @param url {String} The url to send the request
			 * @param el {Object} The HTML element to set the .innerHTML value with the XMLHttpRequest responseText
			 * @return void
			 */
			this.updater = function( url, el ) {

				 var xhr = this.getInstance();
				 xhr.open( 'GET', url, this.isAsync );
				 xhr.setRequestHeader( 'X-Powered-By', 'AgilePHP Framework' );
				 xhr.send( null );

				 if( this.isAsync ) {

					 xhr.onreadystatechange = function() {
	
						 if( xhr.readyState == 4 ) {
	
							 AgilePHP.debug( xhr );
							 AgilePHP.debug( el );
							 new AgilePHP.XHR().updaterHandler( xhr, el );
						 }
					}
				 }
				 else {

					 AgilePHP.debug( xhr );
					 AgilePHP.debug( el );
					 new AgilePHP.XHR().updaterHandler( xhr, el );
				 }
			},

			/**
			 * Performs an el.innerHTML update with XHR responseText response
			 * 
			 * @param o {Object} The XMLHttpRequest response object which contains the responseText
			 * @param el {Object} The HTML element which gets its .innerHTML attribute set with the responseText
			 * @return void
			 * @throws If the responseText attribute does not exist
			 */
			this.updaterHandler = function( o, el ) {

				 try {
					   document.getElementById( el ).innerHTML = o.responseText;
				 }
				 catch( e ) {

					 AgilePHP.debug( e );
					 throw e;
				 }
			},

			/**
			 * Performs an AJAX form submit. The following HTML elements contained in the specified
			 * form will be captured:
			 * 1) input type="text"
			 * 2) input type="password"
			 * 3) input type="checkbox"
			 * 4) input type="radio"
			 * 5) select
			 * 
			 * @param url {string} The url the form is posted to
			 * @param el {Object} The HTML form element to submit
			 * @param callback {function} The callback function or object to invoke with the XHR result
			 * @return void
			 */
			this.formSubmit = function( url, form, callback ) {

				 var data = '';

				 // Standard input form elements
				 for( var i=0; i<form.getElementsByTagName( 'input' ).length; i++ ) {

					  if( form.getElementsByTagName( 'input' )[i].type == 'text')
				          data += form.getElementsByTagName( 'input' )[i].name + '=' + 
				          	form.getElementsByTagName( 'input' )[i].value + '&';
					  
					  if( form.getElementsByTagName( 'input' )[i].type == 'password')
				          data += form.getElementsByTagName( 'input' )[i].name + '=' + 
				          	form.getElementsByTagName("input")[i].value + '&';

				      if( form.getElementsByTagName( 'input' )[i].type == 'checkbox' ) {
				           
				    	  if( form.getElementsByTagName( 'input' )[i].checked )
				              data += form.getElementsByTagName( 'input' )[i].name + '=' + 
				              	form.getElementsByTagName( 'input' )[i].value + '&';
				    	  else
				              data += form.getElementsByTagName( 'input' )[i].name + '=&';
			          }
				      if( form.getElementsByTagName( 'input' )[i].type == 'radio' ) {

				    	  if( form.getElementsByTagName( 'input' )[i].checked ) {

				    		  data += form.getElementsByTagName( 'input' )[i].name + '=' + 
				    		  		  form.getElementsByTagName( 'input' )[i].value + '&';
				          }
				     }

				     // Set AgilePHP RequestScope anti-CSFR token if present 
				     if( form.getElementsByTagName( 'input' )[i].type == 'hidden' ) {

				    	 if( form.getElementsByTagName( 'input' )[i].name == 'AGILEPHP_REQUEST_TOKEN' )
				    		 this.setRequestToken( form.getElementsByTagName( 'input' )[i].value );
				     }
				 }

				 // select elements
				 for( var i=0; i<form.getElementsByTagName( 'select' ).length; i++ ) {

					  var index = form.getElementsByTagName( 'select' )[i].selectedIndex;
						  data += form.getElementsByTagName( 'select' )[i].name + '=' +
					  	      		form.getElementsByTagName( 'select' )[i].options[index].value + '&';
				 }

			 	 // textarea elements
				 for( var i=0; i<form.getElementsByTagName( 'textarea' ).length; i++ )
					  data += form.getElementsByTagName( 'textarea' )[i].name + '=' + 
   		  		  				form.getElementsByTagName( 'textarea' )[i].value + '&';

				 data = data.substring( 0, data.length-1 );
				 if( callback == undefined || callback == null ) {

					 this.setSynchronous( true );
					 return this.post( url, data );
				 }

				 this.post( url, data, callback );
			}
		},

		/**
		 * Allows easy interaction with the AgilePHP MVC system using AJAX.
		 */
		MVC : {

			controller : 'IndexController',
			action : 'index',
			parameters : [],

			/**
			 * Sets the name of the MVC controller to send the request
			 * 
			 * @param controller {String} The name of the controller to send the request
			 * @return void
			 */
			setController : function( controller ) {
				
				AgilePHP.MVC.controller = controller;
			},

			/**
			 * Returns the name of the MVC controller handling the request
			 * 
			 * @return The MVC controller currently handling requests
			 */
			getController : function() {
				
				return AgilePHP.MVC.controller;
			},

			/**
			 * Sets the name of the controllers action method to invoke
			 * 
			 * @param action {String} The name of the controllers action method to invoke
			 * @return void
			 */
			setAction : function( action ) {
				
				AgilePHP.MVC.action = action;
			},

			/**
			 * Returns the name of the controller action method currently being invoked
			 * 
			 * @return The name of the controller action method currently being invoked
			 */
			getAction : function() {
				
				return AgilePHP.MVC.action;
			},

			/**
			 * Sets the parameters which are passed into the invoked method
			 * 
			 * @param params {Array} An array of parameters to pass into the controller action method
			 * @return void
			 */
			setParameters : function( params ) {

				if( typeof params == 'Array' ) {

					AgilePHP.MVC.parameters = params.join( '/' );
					return;
				}

				AgilePHP.MVC.parameters = params;
			},

			/**
			 * Executes the AJAX request to the controller/method/parameters which are presently
			 * set within the AgilePHP.MVC object.
			 * 
			 * @param callback {function} The callback function to execute after the XHR request has completed
			 * @param arguments {String} A string of arguments delimited by a forward slash '/'
			 * @return If no callback function is supplied, the call is treated synchronously and the result is returned. 
			 */
			processRequest : function( callback ) {

				    var url = AgilePHP.getRequestBase() + '/' + this.getController() + '/' + this.getAction();

				    if( this.parameters.length )
				    	url += '/' + this.parameters.join( '/' );

				    if( callback != undefined )
				    	new AgilePHP.XHR().request( url, callback )

				    else {

				    	var xhr = new AgilePHP.XHR();
				    	xhr.setSynchronous( true );
				    	return xhr.request( url );
				    }
			}
		},

		/**
		 * Allows remoting PHP objects from the client
		 */
		Remoting : {

			stubs : [],
			controller : null,

			/**
			 * Sets the name of the remoting controller
			 * 
			 * @param controller {String} The name of the remoting controller
			 * @return void
			 */
			setController : function( controller ) {

				this.controller = controller;
			},

			/**
			 * Returns the name of the remoting controller
			 * 
			 * @return The name of the remoting controller
			 */
			getController : function() {

				return this.controller;
			},

			/**
			 * Returns the client side stub responsible for a remote communication
			 * with a PHP object.
			 * 
			 * @param clazz {String} The name of the class to retrieve the Stub for.
			 * @return {Object} The client side remoting Stub
			 */
			getStub : function( clazz ) {
			
				for( var i=0; i<this.stubs.length; i++ )
					if( this.stubs[i].clazz == clazz )
						return this.stubs[i];
			},

			/**
			 * Constructor for a client side Stub, responsible for handling
			 * remoting calls to a server side PHP object.
			 * 
			 * @param {string} clazz The name of the server side PHP class to remote
			 */
			Stub : function( clazz ) {

				this.clazz = clazz;
				this.callback = null;

				/**
				 * Sets the callback function which gets executed when the remoting call is complete
				 * 
				 * @param {function} callback The callback function to execute when the remoting
				 * 							  AJAX request has completed
				 */
				AgilePHP.Remoting.Stub.prototype.setCallback = function( callback ) {

					this.callback = callback;
				};

				/**
				 * Invokes a server side PHP object.
				 * 
				 * @param method {string} The name of the remote method to invoke
				 * @param parameters {array} An array containing the arguments/parameters to pass into
				 * 							 the remote method.
				 * @param instance {object} The client side Stub instance responsible for remoting to the
				 * 							server side PHP object. The Stub state is used
				 * 							to construct the remote object.
				 */
				AgilePHP.Remoting.Stub.prototype.invoke = function( method, parameters, instance ) {

					 AgilePHP.debug( 'AgilePHP.Remoting.Stub( \'' + this.clazz + '\' ).invoke' );
					 AgilePHP.debug( method );
					 AgilePHP.debug( parameters );
					 AgilePHP.debug( instance );

					 var url = AgilePHP.getRequestBase() + '/' + AgilePHP.Remoting.controller + '/invoke';
					 var data = 'class=' + this.clazz + '&method=' + method + '&stub=' + (instance != undefined ? JSON.stringify( instance ) : '');

					 if( parameters != undefined ) {

						 var o = new Object();
						 for( var i=0; i<parameters.length; i++ )
							  o[ 'argument' + (i+1) ] = parameters[i];

						 data += '&parameters=' + JSON.stringify( o );
					 }

					 if( this.callback == undefined ) {
						 
						 var xhr = new AgilePHP.XHR();
						 xhr.setSynchronous( true );
						 return new xhr.post( url, data );
					 }
					 else
						 new AgilePHP.XHR().post( url, data, this.callback );
				};

				/**
				 * Invokes a stateful instance of a server side PHP object. AgilePHP stores objects
				 * invoked by this method in the SessionScope state.
				 *  
				 * @param method {string} The name of the remote method to invoke
				 * @param parameters {array} An array containing the arguments/parameters to pass into
				 * 							 the remote method.
				 * @param instance {object} The client side Stub instance responsible for remoting to the
				 * 							server side PHP object. The Stub state is used
				 * 							to construct the remote object.
				 */
				AgilePHP.Remoting.Stub.prototype.invokeStateful = function( method, parameters, instance ) {

					 AgilePHP.debug( 'AgilePHP.Remoting.Stub( \'' + this.clazz + '\' ).invokeStateful' );
					 AgilePHP.debug( method );
					 AgilePHP.debug( parameters );
					 AgilePHP.debug( instance );

					 var url = AgilePHP.getRequestBase() + '/' + AgilePHP.Remoting.controller + '/invokeStateful';
					 var data = 'class=' + this.clazz + '&method=' + method + '&stub=' + (instance != undefined ? JSON.stringify( instance ) : '');

					 if( parameters != undefined ) {

						 var o = new Object();
						 for( var i=0; i<parameters.length; i++ )
							  o[ 'argument' + (i+1) ] = parameters[i];

						 data += '&parameters=' + JSON.stringify( o );
					 }

					 if( this.callback == undefined ) {

						 var xhr = new AgilePHP.XHR();
						 xhr.setSynchronous( true );
						 return xhr.post( url, data );
					 }
					 else
						 new AgilePHP.XHR().post( url, data, this.callback );
				};

				/**
				 * Invokes a server side PHP object which has been intercepted. Intercepted classes are renamed
				 * and the InterceptorProxy is given the name of the intercepted class. This call goes around
				 * the proxy to examine the annotations in the remote class to determine the #@RemoteMethod
				 * calls which can be invoked.
				 * 
				 * @param method {string} The name of the remote method to invoke
				 * @param parameters {array} An array containing the arguments/parameters to pass into
				 * 							 the remote method.
				 * @param instance {object} The client side Stub instance responsible for remoting to the
				 * 							server side PHP object. The Stub state is used
				 * 							to construct the remote object.
				 */
				AgilePHP.Remoting.Stub.prototype.invokeIntercepted = function( method, parameters, instance ) {

					 AgilePHP.debug( 'AgilePHP.Remoting.Stub( \'' + this.clazz + '\' ).invokeIntercepted' );
					 AgilePHP.debug( method );
					 AgilePHP.debug( parameters );
					 AgilePHP.debug( instance );

					 var url = AgilePHP.getRequestBase() + '/' + AgilePHP.Remoting.controller + '/invokeIntercepted';
					 var data = 'class=' + this.clazz + '&method=' + method + '&stub=' + (instance != undefined ? JSON.stringify( instance ) : '');

					 if( parameters != undefined ) {

						 var o = new Object();
						 for( var i=0; i<parameters.length; i++ )
							  o[ 'argument' + (i+1) ] = parameters[i];

						 data += '&parameters=' + JSON.stringify( o );
					 }

					 if( this.callback == undefined ) {
						 
						 var xhr = new AgilePHP.XHR();
						 xhr.setSynchronous( true );
						 return new xhr.post( url, data );
					 }
					 else
						 new AgilePHP.XHR().post( url, data, this.callback );
				};

				/**
				 * Invokes a server side PHP object which has been intercepted within a stateful SessionScope context.
				 * Intercepted classes are renamed and the InterceptorProxy is given the name of the intercepted class.
				 * This call goes around the proxy to examine the annotations in the remote class to determine the
				 * #@RemoteMethod calls which can be invoked.
				 * 
				 * @param method {string} The name of the remote method to invoke
				 * @param parameters {array} An array containing the arguments/parameters to pass into
				 * 							 the remote method.
				 * @param instance {object} The client side Stub instance responsible for remoting to the
				 * 							server side PHP object. The Stub state is used
				 * 							to construct the remote object.
				 */ 
				AgilePHP.Remoting.Stub.prototype.invokeInterceptedStateful = function( method, parameters, instance ) {
					
					 AgilePHP.debug( 'AgilePHP.Remoting.Stub( \'' + this.clazz + '\' ).invokeInterceptedStateful' );
					 AgilePHP.debug( method );
					 AgilePHP.debug( parameters );
					 AgilePHP.debug( instance );

					 var url = AgilePHP.getRequestBase() + '/' + AgilePHP.Remoting.controller + '/invokeInterceptedStateful';
					 var data = 'class=' + this.clazz + '&method=' + method + '&stub=' + (instance != undefined ? JSON.stringify( instance ) : '');

					 if( parameters != undefined ) {

						 var o = new Object();
						 for( var i=0; i<parameters.length; i++ )
							  o[ 'argument' + (i+1) ] = parameters[i];

						 data += '&parameters=' + JSON.stringify( o );
					 }

					 if( this.callback == undefined ) {
						 
						 var xhr = new AgilePHP.XHR();
						 xhr.setSynchronous( true );
						 return new xhr.post( url, data );
					 }
					 else
						 new AgilePHP.XHR().post( url, data, this.callback );
				};

				// Push the stub onto the stack
				AgilePHP.Remoting.stubs.push( this );
			}			
		}
}