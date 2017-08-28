<?
/*
 * datatypes.class.php
 *
 * This file has the classes corresponding to all the MySQL tables in the studies database
 * Most of the work is done by the Container class, but display and formatting that is 
 * specific to the data structures and XML output is handled here
 *
 * dumpvalues() doesn't need to be here, and in fact will go away when we don't need debugging info
 *
 */

	
class Author extends Container {
	
	public function Author($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		$this->setDependencyList(array());
		$this->setTriggerList($this->getDependencyList());
	}

	public function dumpvalues() {
		var_dump($this);
	}

	public static function Seek($name) {
		$dbtemp = new db();
		$rval = 0;
		$qstr = "select * from author where fname like '$name' or lname like '$name'";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			$rval = $dbtemp->getRow();
		return($rval);
	}
}

class Comment extends Container {
	
	public function Comment($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		$this->setDependencyList(array());
		$this->setTriggerList($this->getDependencyList());
	}
	
	public function dumpvalues() {
		
		var_dump($this);
	}

}

class Formula extends Container {
	
	public function Formula($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		$this->setDependencyList(array());
		$this->setTriggerList($this->getDependencyList());
	}

	public function dumpvalues() {
		var_dump($this);
	}

	public function seekAuthor() {
		$dbtemp = new db();
		$rval = array();
		$qstr = "select author.* from author, history where history.formula_id=" . $this->getId() . " and author.id=history.author_id order by history.change_datedesc";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			$rval = $dbtemp->getRow();
		return($rval);
	}
	
	public function seekHistoryList() {
		return($this->makeSortedList("History", "change_date DESC");
	}

	public function seekCommentList() {
		return($this->makeSortedList("Comment", "change_date DESC");
	}

}

class History extends Container {
	
	public function History($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		$this->setDependencyList(array());
		$this->setTriggerList($this->getDependencyList());
	}

	public function dumpvalues() {
		var_dump($this);
	}

}

class Keyword extends Container {
	
	public function Keyword($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		$this->setDependencyList(array());
		$this->setTriggerList($this->getDependencyList());
	}
	public function dumpvalues() {
		var_dump($this);
	}

}

class SystemTables extends Container {
	public function SpecialCaseVariableList() {
		$dbtemp = new db();
		$rval = array();
		$qstr = "select variable_name from special_case_variables where 1";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				$rval[] = $data['variable_name'];
			}
		return($rval);
	}
}
class CodeList extends Container {
	
	public function CodeList($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		$this->setDependencyList(array("code_list_item","external_code_list"));
		$this->setTriggerList($this->getDependencyList());
	}
	
	public static function Seek($variable_item_id, $value_item_id=0) {
		$dbtemp = new db();
		$rval = 0;
		if ($value_item_id)
			$qstr = "select id from code_list where value_item_id='" . $value_item_id . "' and rev_number=1";
		else
			$qstr = "select id from code_list where variable_item_id='" . $variable_item_id . "' and rev_number=1";
		
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			$rval = $data['id'];
			}
		return($rval);
	}
	public function IsDatasetActive($standard="") {
		$dbtemp = new db();
		$rval = 0;
		if ($this->getVariableItemId())
			$qstr = "select dataset.active_yn, dataset.standard_type from dataset, variable_item where variable_item.dataset_id=dataset.id and variable_item.id=" . $this->getVariableItemId();
		else
			$qstr = "select dataset.active_yn, dataset.standard_type from dataset, value_item where value_item.dataset_id=dataset.id and value_item.id=" . $this->getValueItemId();
		
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			if ($data['active_yn'] == 'Y' && ($standard == "" || $data['standard_type'] == $standard))
				$rval = 1;
			}
		return($rval);
	}
	public function output($tabs=0) {
		$cl = $this->makeSortedList("CodeListItem", "rank");
		if (count($cl))
			{
			$dbtemp = new db();
			$dbtemp->query("SELECT dataset.dataset_name, variable_item.name, variable_item.data_type FROM dataset,variable_item where dataset.id = variable_item.dataset_id and variable_item.id=" . $this->getVariableItemId());
			$data = $dbtemp->getRow();
			$dt = $data['data_type'];
			$ds = $data['dataset_name'];
			$nm = $data['name'];
			xLineOut(sprintf('<CodeList OID="CodeList.%s" Name="%s.%s" DataType="%s">', $nm, $ds, $nm, $dt), $tabs);
			$cl = $this->makeSortedList("CodeListItem", "rank");
			foreach ($cl as $cli)
				xLineOut(sprintf('<CodeListItem CodedValue="%s"><Decode><TranslatedText xml:lang="%s">%s</TranslatedText></Decode></CodeListItem>',
							$cli->getCodedValue(), "en", $cli->getPreferredTerm()),$tabs+1);
			xLineOut(sprintf('</CodeList>'), $tabs);
			}
	}
	public function dumpvalues() {
		var_dump($this);
	}
}
class CodeListItem extends Container {
	
	public function CodeListItem($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		$this->setDependencyList(array("translation"));
		$this->setTriggerList($this->getDependencyList());
	}
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class CodeListRef extends Container {
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class ComputationMethod extends Container {
	
	public static function Seek($dataset_name, $variable_name) {
		$dbtemp = new db();
		$rval = 0;
		$qstr = "select id from computation_method where " . studyQueryPart() . " and dataset_name='" . $dataset_name . "' and variable_name='" . $variable_name . "' and rev_number=1";
		
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			$rval = $data['id'];
			}
		return($rval);
	}

	public function dumpvalues() {
		var_dump($this);
	}
}
function byname($a, $b)
{
    if ($a['name'] == $b['name']) {
        return 0;
    }
    return ($a['name'] < $b['name']) ? -1 : 1;
}
class Dataset extends Container {
	
	public function Dataset($id=0, $current=1, $fieldname='id') {
		/* The Container class deals with most of the initialization. */
		/* We only want to handle table-specific items outside the default constructor */
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		
		// The array being passed into this function consists of all 
		// tables that will have a field that links back to the id of this table
		$this->setDependencyList(array("variable_item","value_item","value_list","temp_variable_item","temp_value_item"));
		$this->setTriggerList(array("variable_item","temp_variable_item","temp_value_item"));
	}
	
	public static function Seek($dataset_name) {
		$dbtemp = new db();
		$rval = 0;
		$qstr = "select id from dataset where " . studyQueryPart() . " and dataset_name='" . $dataset_name . "' and rev_number=1";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			$rval = $data['id'];
			}
		return($rval);
	}
	
	public function KeyList() {
		$dbtemp = new db();
		$rval = "";
		$qstr = "select name from variable_item where dataset_id=" . $this->getId() . " and rev_number=1 and is_domain_key>0 order by is_domain_key";
		$dbtemp->query($qstr);

		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				$rval .= $data['name'] . ", ";
			}
		return(substr($rval, 0, -2));
	}
	
	public function VarList($indexed=0) {
		$dbtemp = new db();
		$rval = array();
		$qstr = "select id, name from variable_item where dataset_id=" . $this->getId() . " and rev_number=1";
		$dbtemp->query($qstr);

		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				{
				if ($indexed)
					$rval[$data['id']] = $data['name'];
				else
					$rval[] = $data['name'];
				}
			}
		return($rval);
	}
	
	public function ValList($indexed=0) {
		$dbtemp = new db();
		$rval = array();
		$qstr = "select value_item.id, value_item.name, variable_item.name as var_name from value_item,variable_item where value_item.dataset_id=" . $this->getId() . " and variable_item.id=value_item.variable_item_id and value_item.rev_number=1";
		$dbtemp->query($qstr);

		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				{
				if ($indexed)
					$rval[$data['id']] = $data['var_name'] . "." . $data['name'];
				else
					$rval[] = $data['var_name'] . "." . $data['name'];
				}
			}
		return($rval);
	}
	
	public function SdtmigVariableCandidates() {
		$dbtemp = new db();
		$obClass = "";
		$rarray = array();
		$variableList = $this->VarList();
		$dn = $this->getDatasetName();
		if ($dn[0] == "X")
			$obClass = "Findings-General";
		else if ($dn[0] == "Y")
			$obClass = "Events-General";
		else if ($dn[0] == "Z")
			$obClass = "Interventions-General";
		if ($obClass != "")
			$qstr = "select id, variable_name, core from standard_variables_sdtmig where observation_class = '$obClass'";
		else
			$qstr = "select id, variable_name, core from standard_variables_sdtmig where domain_prefix='$dn'";
		$dbtemp->query($qstr);
		
		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				{
				if (array_search($data['variable_name'], $variableList) === FALSE)
					$rarray[] = array("id"=>$data['id'], "name"=>str_replace("--", $dn, $data['variable_name']), "core"=>$data['core']);
				}
			}

		$qstr = "select id, variable_name, core from standard_variables_sdtmig where observation_class = 'All Classes'";
		$dbtemp->query($qstr);
			
		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				{
				if ((array_search($data['variable_name'], $variableList) === FALSE) && (array_search($data['variable_name'], $rarray) === FALSE))
					$rarray[] = array("id"=>$data['id'], "name"=>str_replace("--", $dn, $data['variable_name']), "core"=>$data['core']);
				}
			}
					
		usort($rarray, "byname");
		return($rarray);
	}
	
	public function IsReferenceData() {
		$dbtemp = new db();
		$rval = "";
		$qstr = "select id from variable_item where dataset_id=" . $this->getId() . " and name='USUBJID' and rev_number=1";
		$dbtemp->query($qstr);
		return (($dbtemp->getRowCount()) ? "No" : "Yes");
	}

	public function Repeating() {
		$dbtemp = new db();
		$rval = "No";
		$qstr = "select name from variable_item where dataset_id=" . $this->getId() . " and rev_number=1 and is_domain_key>0 order by is_domain_key";
		$dbtemp->query($qstr);

		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				{
				if ($data['name'] != "STUDYID" && $data['name'] != "USUBJID")
					{
					$rval = "Yes";
					break;
					}
				}
			}
		return($rval);
	}
	
	public function dumpvalues() {
		var_dump($this);
	}
}
	
class StandardCodelistsCdisc extends Container {
	public static function DistinctNames() {
		$dbtemp = new db();
		$ar = array();
		$qstr = "select codelist_name, cdisc_submission_value from standard_codelists_cdisc where codelist_code = '' and rev_number=1 order by codelist_name";
		$dbtemp->query($qstr);

		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				{
				$ar[] = array("id"=>$data['cdisc_submission_value'], "name"=>$data['codelist_name']);
				}
			}
		return($ar);
	}
	public function MemberList($name) {
		$dbtemp = new db();
		$ar = array();
		$qstr = "select code from standard_codelists_cdisc where codelist_code = '' and cdisc_submission_value = '$name' and rev_number=1";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			$cod = $data['code'];
			$qstr = "select * from standard_codelists_cdisc where codelist_code = '$cod' and rev_number=1 order by cdisc_submission_value";
			$dbtemp->query($qstr);
			if ($dbtemp->getRowCount())
				{
				while ($data = $dbtemp->getRow())
					$ar[] = array("defid"=>$data['id'], "id"=>$data['cdisc_submission_value'], "name"=>$data['nci_preferred_term']);
				}
			}
		return($ar);
	}
	public function dumpvalues() {
		var_dump($this);
	}
}
class ExternalCodeList extends Container {
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class GlobalVariables extends Container {
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class Odm extends Container {
	
	public function Odm($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
//		$this->setDependencyList(array("study"));
	}
	public static function Seek($study_id) {
		$dbtemp = new db();
		$rval = 0;
		$qstr = "select id from odm where " . studyQueryPart() . " and rev_number=1";
		
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			$rval = $data['id'];
			}
		return($rval);
	}

	public function dumpvalues() {
		var_dump($this);
	}
}
class Source extends Container {
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class Study extends Container {
	
	public function Study($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		if (!$id || !strlen($this->getDefineVersion()))
			$this->setDefineVersion("1.0.0");
		$this->setDependencyList(array("variable_item","value_item","odm","annotated_crf","code_list","code_list_item","computation_method","dataset","external_code_list","global_variables","supplemental_doc","translation","value_list"));
		$this->setTriggerList(array("dataset","odm","annotated_crf","computation_method","global_variables","supplemental_doc","translation"));
	}
	
	public static function Seek($product, $study, $project, $type) {
		$dbtemp = new db();
		$rval = 0;
		$qstr = "select id from study where biib_product='" . $product . "' and biib_study='" . $study . "' and biib_project='" . $project . "' and study_type='" . $type . "' and rev_number=1";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			$rval = $data['id'];
			}
		return($rval);
	}
	
	public function ExternalCodeLists() {
		$dbtemp = new db();
		$rval = array();
		$qstr = "select distinct external_codelist from variable_item where external_codelist!='No' and external_codelist!='' and study_id=" . $this->getId() . " and rev_number=1";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				{
				$rval[] = $data['external_codelist'];
				
				}
			}
		$qstr = "select distinct external_codelist from value_item where external_codelist!='No' and external_codelist!='' and study_id=" . $this->getId() . " and rev_number=1";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				{
				if (!in_array($data['external_codelist'], $rval))
					$rval[] = $data['external_codelist'];
				}
			}
		return($rval);
	}
	
	public static function CreateVariableDirectory($standard, $adslSwap=1) {
		$dbtemp = new db();
		$rval = array();
		$varray = array();
		$adslarray = array();
		if ($adslSwap)
			{
			if ($standard == "sdtm")
				$qstr = "SELECT vi.id, vi.name, vi.data_type, vi.origin, vi.comments, vi.comp_method_oid, vi.comp_method, vi.standard_algorithms_name, vi.code_list_id, vi.role_codelist_oid, vi.crf_page_number, dataset.dataset_name FROM variable_item as vi, dataset WHERE vi.study_id =" . $_SESSION['cur_study_id'] ." AND vi.dataset_id = dataset.id AND (dataset.standard_type = '$standard' or dataset.dataset_name = 'ADSL') and vi.rev_number=1 ORDER BY dataset_name, name";
			else
				$qstr = "SELECT vi.id, vi.data_type, vi.origin, vi.comments, vi.comp_method_oid, vi.comp_method, vi.standard_algorithms_name, vi.code_list_id, vi.role_codelist_oid, vi.crf_page_number, dataset.dataset_name FROM variable_item as vi, dataset WHERE vi.study_id =" . $_SESSION['cur_study_id'] ." AND vi.dataset_id = dataset.id AND (dataset.standard_type = '$standard' and dataset.dataset_name != 'ADSL') and vi.rev_number=1 ORDER BY dataset_name, name";
			}
		else
			$qstr = "SELECT vi.id, vi.name, vi.data_type, vi.origin, vi.comments, vi.comp_method_oid, vi.comp_method, vi.standard_algorithms_name, vi.code_list_id, vi.role_codelist_oid, vi.crf_page_number, dataset.dataset_name FROM variable_item as vi, dataset WHERE vi.study_id =" . $_SESSION['cur_study_id'] ." AND vi.dataset_id = dataset.id AND dataset.standard_type = '$standard' and vi.rev_number=1 ORDER BY dataset_name, name";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				{
				if ($standard == "sdtm")
					{
					if ($data['dataset_name'] != 'ADSL')
						{
						$varray[] = $data['name'];
						$rval[] = $data;
						}
					else
						$adslarray[$data['name']] = $data;
					}
				else
					$rval[] = $data;
				}
			if ($standard == "sdtm" && count($adslarray))
				{
				foreach ($adslarray as $key => $val)
					{
					if (!in_array($key, $varray))
						$rval[] = $val;
					}
				}
			}
		return($rval);
	}

	public function AllCompMethods() {
		$dbtemp = new db();
		$ar = array();
		$qstr = "select standard_algorithms.name, standard_algorithms.computation from standard_algorithms, variable_item where variable_item.standard_algorithms_name != '' and variable_item.study_id=" . $this->id . " and variable_item.rev_number=1 and variable_item.standard_algorithms_name=standard_algorithms.name group by standard_algorithms.name";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				$ar[] = $data;
			}
		$qstr = "select variable_item.name, dataset.dataset_name,variable_item.comp_method_oid from variable_item,dataset where variable_item.comp_method_oid != '' and variable_item.standard_algorithms_name = '' and variable_item.study_id=" . $this->id . " and variable_item.rev_number=1 and dataset.id=variable_item.dataset_id and dataset.active_yn='Y'";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			while ($data = $dbtemp->getRow())
				$ar[] = array("name"=>$data['dataset_name'] . "." . $data['name'], "computation"=>$data['comp_method_oid']);
			}
		return($ar);		
	}
	public function output($target, $pos="line", $tabs=0) {
		/* In the case of Study, we're going to dump our full header */
		if ($pos == "line")
			{
			$odmid = Odm::Seek($this->getId());
			$odm = new Odm($odmid);
			$globalvariables = $this->listGlobalVariables();
			switch ($target)
				{
				case "var_dump":
					var_dump($this);
					var_dump($odm);
					var_dump($globalvariables);
					break;
				case "define":
					header("Content-type: text/xml"); 	/* we don't want the header unless we're making a file */
				case "xml":
					xLineOut("<?xml version=\"1.0\" encoding=\"utf-8\"?>");
					xLineOut("<?xml-stylesheet type='text/xsl' href='define1-0-0.xsl'?>");
					xLineOut("<!-- ********************************************************************************** -->", $tabs);
					xLineOut("<!-- File: define.xml                                                                   -->", $tabs);
					xLineOut("<!-- Date: " . date('Y-m-d\TH:i:s') . "                                                          -->", $tabs);
					xLineOut("<!-- Description: This is a draft of define.xml document which implements the Case      -->", $tabs);
					xLineOut("<!--   Report Tabulation Data Definition Specification Version 1.0.0 and has a          -->", $tabs);
					xLineOut("<!--   corresponding style sheet reference.                                             -->", $tabs);
					xLineOut("<!-- ********************************************************************************** -->", $tabs);
					xLineOut(sprintf('<ODM xmlns="http://www.cdisc.org/ns/odm/v1.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:def="http://www.cdisc.org/ns/def/v1.0" xsi:schemaLocation="http://www.cdisc.org/models/def/v1.2 define1-0-0.xsd" FileOID="%s" ODMVersion="1.2" FileType="Snapshot" CreationDateTime="%s">',
										 $this->getDeliverableTitle(), date('Y-m-d\TH:i:s')), $tabs);
					xLineOut(sprintf('<Study OID="%s">', $this->getDeliverableTitle()), $tabs+1);
					++$tabs;
					xLineOut(sprintf('<GlobalVariables>'), $tabs);
					xLineOut(sprintf('<StudyName>%s</StudyName>', $this->getDeliverableTitle()), $tabs+1);
					xLineOut(sprintf('<StudyDescription>%s</StudyDescription>', htmlentities($this->getDescription)()), $tabs+1);
					xLineOut(sprintf('<ProtocolName>%s</ProtocolName>', $this->getName()), $tabs+1);
					xLineOut(sprintf('</GlobalVariables>'), $tabs);

					if ($this->getStandardName() == "sdtm")
						$printstandard = "CDISC SDTM";
					else						
						$printstandard = "ADaM";
					xLineOut(sprintf('<MetaDataVersion OID="%s" Name="%s" Description="%s" def:DefineVersion="%s" def:StandardName="%s" def:StandardVersion="%s">',
								$this->getDeliverableTitle(), $this->getName(), $this->getDescription(), $this->getDefineVersion(), $printstandard, $this->getStandardVersion()), $tabs+1);
					if ($this->getStandardName() == "sdtm")
						{
						xLineOut('<def:AnnotatedCRF>', $tabs+2);
						xLineOut('<def:DocumentRef leafID="blankcrf" />', $tabs+3);
						xLineOut('</def:AnnotatedCRF>', $tabs+2);
						xLineOut('<def:SupplementalDoc>', $tabs+2);
						xLineOut('<def:DocumentRef leafID="SupplementalDataDefinitions" />', $tabs+3);
						xLineOut('</def:SupplementalDoc>', $tabs+2);
						xLineOut('<def:leaf ID="blankcrf" xlink:href="blankcrf.pdf">', $tabs+2);
						xLineOut('<def:title>Annotated Case Report Form</def:title>', $tabs+3);
						xLineOut('</def:leaf>', $tabs+2);
						xLineOut('<def:leaf ID="SupplementalDataDefinitions" xlink:href="supplementaldatadefinitions.pdf">', $tabs+2);
						xLineOut('<def:title>Supplemental Data Definitions Document</def:title>', $tabs+3);
						xLineOut('</def:leaf>', $tabs+2);
						}
					else
						{
						xLineOut('<def:SupplementalDoc>', $tabs+2);
						xLineOut('<def:DocumentRef leafID="SupplementalDataDefinitions" />', $tabs+3);
						xLineOut('</def:SupplementalDoc>', $tabs+2);
						xLineOut('<def:leaf ID="SupplementalDataDefinitions" xlink:href="supplementaldatadefinitions.pdf">', $tabs+2);
						xLineOut('<def:title>Supplemental Data Definitions Document</def:title>', $tabs+3);
						xLineOut('</def:leaf>', $tabs+2);
						}
					break;
				}
			}
		else if ($pos == "post")
			{
			xLineOut("</MetaDataVersion>", $tabs+2);
			xLineOut("</Study>", $tabs+1);
			xLineOut("</ODM>", $tabs);
			}
	}
}
class StudyData extends Container {
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class SupplementalDoc extends Container {
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class Synonyms extends Container {
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class Translation extends Container {
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class ValueItem extends Container {
	
	public function ValueItem($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		$this->setDependencyList(array("code_list"));
		$this->setTriggerList($this->getDependencyList());
	}

	public static function Seek($study_id, $variable_id, $value_name) {
		$dbtemp = new db();
		$rval = 0;

		$qstr = "select id from value_item where " . studyQueryPart() . " and variable_item_id='" . $variable_id . "' and name='" . $value_name . "' and rev_number=1";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())	/* We have a unique field name and it's been found */
			{
			$data = $dbtemp->getRow();
			$rval = $data['id'];
			}
		return($rval);
	}
	public static function SeekCount($variable_id) {
		$dbtemp = new db();
		$rval = 0;

		$qstr = "select count(*) as cnt from value_item where variable_item_id='" . $variable_id . "' and rev_number=1";
		$dbtemp->query($qstr);
		$data = $dbtemp->getRow();
		$rval = $data['cnt'];
		return($rval);
	}
	
	public function outputItemDef($dsname, $tabs=0) {
		$closedDeclaration = 0;
		$sdsec = ($this->getDataType() == "float") ? " SignificantDigits=\"" . $this->getSigDigits() . "\" " : " ";
		$cmsec = (strlen($this->getStandardAlgorithmsName())) ? " def:ComputationMethodOID=\"COMPMETHOD." . $this->getStandardAlgorithmsName() . "\"" : "";
		$org = ($this->getOrigin() == "CRF" && strlen($this->getCrfPageNumber())) ? "CRF Page " . $this->getCrfPageNumber() : $this->getOrigin();
		$codeList = $this->HasCodeList();
		$vstring = sprintf('<ItemDef OID="%s.%s.%s" Name="%s" DataType="%s" Length="%s"%sOrigin="%s" Comment="%s" def:Label="%s"%s',
					$dsname, VariableItem::SeekName($this->getVariableItemId()), $this->getName(), $this->getName(), $this->getDataType(), $this->getLength(), 
					$sdsec, $org, ConvertSpecialChars($this->getComments()), UnicodeStripper($this->getLabel()), $cmsec);
		if ($codeList)
			{
			if ($closedDeclaration == 0)
				{
				$vstring .= '>';
				$closedDeclaration = 1;
				}
			$vstring .= sprintf('<CodeListRef CodeListOID="CodeList.%s" />', $this->getName());
			}
		if (strlen($this->getExternalCodelist()) && $this->getExternalCodelist() != "No")
			{
			if ($closedDeclaration == 0)
				{
				$vstring .= '>';
				$closedDeclaration = 1;
				}
			$vstring .= sprintf('<CodeListRef CodeListOID="ExternalCodeList.%s" />', strtoupper($this->getExternalCodelist()));
			}
		$vstring .= ($closedDeclaration) ? '</ItemDef>' : ' />';
		xLineOut($vstring, $tabs);
	}
	
	public function outputItemRef($dsname, $tabs=0) {
		xLineOut(sprintf('<ItemRef ItemOID="%s.%s.%s" OrderNumber="%s" Mandatory="%s" />',
					$dsname, VariableItem::SeekName($this->getVariableItemId()), $this->getName(), $this->getOrderNumber(), $this->getMandatory()), $tabs);
	}
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class ValueList extends Container {
	
	public function ValueList($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
//		$this->setDependencyList(array("value_item","variable_item"));
	}
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class ValueListRef extends Container {
	
	public function dumpvalues() {
		var_dump($this);
	}
}
class VariableItem extends Container {
	
	public function VariableItem($id=0, $current=1, $fieldname='id') {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, $current, $fieldname);
		$this->setDependencyList(array("value_item","code_list","code_list_ref"));
		$this->setTriggerList($this->getDependencyList());
	}
	public static function Seek($variable_name, $dataset_id=0) {
		$dbtemp = new db();
		$rval = 0;
		if ($dataset_id)
			$qstr = "select id from variable_item where  " . studyQueryPart() . " and name='" . $variable_name . "'  and dataset_id='" . $dataset_id . "' and rev_number=1";
		else
			$qstr = "select id from variable_item where  " . studyQueryPart() . " and name='" . $variable_name . "'  and rev_number=1";
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount() == 1) // search for exactly one. If we have duplicates, we don't know what we found
			{
			$data = $dbtemp->getRow();
			$rval = $data['id'];
			}
		return($rval);
	}
	
	public static function SeekName($variable_id) {
		$dbtemp = new db();
		$rval = 0;
		$qstr = "select name from variable_item where id=" . $variable_id;
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			$rval = $data['name'];
			}
		return($rval);
	}
	public function HasCodeList() {
		$dbtemp = new db();
		$rval = 0;
		$qstr = "select id from code_list where variable_item_id=" . $this->getId();
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			$rval = $data['id'];
			}
		return($rval);
	}
	public function outputItemDef($dsname, $tabs=0) {
		$closedDeclaration = 0;
		$sdsec = ($this->getDataType() == "float") ? " SignificantDigits=\"" . $this->getSigDigits() . "\" " : " ";
		$cmsec = (strlen($this->getStandardAlgorithmsName())) ? " def:ComputationMethodOID=\"COMPMETHOD." . $this->getStandardAlgorithmsName() . "\"" : "";
		$org = ($this->getOrigin() == "CRF" && strlen($this->getCrfPageNumber())) ? "CRF Page " . $this->getCrfPageNumber() : $this->getOrigin();
		$valueList = $this->makeSortedList("ValueItem", "order_number");
		$codeList = $this->HasCodeList();

		$vstring = sprintf('<ItemDef OID="%s.%s" Name="%s" DataType="%s" Length="%s"%sOrigin="%s" Comment="%s" def:Label="%s"%s',
						$dsname, $this->getName(), $this->getName(), $this->getDataType(), $this->getLength(), $sdsec, 
						$org, ConvertSpecialChars($this->getComments()), UnicodeStripper($this->getLabel()), $cmsec);
		if (count($valueList))
			{
			$vstring .= sprintf('><def:ValueListRef ValueListOID="ValueList.%s.%s" />', $dsname, $this->getName());
			$closedDeclaration = 1;
			}
		if ($codeList)
			{
			if ($closedDeclaration == 0)
				{
				$vstring .= '>';
				$closedDeclaration = 1;
				}
			$vstring .= sprintf('<CodeListRef CodeListOID="CodeList.%s" />', $this->getName());
			}
		if (strlen($this->getExternalCodelist()) && $this->getExternalCodelist() != "No")
			{
			if ($closedDeclaration == 0)
				{
				$vstring .= '>';
				$closedDeclaration = 1;
				}
			$vstring .= sprintf('<CodeListRef CodeListOID="ExternalCodeList.%s" />', strtoupper($this->getExternalCodelist()));
			}
		$vstring .= ($closedDeclaration) ? '</ItemDef>' : ' />';
		xLineOut($vstring, $tabs);
	}
	
	public function outputItemRef($dsname, $tabs=0) {
		xLineOut(sprintf('<ItemRef ItemOID="%s.%s" OrderNumber="%s" Mandatory="%s" Role="%s" />',
					$dsname, $this->getName(), $this->getOrderNumber(), $this->getMandatory(), $this->getRole()), $tabs);
	}

	public function dumpvalues() {
		var_dump($this);
	}
}
class TempDataset extends Container {
	public function TempDataset($id=0) {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, 1, 'id');
		$this->setMaxVersions(1);
	}
	
	public function write($line=NULL, $id=0, $exec=1) {
//		$this->db->query("select id from study where name='" . $this->getStudyNameFromXpt() . "' and rev_number=1");
//		if ($this->db->getRowCount())
//			{
//			$data = $this->db->getRow();
//			$this->setStudyId($data['id']);
//			}
//		else
//			return(-2);	/* The study isn't in the database */
		$this->setStudyId($_SESSION['cur_study_id']);
			
// Setting MaxVersions to one removes this step
//		/* We only keep one record in temp */
//		$qstr = "select id from temp_dataset where study_type='" . $_SESSION['cur_study_type'] . "' and dataset_name='" . $this->getDatasetName() . "' and study_id=" . $this->getStudyId() . " and rev_number = 1";
//		$this->db->query($qstr);
//		if ($this->db->getRowCount())
//			{
//			$data = $this->db->getRow();
//			$this->db->query("delete from temp_database where id=" . $data['id']);
//			}

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
		$flist = array("rev_number" => 1, "username" => $_SESSION['login_name']);
		$this->update($flist, $curidx, 0);
		return($curidx);		
	}
	public function promote($remove_from_temp_table=1) {
//		/* This function will move whatever is in the current Dataset record to the main database */
//		//* study_name_from_xpt must be set for this to work */
//		if (DEBUG) print "select id from study where name='" . $this->getStudyNameFromXpt() . "' and rev_number=1";
//		$this->db->query("select id from study where study_type='" . $_SESSION['cur_study_type'] . "' and name='" . $this->getStudyNameFromXpt() . "' and rev_number=1");
//		
//		if ($this->db->getRowCount())
//			{
//			$data = $this->db->getRow();
//			$this->setStudyId($data['id']);
//			}
//		else
//			return(-2);	/* The study isn't in the database */

		$this->setStudyId($_SESSION['cur_study_id']);
		
		/* We only have to get the main record, because insert will take care of the children */
		$qstr = "select id, xpt_creation_date from dataset where dataset_name='" . $this->getDatasetName() . "' and study_id=" . $this->getStudyId() . " and rev_number = 1";
		$this->db->query($qstr);

		if ($this->db->getRowCount())
			{
			/* We have a record, so this is an update, not an insert */
			$data = $this->db->getRow();
			$ds = new Dataset($data['id']);
//			print "about to check date";
//			if (strtotime(date( 'Y-m-d H:i:s', $this->getXptCreationDate())) <= strtotime(date( 'Y-m-d H:i:s', $data['xpt_creation_date'])))
//				return(-1);	/* We already have either this record or a later one */
//			print " checked date";
			}
		else
			$ds = new Dataset(0);

		/* Copy all values in the temp record to our new record, if the field exists in both */
		foreach ($this->db_fields as $fld)
			{
			if (in_array($fld, $ds->db_fields))
				$ds->$fld = $this->$fld;
			}
		$ds->setSourceId(1);
		$ee = $ds->getDatasetName();
		if (strlen($ee) == 2 || substr($ee,0,4) == "SUPP" || $ee == "RELREC")
			$ds->setStandardType("sdtm");
		else
			$ds->setStandardType("adam");
		$ds->setActiveYn("Y");
		$new_ds_index = $ds->write();
		if ($new_ds_index > 0 && $remove_from_temp_table)
			{
			$qstr = "delete from " . field_name_format(get_class($this)) . " where id=" . $this->id;
			$this->db->query($qstr);
			}
		return($new_ds_index);
	}
}

class TempValueItem extends Container {
	public function TempValueItem($id=0) {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, 1, 'id');
		$this->setMaxVersions(1);
	}
	public function write($line=NULL, $id=0, $exec=1) {
//		$this->db->query("select id from study where study_type='" . $_SESSION['cur_study_type'] . "' and name='" . $this->getStudyNameFromXpt() . "' and rev_number=1");
//		if ($this->db->getRowCount())
//			{
//			$data = $this->db->getRow();
//			$this->setStudyId($data['id']);
//			}
//		else
//			return(-2);	/* The study isn't in the database */
		$this->setStudyId($_SESSION['cur_study_id']);
		
		$this->db->query("select id from dataset where dataset_name='" . $this->getDatasetNameFromXpt() . "' and study_id=" . $this->getStudyId() . " and rev_number=1");
		if ($this->db->getRowCount())
			{
			$data = $this->db->getRow();
			$this->setDatasetId($data['id']);
			}
		else
			return(-3);	/* The dataset isn't in the database */
			
// Setting MaxVersions to one removes this step
//		/* We only keep one record in temp */
//		$qstr = "select id from temp_value_item where name='" . $this->getName() . "' and dataset_id=" . $this->getDatasetId() . " and study_id=" . $this->getStudyId() . " and rev_number = 1";
//		$this->db->query($qstr);
//		if ($this->db->getRowCount())
//			{
//			$data = $this->db->getRow();
//			$this->db->query("delete from temp_value_item where id=" . $data['id']);
//			}

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
		$flist = array("rev_number" => 1, "username" => $_SESSION['login_name']);
		$this->update($flist, $curidx, 0);
		return($curidx);		
	}
	public function promote($remove_from_temp_table=1) {
//		/* This function will move whatever is in the current VI record to the main database */
//		/* dataset_name_from_xpt and study_name_from_xpt must be set for this to work */
//		$this->db->query("select id from study where name='" . $this->getStudyNameFromXpt() . "' and rev_number=1");
//		if ($this->db->getRowCount())
//			{
//			$data = $this->db->getRow();
//			$this->setStudyId($data['id']);
//			}
//		else
//			return(-2);	/* The study isn't in the database */

		$this->setStudyId($_SESSION['cur_study_id']);

		$qstr = "select id,standard_type from dataset where dataset_name='" . $this->getDatasetNameFromXpt() . "' and study_id=" . $this->getStudyId() . " and rev_number=1";
		$this->db->query($qstr);
		if ($this->db->getRowCount())
			{
			$data = $this->db->getRow();
			$this->setDatasetId($data['id']);
			$this->setStandardType($data['standard_type']);
			$this->setOrigin(($this->getStandardType() == "sdtm") ? "CRF" : "Derived");
			}
		else
			return(-3);	/* The dataset isn't in the database */
					
		$this->db->query("select id from value_list where dataset_name='" . $this->getDatasetNameFromXpt() . "' and study_id=" . $this->getStudyId() . " and variable_name='" . $this->getVariableNameFromXpt() . "' and rev_number=1");
		if ($this->db->getRowCount())
			{
			$data = $this->db->getRow();
			$this->setValueListId($data['id']);
			}
		else
			{
			$vl = new ValueList(0);
			$vl->setStudyId($this->getStudyId());
			$vl->setDatasetId($this->getDatasetId());
			$vl->setDatasetName($this->getDatasetNameFromXpt());
			$vl->setVariableName($this->getVariableNameFromXpt());
			$vli = $vl->write();
			$this->setValueListId($vli);
			}
					
		$this->db->query("select id, name, origin from variable_item where dataset_id='" . $this->getDatasetId() . "' and study_id=" . $this->getStudyId() . " and name='" . $this->getVariableNameFromXpt() . "' and rev_number=1");
		if ($this->db->getRowCount())
			{
			$data = $this->db->getRow();
			$this->setVariableItemId($data['id']);
			$this->setVariableName($data['name']);
			$this->setOrigin($data['origin']);
			}
		else
			return(-3);	/* The variable isn't in the database */

/* We only have to get the main record, because write will take care of the children */
		$qstr = "select id from value_item where name='" . $this->getName() . "' and dataset_id=" . $this->getDatasetId() . " and study_id=" . $this->getStudyId() . " and rev_number = 1";
		$this->db->query($qstr);
		if ($this->db->getRowCount())
			{
			/* We have a record, so this is an update, not an insert */
			$data = $this->db->getRow();
			$vi = new ValueItem($data['id']);
			}
		else
			$vi = new ValueItem(0);
			
		/* Copy all values in the temp record to our new record, if the field exists in both */
		foreach ($this->db_fields as $fld)
			{
			if (in_array($fld, $vi->db_fields))
				$vi->$fld = $this->$fld;
			}

		$vi->setSourceId(1);
		if ($vi->getDataType() == "")
			$vi->setDataType("text");
		if ($vi->getMandatory() == "")
			$vi->setMandatory("No");
		if ($vi->getDataType() == "float" && $vi->getSignificantDigits() == 0)
			$vi->setSignificantDigits("2");
		$new_vi_index = $vi->write();		
		if ($new_vi_index > 0 && $remove_from_temp_table)
			{
			$qstr = "delete from " . field_name_format(get_class($this)) . " where id=" . $this->id;
			$this->db->query($qstr);
			}
		return($new_vi_index);
	}
}

class TempVariableItem extends Container {
	public function TempVariableItem($id=0) {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, 1, 'id');
		$this->setMaxVersions(1);
	}
	public function write($line=NULL, $id=0, $exec=1) {
//		$this->db->query("select id from study where study_type='" . $_SESSION['cur_study_type'] . "' and name='" . $this->getStudyNameFromXpt() . "' and rev_number=1");
//		if ($this->db->getRowCount())
//			{
//			$data = $this->db->getRow();
//			$this->setStudyId($data['id']);
//			}
//		else
//			return(-2);	/* The study isn't in the database */
		$this->setStudyId($_SESSION['cur_study_id']);

		$this->db->query("select id from dataset where dataset_name='" . $this->getDatasetNameFromXpt() . "' and study_id=" . $this->getStudyId() . " and rev_number=1");
		if ($this->db->getRowCount())
			{
			$data = $this->db->getRow();
			$this->setDatasetId($data['id']);
			}
		else
			return(-3);	/* The dataset isn't in the database */
			
		$this->setStudyId($_SESSION['cur_study_id']);

		/* We only keep one record in temp */
//		$qstr = "select id from temp_variable_item where name='" . $this->getName() . "' and dataset_id=" . $this->getDatasetId() . " and study_id=" . $this->getStudyId() . " and rev_number = 1";
//		$this->db->query($qstr);
//		if ($this->db->getRowCount())
//			{
//			$data = $this->db->getRow();
//			$this->db->query("delete from temp_variable_item where id=" . $data['id']);
//			}

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
		$flist = array("rev_number" => 1, "username" => $_SESSION['login_name']);
		$this->update($flist, $curidx, 0);
		return($curidx);		
	}
	public function promote($remove_from_temp_table=1) {
//		/* This function will move whatever is in the current VI record to the main database */
//		/* dataset_name_from_xpt and study_name_from_xpt must be set for this to work */
//		$qstr = "select id from study where study_type='" . $_SESSION['cur_study_type'] . "' and name='" . $this->getStudyNameFromXpt() . "' and rev_number=1";
//		$this->db->query($qstr);
//		if ($this->db->getRowCount())
//			{
//			$data = $this->db->getRow();
//			$this->setStudyId($data['id']);
//			}
//		else
//			return(-2);	/* The study isn't in the database */
		$this->setStudyId($_SESSION['cur_study_id']);

		$qstr = "select id,standard_type from dataset where dataset_name='" . $this->getDatasetNameFromXpt() . "' and study_id=" . $this->getStudyId() . " and rev_number=1";
		$this->db->query($qstr);
		if ($this->db->getRowCount())
			{
			$data = $this->db->getRow();
			$this->setDatasetId($data['id']);
			$this->setStandardType($data['standard_type']);
			$this->setOrigin(($this->getStandardType() == "sdtm") ? "CRF" : "Derived");
			}
		else
			return(-3);	/* The dataset isn't in the database */
			
		/* We only have to get the main record, because write will take care of the children */
		$qstr = "select id from variable_item where study_id='" . $_SESSION['cur_study_id'] . "' and name='" . $this->getName() . "' and dataset_id=" . $this->getDatasetId() . " and rev_number = 1";
		$this->db->query($qstr);
		if ($this->db->getRowCount())
			{
			/* We have a record, so this is an update, not an insert */
			$data = $this->db->getRow();
			$vi = new VariableItem($data['id']);
			}
		else
			$vi = new VariableItem(0);
	

		/* Copy all values in the temp record to our new record, if the field exists in both */
		foreach ($this->db_fields as $fld)
			{
			if (in_array($fld, $vi->db_fields))
				$vi->$fld = $this->$fld;
			}

		if ($vi->getName() == "AEBODSYS" || $vi->getName() == "AEDECOD")
			$vi->setExternalCodelist("MedDRA");
		else
			$vi->setExternalCodelist("No");
		if ($vi->getDataType() == "")
			$vi->setDataType("text");
		if ($vi->getMandatory() == "")
			$vi->setMandatory("No");
		if ($vi->getDataType() == "float" && $vi->getSignificantDigits() == 0)
			$vi->setSignificantDigits("2");
		$vi->setSourceId(1);
		$new_vi_index = $vi->write();		
		if ($new_vi_index > 0 && $remove_from_temp_table)
			{
			$qstr = "delete from " . field_name_format(get_class($this)) . " where id=" . $this->id;
			$this->db->query($qstr);
			}
		return($new_vi_index);
	}
}
class StandardAlgorithms extends Container {

	public static function Seek($name) {
		$dbtemp = new db();
		$rval = 0;
		$qstr = "select id from standard_algorithms where name='" . $name . "' and rev_number=1";
		
		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			$rval = $data['id'];
			}
		return($rval);
	}

	/* This is just a shorthand function */
	public function SelectList() {
		return($this->dirName("name"));
	}
	public function promote($study_id, $dataset_name, $variable_name) {
		$currentIdx = 0;
		/* This function will write a new record to computational_method associated with the study/dataset/variable */
		$qstr = "select id from computation_method where study_id=$study_id and dataset_name='" . $dataset_name . "' and variable_name='" . $variable_name . "' and rev_number=1";
		$this->db->query($qstr);
		if ($this->db->getRowCount())
			{
			$data = $this->db->getRow();
			$currentIdx = $data['id'];
			}

		$cm = new ComputationMethod($currentIdx);

		/* Copy all values in the temp record to our new record, if the field exists in both */
		foreach ($this->db_fields as $fld)
			{
			if (in_array($fld, $cm->db_fields))
				$cm->$fld = $this->$fld;
			}
			
		$cm->setStudyId($study_id);
		$cm->setDatasetName($dataset_name);
		$cm->setVariableName($variable_name);
		$cm->setSourceId($this->getSourceId());

		$new_cm_index = $cm->write();		

		return($new_cm_index);
	}
}	

class StandardDatasetsSdtmig extends Container {
	
	public function StandardDatasetsSdtmig($id=0) {
		$parentClass = get_parent_class($this);
		$this->$parentClass($id, 1, 'id');
		$this->db->query("select id from source where source_table='" . field_name_format(get_class($this)) . "'");
		if ($this->db->getRowCount())
			{
			$data = $this->db->getRow();
			$this->setSourceId($data['id']);
			}
	}
	public static function Seek($dataset_name) {
		$dbtemp = new db();
		$rval = 0;
		if ($dataset_name[0] == "X" || $dataset_name[0] == "Y" || $dataset_name[0] == "Z")
			$dataset_name = $dataset_name[0] . "_";
		else if (substr($dataset_name, 0, 4) == "SUPP")
			$dataset_name = "SUPP__";

		$qstr = "select id from standard_datasets_sdtmig where dataset_name='" . $dataset_name . "' and rev_number=1";

		$dbtemp->query($qstr);
		if ($dbtemp->getRowCount())
			{
			$data = $dbtemp->getRow();
			$rval = $data['id'];
			}
		return($rval);
	}

	/* This is just a shorthand function */
	public function SelectList() {
		return($this->dirDatasetName("dataset_name"));
	}

?>