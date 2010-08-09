<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp.test.control
 */

/**
 * Default test controller used to service client requests. Handles rendering
 * views for simple top level navigation items defined in header.phtml
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 */
class IndexController extends BaseController {

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseModelController#getModel()
	   */
	  public function getModel() {

	  	     return $this->model;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
	  public function index() {

	  	     i18n::getInstance(); // initalizes itself based on http language header
	   		 // i18n::getInstance()->setLocale('es_ES');   language can also be specified manually
	   		 $welcome = i18n::translate('Welcome to the demo application');

	  	     $this->set('title', 'AgilePHP Framework :: Home');
	  	     $this->set('content', $welcome . '. This is the default PHTML renderer.');
	  	     $this->render('index');
	  }

	  /**
	   * Renders the 'about us' page.
	   *
	   * @return void
	   */
	  public function about() {

	  	     $this->set('title', 'AgilePHP Framework :: About');
	  	     $this->render('about');
	  }

	  /**
	   * Renders the 'services' page.
	   *
	   * @return void
	   */
	  public function services() {

	  	     $this->set('title', 'AgilePHP Framework :: Services');
	  	     $this->render('services');
	  }

	  /**
	   * Renders the 'contact us' page.
	   *
	   * @return void
	   */
	  public function contact() {

	  	     $this->set('title', 'AgilePHP Framework :: Contact');
	  	     $this->render('contact');
	  }

	  /**
	   * Handles 'contact us' form submit.
	   *
	   * @return void
	   */
	  public function contactSubmit() {

	  		 $request = Scope::getRequestScope();

	  		 if(!$name = $request->getSanitized('name'))
	  		    throw new FrameworkException('Name Required');

	  		 if(!$email = $request->getSanitized('email'))
	  		    throw new FrameworkException('Email required');

	  		 if(!$comments = $request->getSanitized('comments'))
	  		    throw new FrameworkException('Commentary required');

	  	     $body = 'Name: ' . $name .
	  	     		 "\nEmail: " . $email .
	  	     		 "\nComments: " . $comments;

	  		 try {
	  	     	    Mailer::setToName('Tester');
	  	     	    Mailer::setTo('root@localhost');
	  	     	    Mailer::setFromName('AgilePHP Framework Test Application');
	  	     	    Mailer::setFrom('agilephp@localhost');
	  	     	    Mailer::setSubject('AgilePHP Demo Applicaiton :: Contact Form Submission');
	  	     	    Mailer::setBody($body);
	  	     	    Mailer::send();
  	     	  }
  	     	  catch(FrameworkException $e) {

  	     	  		array_push($failed, $model->getEmail());
  	     	  }

  	     	  $result = 'Thank you, ' . $request->getSanitized('name') . '. We have received your comments.';

  	     	  $this->set('title', 'AgilePHP Framework :: Contact Us');
  	     	  $this->set('formResult', $result);
  	     	  $this->render('contact');
	  }

	  /**
	   * Demonstrates the ability to easily render and process forms
	   *
	   * @return void
	   */
	  public function formExample() {

	  	 	 $user = new User();
	  	 	 $user->setUsername('username');
	  	 	 $user->setPassword('password');
	  	 	 $user->setEmail('root@localhost');
	  	 	 $user->setCreated(date('c', strtotime('now')));
	  	 	 $user->setLastLogin(date('c', strtotime('now')));
	  	 	 $user->setRole(new Role('asdfasdf'));

	  		 $form = new Form($user, 'frmUserExample', 'frmUserExample', 'formExamplePOST', null, null);
	  		 $form->setRequestToken(Scope::getRequestScope()->createToken());

	  		 $this->set('title', 'AgilePHP Framework :: Form Example');
	  		 $this->set('form', $form->getHTML());
	  	     $this->render('form-example');
	  }

	  /**
	   * Shows the array of POST variables and their values.
	   *
	   * @return void
	   */
	  public function formExamplePOST() {

	  		 $params = Scope::getRequestScope()->getParameters();

	  		 $this->set('title', 'AgilePHP Framework :: Form Example - POSTED!');
	  		 $this->set('parameters', $params);
	  		 $this->render('form-example');
	  }

	  /**
	   * Renders the admin PHTML view
	   *
	   * @return void
	   */
	  public function admin() {

	  		 $this->set('title', 'AgilePHP Framework :: Administration');
	  	     $this->render('admin');
	  }

	  /**
	   * Renders the admin PHTML view displaying phpinfo()
	   *
	   * @return void
	   */
	  public function info() {

	  		 $this->set('title', 'AgilePHP Framework :: Administration');
	  		 $this->set('content', phpinfo());
	  	     $this->render('admin');
	  }

	  /**
	   * Sets error variable for PHTML renderer and loads system messages view.
	   *
	   * @param $message The error message to display
	   * @return void
	   */
	  private function handleError($message) {

	  	      $this->set('title', 'AgilePHP Framework :: Error Page');
	  		  $this->set('error', $message);
		  	  $this->render('error');
		  	  exit;
	  }
}
?>