<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009 Make A Byte, inc
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
 * @package com.makeabyte.agilephp
 */

/**
 * NOTE: PDO objects can *NOT* be serialized. Since this component
 * 		 is stored in the HTTP session (serialized), this class
 * 		 can not extend any of the base MVC controllers that store
 * 		 an instance of PDO, nor can it store the PersistenceManager
 * 		 itself!
 * 
 * Includes all identity package dependancies
 */
require_once 'identity/IdentityManager.php';
require_once 'identity/IdentityModel.php';
require_once 'identity/Role.php';

/**
 * AgilePHP :: Identity
 * Provides a means for tracking a users identity throughout the
 * web application. The Identity component automatically handles
 * creating sessions, persistence, and sending emails to deal with
 * password resets.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class Identity implements IdentityManager {

  	  private static $instance;

	  private $model;
	  private $modelName;

	  private $isCustomModel;
	  private $resetPasswordUrl;

	  private $session;

	  private function __construct() {

	  		  // agilephp.xml configuration
	  		  $xml = AgilePHP::getFramework()->getXmlConfiguration();

	  		  if( !$xml->identity )
	  		  	  throw new AgilePHP_Exception( 'Identity component requires a valid component configuration entry in agilephp.xml' );

	  	      if( (string)$xml->identity->attributes()->model ) {

	  	      	  $this->model = new $model();
		  		  $this->modelName = $model;
		  		  Logger::getInstance()->debug( 'Identity::__construct Initalizing domain model object \'' . $this->getModelName() . '\'.' );
	  	      }
	  	      else {

	  	      	  $this->model = new User();
	  	      	  $this->modelName = 'User';
	  	      	  Logger::getInstance()->debug( 'Identity::__construct Initalizing with framework \'User\' domain model object.' );
	  	      }

	  		  $passwordResetUrl = (string)$xml->identity->attributes()->resetPasswordUrl;
	  		  if( $passwordResetUrl )
	  		      $this->resetPasswordUrl = $passwordResetUrl;

	  		  $this->session = Scope::getInstance()->getSessionScope();

	  		  // Initalize Identity from previous session if one exits
      		  if( $username = $this->session->get( 'IDENTITY_USERNAME' ) ) {

	  		  	  $this->model->setUsername( $username );
	  		  	  $pm = new PersistenceManager();
	 		  	  $this->model = $pm->find( $this->model ); 

	 		  	  if( $this->model->getRoles() ) {

	      		  	  foreach( $this->model->getRoles() as $Role ) {
	
		  		 		  if( $this->getModel()->getRoleId() == $Role->getName() ) {
	
		  		 		  	  $this->setRole( $Role );
		  		 		  	  break;
		  		 		  }
		  		 	 }
	 		  	  }
	 		  	  $this->getModel()->setSession( $this->session->getSession() );
      		  }
	  }

	  /**
	   * Returns a singleton instance of the 'Identity' component.
	   *  
	   * @return void
	   */
	  public static function getInstance() {

			 if( self::$instance == null )
		 	 	 self::$instance = new self;

	  	     return self::$instance;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#setModel($model)
	   */
	  public function setModel( $model ) {

	  		 $this->model = $model;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#getModel()
	   */
	  public function getModel() {

	  		 return $this->model;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#setUsername($username)
	   */
	  public function setUsername( $username ) {

	  		 $this->getModel()->setUsername( $username );
	  }

	  /**
	   * Sets encrypted/hashed password according to the configuration of the 'Crypto'
	   * component.
	   * 
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#setPassword($password)
	   */
	  public function setPassword( $password ) {

	  		 $hashedPassword = Crypto::getInstance()->getDigest( $password );
	  		 $this->getModel()->setPassword( $hashedPassword );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#getUsername()
	   */
	  public function getUsername() {

	  		 return $this->getModel()->getUsername();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#getPassword()
	   */
	  public function getPassword() {

	  		 return $this->getModel()->getPassword();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#setEmail($email)
	   */
	  public function setEmail( $email ) {

	  		 $this->getModel()->setEmail( $email );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#getEmail()
	   */
	  public function getEmail() {

	  		 return $this->getModel()->getEmail();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#setCreated($dateTime)
	   */
	  public function setCreated( $dateTime ) {

	  		 $this->getModel()->setCreated( $dateTime );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#getCreated()
	   */
	  public function getCreated() {
	  	
	  		 return $this->getModel()->getCreated();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#setLastLogin($dateTime)
	   */
	  public function setLastLogin( $dateTime ) {
	  	
	  		 $this->getModel()->setLastLogin( $dateTime );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#getLastLogin()
	   */
	  public function getLastLogin() {

	  		 return $this->getModel()->getLastLogin();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#setRole(Role $role)
	   */
	  public function setRole( Role $role ) {

	  		 $this->getModel()->setRole( $role );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#getRole()
	   */
	  public function getRole() {

	  		 return $this->getModel()->getRole();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#hasRole($role)
	   */
	  public function hasRole( $role ) {

	  		 if( isset( $this->model ) )
	  		 	 return $this->getModel()->getRole()->getName() == $role;

	  		 return false;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#revokeRole($role)
	   */
	  public function revokeRole() {

	  		 $this->setRole( null );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#setPasswordResetUrl($url)
	   */
	  public function setPasswordResetUrl( $url ) {
	  	
	  		 $this->resetPasswordUrl = $url;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#getPasswordResetUrl()
	   */
	  public function getPasswordResetUrl()  {

	  		 return $this->resetPasswordUrl;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#forgotPassword()
	   */
	  public function forgotPassword() {

	  		 if( !$this->getPasswordResetUrl() )
	  		 	 throw new AgilePHP_Exception( 'Identity::forgotPassword requires a valid passwordResetUrl property value.' );

  		 	 if( !$this->getModel()->getUsername() )
	  		 	 throw new AgilePHP_Exception( 'Identity::forgotPassword requires a valid username property value for model \'' . $this->getModelName() . '\'.' );

	  		 if( !$this->getModel()->getEmail() )
	  		 	 throw new AgilePHP_Exception( 'Identity::forgotPassword requires a valid email property value for model \'' . $this->getModelName() . '\'.' );

	  		 $token = $this->createResetPasswordToken();
	  		 $this->session->set( 'resetPasswordToken', $token );
	  		 $this->session->set( 'username', $this->getUsername() );

	  		 // Make sure email exists!
	  		 $pm = new PersistenceManager();
	  		 $table = $table = $pm->getTableByModelName( $this->getModelName() );
	  		 $emailColumn = $table->getColumnNameByProperty( 'email' );
	  		 $pm->prepare( 'SELECT ' . $emailColumn . ' FROM ' . $table->getName() . ' WHERE ' . $emailColumn . '=? AND username=?;' );
	  		 $params = array( $this->getEmail(),
	  		 				  $this->getUsername() );
	  		 if( !$pm->execute( $params )->fetch() )
	  		 	 throw new AgilePHP_Exception( 'The information provided does not match our records.' );

	  		 // Send email
	  		 $subject = AgilePHP::getFramework()->getAppName() . ' :: Forgotten Password';
	  		 $body = 'Click on the following link to reset your password: ' . $this->getPasswordResetUrl() . '/' . $token . '/' . $this->session->getSessionId();

	  		 if( !mail( $this->getModel()->getEmail(), $subject, $body, $this->getMailHeaders() ) )
	  		 	 throw new AgilePHP_Exception( 'Error sending forgot password email.' );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#resetPassword()
	   */
	  public function resetPassword( $token, $sessionId ) {

	  		 $this->session->setSessionId( $sessionId );

	  		 if( $token !== $session->get( 'resetPasswordToken' ) )
	  		 	 throw new AgilePHP_Exception( 'Invalid token: ' . $token );

	  		 $newPassword = $this->createResetPasswordToken();
	  		 $subject = AgilePHP::getFramework()->getAppName() . ' :: New Password';
	  		 $body = 'Your new password is: ' . $newPassword;

	  		 $this->setUsername( $session->get( 'username' ) );
	  		 $this->refresh();
	  		 $this->setPassword( $newPassword );
	  		 $this->merge();

	  		 if( !mail( $this->getModel()->getEmail(), $subject, $body, $this->getMailHeaders() ) )
	  		 	 throw new AgilePHP_Exception( 'Error sending reset password email.' );
	  }

	  /**
	   * Authenticates the specified username and password.
	   * 
	   * @param $username The username to authenticate
	   * @param $password The password used to authenticate the account. The password
	   * 				  is hashed using the Crypto component.
	   * @return True if the authentication was successful, false otherwise.
	   * 
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#authenticate()
	   */
	  public function login( $username, $password ) {

	  		 if( !$this->getModel() ) throw new AgilePHP_Exception( 'Identity::login Valid user domain model required' );

	  	     Logger::getInstance()->debug( 'Identity::login Authenticating username \'' . $username . '\' with password \'' . $password . '\'.' );

	  		 $pm = new PersistenceManager();

	  		 $this->getModel()->setUsername( $username );
	  		 $this->getModel()->setSession( $this->session->getSession() );
	  		 $this->setModel( $pm->find( $this->getModel() ) );

	  		 if( !$this->getModel() )
	  		 	 return false;

	  		 $hash = Crypto::getInstance()->getDigest( $password );

			 if( !preg_match( '/' . $hash . '/', $this->getPassword() ) )
				 return false;

	  		 if( $this->getModel()->getRoles() ) {

	  		 	 foreach( $this->getModel()->getRoles() as $Role ) {
	
		  		 		  if( $this->getModel()->getRoleId() == $Role->getName() ) {
	
		  		 		  	  $this->setRole( $Role );
		  		 		  	  break;
		  		 		  }
		  		 }
	  		 }

	  		 // The session needs to be persisted first to avoid primary key constraint violation
  	  		 $this->session->set( 'IDENTITY_LOGGEDIN', true );
	  		 $this->session->set( 'IDENTITY_USERNAME', $this->getUsername() );
	  		 if( !$this->session->isPersisted() )
	  		 	 $this->session->persist();

	  		 $this->getModel()->setLastLogin( date( 'c', strtotime( 'now' ) ) );
	  		 $this->getModel()->setSession( $this->session->getSession() );
	  		 $this->merge();
			 return true;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#logout()
	   */
	  public function logout() {

	  		 Logger::getInstance()->debug( 'Identity::logout' );
	  		 if( isset( $this->session ) ) $this->session->destroy();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#isLoggedIn()
	   */
	  public function isLoggedIn() {

	  		 return (isset( $this->session ) && $this->session->get( 'IDENTITY_LOGGEDIN' )) ? true : false;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#persist()
	   */
	  public function persist() {

	  		 $pm = new PersistenceManager();
	  		 $pm->persist( $this->getModel() );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#merge()
	   */
	  public function merge() {

	  		 $pm = new PersistenceManager();
	  		 $pm->merge( $this->getModel() );
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/core/Identity#delete()
	   */
	  public function delete() {

	  	     $pm = new PersistenceManager();
	  	     $pm->delete( $this->getModel() );
	  }

	  /**
	   * Refreshes the IdentityManager domain object model's state by performing
	   * a fresh sql SELECT query.
	   * 
	   * @return void
	   */
	  public function refresh() {

	  		 $pm = new PersistenceManager();
	  		 $this->setModel( $pm->find( $this->getModel() ) );
	  }

	  /**
	   * Returns the name of the model the IdentityManager is currently managing.
	   *  
	   * @return A string representing the name of the domain object model which the
	   * 		 IdentityManager is currently managing.
	   */
	  private function getModelName() {

	  		  return $this->modelName;
	  }

	  /**
	   * Generates a variable length character token used to reset the identity's password.
	   * 
	   * @return Variable length token that must be present in the reset password
	   * 	     url in order to successfully complete the process.
	   */
	  private function createResetPasswordToken() {

			  $numbers = '1234567890';
			  $lcase = 'abcdefghijklmnopqrstuvwzyz';
			  $ucase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			  $length = rand( 1, 20 );

			  $token = null;
			  for( $i=0; $i<$length; $i++ ) {

			  	   if( rand( 0, 1 ) ) {

			  	   	   $cRand = rand( 0, 25 );
			  	   	   $token .= (rand( 0, 1) ) ? $lcase[$cRand] : $ucase[$cRand];
			  	   }
			  	   else {

			  	   	   $nRand = rand( 0, 9 );
			  	   	   $token .= $numbers[$nRand];
			  	   }			  	     
			  }

	  		  return $token;
	  }

	  /**
	   * Mail headers to use when performing forgot password and reset password operations.
	   * 
	   * @return Mail headers to use in the sent messsage.
	   */
	  private function getMailHeaders() {

	  		 $headers = 'From: ' . AgilePHP::getFramework()->getAppName() . ' <no-reply@' . AgilePHP::getFramework()->getAppName() . '>' . "\n";
	  		 $headers .= 'To: ' . $this->getModel()->getUsername() . ' <' . $this->getModel()->getEmail() . '>' . "\n";
        	 $headers .= 'Reply-To: ' . $this->getModel()->getEmail() . "\n";
          	 $headers .= 'Return-Path: ' . $this->getModel()->getEmail() . "\n";
        	 $headers .= 'X-mailer: AgilePHP Framework on PHP (' . phpversion() . ')' . "\n";

        	 return $headers;
	  }

	  /**
	   * Destructor
	   * 
	   * @return void
	   */
	  public function __destruct() {

	  		 Logger::getInstance()->debug( 'Identity::__destruct Instance destroyed' );
	  }
}
?>