<?

/*
 * Container.class.php
 *
 * Description: Generic container class. Provides automatic get/set/list functions for 
 * all data types that extend it, along with some special case functions
 *
 * General overview of the overloaded functions:
 *
 *   $x = getVariableName() will return the value of $this->variable_name
 *
 *   setVariableName($value) will set $this->variable_name to $value
 *
 *   listVariableName() will return an array of values from an external table where the 
 *                      keyfield matches the current class name. 
 *                      So, ItemClass::listVariableName() will get all id's from the MySQL
 *                      table variable_name where item_class_id is equal to $this->id
 *
 *   db_field_array($exclude_id) reads all fields from the MySQL table and returns an array.
 *                      The ID field can be omitted if the list is to be used for creating a
 *                      new record, because we don't want to pass a value for that field on insert
 *
 *   write($line, $id, $exec) inserts a new record into the table, handles all version housework,
 *                      and updates any external id fields that reference the table.
 *
 *   update($line, $id, $all_versions, $exec) modifies the specified record in the database with no versioning.
 *                      IMPORTANT: Only use this function if you do not want the version and username
 *                      to change, ostensibly, if we're editing something as a result of an XPT change.
 *
 *   delete($id) deletes a record and takes care of external references and versioning. $id should be
 *                      left null if we're deleting the current record
 */
class Container {

	private $max_versions = 4;
	private $dependency_list = array();
	private $trigger_list = array();
			
	public function Container($id, $current=1, $fieldname='id') {
		$this->db = new db();
		if ($id)	/* if this is zero, we have a new record */
			{
			$qstr = "select * from " . field_name_format(get_class($this)) . " where $fieldname='$id'";
			$this->database_init($qstr);
			}
		$this->db_fields = $this->db_field_array($current);
	}
	
	public function fresh() {
		$qstr = "select parent_id from " . field_name_format(get_class($this)) . " where id=" . $this->id;
		$this->db->query($qstr);
		$data = $this->db->getRow();
		return($data['parent_id']);
	}
	
	public static function FreshId($id) {
		$rval = 0;
		$tempdb = new db();
		$qstr = "select parent_id from " . field_name_format(get_class($this)) . " where id=" . $id;
		$tempdb->query($qstr);
		if ($tempdb->getRowCount())
			{
			$data = $tempdb->getRow();
			$rval = $data['parent_id'];
			}
		return($rval);
	}
	
	public function __set($name, $value) {
			$this->$name = $value;
		}

	public function __get($name) {
		return($this->$name);
		}
	
	public function __call($methodName, $args) {
		if (preg_match('~^(set|get|list|dir)([A-Z])(.*)$~', $methodName, $matches))
			{
			$fieldname = $matches[2] . $matches[3];
			$property = field_name_format($fieldname);
			switch($matches[1])
				{
				case 'set':
					$this->$property = $args[0];
					break;
				case 'get':
					return $this->$property;
				case 'list':		/* In the case of a list, either populate a class member or just return the data */
					$tmp = $this->makeList($fieldname);
					if (is_array($args) && count($args) && $args[0] == TRUE)
						$this->$property = $tmp;
					else
						return($tmp);
					break;
				case 'dir':		/* Not quite a list, this isn't getting a member set, it's returning a list of its own values */
					$mask = (count($args)) ? $args[0] : "";		/* if there is no mask, we're probably just getting an ID list */
					$qual = (count($args) > 1) ? $args[1] : "";
					$multi = (count($args) > 2) ? $args[2] : "";
					$maxlen = (count($args) > 3) ? $args[3] : 0;
					$curidx = (count($args) > 4) ? $args[4] : 0;
					$tmp = $this->makeDir($fieldname, $mask, $qual, $multi, $maxlen, $curidx);
					return($tmp);
					break;
				}
			}
	}

	public function db_field_array($exclude_id=1) {
		/* We want to exclude the ID if we're creating a new record, because we'd get a duplicate ID when we wrote it */
		$rarray = array();
		$qstr = "select column_name from information_schema.columns where table_schema='" . DB_NAME ."' and table_name='" . field_name_format(get_class($this)) . "'";
		if ($exclude_id)
			$qstr .= " and column_name not like 'id'";
		$this->db->query($qstr);
		while ($data = $this->db->getRow())
			$rarray[] = $data['column_name'];
		return($rarray);
	}


	/* The following is probably not necessary, but can be called if there  */
	/* is any question about the class holding data from a previous instance. */
	/* To be ultra-obsessive, overload the destructor, but that is extreme */
/*	function __destruct() { */
	function destroy($remove_id=1) {
		$dataset = get_object_vars($this);
		foreach($dataset as $key=>$var)
			{
			if ($remove_id == 1 || $key != "id")
				unset($this->$key);
			}
	}

	public function write($line=NULL, $id=0, $exec=1) {
		$dbtemp = new db();
		$this->date_time = Date("Y-m-d H:i:s");
		/* If we have no data provided, use the current record */
		if (!is_array($line) || !count($line))
			{
			if ($this->id)
				$id = $this->id;
			$line = array();
			foreach ($this->db_fields as $fld)
				$line[$fld] = $this->$fld;
			}
		$curidx = $this->db->Insert(get_class($this), $line, $exec);
		if (DEBUG) print "new index is " . $curidx . "<br>";
		if ($id)	/* if it is a completely new record, there can't be parents */
			{
			$qstr = "select id, rev_number from " . field_name_format(get_class($this)) . " where parent_id=" . $id . " order by rev_number";
			$this->db->query($qstr);
			if (DEBUG) print $qstr . "<br>";			
			if ($this->db->getRowCount())
				{
				while ($data = $this->db->getRow())
					{
					if ($data['rev_number'] >= $this->max_versions)	/* GE because we're going to increase it past max, potentially */
						$qstr = "delete from " . field_name_format(get_class($this)) . " where id=" . $data['id'];
					else
						$qstr = "update " . field_name_format(get_class($this)) . " set rev_number=" . ($data['rev_number'] + 1) . ", parent_id=" . $curidx . " where id=" . $data['id'];
					$dbtemp->query($qstr);
					if (DEBUG) print $qstr . "<br>";			
					}
				if (count($this->dependency_list))
					{
					foreach ($this->dependency_list as $dep)
						{
						$darray = array(field_name_format(get_class($this)) . "_id" => $curidx);
						$this->db->Update($dep, $darray, field_name_format(get_class($this)) . "_id=$id", $exec);
						}
					}
				}
			}
		$flist = array("parent_id" => $curidx, "rev_number" => 1, "username" => $_SESSION['login_name']);
		$this->update($flist, $curidx, 0);
		return($curidx);
	}

	public function cloneItem($exec=1, $debug=0) {
		global $fieldNameList;
		$class = get_class($this);
		$dbtemp = new db();
		$this->date_time = Date("Y-m-d H:i:s");
		if (!is_array($line) || !count($line))
			{
			$line = array();
			foreach ($this->db_fields as $fld)
				$line[$fld] = $this->$fld;
			$line['id'] = 0;
			}
		if ($class == "Study")
			$line['deliverable_title'] = $_SESSION['cur_study_name'];
		$curidx = $this->db->Insert(get_class($this), $line, $exec);
		if ($debug) print "new index is " . $curidx . "<br>";
		if ($class == "Study")
			CloneStudyId($curidx);
		if ($class == "Dataset")
			CloneDatasetId($curidx);
		if (count($this->trigger_list))
			{
			foreach ($this->trigger_list as $dep)
				{
				$depName = preg_replace('/(?:^|_)(.?)/e',"strtoupper('$1')",$dep);
				$depItemList = $this->makeList($depName);
				if (is_array($fieldNameList) && array_key_exists(field_name_format($class), $fieldNameList))
					print "<h4>" . $this->$fieldNameList[field_name_format($class)] . "</h4>";
				ob_flush(); flush();
				if ($debug) var_dump($depItemList);
				foreach ($depItemList as $dep)
					{
					$ixname = field_name_format($class) . "_id";
					$dep->$ixname = $curidx;
					$dep->study_id = CloneStudyId();
					$dep->dataset_id = CloneDatasetId();
					$dep->cloneItem();
					}
				}
			}
		$flist = array("parent_id" => $curidx, "rev_number" => 1, "username" => $_SESSION['login_name']);
		$this->update($flist, $curidx, 0);
		return($curidx);
	}

	public function history() {
		$class = get_class($this);
		$this->db->query("select id from " . field_name_format($class) . " where parent_id=" . $this->getId() . " order by rev_number");
		while ($data = $this->db->getRow())
			$rarray[] = new $class($data['id']);
		return($rarray);
	}
	
	public function update($line=NULL, $id=0, $all_versions = 1, $exec=1) {
		$dbtemp = new db();

		if (!$id && $this->id)
			$id = $this->id;
		if (!$id)
			return(0);	/* we can't update something we don't have */

		/* If we have no data provided, use the current record */
		/* In the case of update, we should usually have the data passed */
		if (!is_array($line) || !count($line))
			{
			$line = array();
			foreach ($this->db_fields as $fld)
				$line[$fld] = $this->$fld;
			}
		// You would use write() if you wanted versioning. This is just a straight record update
		if ($all_versions)
			{
			if (!$this->parent_id)	/* If we come in here with just an id and a value, get the record */
				{
				$qstr = $dbtemp->query("select parent_id from " . field_name_format(get_class($this)) . " where id=" . $id);
				$data = $dbtemp->getRow();
				$parid = $data['parent_id'];
				}
			else
				$parid = $this->parent_id;
			$done = $dbtemp->Update(get_class($this), $line, "parent_id=$parid", $exec);
			}	
		else
			$done = $dbtemp->Update(get_class($this), $line, "id=$id", $exec);
		return($done);
	}

	public function delete($id=0) {
		if (!$id)
			$id = $this->id;
		$qstr = "select parent_id, rev_number from " . field_name_format(get_class($this)) . " where id=" . $id;
		$this->db->query($qstr);
		$data = $this->db->getRow();

		$rev = $data['rev_number'];
		$parent = $data['parent_id'];

		$dbtemp = new db();

		if ($rev == 1)
			{
			$qstr = "select id, rev_number from " . field_name_format(get_class($this)) . " where parent_id=" . $id . " and rev_number > 1 order by rev_number asc";
			$this->db->query($qstr);
			if (DEBUG) print $qstr . "<br>";			
			if ($this->db->getRowCount())
				{
				// decrease all version numbers by one, make the current rev 2 everyone's parent
				while ($data = $this->db->getRow())
					{
					if ($data['rev_number'] == 2)
						$new_parent = $data['id'];
					$dbtemp->Update(field_name_format(get_class($this)), array("rev_number" => ($data['rev_number'] - 1), "parent_id" => $new_parent), "id=" . $data['id']);
					}
				// update the parent to new version 1
				if (count($this->dependency_list))
					{
					foreach ($this->dependency_list as $dep)
						{
						$this->db->Update($dep, array(field_name_format(get_class($this)) . "_id" => $new_parent), field_name_format(get_class($this)) . "_id=$id");
						}
					}
				}
			else
				{
				// we're trying to delete something that doesn't exist, because even rev 1 has itself as a parent
				return(0);
				}
			}
		else
			{
			// decrease all version numbers of the parent that are higher than this record
			$qstr = "select id, rev_number from " . field_name_format(get_class($this)) . " where parent_id = " . $parent . " and rev_number > " . $rev;
			$this->db->query($qstr);
			if (DEBUG) print $qstr . "<br>";			
			if ($this->db->getRowCount())
				{
				while ($data = $this->db->getRow())
					{
					$qstr = "update " . field_name_format(get_class($this)) . " set rev_number=" . ($data['rev_number'] - 1) . " where id=" . $data['id'];
					$dbtemp->query($qstr);
					if (DEBUG) print $qstr . "<br>";			
					}
				}
			}
		$qstr = "delete from " . field_name_format(get_class($this)) . " where id=" . $id;
		$this->db->query($qstr);
if (DEBUG) print $qstr . "<br>";			
	}
	
	// delete operates on the current version. Remove operates on all versions
	public function remove($id=0) {
		$dbtemp = new db();
		if (!$id)
			$id = $this->id;
		$qstr = "delete from " . field_name_format(get_class($this)) . " where parent_id=" . $id;
		$this->db->query($qstr);
		// If there are dependencies, orphan them by this field. We can clean up later
		if (count($this->dependency_list))
			{
			foreach ($this->dependency_list as $dep)
				$this->db->Update($dep, array(field_name_format(get_class($this)) . "_id" => 0), field_name_format(get_class($this)) . "_id=$id");
			}
	}

	public function makeList($class, $maxlen=0, $curidx=0) {
		$ar = array();
		$dbfld = field_name_format($class);
		$indexfld = field_name_format(get_class($this)) . '_id';
		$qstr = "select id from $dbfld where $indexfld=".$this->id . " and rev_number=1";
		if ($maxlen)
			$qstr .= " limit " . $maxlen;
		if ($curidx)
			$qstr .= ($maxlen) ? (", " . $curidx) : (" offset " . $curidx);
		$this->db->query($qstr);

		if ($this->db->getRowCount())
			{
			$i = 0;
			while ($data = $this->db->getRow())
				{
				foreach($data as $key=>$value)
					$ar[] = new $class($value);
				++$i;
				}
			}
		return($ar);
	}	

	public function makeSortedList($class, $sortfield="", $maxlen=0, $curidx=0) {
		$ar = array();
		$dbfld = field_name_format($class);
		$indexfld = field_name_format(get_class($this)) . '_id';
		$qstr = "select id from $dbfld where $indexfld=".$this->id . " and rev_number=1";
		if ($sortfield != "")
			$qstr .= " order by " . $sortfield;
		if ($maxlen)
			$qstr .= " limit " . $maxlen;
		if ($curidx)
			$qstr .= ($maxlen) ? (", " . $curidx) : (" offset " . $curidx);
		$this->db->query($qstr);

		if ($this->db->getRowCount())
			{
			$i = 0;
			while ($data = $this->db->getRow())
				{
				foreach($data as $key=>$value)
					$ar[] = new $class($value);
				++$i;
				}
			}
		return($ar);
	}	

	/* All sorts of things going on here, mostly unused */
	/* The mask returns something other than the id */
	/* qualifier is for narrowing the search */
	/* Multisort allows a sort on multiple columns */
	public function makeDir($sortfld, $mask, $qualifier="", $multisort="", $maxlen=0, $curidx=0) {
		$dbtemp = new db();
		$ar = array();
		if (strlen($multisort))
			$dbfld = $multisort;
		else
			$dbfld = field_name_format($sortfld);
		$qstr = "select * from " . field_name_format(get_class($this)) . " where rev_number=1";
		if (strlen($qualifier))
			$qstr .= " and (" . $qualifier . ")";
		$qstr .= " order by $dbfld";
		if ($maxlen)
			$qstr .= " limit " . $maxlen;
		if ($curidx)
			$qstr .= ($maxlen) ? (", " . $curidx) : (" offset " . $curidx);
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				{
				$masked = explode("@", $mask);
				for ($i = 0; $i < count($masked); ++$i)
					{
					if (array_key_exists($masked[$i], $data))
						{
						$key = $masked[$i];
						$masked[$i] = $data[$key];
						}
					}
				$tlist = implode("", $masked);
				$ar[] = (strlen($tlist)) ? array("id"=>$data['id'], "name"=>$tlist) : array("id"=>$data['id']);
				}
			}
		return($ar);
	}	

	public function database_init($query) {
		$this->db->query($query);
		if ($this->db->getRowCount())
				{ 
				$data = $this->db->getRow();
				foreach($data as $key=>$value)
					$this->$key = $value;
				return $this->db->getRowCount();
				}
			else
				return(0);
	}
	
}
?>