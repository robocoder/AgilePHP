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
 * Overrides BaseModel* classes to provide implementation
 * specific logic for front controller style components.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 * @abstract
 */
abstract class ComponentModelActionController extends BaseModelActionController {

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
	  		    $xsl = $this->getModelListXSL(null, $this->getComponentName() . '/' . MVC::getAction());
	  		    $xml = $this->getResultListAsPagedXML($this->getComponentName() . '/' . MVC::getAction());
	  	        $this->set('content', $this->xsltRenderer->transform($xsl, $xml));
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
	     		$xsl = $this->getModelFormXSL(null, $this->getComponentName() . '/' . MVC::getAction());
	     		$xml = $this->getModelAsFormXML($this->getComponentName() . '/' . MVC::getAction());
  	     		$this->set('content', $this->xsltRenderer->transform($xsl, $xml));
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
            	$xsl = $this->getModelFormXSL(null, $this->getComponentName() . '/' . MVC::getAction());
            	$xml = $this->getModelAsFormXML($this->getComponentName() . '/' . MVC::getAction());
	  	        $this->set('content', $this->xsltRenderer->transform($xsl, $xml));
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
	     		$xsl = $this->getModelAsReadOnlyXSL($this->getComponentName() . '/' . MVC::getAction());
	     		$xml = $this->getModelAsFormXML($this->getComponentName() . '/' . MVC::getAction());
  	     		$this->set('content', $this->xsltRenderer->transform($xsl, $xml));
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
     			
     			$xsl = $this->getModelListXSL(null, $this->getComponentName() . '/' . MVC::getAction());
     			$xml = $this->getResultListAsPagedXML($this->getComponentName() . '/' . MVC::getAction(), 'search', $params);
     			$content = $this->xsltRenderer->transform($xsl, $xml);
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

				$xsl = $this->getModelListXSL(null, $this->getComponentName() . '/' . MVC::getAction());
				$xml = $this->getResultListAsPagedXML($this->getComponentName() . '/' . MVC::getAction());
	     		$content = $this->xsltRenderer->transform($xsl, $xml);

	  	        $this->set('content', $content);
	  	        $this->render($view);
	     }

	     /**
	      * Returns the name of the component which the controller belongs. This
	      * is used to dispatch requests to the correct location.
	      * 
	      * @return string The component name
	      */
	     abstract protected function getComponentName();
}
?>