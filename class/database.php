<?php
	class Database {

		private $tables = array();

		public function __construct($table){

			$this->tables[$table->getTableName()] = $table;
		}

		public function addTable($table){

			$this->tables[$table->getTableName()] = $table; 
		}

		public function getTableByTableName($tableName){

			if(array_key_exists($tableName, $this->tables)){
				return $this->tables[$tableName];
			}
			else{
				return FALSE;
			}
			
		}

		public function getTables(){

			return $this->tables;
		}

		public function display(){

			foreach($this->tables as $table){
				echo "<h3>Table name: " .$table->getTableName(). "</h3>";
				$table->display();
			}
		}

	}