<?php

// Global variable for table object
$laboratory_testings_report = NULL;

//
// Table class for laboratory testings report
//
class crlaboratory_testings_report extends crTableBase {
	var $ShowGroupHeaderAsRow = FALSE;
	var $ShowCompactSummaryFooter = TRUE;
	var $lab_id;
	var $lab_username;
	var $lab_password;
	var $lab_name;
	var $lab_address;
	var $contact_no;
	var $lab_pincode;
	var $lab_status;
	var $lab_accepting_status;
	var $testing_name;
	var $testing_price;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage, $gsLanguage;
		$this->TableVar = 'laboratory_testings_report';
		$this->TableName = 'laboratory testings report';
		$this->TableType = 'VIEW';
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0;

		// lab_id
		$this->lab_id = new crField('laboratory_testings_report', 'laboratory testings report', 'x_lab_id', 'lab_id', '`lab_id`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->lab_id->Sortable = TRUE; // Allow sort
		$this->lab_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['lab_id'] = &$this->lab_id;
		$this->lab_id->DateFilter = "";
		$this->lab_id->SqlSelect = "";
		$this->lab_id->SqlOrderBy = "";

		// lab_username
		$this->lab_username = new crField('laboratory_testings_report', 'laboratory testings report', 'x_lab_username', 'lab_username', '`lab_username`', 200, EWR_DATATYPE_STRING, -1);
		$this->lab_username->Sortable = TRUE; // Allow sort
		$this->fields['lab_username'] = &$this->lab_username;
		$this->lab_username->DateFilter = "";
		$this->lab_username->SqlSelect = "";
		$this->lab_username->SqlOrderBy = "";

		// lab_password
		$this->lab_password = new crField('laboratory_testings_report', 'laboratory testings report', 'x_lab_password', 'lab_password', '`lab_password`', 200, EWR_DATATYPE_STRING, -1);
		$this->lab_password->Sortable = TRUE; // Allow sort
		$this->fields['lab_password'] = &$this->lab_password;
		$this->lab_password->DateFilter = "";
		$this->lab_password->SqlSelect = "";
		$this->lab_password->SqlOrderBy = "";

		// lab_name
		$this->lab_name = new crField('laboratory_testings_report', 'laboratory testings report', 'x_lab_name', 'lab_name', '`lab_name`', 201, EWR_DATATYPE_MEMO, -1);
		$this->lab_name->Sortable = TRUE; // Allow sort
		$this->fields['lab_name'] = &$this->lab_name;
		$this->lab_name->DateFilter = "";
		$this->lab_name->SqlSelect = "";
		$this->lab_name->SqlOrderBy = "";

		// lab_address
		$this->lab_address = new crField('laboratory_testings_report', 'laboratory testings report', 'x_lab_address', 'lab_address', '`lab_address`', 201, EWR_DATATYPE_MEMO, -1);
		$this->lab_address->Sortable = TRUE; // Allow sort
		$this->fields['lab_address'] = &$this->lab_address;
		$this->lab_address->DateFilter = "";
		$this->lab_address->SqlSelect = "";
		$this->lab_address->SqlOrderBy = "";

		// contact_no
		$this->contact_no = new crField('laboratory_testings_report', 'laboratory testings report', 'x_contact_no', 'contact_no', '`contact_no`', 20, EWR_DATATYPE_NUMBER, -1);
		$this->contact_no->Sortable = TRUE; // Allow sort
		$this->contact_no->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['contact_no'] = &$this->contact_no;
		$this->contact_no->DateFilter = "";
		$this->contact_no->SqlSelect = "";
		$this->contact_no->SqlOrderBy = "";

		// lab_pincode
		$this->lab_pincode = new crField('laboratory_testings_report', 'laboratory testings report', 'x_lab_pincode', 'lab_pincode', '`lab_pincode`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->lab_pincode->Sortable = TRUE; // Allow sort
		$this->lab_pincode->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['lab_pincode'] = &$this->lab_pincode;
		$this->lab_pincode->DateFilter = "";
		$this->lab_pincode->SqlSelect = "";
		$this->lab_pincode->SqlOrderBy = "";

		// lab_status
		$this->lab_status = new crField('laboratory_testings_report', 'laboratory testings report', 'x_lab_status', 'lab_status', '`lab_status`', 200, EWR_DATATYPE_STRING, -1);
		$this->lab_status->Sortable = TRUE; // Allow sort
		$this->fields['lab_status'] = &$this->lab_status;
		$this->lab_status->DateFilter = "";
		$this->lab_status->SqlSelect = "";
		$this->lab_status->SqlOrderBy = "";

		// lab_accepting_status
		$this->lab_accepting_status = new crField('laboratory_testings_report', 'laboratory testings report', 'x_lab_accepting_status', 'lab_accepting_status', '`lab_accepting_status`', 200, EWR_DATATYPE_STRING, -1);
		$this->lab_accepting_status->Sortable = TRUE; // Allow sort
		$this->fields['lab_accepting_status'] = &$this->lab_accepting_status;
		$this->lab_accepting_status->DateFilter = "";
		$this->lab_accepting_status->SqlSelect = "";
		$this->lab_accepting_status->SqlOrderBy = "";

		// testing_name
		$this->testing_name = new crField('laboratory_testings_report', 'laboratory testings report', 'x_testing_name', 'testing_name', '`testing_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->testing_name->Sortable = TRUE; // Allow sort
		$this->fields['testing_name'] = &$this->testing_name;
		$this->testing_name->DateFilter = "";
		$this->testing_name->SqlSelect = "";
		$this->testing_name->SqlOrderBy = "";

		// testing_price
		$this->testing_price = new crField('laboratory_testings_report', 'laboratory testings report', 'x_testing_price', 'testing_price', '`testing_price`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->testing_price->Sortable = TRUE; // Allow sort
		$this->testing_price->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['testing_price'] = &$this->testing_price;
		$this->testing_price->DateFilter = "";
		$this->testing_price->SqlSelect = "";
		$this->testing_price->SqlOrderBy = "";
	}

	// Set Field Visibility
	function SetFieldVisibility($fldparm) {
		global $Security;
		return $this->$fldparm->Visible; // Returns original value
	}

	// Multiple column sort
	function UpdateSort(&$ofld, $ctrl) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			if ($ofld->GroupingFieldId == 0) {
				if ($ctrl) {
					$sOrderBy = $this->getDetailOrderBy();
					if (strpos($sOrderBy, $sSortField . " " . $sLastSort) !== FALSE) {
						$sOrderBy = str_replace($sSortField . " " . $sLastSort, $sSortField . " " . $sThisSort, $sOrderBy);
					} else {
						if ($sOrderBy <> "") $sOrderBy .= ", ";
						$sOrderBy .= $sSortField . " " . $sThisSort;
					}
					$this->setDetailOrderBy($sOrderBy); // Save to Session
				} else {
					$this->setDetailOrderBy($sSortField . " " . $sThisSort); // Save to Session
				}
			}
		} else {
			if ($ofld->GroupingFieldId == 0 && !$ctrl) $ofld->setSort("");
		}
	}

	// Get Sort SQL
	function SortSql() {
		$sDtlSortSql = $this->getDetailOrderBy(); // Get ORDER BY for detail fields from session
		$argrps = array();
		foreach ($this->fields as $fld) {
			if ($fld->getSort() <> "") {
				$fldsql = $fld->FldExpression;
				if ($fld->GroupingFieldId > 0) {
					if ($fld->FldGroupSql <> "")
						$argrps[$fld->GroupingFieldId] = str_replace("%s", $fldsql, $fld->FldGroupSql) . " " . $fld->getSort();
					else
						$argrps[$fld->GroupingFieldId] = $fldsql . " " . $fld->getSort();
				}
			}
		}
		$sSortSql = "";
		foreach ($argrps as $grp) {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $grp;
		}
		if ($sDtlSortSql <> "") {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $sDtlSortSql;
		}
		return $sSortSql;
	}

	// Table level SQL
	// From

	var $_SqlFrom = "";

	function getSqlFrom() {
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`laboratory testings report`";
	}

	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}

	// Select
	var $_SqlSelect = "";

	function getSqlSelect() {
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}

	// Where
	var $_SqlWhere = "";

	function getSqlWhere() {
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}

	// Group By
	var $_SqlGroupBy = "";

	function getSqlGroupBy() {
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}

	// Having
	var $_SqlHaving = "";

	function getSqlHaving() {
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}

	// Order By
	var $_SqlOrderBy = "";

	function getSqlOrderBy() {
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Select Aggregate
	var $_SqlSelectAgg = "";

	function getSqlSelectAgg() {
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelectAgg() { // For backward compatibility
		return $this->getSqlSelectAgg();
	}

	function setSqlSelectAgg($v) {
		$this->_SqlSelectAgg = $v;
	}

	// Aggregate Prefix
	var $_SqlAggPfx = "";

	function getSqlAggPfx() {
		return ($this->_SqlAggPfx <> "") ? $this->_SqlAggPfx : "";
	}

	function SqlAggPfx() { // For backward compatibility
		return $this->getSqlAggPfx();
	}

	function setSqlAggPfx($v) {
		$this->_SqlAggPfx = $v;
	}

	// Aggregate Suffix
	var $_SqlAggSfx = "";

	function getSqlAggSfx() {
		return ($this->_SqlAggSfx <> "") ? $this->_SqlAggSfx : "";
	}

	function SqlAggSfx() { // For backward compatibility
		return $this->getSqlAggSfx();
	}

	function setSqlAggSfx($v) {
		$this->_SqlAggSfx = $v;
	}

	// Select Count
	var $_SqlSelectCount = "";

	function getSqlSelectCount() {
		return ($this->_SqlSelectCount <> "") ? $this->_SqlSelectCount : "SELECT COUNT(*) FROM " . $this->getSqlFrom();
	}

	function SqlSelectCount() { // For backward compatibility
		return $this->getSqlSelectCount();
	}

	function setSqlSelectCount($v) {
		$this->_SqlSelectCount = $v;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {

			//$sUrlParm = "order=" . urlencode($fld->FldName) . "&ordertype=" . $fld->ReverseSort();
			$sUrlParm = "order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort();
			return ewr_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld) {
		global $gsLanguage;
		switch ($fld->FldVar) {
		case "x_lab_name":
			$sSqlWrk = "";
		$sSqlWrk = "SELECT DISTINCT `lab_name`, `lab_name` AS `DispFld`, '' AS `DispFld2`, '' AS `DispFld3`, '' AS `DispFld4` FROM `laboratory testings report`";
		$sWhereWrk = "";
		$this->lab_name->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "DB", "f0" => '`lab_name` = {filter_value}', "t0" => "201", "fn0" => "", "dlm" => ewr_Encrypt($fld->FldDelimiter));
			$sSqlWrk = "";
		$this->Lookup_Selecting($this->lab_name, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `lab_name` ASC";
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld) {
		global $gsLanguage;
		switch ($fld->FldVar) {
		}
	}

	// Table level events
	// Page Selecting event
	function Page_Selecting(&$filter) {

		// Enter your code here
	}

	// Page Breaking event
	function Page_Breaking(&$break, &$content) {

		// Example:
		//$break = FALSE; // Skip page break, or
		//$content = "<div style=\"page-break-after:always;\">&nbsp;</div>"; // Modify page break content

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here
	}

	// Cell Rendered event
	function Cell_Rendered(&$Field, $CurrentValue, &$ViewValue, &$ViewAttrs, &$CellAttrs, &$HrefValue, &$LinkAttrs) {

		//$ViewValue = "xxx";
		//$ViewAttrs["style"] = "xxx";

	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>);

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}

	// Load Filters event
	function Page_FilterLoad() {

		// Enter your code here
		// Example: Register/Unregister Custom Extended Filter
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A', 'GetStartsWithAFilter'); // With function, or
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A'); // No function, use Page_Filtering event
		//ewr_UnregisterFilter($this-><Field>, 'StartsWithA');

	}

	// Page Filter Validated event
	function Page_FilterValidated() {

		// Example:
		//$this->MyField1->SearchValue = "your search criteria"; // Search value

	}

	// Page Filtering event
	function Page_Filtering(&$fld, &$filter, $typ, $opr = "", $val = "", $cond = "", $opr2 = "", $val2 = "") {

		// Note: ALWAYS CHECK THE FILTER TYPE ($typ)! Example:
		//if ($typ == "dropdown" && $fld->FldName == "MyField") // Dropdown filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "extended" && $fld->FldName == "MyField") // Extended filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "popup" && $fld->FldName == "MyField") // Popup filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "custom" && $opr == "..." && $fld->FldName == "MyField") // Custom filter, $opr is the custom filter ID
		//	$filter = "..."; // Modify the filter

	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}
}
?>
