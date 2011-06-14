<?php
/**
 * Test application Table1 model 
 */
class Table1 extends DomainModel {

      public function __construct() { }
    
      private $id;
      private $field1;
      private $field2;
       
      private $Table2;

      private $tables = array();

      #@Id
      public function setId($value) {
    
         $this->id = $value;
      }
    
      public function getId() {
    
         return $this->id;
      }
    
      public function setField1($value) {
    
         $this->field1 = $value;
      }
    
      public function getField1() {
    
         return $this->field1;
      }
    
      public function setField2($value) {
    
         $this->field2 = $value;
      }
    
      public function getField2() {
    
         return $this->field2;
      }
    
      public function setTable2(Table2 $value = null) {
    
         $this->Table2 = $value;
      }
    
      public function getTable2() {
    
         return $this->Table2;
      }
      
      public function setTables(array $tables) {
      	   $this->tables = $tables;
      }
      
      public function getTables() {
      	   return $this->tables;
      }
      
      public function addTable(Table2 $table) {
      	   array_push($this->tables, $table);
      } 
}
?>