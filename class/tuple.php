<?php
	class Tuple {
		private $tableName;
		private $tuple;
		private $primaryKey;
		private $PKattribute;

		public function __construct($tableName, $tuple, $PKattribute) {

			$this->tableName=$tableName;
			$this->tuple=$tuple;
			$this->PKattribute=$PKattribute;
		}

		public function getTuple(){
			return $this->tuple;
		}

		public function getTableName(){
			return $this->tableName;
		}

		public function getPK(){
			return $this->primaryKey;
		}

		public function getPKattribute(){
			return $this->PKattribute;
		}

		public function setPK($id){
			$this->primaryKey = $id;
		}

		public function setPKattribute($PKattribute){
			$this->PKattribute = $PKattribute;
		}

		public function setTupleData($attribute, $data){
			$this->tuple[$attribute]=$data;
		}

		public function getDataByAttribute($searchAttribute){
			
			$result = FALSE;

			foreach($this->tuple as $attribute=>$key){
				if($searchAttribute==$attribute){
					$result=$key;
				}
			}
			return $result;
		}

		public function display(){
			echo "<p>primary key: " .$this->primaryKey. "<br>";
			echo "primary key attribute: " .$this->PKattribute. "</p>";
			foreach($this->tuple as $attribute => $data){
				echo $attribute. " => " .$data. "<br>";
			}

		}
	}
?>