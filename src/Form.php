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
 * @package com.makeabyte.agilephp
 */

/**
 * Generates a form by reverse engineering the specified domain model.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.2a
 */
class Form {

	  private $model;
	  private $id;
	  private $name;
	  private $action;
	  private $enctype;
	  private $request_token;

	  private $mode;

	  /**
	   * Creates a new instance of Form.
	   * 
	   * @param mixed $model Optional domain model instance used to create the form fields
	   * @param String $id Optional form id
	   * @param String $name Optional form name
	   * @param String $action Optional form action
	   * @param String $enctype Optional form enctype
	   * @param String $request_token Optional AgilePHP request token
	   * @return void
	   */
	  public function __construct( $model = null, $id = null, $name = null,
	  						$action = null, $enctype = null, $request_token = null ) {

	  		 $this->model = $model;
	  		 $this->id = $id;
	  		 $this->name = $name;
	  		 $this->action = $action;
	  		 $this->enctype = $enctype;
	  		 $this->request_token = $request_token;
	  }

	  /**
	   * Sets the domain model that is used to create the form 
	   * 
	   * @param Object $model The domain model object used to create the form
	   * @return void
	   */
	  public function setModel( $model ) {

	  		 $this->model = $model;
	  }

	  /**
	   * Returns the domain model instance being used to create the form.
	   * 
	   * @return Object The domain model instance
	   */
	  public function getModel() {

	  		 return $this->model;
	  }

	  /**
	   * Sets the form presentation mode.
	   * 
	   * @param String $mode The presentation mode (persist|merge).
	   * @return void
	   */
	  public function setMode( $mode ) {

	  		 $this->mode = $mode;
	  }

	  /**
	   * Returns the form presentation mode.
	   * 
	   * @return String The presentation mode of the form (persist|merge)
	   */
	  public function getMode() {

	  		 return $this->mode;
	  }

	  /**
	   * Sets the HTML id attribute value.
	   * 
	   * @param String $id The form's id
	   * @return void
	   */
	  public function setId( $id ) {

	  		 $this->id = $id;
	  }

	  /**
	   * Returns the form's id attribute value
	   * 
	   * @return String The id value
	   */
	  public function getId() {

	  		 return $this->id;
	  }

	  /**
	   * Sets the HTML name attribute value
	   * 
	   * @param String $name The form's name
	   * @return void
	   */
	  public function setName( $name ) {

	  		 $this->name = $name;
	  }

	  /**
	   * Returns the form's HTML name attribute value
	   * 
	   * @return String The form's name
	   */
	  public function getName() {

	  		 return $this->name;
	  }

	  /**
	   * Sets the form action attribute value
	   * 
	   * @param String $action The form's action
	   * @return void
	   */
	  public function setAction( $action ) {

	  		 return $this->action = $action;
	  }

	  /**
	   * Returns the forms action attribute value
	   * 
	   * @return String The controller action method
	   */
	  public function getAction() {

	  		 return $this->action;
	  }

	  /**
	   * Sets the form's enctype attribute value
	   * 
	   * @param String $enctype The form's enctype
	   * @return void
	   */
	  public function setEnctype( $enctype ) {

	  		 $this->enctype = $enctype;
	  }

	  /**
	   * Returns the form's enctype attribute value
	   * 
	   * @return String The form's enctype
	   */
	  public function getEnctype() {

	  		 return $this->enctype;
	  }

	  /**
	   * Sets the anti-CSFR token to render within the form. Note that the CSFR token
	   * should be different on every form!
	   * 
	   * @param String $token CSFR token as supplied by RequestScope->getToken().
	   * @return void
	   * @see RequestScope::createToken()
	   */
	  public function setRequestToken( $token ) {

	  		 $this->request_token = $token;
	  }

	  /**
	   * Returns the anti-CSFR token
	   * 
	   * @return String anti-CSFR token specific to the form
	   */
	  public function getRequestToken() {

	  		 return $this->request_token;
	  }

	  /**
	   * Returns the form in HTML format
	   * 
	   * @return String The HTML generated form
	   */
	  public function getHTML() {

	  		 if( !is_object( $this->getModel() ) ) throw new AgilePHP_Exception( 'Valid domain model required' );

	  	     $id = $this->id == null ? '' : ' id="' . $this->id . '" ';
	  	     $name = $this->name == null ? '' : ' name="' . $this->name . '" ';
	  	     $action = $this->action == null ? '' : ' action="' . $this->action . '" ';
	  	     $enctype = $this->enctype == null ? '' : ' enctype="' . $this->enctype . '" ';

	  		 $html = '<form ' . $id . $name . $action . $enctype . ' method="post">';
	  		 $html .= '<table class="agilephpGeneratedTable" border="0">';

	  		 $pm = new PersistenceManager();
	  		 $table = $pm->getTableByModel( $this->model );
	  		 foreach( $table->getColumns() as $column ) {

	  		 		  if( !$column->isVisible() ) continue;

			  		  $name = $column->getModelPropertyName();
			  		  $displayName = $column->getViewDisplayName();
			  		  $accessor = $pm->toAccessor( $name );
			  		  $value = $this->getModel()->$accessor();

			  		  $html .= '<tr>';

	  				  if( !$column->isVisible() ) {

	  		 		  	  if( $column->isPrimaryKey() )
		 	      	  	  	  $xsl .= '<input type="hidden" name="' . $name . '" value="' . $value . '"/>';
	  		 		  }

			  		  // Process foreign keys first
			  		  else if( $column->isForeignKey() ) {

			  		  	  $html .= '<td>' . $displayName . '</td>
			  		  	  		    <td>' . $this->getForeignKeySelection( $column->getForeignKey() ) . '</td>';
			  		  }

  		 	      	  // Primary key during merge is read only
  		 	      	  else if( $column->isPrimaryKey() ) {

  		 	      	  	  $html .= '<td>' . $displayName . '</td>';
  		 	      	      $html .= ($this->getMode() == 'merge') ?
  		 	      	  	  	  			'<td><input type="text" readonly="true" name="' . $name . '" value="' . $value . '"/></td>'
  		 	      	  	  	  			: '<td><input type="text" name="' . $name . '" value="' . $value . '"/></td>';
  		 	      	  }

  		 	      	  // Password field
  		 	      	  else if( $column->getType() == 'password' || $column->getName() == 'password' || $column->getProperty() == 'password' ) {

  		 	      	      $html .= '<td>Password</td>
  		 	      	      			<td><input type="password" name="password1" value="' . $value . '"/></td>
  		 	      	      		</tr>
  		 	      	      		<tr>
  		 	      	      			<td>Confirm</td>
  		 	      	      			<td>
  		 	      	      				<input type="password" name="password2" value="' . $value . '"/>
  		 	      	      				<input type="hidden" name="oldPassword" value="' . $value . '"/>
  		 	      	      			</td>';
  		 	      	  }

  		 	      	  // Auto-increment
  		 	      	  else if( $column->isAutoIncrement() ) {

  		 	      	  	  $html .= '<td>' . $displayName . '</td>';
						  $html .= ($this->getMode() == 'merge') ? 
  		 	      	  	  	  			'<td><input type="text" readonly="true" name="' . $name . '" value="' . $value . '"/></td>'
  		 	      	  	  	  			: '<td><input type="text" name="' . $name . '" value="' . $value . '"/></td>';
  		 	      	  }

  		 	      	  // Checkbox
					  else if( $column->getType() == 'bit' ) {

					  	  //$value = (ord($value) == 1);

					  	  $html .= '<td>' . $displayName . '</td>';
					  	  $html .= ($value == 1) ?
						                '<td><input type="checkbox" checked="true" name="' . $name . '" value="1"/></td>'
					  	  	       		: '<td><input type="checkbox" checked="true" name="' . $name . '" value="0"/></td>';
					  }

					  // Textarea
					  else if( $column->getType() == 'text' ) {

						  $html .= '<td>' . $displayName . '</td>
						  			<td><textarea rows="10" name="' . stripslashes( $name ) . '"></textarea></td>';
					  }

					  // File upload
					  else if( $column->getType() == 'blob' ) {

						  $html .= '<td>' . $displayName . '</td>
						  			<td><input type="file" name="' . $name . '" value="' . $value . '"/></td>';
						  continue;
					  }

					  // Default element (textfield)
					  else {

		  		  		  $html .= '<td>' . $displayName . '</td>
		  		  		  			<td><input type="text" name="' . $name . '" value="' . stripslashes( $value ) . '"/></td>';
					  }

	  		  		  $html .= '</tr>';
	  		 }

	  		 $html .= '<tr>
 						<td> </td>
						<td>';
	  		 $html .= $this->getMode() == 'persist' ? '<input type="submit" value="Create"/> <input type="button" value="Cancel" onclick="javascript:history.go( -1 );"/>' 
        							 : '<input type="submit" value="Update"/>
									   <input type="button" value="Delete" onclick="javascript:AgilePHP.Persistence.confirmDelete( \'' . AgilePHP::getFramework()->getRequestBase() .
        							   '\', \'' . $pkeyValues . '\', \'' . $this->getPage() . '\', \'delete\' )"/>
        							   <input type="button" value="Cancel" onclick="javascript:history.go( -1 );"/>';

        	 $html .= '</td>
							</tr>';

	  		 if( $token = $this->getRequestToken() )
	  		 	 $html .= '<input type="hidden" name="AGILEPHP_REQUEST_TOKEN" value="' . $token . '"/>';

	  		 $html .= '</form>';

	  		 return $html;
	  }

	  /**
	   * Returns an HTML select drop-down which contains a list
	   * of values for foreign key columns used in one-to-many
	   * relationships.
	   * 
	   * @return String The populated HTML <select> element
	   */
	  public function getForeignKeySelection( ForeignKey $foreignKey ) {

	  		 $pm = new PersistenceManager();

	  		 $selectedColumn = $foreignKey->getSelectedColumnInstance();
	  		 $selectedProperty = $selectedColumn->getModelPropertyName();

   			 // Set the SQL distinct clause to the desired parent column
   			 $pm->setDistinct( $selectedProperty );

   			 // Create an instance of the foreign model
   			 $foreignInstance = $foreignKey->getReferencedTableInstance()->getModelInstance();

   			 // Find all foreign models by distinct parent column name
          	 $foreignModels = $pm->find( $foreignInstance );

          	 // The actual foreign key property name in the child table
          	 $property = $foreignKey->getColumnInstance()->getModelPropertyName();

          	 $html = '<select name="' . $property . '">
          	 			<option value="NULL" selected="yes">Choose...</option>';

	  		 if( is_array( $foreignModels ) ) {

	          	 // Create each of the option values. If the foreign key value matches the
	          	 // selected foreign model property value, the item is shown as the default.
	          	 foreach( $foreignModels as $fModel ) {
	
	          	 		  $fAccessor = $pm->toAccessor( $selectedProperty );
	          	 		  $fkInstanceAccessor = $pm->toAccessor( $foreignKey->getReferencedTableInstance()->getModel() );
	
	          	 		  if( is_object( $this->getModel()->$fkInstanceAccessor() ) && 
	          	 		  			$this->getModel()->$fkInstanceAccessor()->$fAccessor() == $fModel->$fAccessor() )
	          	 		  	  $html .= '<option value="' . $fModel->$fAccessor() . '" selected="yes">' . $fModel->$fAccessor() . '</option>';
	          	 		  else
	          	 		  	  $html .= '<option value="' . $fModel->$fAccessor() . '">' . $fModel->$fAccessor() . '</option>';
	          	 }
          	 }

          	 else if( is_object( $foreignModels ) ) {
          	 	
          	 	  $fAccessor = $pm->toAccessor( $selectedProperty );
	          	  $html .= '<option value="' . $foreignModels->$fAccessor() . '" selected="yes">' . $foreignModels->$fAccessor() . '</option>';
          	 }

          	 $html .= '</select>';

          	 return $html;
	  }

	  /**
	   * Returns the form in XSL format
	   * 
	   * @param String $pkeyValues The primary key values for the ActiveRecord displayed in the form. Multiple
	   * 						   primary keys must be separated by an underscore _ character. Default is null
	   * @param Integer $page The list page number to return the user after the form is submitted. Default is 1.
	   * @return String XSL formatted form
	   */
	  public function getXSL( $pkeyValues = null, $page = 1 ) {

	 	  	 $id = $this->id == null ? '' : ' id="' . $this->id . '" ';
	  	     $name = $this->name == null ? '' : ' name="' . $this->name . '" ';
	  	     $action = $this->action == null ? '' : ' action="' . $this->action . '" ';
	  	     $enctype = $this->enctype == null ? '' : ' enctype="' . $this->enctype . '" ';

	  		 $xsl = '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	  		 		 	<xsl:template match="Form">
	  		 		 		<form ' . $id . $name . $action . $enctype . ' method="post">
	  		 		 			<table class="agilephpGeneratedTable" border="0">';

						  		 $pm = new PersistenceManager();
						  		 $table = $pm->getTableByModel( $this->model );
						  		 foreach( $table->getColumns() as $column ) {

								  		  $name = $column->getModelPropertyName();
								  		  $displayName = $column->getViewDisplayName();
								  		  $accessor = $pm->toAccessor( $name );
			  		  					  $value = $this->getModel()->$accessor();

								  		  $xsl .= '<tr>';

						  		 		  if( !$column->isVisible() ) {
						  		 		  	
						  		 		  	  if( $column->isPrimaryKey() )
					  		 	      	  	  	  $xsl .= '<input type="hidden" name="' . $name . '" value="{/Form/' . $table->getModel() . '/' . $name . '}"/>';
						  		 		  }

								  		  // Process foreign keys first
								  		  else if( $column->isForeignKey() ) {
					
								  		  	  $xsl .= '<td>' . $displayName . '</td>
								  		  	  		   <td>' . $this->getForeignKeySelection( $column->getForeignKey() ) . '</td>';
								  		  }
					
					  		 	      	  // Primary key during merge is read only
					  		 	      	  else if( $column->isPrimaryKey() ) {
					
					  		 	      	  	  $xsl .= '<td>' . $displayName . '</td>';
					  		 	      	      $xsl .= ($this->getMode() == 'merge') ?
					  		 	      	  	  	  			'<td><input type="text" readonly="true" name="' . $name . '" value="{/Form/' . $table->getModel() . '/' . $name . '}"/></td>'
					  		 	      	  	  	  			: '<td><input type="text" name="' . $name . '" value="{/Form/' . $table->getModel() . '/' . $name . '}"/></td>';
					  		 	      	  }

					  		 	      	  // Password field
					  		 	      	  else if( $column->getType() == 'password' || $column->getName() == 'password' || $column->getProperty() == 'password' ) {

					  		 	      	      $xsl .= '<td>Password</td>
					  		 	      	      		   <td><input type="password" name="password1" value="{/Form/' . $table->getModel() . '/' . $name . '}"/></td>
					  		 	      	      		 </tr>
					  		 	      	      		 <tr>
					  		 	      	      		   	<td>Confirm</td>
					  		 	      	      		    <td>
					  		 	      	      		    	<input type="password" name="password2" value="{/Form/' . $table->getModel() . '/' . $name . '}"/>
					  		 	      	      		    	<input type="hidden" name="oldPassword" value="{/Form/' . $table->getModel() . '/' . $name . '}"/>
					  		 	      	      		    </td>';
					  		 	      	  }

					  		 	      	  // Auto-increment
					  		 	      	  else if( $column->isAutoIncrement() ) {
					
					  		 	      	  	  $xsl .= '<td>' . $displayName . '</td>';
											  $xsl .= ($this->getMode() == 'merge') ? 
					  		 	      	  	  	  			'<td><input type="text" readonly="true" name="' . $name . '" value="{/Form/' . $table->getModel() . '/' . $name . '}"/></td>'
					  		 	      	  	  	  			: '<td><input type="text" name="' . $name . '" value="{/Form/' . $table->getModel() . '/' . $name . '}"/></td>';
					  		 	      	  }

					  		 	      	  // Checkbox
										  else if( $column->getType() == 'bit' ) {
	  	  
										  	  $xsl .= '<td>' . $displayName . '</td>';
										  	  
										  	  // enabled
										  	  $xsl .= '<xsl:if test="/Form/' . $table->getModel() . '/' . $name . ' = 1">';
										  	  		$xsl .= '<td><input type="checkbox" checked="true" name="' . $name . '" value="1"/></td>';
										  	  $xsl .= '</xsl:if>';

										  	  // disabled
										  	  $xsl .= '<xsl:if test="/Form/' . $table->getModel() . '/' . $name . ' != 1">';
										  	  		$xsl .= '<td><input type="checkbox" name="' . $name . '" value="1"/></td>';
										  	  $xsl .= '</xsl:if>';

										  	  // add operation that doesnt contain a model - default to disabled 
										  	  $xsl .= '<xsl:if test="/Form/' . $table->getModel() . ' = \'\'">';
										  	  		$xsl .= '<td><input type="checkbox" name="' . $name . '" value="1"/></td>';
										  	  $xsl .= '</xsl:if>';

										  }

						  		 		  // Textarea
										  else if( $column->getType() == 'text' ) {

										  	  $xslValue = mb_convert_encoding( html_entity_decode( $value ), 'UTF-8', 'ISO-8859-1' );

											  $xsl .= '<td>' . $displayName . '</td>
											  		   <td><textarea rows="10" name="' . $name . '">';
											  				if( !$xslValue ) $xsl .= '<xsl:comment/>';
											  				$xsl .= '<xsl:value-of select="/Form/' . $table->getModel() . '/' . $name . '"/></textarea>
							  		 				   </td>';
										  }

										  // File upload
										  else if( $column->getType() == 'blob' ) {

											  $xsl .= '<td>' . $displayName . '</td>';
											  			
											  $xsl .= ($this->getMode() == 'merge') ?
											  	           '<td><img border="0" height="30" width="200" src="{/Form/' . $table->getModel() . '/' . $name . '}"/>
											  	               <br/>
											  	  		       <input type="file" name="' . $name . '" value="{/Form/' . $table->getModel() . '/' . $name . '}"/>
											  	  		   </td>'
											  	  		   : '<td><input type="file" name="' . $name . '" value="{/Form/' . $table->getModel() . '/' . $name . '}"/></td>';
										  }

										  // Default element (textfield)
										  else {

							  		  		  $xsl .= '<td>' . $displayName . '</td>
							  		  		  		   <td><input type="text" name="' . $name . '" value="{/Form/' . $table->getModel() . '/' . $name . '}"/></td>';
										  }
					
						  		  		  $xsl .= '</tr>';
						  		 }

	  		   $xsl .= '<tr>
		 				  <td> </td>
						  <td>';
        	              $xsl .= $this->getMode() == 'persist' ? '<input type="submit" value="Create"/> <input type="button" value="Cancel" onclick="javascript:history.go( -1 );"/>' 
        				        						 : '<input type="submit" value="Update"/>
															<input type="button" value="Delete" onclick="javascript:AgilePHP.Persistence.confirmDelete( \'' . AgilePHP::getFramework()->getRequestBase() .
        													   '\', \'' . $pkeyValues . '\', \'' . $page . 
        													   '\', \'{/Form/controller}\', \'delete\' )"/>
        													   <input type="button" value="Cancel" onclick="javascript:history.go( -1 );"/>';
			   $xsl .= '  </td>
				        </tr>';

	  		   if( $token = $this->getRequestToken() )
	  		 	   $xsl .= '<input type="hidden" name="AGILEPHP_REQUEST_TOKEN" value="' . $token . '"/>';

	  		   $xsl .= '  </table>
	  		   			 </form>
		  		        </xsl:template>
					  </xsl:stylesheet>';

	  		   Logger::getInstance()->debug( $xsl );

	  		   return $xsl;
	  }

	  /**
	  public function getJSON() {

	  		 $js = '{ form : { 
	  		 					"id" : "' . $this->id == null ? '' : $this->id . ',
	  	     					"name" : "' . $this->name == null ? '' : $this->name . ',
	  	     					"action" : "' . $this->action == null ? '' : $this->action . ',
	  	     					"enctype" : "' . $this->enctype == null ? '' : $this->enctype . '
	  						 }
	   				}';

	  		 return $js;
	  
	  }
	  */
}
?>