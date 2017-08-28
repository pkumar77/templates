<?
/*
 * db.class.php
 *
 * Description: Very basic MySQL wrapper class
 *
 * Usage as in the following example
 *
 * $db = new db();
 * $db->query("select name from my_table where id=1");
 *	if ($db->getRowCount()) {
 * 	while ($data = $db->getRow())
 * 		print $data['name'] / . "<br>";
 *   }
 *
 * Other functions:
 *    Insert() - does a database insert
 *    Update() - updates a record
 */
class db {
	
	public function db($server=DB_SERVER, $user=DB_USER, $pass=DB_PASS, $name=DB_NAME) {
		$this->conn = mysql_connect($server, $user, $pass, 1) or die("Database error");
		mysql_select_db($name, $this->conn);
		mysql_set_charset('latin1',$this->conn);
	}
	
	public function query($q, $ret=1) {
		if ($this->conn)
			{
			$this->result = mysql_query($q, $this->conn);
			if (DB_PRINT_ERRORS && mysql_errno())
				print mysql_error() . ":: $q<br>";
			if (DB_PRINT_QUERIES)
				print "<br>$q<br>";
			if (!is_bool($this->result))
				$this->row_count = mysql_num_rows($this->result);
			}
	}
	
	public function Insert($datatype, $recArray, $exec=1, $raw=0) {
		global $currentUser;
		if ($this->conn)
			{
			$fieldlist = array();
			$valuelist = array();
			$this->last_insert_id = 0;

			foreach ($recArray as $key=>$value)
				{
				$fieldlist[] = field_name_format($key);
				if ($raw)
					$valuelist[] = (is_array($value)) ? implode(", ", $value) : $value;
				else
					$valuelist[] .= mysql_real_escape_string((is_array($value)) ? implode(", ", $value) : $value);
				}

//			if (!array_key_exists("username", $recArray) || $recArray['username'] == "")
//				$recArray['username'] = $currentUser->getUserId();
				
			$query = sprintf("insert into %s (%s) values ('%s');", field_name_format($datatype), 
						implode(",", $fieldlist), 
						implode("','", $valuelist));

			if ($exec)
				{
				$this->result = mysql_query($query, $this->conn);
				$this->last_insert_id = mysql_insert_id($this->conn);
				if (mysql_error())
					print "<!-- " . mysql_error() . ":: $query -->";
				}
			else
				print "<br>$query<br>";
			}
		if ($exec)
			return($this->last_insert_id);
		else
			return(1);
	}

	public function getColumns($table_name){
		$this->query("SHOW COLUMNS FROM ". $table_name);
	  if ($this->getRowCount() > 0) {
	    while($row = $this->getRow()){
	    	$this->fieldset[]=$row['Field'];
	    }
	  }
	  return($this->fieldset);
	}

	public function Update($datatype, $recArray, $exec=1, $criteria="", $indexFld="id") {
		if ($this->conn)
			{
			$i = 0;
			$valuelist = "";
			foreach ($recArray as $key=>$value)
				{
				$key = field_name_format($key);
				if ($key != $indexFld)
					{
					if ($i++)
						$valuelist .= ", ";
					$valuelist .= "$key='" . mysql_real_escape_string((is_array($value)) ? implode(", ", $value) : $value) . "'";
					}
				}
			if ($criteria == "" && $recArray[$indexFld])
				$criteria = "$indexFld = '" . $recArray[$indexFld] . "'";
			$query = sprintf("update %s set %s where %s;", field_name_format($datatype), $valuelist, $criteria);
			if ($exec)
				$this->result = mysql_query($query, $this->conn);
			else
				print "<br>$query<br>";
			if (DB_PRINT_ERRORS && mysql_errno())
				print mysql_error() . ":: $query<br>";
			}
		if ($exec)
			return($this->result);	/* In this case, it will just be true or false */
		else
			return(1);	/* We're not even trying the db write, so don't return an error */
	}

	public function getRowCount() {
		return $this->row_count;
	}
	
	public function getRow() {
		return(mysql_fetch_assoc($this->result));
		}
		
}
?>