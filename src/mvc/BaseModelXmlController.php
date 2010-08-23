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
 * Provides base implementation for model xml controllers.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 * @abstract
 */
abstract class BaseModelXmlController extends BaseModelController {

		 /**
		  * Creates an XML document representing a model. If the 'id' parameter is set, a
		  * lookup is performed for the model with the specified 'id' and the XML is returned with
		  * data populated from the database result set.. If there is no 'id' set, the model's property
		  * nodes will be null. A custom controller and action can be set to modify default behavior.
		  * 
		  * @param String $controller Optional controller to use for CRUD operations. Defaults to the name of the controller that invoked this method.
		  * @param String $action The controllers action method to invoke. Defaults to the persistence mode (persist|merge)
		  * @param array $params An array of parameters to pass into the action method. Defaults to null.
	      */
	     protected function getModelAsFormXML($controller = null, $action = null, $params = null) {

	               if(!$controller) {

	                   $thisController = new ReflectionClass($this);
	                   $c = $thisController->getName();
	               }
	               else
  			 	       $c = $controller;

  			 	   $a = ($action) ? $action : $this->getModelPersistenceAction();

   			 	   // php namespace support
     		   	   $namespace = explode('\\', $c);
     		   	   $c = $namespace[0];

     		   	   $modelNamespace = explode('\\', $this->getModelName());
  			 	   $modelName = array_pop($modelNamespace);

  			 	   $xml = '<Form>';

  			 	   $fieldCount = 0;

  			 	   if($this->getModelPersistenceAction() == 'merge') {

  			 	       $model = $this->getModel();
  			 	       $model->get();
  			 	   	   $fieldCount = count(ORM::getTableByModel($model)->getColumns());
  			 	       $xml .= XmlRenderer::render($model, $modelName, $modelName, false, false);
  			 	   }
  			 	   else
  			 	       $xml .= XmlRenderer::render($this->getModel(), $modelName, $modelName, false, false);

	  			   $xml .= '<controller>' . $c . '</controller>
	  			 	   		<action>' . $a . '</action>';
	  			   $xml .= ($params ? '<params>' . $params . '</params>' : '');
	  			   $xml .= '<fieldCount>' . $fieldCount . '</fieldCount>
	  			 	   	</Form>';

	  			   Log::debug('BaseModelXmlController::getModelAsFormXML called with parameters controller = ' . $controller . ', action = ' . $action . ', params = ' . print_r($params, true));
	  			   Log::debug('BaseModelXmlController::getModelAsFormXML returning xml ' . $xml);

  			 	   return $xml;
	     }

		 /**
	      * Returns a result set from the database as XML. The XML document is returned
	      * with the root node 'ResultList' containing an element named after the model
	      * which then contains each of the models properties and values as children.
	      * For example:
	      * <ResultList>
	      * 	<your_model_name>
	      * 		<model_prop1>*</model_prop1>
	      * 		<model_prop2>*</model_prop2>
	      * 	</your_model_name>
	      * </ResultList>. 
	      * 
	      * @return An XML document representing the result list
	      */
	     protected function getResultListAsXML() {

             	   if(!$this->getResultList())
             	   	  throw new FrameworkException('BaseModelXmlController::getResultListAsXml() requires a valid result set to transform to XML.');

             	   $xml = '<ResultList>';
             	   $xml .= XmlRenderer::render($this->getResultList(), $this->getModelName(), 'Model', false, false);
			 	   $xml .= '</ResultList>';

			 	   return $xml;			 	
	     }

		 /**
	      * Returns a paged result set from the database as XML (including foreign model instances with their
	      * primary keys set). The XML document is returned in the following format:
	      * 
	      * <ResultList>
	      * 	<Model>
	      * 		<your_model_name>
		  *     		<foreign_model_name>
		  *     			<primary_key1>*</primary_key1>
		  *     			<primary_key2>*</primary_key2> 
		  *     		</foreign_model_name>
	      * 			<model_prop1>*</model_prop1>
	      * 			<model_prop2>*</model_prop2>
	      *			</your_model_name>
	      *		</Model>
	      *		<Pagination>
	      *			<page>*</page>
	      *	        <pageCount>*</pageCount>
	      *	        <nextExists>*</nextExists>
	      *	       	<previousExists>*</previousExists>
	      *			<controller>*</controller>
	      *			<action>*</action>
	      *		</Pagination>
	      * </ResultList> 
	      * 
	      * @param string $controller 
	      * @return An XML document representing the result list
	      */
	     protected function getResultListAsPagedXML($controller = null, $action = null, $params = null) {

	               if(!$controller) {
	                   
	                   $thisController = new ReflectionClass($this);
	                   $c = $thisController->getName();
	               }
	               else
  			 	       $c = $controller;

	     		   // php namespace support
     		   	   $namespace = explode('\\', $c);
     		   	   $c = array_pop($namespace);
   		   		   $a = (!$action) ? 'index' : $action;

             	   $start = ($this->getPage() * $this->getMaxResults()) - ($this->getMaxResults() - 1);
             	   if(!$this->getCount()) $start = 0;

             	   $end = $start + ($this->getMaxResults() - 1);
			 	   if($end > $this->getCount()) $end = $this->getCount();

             	   $xml = '<ResultList>';
             	   $xml .= XmlRenderer::render($this->getResultList(), $this->getModelName(), 'Model', false, false);
             	   $xml .= '<Pagination>
             	   				<page>' . $this->getPage() . '</page>
             	   				<pageCount>' . $this->getPageCount() . '</pageCount>
			 	   				<nextExists>' . (($this->nextExists() == true) ? 1 : 0) . '</nextExists>
			 	   				<previousExists>' . (($this->previousExists()) ? 1 : 0) . '</previousExists>
			 	   				<resultCount>' . $this->getResultCount() . '</resultCount>
			 	   				<recordCount>' . $this->getCount() . '</recordCount>
			 	   				<recordStart>' . (($start <= 0) ? 0 : $start) . '</recordStart>
			 	   				<recordEnd>' . $end . '</recordEnd>
			 	   				<controller>' . $c . '</controller>
			 	   				<action>' . $a . '</action>';
			 	   if($params) $xml .= '<params>' . $params . '</params>';

			 	   $xml .= '</Pagination>
			 	   	</ResultList>';

			 	   Log::debug('BaseModelXmlController::getResultListAsPagedXML' . $xml);

			 	   return $xml;			 	   
	     }

	     /**
	      * Returns the type of action which the controllers should take when deciphering
	      * whether the operation is a persist or merge operation. If the primary key(s)
	      * contain a value, the action is assumed a merge. If the primary key(s) do not
	      * contain a value, the action is assumed persist.
	      * 
	      * @todo this needs to be made more robust - should probably be moved to ORM
	      * 
	      * @return 'persist' if the primary key value(s) are not present, 'merge' if
	      * 	    the primary keys are present.
	      */
	     protected function getModelPersistenceAction() {

	     		   $table = ORM::getTableByModel($this->getModel());
	     		   $pkeyColumns = $table->getPrimaryKeyColumns();
  			 	   foreach($pkeyColumns as $column) {

  			 	   		   $accessor = 'get' . ucfirst($column->getModelPropertyName());
  			 	   		   if(!$this->getModel()->$accessor())
  			 	   			  return 'persist';
  			 	   }

  			 	   return 'merge';
	     }
}
?>