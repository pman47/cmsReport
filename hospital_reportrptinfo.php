<?php

// Global variable for table object
$hospital_report = NULL;

//
// Table class for hospital report
//
class crhospital_report extends crTableBase {
	var $ShowGroupHeaderAsRow = FALSE;
	var $ShowCompactSummaryFooter = TRUE;
	var $hospital_username;
	var $hospital_name;
	var $hospital_address;
	var $contact_no;
	var $hospital_status;
	var $hospital_accepting_status;
	var $area_name;
	var $district_name;
	var $state_name;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage, $gsLanguage;
		$this->TableVar = 'hospital_report';
		$this->TableName = 'hospital report';
		$this->TableType = 'VIEW';
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0;

		// hospital_username
		$this->hospital_username = new crField('hospital_report', 'hospital report', 'x_hospital_username', 'hospital_username', '`hospital_username`', 200, EWR_DATATYPE_STRING, -1);
		$this->hospital_username->Sortable = TRUE; // Allow sort
		$this->fields['hospital_username'] = &$this->hospital_username;
		$this->hospital_username->DateFilter = "";
		$this->hospital_username->SqlSelect = "";
		$this->hospital_username->SqlOrderBy = "";

		// hospital_name
		$this->hospital_name = new crField('hospital_report', 'hospital report', 'x_hospital_name', 'hospital_name', '`hospital_name`', 201, EWR_DATATYPE_MEMO, -1);
		$this->hospital_name->Sortable = TRUE; // Allow sort
		$this->fields['hospital_name'] = &$this->hospital_name;
		$this->hospital_name->DateFilter = "";
		$this->hospital_name->SqlSelect = "";
		$this->hospital_name->SqlOrderBy = "";

		// hospital_address
		$this->hospital_address = new crField('hospital_report', 'hospital report', 'x_hospital_address', 'hospital_address', '`hospital_address`', 201, EWR_DATATYPE_MEMO, -1);
		$this->hospital_address->Sortable = TRUE; // Allow sort
		$this->fields['hospital_address'] = &$this->hospital_address;
		$this->hospital_address->DateFilter = "";
		$this->hospital_address->SqlSelect = "";
		$this->hospital_address->SqlOrderBy = "";

		// contact_no
		$this->contact_no = new crField('hospital_report', 'hospital report', 'x_contact_no', 'contact_no', '`contact_no`', 20, EWR_DATATYPE_NUMBER, -1);
		$this->contact_no->Sortable = TRUE; // Allow sort
		$this->contact_no->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['contact_no'] = &$this->contact_no;
		$this->contact_no->DateFilter = "";
		$this->contact_no->SqlSelect = "";
		$this->contact_no->SqlOrderBy = "";

		// hospital_status
		$this->hospital_status = new crField('hospital_report', 'hospital report', 'x_hospital_status', 'hospital_status', '`hospital_status`', 200, EWR_DATATYPE_STRING, -1);
		$this->hospital_status->Sortable = TRUE; // Allow sort
		$this->fields['hospital_status'] = &$this->hospital_status;
		$this->hospital_status->DateFilter = "";
		$this->hospital_status->SqlSelect = "";
		$this->hospital_status->SqlOrderBy = "";

		// hospital_accepting_status
		$this->hospital_accepting_status = new crField('hospital_report', 'hospital report', 'x_hospital_accepting_status', 'hospital_accepting_status', '`hospital_accepting_status`', 200, EWR_DATATYPE_STRING, -1);
		$this->hospital_accepting_status->Sortable = TRUE; // Allow sort
		$this->fields['hospital_accepting_status'] = &$this->hospital_accepting_status;
		$this->hospital_accepting_status->DateFilter = "";
		$this->hospital_accepting_status->SqlSelect = "";
		$this->hospital_accepting_status->SqlOrderBy = "";

		// area_name
		$this->area_name = new crField('hospital_report', 'hospital report', 'x_area_name', 'area_name', '`area_name`', 201, EWR_DATATYPE_MEMO, -1);
		$this->area_name->Sortable = TRUE; // Allow sort
		$this->fields['area_name'] = &$this->area_name;
		$this->area_name->DateFilter = "";
		$this->area_name->SqlSelect = "";
		$this->area_name->SqlOrderBy = "";

		// district_name
		$this->district_name = new crField('hospital_report', 'hospital report', 'x_district_name', 'district_name', '`district_name`', 201, EWR_DATATYPE_MEMO, -1);
		$this->district_name->Sortable = TRUE; // Allow sort
		$this->fields['district_name'] = &$this->district_name;
		$this->district_name->DateFilter = "";
		$this->district_name->SqlSelect = "";
		$this->district_name->SqlOrderBy = "";

		// state_name
		$this->state_name = new crField('hospital_report', 'hospital report', 'x_state_name', 'state_name', '`state_name`', 201, EWR_DATATYPE_MEMO, -1);
		$this->state_name->Sortable = TRUE; // Allow sort
		$this->fields['state_name'] = &$this->state_name;
		$this->state_name->DateFilter = "";
		$this->state_name->SqlSelect = "";
		$this->state_name->SqlOrderBy = "";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`hospital report`";
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
		case "x_area_name":
			$sSqlWrk = "";
		$sSqlWrk = "SELECT DISTINCT `area_name`, `area_name` AS `DispFld`, '' AS `DispFld2`, '' AS `DispFld3`, '' AS `DispFld4` FROM `hospital report`";
		$sWhereWrk = "";
		$this->area_name->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "DB", "f0" => '`area_name` = {filter_value}', "t0" => "201", "fn0" => "", "dlm" => ewr_Encrypt($fld->FldDelimiter));
			$sSqlWrk = "";
		$this->Lookup_Selecting($this->area_name, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `area_name` ASC";
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
