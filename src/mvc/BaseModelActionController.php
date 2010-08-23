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
 * @package com.makeabyte.agilephp.mvc
 */

/**
 * Provides a base implementation of a working model action controller.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 * @abstract
 */
abstract class BaseModelActionController extends BaseModelXslController {

		 protected $xsltRenderer;

		 /**
		  * Base constructor which allows configuration options in extended classes.
		  *
		  * @param bool $requireLogon True to require the user to be logged in, false to allow calls
		  * 			to unauthenticated calls (uses AgilePHP Identity component to validate logged in session).
		  * @param String $requiredRole An optional role to require
		  * @return void
		  */
	     public function __construct($requireLogon = true, $requiredRole = 'admin') {

	     	       if($requireLogon) {

		     		  if(!Identity::isLoggedIn())
		  	     		 throw new NotLoggedInException('Login Required');

			  	      if(!Identity::hasRole(new Role(false, $requiredRole)))
			  	         throw new AccessDeniedException('Access Denied. This area is reserved for ' . $requiredRole);
	     		   }

	     		   parent::__construct();
	     		   $this->xsltRenderer = MVC::createRenderer('XSLTRenderer');
	     		   $this->set('title', 'Administration :: ' . $this->getModelName());
	     }

	     /**
	      * Returns an instance of the MVC XSLTRenderer object.
	      *
	      * @return An instance of XSLTRenderer
	      */
	     protected function getXsltRenderer() {

	     		   return $this->xsltRenderer;
	     }

		 /**
	      * Performs a search for the model defined in the extension class and displays a
	      * paginated result list with edit and delete actions, as well as sortable
	      * column headers.
	      *
	      * @param Integer $page The page number within the result set to display. Default is page 1.
	      * @param String $view The view to render. Default is 'admin'.
	      * @return void
	      */
	     public function index($page = 1, $view = null) {

	     	    if(!$view) $view = 'admin';

	     		// Defaults sorting by the first primary key column
	     		//
	     		//$table = ORM::getTableByModel($this->getModel());
	  	        //$pkeyColumns = $table->getPrimaryKeyColumns();
	  	        //if($pkeyColumns) $this->setOrderBy($pkeyColumns[0]->getModelPropertyName(), 'ASC');

	  		    $this->setPage($page);
	  		    $content = $this->getXsltRenderer()->transform($this->getModelListXSL(), $this->getResultListAsPagedXML());

	  	        $this->set('content', $content);
	  	        $this->render($view);
	     }

	     /**
	      * Displays an 'add' form for the model defined in the extension class.
	      *
	      * @param int $page The page number to display.
	      * @param string $view The view to render. Defaults to 'admin'.
	      * @return void
	      */
	     public function add($page, $view = 'admin') {

	     		$this->setPage($page);
  	     		$this->set('content', $this->getXsltRenderer()->transform($this->getModelFormXSL(), $this->getModelAsFormXML()));
  	     	    $this->render($view);
	     }

	     /**
	      * Displays an 'edit' form for the model defined in the extension class.
	      *
	      * @param string $ids Underscore delimited list of primary key id's in same ordinal position as defined in orm.xml
	      * @param int $page The page number to display.
	      * @param string $view The view to render. Defaults to 'admin'.
	      * @return void
	      */
	     public function edit($ids, $page = 1, $view = 'admin') {

	     		$this->setPrimaryKeys($ids);
            	$this->setPage($page);
            	$this->set('content', $this->getXsltRenderer()->transform($this->getModelFormXSL(), $this->getModelAsFormXML()));
            	$this->render($view);
	     }

	     /**
	      * Displays a read only text table for the model defined in the extension class.
	      *
	      * @param string $ids Underscore delimited list of primary key id's in same ordinal position as defined in orm.xml
	      * @param string $view The view to render. Defaults to 'admin'.
	      * @return void
	      */
	     public function read($ids, $view = 'admin') {

	     		$this->setPrimaryKeys($ids);

  	     		$this->set('content', $this->getXsltRenderer()->transform($this->getModelAsReadOnlyXSL(), $this->getModelAsFormXML()));
  	     	    $this->render($view);
	     }

	     /**
	      * Performs a search on the model defined in the extension class.
	      *
	      * @param string $field The database column name to filter on
	      * @param string $keyword The keyword used as the search criteria. Defaults to null (show everything)
	      * @param string $view The view to render. Defaults to 'admin'.
	      * @param int $page The page number to display.
	      * @return void
	      */
	     public function search($page = 1, $view = 'admin', $field = null, $keyword = null) {

	     		$table = ORM::getTableByModel($this->getModel());
	     		$columns = $table->getColumns();

	     		if(!$field) {

	     		   $columns = $table->getPrimaryKeyColumns();
	     		   $field = $columns[0]->getName();
	     		}

				foreach($columns as $column) {

     					if($field == $column->getName())
     			 		   $this->setRestrictions(array($field => '%' . $keyword . '%'));
     			}

     			$this->setComparisonLogicOperator('LIKE');
     			$this->setPage($page);

     			$params = $view . '/' . $field . '/' . $keyword;
     			$content = $this->xsltRenderer->transform($this->getModelListXSL(), $this->getResultListAsPagedXML(false, 'search', $params));

  	         	$this->set('content', $content);
	  	        $this->render($view);
	     }

	     /**
	      * Sorts the modelList according to the specified column name and direction.
	      *
	      * @param String $column The column name to sort on
	      * @param String $direction The direction to sort. Default is 'ASC' (ascending). (ASC|DESC)
	      * @param Integer $page The page within the result set to display.
	      * @param String $view The view to render. Default is 'admin'.
	      * @return void
	      */
	     public function sort($column, $direction = 'DESC', $page = 1, $view = 'admin') {

	     		$this->setOrderBy($column, $direction);
	     		$this->setPage($page);
				$this->setOrderBy($column, ($direction == 'ASC') ? 'DESC' : 'ASC');

	     		$content = $this->xsltRenderer->transform($this->getModelListXSL(), $this->getResultListAsPagedXML());

	  	        $this->set('content', $content);
	  	        $this->render($view);
	     }

	     /**
	      * Persists a the ActiveRecord state defined by the model in the
	      * extension class.
	      *
	      * @return void
	      */
	     public function persist() {

	     		$this->setModelValues();

	    	    parent::persist($this->getModel());
	    	    $this->__construct();
	  	 	    $this->index($this->getPage());
	     }

 	     /**
	      * Merges the ActiveRecord state defined by the model in the
	      * extension class.
	      *
	      * @return void
	      */
	     public function merge() {

	  		    $this->setModelValues();

	  	 	    parent::merge($this->getModel());
	  	 	    $this->__construct();
	  	 	    $this->index($this->page);
	     }

	     /**
	      * Deletes the ActiveRecord state defined by the model in the
	      * extension class and takes the user back to the 'list' view,
	      * including the page they left off on.
	      *
	      * @param string $ids An underscope delimited list of primary key values. Defaults to null
	      * @param int $page An optional page number. Defaults to 1.
	      * @return void
	      */
	     public function delete($ids = null, $page = 1) {

	     		$this->setPrimaryKeys($ids);
	  		    $this->setModelValues();

	  		    parent::delete($this->getModel());
	  		    $this->__construct();
	  		    $this->index($page);
	     }

	     /**
	      * Parses $ids sent in by the action method and sets all primary keys
	      * in the model defined in the extension class.
	      *
	      * @param String $ids The 'ids' as passed in from the form submit. These 'ids' are
	      * 			       automagically generated by BaseModelXslController.
	      * @return void
	      */
	     protected function setPrimaryKeys($ids) {

	  		       if($ids == null) return;

	  	           $table = ORM::getTableByModel($this->getModel());
	  	           $pkeyColumns = $table->getPrimaryKeyColumns();

	  	           if(!$pkeyColumns) return; // Poor SQL programming - table should contain a key

  			       // Single primary key
	  	           if(strpos($ids, '_') === false) {
	  	               
		  	          $mutator = $this->toMutator($pkeyColumns[0]->getModelPropertyName());
	  		 	      $this->getModel()->$mutator($ids);
	  		 	      return;
	  		       }

	  		       // Compound primary key
			       $idz = explode('_', $ids);
	  	           for($i=0; $i<count($idz); $i++) {

	  	               $mutator = $this->toMutator($pkeyColumns[$i]->getModelPropertyName());

	  	               // Primary key is also a foreign key - (many-to-many)
	  	               if($pkeyColumns[$i]->isForeignKey()) {

	  	                  $fModel = ORM::getTableByName($pkeyColumns[$i]->getForeignKey()->getReferencedTable())->getModel();
	  	                  $fModelMutator = $this->toMutator($pkeyColumns[$i]->getForeignKey()->getReferencedColumnInstance()->getModelPropertyName());

	  	                  $fModelInstance = new $fModel();
	  	                  $fModelInstance->$fModelMutator($idz[$i]);

	  	                  $this->getModel()->$mutator($fModelInstance);
	  	                  continue;
	  	               }

	  	               // Stand alone primary key
	  	     	 	   $this->getModel()->$mutator($idz[$i]);
	  	          }
	     }

		 /**
	      * Uses the AgilePHP RequestScope component to retrieve POST parameters
	      * from a form submit and set the model properties defined in the extension
	      * class. This method expects the form element names to match the name
	      * of the model's property.
	      *
	      * @return void
	      */
		 protected function setModelValues() {

	  		       $request = Scope::getRequestScope();
	     	       $table = ORM::getTableByModel($this->getModel());

	  		       if(!$request->getParameters()) return;

  	 	  	       foreach($table->getColumns() as $column) {

  	 	  	       		   $name = $column->getModelPropertyName();
		  	 	  	       $value = $request->get($name);

		  	 	  	       $accessor = $this->toAccessor($name);
		  	 	  	       $mutator = $this->toMutator($name);

		  	 	  	       // Password fields usually have a confirm box that needs to verify the integrity
		  	 	  	       // of the password. This logic will make sure 'password1' and 'password2' fields match.
		  	 	  	       // The password present in the database at the time the form is loaded is expected to be
		  	 	  	       // present in the POST array named 'oldPassword'.
  	 	  	       		   if($name == 'password') {

  	 	  	       			  if($request->getSanitized('password1') !== $request->getSanitized('password2')) {

			  		     		 $this->set('error', 'Passwords don\'t match');
			  		     		 $this->render('error');
			  		     		 exit;
			  		     	  }

			  		     	  $password = $request->getSanitized('password1');
			  		     	  if($request->getSanitized('oldPassword') != $password)
							    $this->getModel()->setPassword($password);

							  continue;
			  		      }

			  		      // Set model values to null if they are not present in the POST array
		  	 	  	      if(!$request->get($name)) {

	  		 	  	         $this->getModel()->$mutator(null);
	  		 	  	         continue;
		  	 	  	      }

			  		      // Dont sanitize the value if the column has sanitize="false" set in orm.xml
		  		     	  $value = ($column->getSanitize() === true) ?
	   	  	        					urldecode(stripslashes(stripslashes($request->sanitize($value)))) :
	   	  	        					urldecode(stripslashes(stripslashes($value)));

  	 	  	       		  if($column->isForeignKey()) {

  	 	  	       			 if(!$value || $value == 'NULL') continue;

  	 	  	        		 $fmodelName = $column->getForeignKey()->getReferencedTableInstance()->getModel();
  	 	  	        		 $fModel = new $fmodelName();
						     $refMutator = $this->toMutator($column->getForeignKey()->getReferencedColumnInstance()->getModelPropertyName());
						     $fModel->$refMutator($value);
 				     		 $this->getModel()->$mutator($fModel);

						     continue;
			     		  }

   		   		 		  $this->getModel()->$mutator($value);
  	 	  	     }
	     }
}
?>