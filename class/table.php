<?php
	class Table {

		private $tuples=array();
		private $tableName;

		public function __construct($tableName, $tuple){

			$this->tableName = $tableName;
			array_push($this->tuples, $tuple);
		}

		public function addTuple($tuple){

			array_push($this->tuples, $tuple); 
		}

		public function getTuples(){

			return $this->tuples;
		}

		public function getTableName(){

			return $this->tableName;
		}

		public function display(){

			foreach($this->tuples as $tuple){
				$tuple->display();
			}
		}

	}