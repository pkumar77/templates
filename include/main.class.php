<?php 
class Ajax{
	
	
	public function __construct(){
		$db = new db();
		$this->cols = $db->getColumns(TABLE_NAME);
	}

  public function _setVar($var,$val){
  	$this->$var=$val;
  }

  public function _getVar($var){
  		return ($this->${$var});
 	}
 
 	public function getFieldLabel(){
 		return strtoupper($this->dbcolumn);
	}

 	public function getElement(){
 		return ("<input type='text'>");
	}


	// build html form here from the fields retreived from table.
	public function showfrom_db(){
		echo "<table>";
		foreach($this->cols as $dbcolumn){
			$this->_setVar("dbcolumn",$dbcolumn);
			//echo "<tr><td>".$this->getFieldLabel()."</td><td>".$this->getElement()."</td></tr>";
		}
		echo "</table>";
	}
	
}

?>
