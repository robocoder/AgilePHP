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
 * @package com.makeabyte.agilephp.mvc
 */

/**
 * Provides base implementation for model xsl controllers.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 * @abstract
 */
abstract class BaseModelXslController extends BaseModelXmlController {

		 /**
	      * Generates an XSL stylesheet from the domain model object's properties. Designed to be used in conjuction
	      * with getResultListAsPagedXML() to perform an XSLT transformation.
	      *
	      * @param String $pkeyFields Optional name of the model property to send as the 'id' field to the action when an action
	      * 			  		      button is clicked. Defaults to the primary key(s) of the model as defined in orm.xml.
	      * @param String $controller Optional name of the controller to use when an action button is clicked. Defaults
	      * 				   		  to the name of the controller which invoked this method. Defaults to the extension controller.
	      * @param string $view Optional name of a PHTML view to render. Defaults to 'admin'.
	      * @return XSL stylesheet for BaseModelXmlController
	      */
	     protected function getModelListXSL($pkeyFields = null, $controller = null, $view = 'admin') {

	     		   $table = ORM::getTableByModelName($this->getModelName());

	     		   if(!$controller) {

	     		      $thisController = new ReflectionClass($this);
	     		      $controller = $thisController->getName();
	     		   }

	     	       // php namespace support
     		   	   $namespace = explode('\\', $controller);
     		   	   //$controller = $namespace[0];
     		   	   $modelNamespace = explode('\\', $this->getModelName());
     		   	   $modelName = array_pop($modelNamespace);

     		   	   $requestBase = AgilePHP::getRequestBase();
   		   		   if(!$pkeyFields)  $pkeyFields = $this->getSerializedPrimaryKeyColumns($table);

   		   		   $fkeyXslValues = $this->getSerializedForeignKeyValuesAsXSL($table);
   		   		   $pkeyXslValues = $this->getSerializedPrimaryKeyColumnsAsXSL($pkeyFields);

   		   		   // If this is a many to many relationship, primary keys are foreign key values
	     	       $pkeys = $table->getPrimaryKeyColumns();
	     	       foreach($pkeys as $pkey) {
	     	         if($pkey->isForeignKey()) {
	     	            $pkeyXslValues = $fkeyXslValues;
	     	            break;
	     	         }
	     	       }

   		   		   $order = $this->getOrderBy();

	     		   $xsl = '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">';

				   		$xsl .= $this->getPaginationXSL();

						$xsl .=	'<xsl:template match="/">

										<div class="agilephpTableDescription">';

						                    $display = $table->getDisplay();
						                    $description = $table->getDescription();

											if($display) $xsl .= $display;

											if($display && $description) $xsl .= ' :: ';

											if($description) $xsl .= $description;

											// Prevents broken document if both display and description are missing from orm.xml
											if(!$display && !$description) $xsl .= '<p/>';

										$xsl .= '</div>

										<div class="agilephpSearchBar">
											 Search
											 <input type="text" id="agilephpSearchText" name="agilephpSearchText"/>
											 <select id="agilephpSearchField" name="agilephpSearchField">';
												foreach($table->getColumns() as $column)
													if($column->isVisible())
														$xsl .= '<option value="' . $column->getName() . '">' . $column->getViewDisplayName() . '</option>';
										$xsl .= '</select>
											 <input type="hidden" value="' . $this->page . '" id="page"/>
											 <input type="hidden" value="' . $view . '" id="view"/>
											 <input type="button" value="Search" onclick="javascript:AgilePHP.ORM.search(' . ((strpos($controller, '/') ? 'true' : 'false'))  . ')"/>
										</div>

										<table class="agilephpTable" border="0" width="100%">';

												$flag = false;
									     	    foreach($table->getColumns() as $column) {

									     	    		if(!$table->isVisible($column->getModelPropertyName())) continue;

			 	   	   			      			    	if(!$flag) $xsl .= '<tr class="agilephpHeader">';
			 	   	   			      			        $flag = true;

			 	   	   			      			        if($column->isSortable()) {

			 	   	   			      			           $display = null;  // rendered content
			 	   	   			      			           $arrow = null;    // display an HTML arrow on active sort columns
			 	   	   			      			           if($column->getName() == $order['column']) {

			 	   	   			      			         	  $arrow = $order['direction'] == 'ASC' ? '&#8593;' : '&#8595;';
			 	   	   			      			         	  $display = ucfirst($column->getViewDisplayName()) . ' ' . $arrow;
			 	   	   			      			           }
			 	   	   			      			           else {

			 	   	   			      			         	  $display = ucfirst($column->getViewDisplayName());
			 	   	   			      			           }

				 	   	   			      			         $xsl .= '<td style="font-weight: bold; padding-left: 5px; padding-right: 5px;">
				 	   	   			      			         			<a href="' . $requestBase . '/' .
				 	   	   			      			         			 	$controller . '/sort/' . $column->getName() . ($order['direction'] ? '/' . $order['direction'] : '') . '">' .
				 	   	   			      			         			 	$display . '</a></td>';
			 	   	   			      			         }
			 	   	   			      			         else
			 	   	   			      			         	$xsl .= '<td style="font-weight: bold; padding-left: 5px; padding-right: 5px;">' . ucfirst($column->getViewDisplayName()) . '</td>';
									     	    }

												$xsl .= '<td colspan="2" style="font-weight: bold;">Actions</td>
													   </tr>
													<xsl:apply-templates select="/ResultList/Model/' . $modelName . '"/>
										</table>

										<xsl:apply-templates select="/ResultList/Pagination"/>

										<table border="0">
											<tr>
												<td><input type="button" onclick="location.href= \'' . $requestBase . '/' . $controller . '/add/' . $this->getPage() . '\';" value="Create ' . $table->getViewDisplayName() . '"/></td>
											</tr>
										</table>';

						   $xsl .= '</xsl:template>

									<xsl:template match="' . $modelName . '">

										<tr>

										<xsl:choose>

											<xsl:when test="(position() mod 2 = 1)">
												<xsl:attribute name="class">agilephpRow1</xsl:attribute>
												<xsl:attribute name="onmouseover">AgilePHP.ORM.setStyle(this, \'agilephpHighlight\');</xsl:attribute>
												<xsl:attribute name="onmouseout">AgilePHP.ORM.setStyle(this, \'agilephpRow1\');</xsl:attribute>
											</xsl:when>

											<xsl:otherwise>
												<xsl:attribute name="class">agilephpRow2</xsl:attribute>
												<xsl:attribute name="onmouseover">AgilePHP.ORM.setStyle(this, \'agilephpHighlight\');</xsl:attribute>
												<xsl:attribute name="onmouseout">AgilePHP.ORM.setStyle(this, \'agilephpRow2\');</xsl:attribute>
											</xsl:otherwise>

										</xsl:choose>';

						   					foreach($table->getColumns() as $column) {

								     	    	   if(!$table->isVisible($column->getModelPropertyName())) continue;

			 	   	   			      			   if($column->isForeignKey()) {

			 	   	   			      			      $namespace = explode('\\', $column->getForeignKey()->getReferencedTableInstance()->getModel());
			 	   	   			      			      $fModelName = array_pop($namespace);
			 	   	   			      			      $fkey = $column->getForeignKey();

			 	   	   			      			      if($column->isPrimaryKey()) $primaryAndForeignKey = true;

			 	   	   			      			      switch($fkey->getType()) {

				 	   	   			      			     	 case 'one-to-one':

				 	   	   			      			     	      $xsl .= '<td>
				 	   	   			      			     	  		          <xsl:if test="' . $fModelName . '/' . $fkey->getReferencedColumn() . ' != \'\'">
							 	   	   			      			      		       <a href="' . $requestBase . '/' . $fkey->getReferencedController() . '/read/' . $fkeyXslValues . '">' .
							 	   	   			      			     	  				  $fkey->getReferencedTableInstance()->getViewDisplayName() .  '</a>
							 	   	   			      			      	      </xsl:if>
							 	   	   			      			           </td>';
						 	   	   			      			 break;

						 	   	   			      			 case 'one-to-many':
						 	   	   			      			 case 'many-to-one':

						 	   	   			      			  	  $xsl .= '<td>
						 	   	   			      			        			<xsl:if test="' . $fModelName . '/' . $fkey->getReferencedColumn() . ' != \'\'">
						 	   	   			      			     	 				<a href="' . $requestBase . '/' . $fkey->getReferencedController() .
						 	   	   			      			     	 						'/read/{' . $fModelName . '/' . $fkey->getReferencedColumnInstance()->getModelPropertyName() . '}">' .
						 	   	   			      			     	 						$fkey->getReferencedTableInstance()->getViewDisplayName() . '</a>
						 	   	   			      			     	 			</xsl:if>
						 	   	   			      			  	 		  </td>';
						 	   	   			      			 break;

				 	   	   			      			     	 default:
				 	   	   			      			     	 	throw new FrameworkException('Unsupported relationship type \'' . $fkey->getType() . '\'.');
			 	   	   			      			     	 }

					 	   	   			      			 continue;
			 	   	   			      			     }
								     	    		 $xsl .= '<td>
																<xsl:value-of select="' . $column->getModelPropertyName() . '"/>
															  </td>';
								     	    }


							       $xsl .= '<td>
												<a href="' . $requestBase . '/' . $controller . '/edit/' . $pkeyXslValues . '/' . $this->getPage() . '">Edit</a>
											</td>
											<td>
												<a href="JavaScript:AgilePHP.ORM.confirmDelete( \'' . $requestBase . '\', \'' . $pkeyXslValues . '\', \'' . $this->getPage() . '\', \'' . $controller . '\', \'delete\');">Delete</a>
											</td>
										</tr>
									</xsl:template>
								</xsl:stylesheet>';

				   Log::debug('BaseModelXslController::getModelListXSL Returning ' . $xsl);

	     		   return $xsl;
	     }

	     /**
		  * Returns an XSL stylesheet used for add and update actions using the Form component.
		  *
		  * @param string $controller Optional controller responsible for edits. Defaults to the extension controller
		  * @return String The XSL stylesheet.
	      */
	     protected function getModelFormXSL($controller = null, $action = null, $view = 'admin') {

	     	       $table = ORM::getTableByModel($this->getModel());
	     	       $fkeyXslValues = $this->getSerializedForeignKeyValuesAsXSL($table);
	     	       $pkeyValues = $this->getSerializedPrimaryKeyValues($table);

	     	       // If this is a many to many relationship, primary keys are foreign key values
	     	       $pkeys = $table->getPrimaryKeyColumns();
	     	       $fkeys = $table->getForeignKeyColumns();
	     	       foreach($fkeys as $fColumn) {
	     	           foreach($pkeys as $pColumn) {
	     	              if($fColumn->getName() == $pColumn->getName()) {

	     	                  $pkeyValues = preg_replace('/{/', '{' . $this->getModelName() . '/', $fkeyXslValues);
	     	                  break;
	     	              }
	     	           }
	     	       }

	     	       $action = AgilePHP::getRequestBase() . '/{/Form/controller}/{/Form/action}/' . $pkeyValues . '/' . $this->getPage();
	     	       $token = Scope::getRequestScope()->createToken();

	     	       // php namespace support
	     	       $namespace = explode('\\', $this->getModelName());
	     	       $name = array_pop($namespace);

	     	       $form = $table->hasBlobColumn() ? new Form($this->getModel(), 'frm' . $name, $name, $action, 'multipart/form-data', $token)
	     	       							       : new Form($this->getModel(), 'frm' . $name, $name, $action, null, $token);
				   $form->setMode($this->getModelPersistenceAction());
	     	       $xsl = $form->getXSL($pkeyValues, $this->getPage());

	     	       Log::debug('BaseModelXslController::getModelFormXSL Returning ' . $xsl);

	     	       return $xsl;
	     }

		 /**
		  * Returns an XSL stylesheet used for read-only (this is the Read in CRUD).
		  *
		  * @return An XSL stylesheet used for read operations
	      */
	     protected function getModelAsReadOnlyXSL($controller = null) {

	               if(!$controller) $controller = MVC::getController();

	     	       $table = ORM::getTableByModelName($this->getModelName());
	     	       $fkeyXslValues = $this->getSerializedForeignKeyValuesAsXSL($table);
	     	       $pkeyValues = $this->getSerializedPrimaryKeyValues($table);

	     	       // If this is a many to many relationship, primary keys are foreign key values
	     	       $pkeys = $table->getPrimaryKeyColumns();
	     	       $fkeys = $table->getForeignKeyColumns();
	     	       foreach($fkeys as $fColumn) {
	     	           foreach($pkeys as $pColumn) {
	     	              if($fColumn->getName() == $pColumn->getName()) {

	     	                  $pkeyValues = preg_replace('/{/', '{' . $this->getModelName() . '/', $fkeyXslValues);
	     	                  break;
	     	              }
	     	           }
	     	       }

	     	       $requestBase = AgilePHP::getRequestBase();
	     	       $action = $this->getModelPersistenceAction();

	     	       // php namespace support
	     	       $namespace = explode('\\', $this->getModelName());
	     	       $modelName = array_pop($namespace);

  			 	   $xsl = '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

							    <xsl:template match="Form">';

				    				$xsl .= '<table class="agilephpTable" border="0" cellpadding="3">';

  			 	   	  						foreach($table->getColumns() as $column) {

	  			 	   	  							if($column->isVisible() == false) {

	  			 	   	  								if($column->isPrimaryKey())
	  			 	   	  									$xsl .= '<input type="hidden" name="' . $column->getModelPropertyName() . '" value="{/Form/' . $modelName . '/' . $column->getModelPropertyName() . '}"/>';
	  			 	   	  								continue;
	  			 	   	  							}

	  			 	   	  							if($column->isForeignKey()) {

	  			 	   	  							   $xsl .= '<tr>
    	  			 	   	  				     					<td>' . ucfirst($table->getDisplayNameByProperty($column->getModelPropertyName())) . '</td>
    	  			 	   	  				     		   			<td><xsl:value-of select="/Form/' . $modelName . '/' . $column->getForeignKey()->getReferencedColumnInstance()->getModelPropertyName() . '" /></td>
    	  			 	   	  				     		    	</tr>';
	  			 	   	  							   continue;
	  			 	   	  							}

	  			 	   	  				     		$xsl .= '<tr>
	  			 	   	  				     					<td>' . ucfirst($table->getDisplayNameByProperty($column->getModelPropertyName())) . '</td>
	  			 	   	  				     		   			<td><xsl:value-of select="/Form/' . $modelName . '/' . $column->getModelPropertyName() . '" /></td>
	  			 	   	  				     		    	</tr>';
	  			 	   	  						}

	  			 	   	  						$xsl .= '<tr>
								 							<td>&#8201;</td>
															<td>';
	  			 	   	  						$xsl .= (($action == 'persist') ? '<input type="submit" value="Create"/> <input type="button" value="Cancel" onclick="javascript:history.go(-1);"/>'
  			 	   	  													 : '<input type="button" value="Edit" onclick="javascript:location.href=\'' . $requestBase .
  			 	   	  													   '/' . $controller . '/edit/' . $pkeyValues . '\';"/>
																			<input type="button" value="Delete" onclick="javascript:AgilePHP.ORM.confirmDelete(\'' . $requestBase .
  			 	   	  													   '\', \'' . $pkeyValues . '\', \'' . $this->getPage() .
  			 	   	  													   '\', \'{/Form/controller}\', \'delete\')"/>
  			 	   	  													   <input type="button" value="Cancel" onclick="javascript:history.go(-1);"/>');
												$xsl .= '</td>
														</tr>';
				$xsl .= '			</table>
				  	  		</xsl:template>
						</xsl:stylesheet>';

				Log::debug('BaseModelXslController::getModelAsReadOnlyXSL Returning ' . $xsl);

	     	    return $xsl;
	     }

	     /**
	      * Returns an XSL stylesheet used for pagination.
	      *
	      * @return Pagination XSL stylesheet
	      */
	     protected function getPaginationXSL() {

	               $requestBase = AgilePHP::getRequestBase();
	     	       $xsl = '<xsl:template match="Pagination">

								<table class="agilephpPaginationTable" border="0" style="padding-top: 10px;">

									<tr class="agilephpPaginationHeader">

								    			    <xsl:if test="previousExists = 1">
								 						<td><a href="' . $requestBase . '/{controller}/{action}/{page - 1}/{params}">Previous</a></td>
													</xsl:if>

													<xsl:call-template name="pageNumberGenerator">
														<xsl:with-param name="page" select="/ResultList/Pagination/page"/>
											   			<xsl:with-param name="action" select="/ResultList/Pagination/action"/>
											   			<xsl:with-param name="pageCount" select="/ResultList/Pagination/pageCount"/>
											   			<xsl:with-param name="controller" select="/ResultList/Pagination/controller"/>
											   			<xsl:with-param name="params" select="/ResultList/Pagination/params"/>
											  		</xsl:call-template>

											       	<xsl:if test="nextExists = 1">
											    		<td><a href="' . $requestBase . '/{controller}/{action}/{page + 1}/{params}">Next</a></td>
											  		</xsl:if>

											  	</tr>

											  </table>

											  <table border="0" class="agilephpTable">
												  	<tr class="agilephpPaginationRecordCount">
												    	<xsl:choose>

												    		<xsl:when test="recordEnd &gt; recordCount">

												    			<td style="padding-top: 5px;">Displaying <xsl:value-of select="recordStart" /> through <xsl:value-of select="recordCount" /> of <xsl:value-of select="recordCount" /> records.</td>

												    		</xsl:when>

												    		<xsl:otherwise>
												    			<td style="padding-top: 5px;">Displaying <xsl:value-of select="recordStart" /> through <xsl:value-of select="recordEnd" /> of <xsl:value-of select="recordCount" /> records.</td>
												    		</xsl:otherwise>

												    	</xsl:choose>
													</tr>
											 </table>

									</xsl:template>

								    <xsl:template name="pageNumberGenerator">

										<xsl:param name="page" select="1"/>
										<xsl:param name="pageCount" select="1"/>
										<xsl:param name="action" select="index"/>
										<xsl:param name="controller" select="IndexController"/>
										<xsl:param name="params"/>
										<xsl:param name="i" select="1"/>

										<xsl:param name="maxResults" select="recordEnd - recordStart"/>

										<xsl:if test="$i = $page">
								   			<td><xsl:value-of select="$i" /></td>
								   		</xsl:if>

								   		<xsl:if test="$i != $page and not($i &lt; ($page - ($maxResults + 1)))">
								      			<td><a href="' . $requestBase . '/{$controller}/{$action}/{$i}/{$params}"><xsl:value-of select="$i" /></a></td>
										</xsl:if>

										<xsl:if test="not($i >= $pageCount or $i > ($page + $maxResults))">
								   			<xsl:call-template name="pageNumberGenerator">
								   				<xsl:with-param name="i" select="$i + 1"/>
								   				<xsl:with-param name="page" select="$page"/>
								   				<xsl:with-param name="action" select="$action"/>
								   				<xsl:with-param name="pageCount" select="$pageCount"/>
												<xsl:with-param name="controller" select="$controller"/>
												<xsl:with-param name="params" select="$params"/>
								   			</xsl:call-template>
								    	</xsl:if>

								   </xsl:template>';

	     	       return $xsl;
	     }

		 /**
	      * Returns an 'AgilePHP serialized' string of primary key column (property names if
	   	  * exists otherwise the column name) suitable for use in xml/xsl controllers.
	   	  *
	   	  * @param Table $table Table instance used to get primary keys.
	   	  * @return The 'AgilePHP serialized' string of primary keys.
	   	  */
	  	private function getSerializedPrimaryKeyColumns(Table $table) {

	  		   $pkeyColumns = array();
   		   	   foreach($table->getPrimaryKeyColumns() as $column)
   		   		        array_push($pkeyColumns, $column->getModelPropertyName());

   		   	   if(count($pkeyColumns))
   		   	 	   return implode('_', $pkeyColumns);

   		   	   return null;
	  	 }

	  	 /**
	  	  * Returns an 'AgilePHP serialized' string of primary key values. This method
	  	  * uses the AgilePHP 'Scope' component (RequestScope) to pull in the values
	  	  * as they were submitted by the form (rendered by getModelFormXSL).
	  	  *
	  	  * @param Table $table The AgilePHP ORM 'Table' object to get the
	  	  * 					primary key columns for.
	  	  * @return An array of AgilePHP ORM 'Column' objects configured for
	  	  * 		the specified 'Table'.
	  	  */
		 private function getSerializedPrimaryKeyValues(Table $table) {

		 		 $values = array();
		 		 $pkeyColumns = $table->getPrimaryKeyColumns();
		 		 for($i=0; $i<count($pkeyColumns); $i++) {

		 		   	 $accessor = 'get' . ucfirst($pkeyColumns[$i]->getModelPropertyName());
		 		   	 $value = $this->getModel()->$accessor();
		 		   	 if(is_object($value)) continue; // Foreign key that will be handled by getSerializedForeignKeyValuesAsXSL
					 array_push($values, $value);
				 }

				 if(count($values))
		 		 	return implode('_', $values);
	  	 }

	  	 /**
	  	  * Returns an 'AgilePHP serialized' string of primary key values suitable for
	  	  * use in xml/xsl controllers. These values are replaced by the XML data once
	  	  * a transformation occurrs.
	  	  *
	  	  * @param String $pkeyFields A serialized array of primary key values are returned by
	  	  * 			  		      getSerializedPrimaryKeyColumns.
	  	  *
	  	  * @return The XSL string
	  	  */
	     private function getSerializedPrimaryKeyColumnsAsXSL($pkeyFields) {

	     		 $xsl = null;
	     		 $pieces = explode('_', $pkeyFields);
	     	     for($i=0; $i<count($pieces); $i++) {

	     	       	  $xsl .= '{' . $pieces[$i] . '}';
	     	       	  if(($i+1) < count($pieces))
	     	       	  	  $xsl .= '_';
	     	     }

	   		   	 return $xsl;
	     }

	  	 /**
	  	  * Returns an 'AgilePHP serialized' string of primary key values for a foreign
	  	  * table reference.
	  	  *
	  	  * @param Table $table The table instance used to extract foreign key values
	  	  * @return An 'AgilePHP serialized' string for use in XSL rendering
	  	  */
		 private function getSerializedForeignKeyValuesAsXSL(Table $table) {

		 		 $keys = array();
		 		 $fkeys = $table->getForeignKeyColumns();
		 		 for($i=0; $i<count($fkeys); $i++) {

		 		     $fkey = $fkeys[$i]->getForeignKey();
		 		     array_push($keys, '{' . $fkey->getReferencedTableInstance()->getModel() . '/' . $fkey->getReferencedColumn() . '}');
		 		 }

		 		 return implode('_', $keys);
	  	 }
}
?>