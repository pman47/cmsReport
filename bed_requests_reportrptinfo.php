<?php

// Global variable for table object
$bed_requests_report = NULL;

//
// Table class for bed requests report
//
class crbed_requests_report extends crTableBase {
	var $ShowGroupHeaderAsRow = FALSE;
	var $ShowCompactSummaryFooter = TRUE;
	var $request_date;
	var $user_name;
	var $user_aadhar_no;
	var $mobile_no;
	var $user_dob;
	var $user_gender;
	var $user_blood_group;
	var $hospital_name;
	var $hospital_username;
	var $ward_name;
	var $patient_status;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage, $gsLanguage;
		$this->TableVar = 'bed_requests_report';
		$this->TableName = 'bed requests report';
		$this->TableType = 'VIEW';
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0;

		// request_date
		$this->request_date = new crField('bed_requests_report', 'bed requests report', 'x_request_date', 'request_date', '`request_date`', 133, EWR_DATATYPE_DATE, 0);
		$this->request_date->Sortable = TRUE; // Allow sort
		$this->request_date->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EWR_DATE_FORMAT"], $ReportLanguage->Phrase("IncorrectDate"));
		$this->fields['request_date'] = &$this->request_date;
		$this->request_date->DateFilter = "";
		$this->request_date->SqlSelect = "SELECT DISTINCT `request_date`, `request_date` AS `DispFld` FROM " . $this->getSqlFrom();
		$this->request_date->SqlOrderBy = "`request_date`";

		// user_name
		$this->user_name = new crField('bed_requests_report', 'bed requests report', 'x_user_name', 'user_name', '`user_name`', 201, EWR_DATATYPE_MEMO, -1);
		$this->user_name->Sortable = TRUE; // Allow sort
		$this->fields['user_name'] = &$this->user_name;
		$this->user_name->DateFilter = "";
		$this->user_name->SqlSelect = "";
		$this->user_name->SqlOrderBy = "";

		// user_aadhar_no
		$this->user_aadhar_no = new crField('bed_requests_report', 'bed requests report', 'x_user_aadhar_no', 'user_aadhar_no', '`user_aadhar_no`', 20, EWR_DATATYPE_NUMBER, -1);
		$this->user_aadhar_no->Sortable = TRUE; // Allow sort
		$this->user_aadhar_no->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['user_aadhar_no'] = &$this->user_aadhar_no;
		$this->user_aadhar_no->DateFilter = "";
		$this->user_aadhar_no->SqlSelect = "";
		$this->user_aadhar_no->SqlOrderBy = "";

		// mobile_no
		$this->mobile_no = new crField('bed_requests_report', 'bed requests report', 'x_mobile_no', 'mobile_no', '`mobile_no`', 20, EWR_DATATYPE_NUMBER, -1);
		$this->mobile_no->Sortable = TRUE; // Allow sort
		$this->mobile_no->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['mobile_no'] = &$this->mobile_no;
		$this->mobile_no->DateFilter = "";
		$this->mobile_no->SqlSelect = "";
		$this->mobile_no->SqlOrderBy = "";

		// user_dob
		$this->user_dob = new crField('bed_requests_report', 'bed requests report', 'x_user_dob', 'user_dob', '`user_dob`', 133, EWR_DATATYPE_DATE, 0);
		$this->user_dob->Sortable = TRUE; // Allow sort
		$this->user_dob->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EWR_DATE_FORMAT"], $ReportLanguage->Phrase("IncorrectDate"));
		$this->fields['user_dob'] = &$this->user_dob;
		$this->user_dob->DateFilter = "";
		$this->user_dob->SqlSelect = "";
		$this->user_dob->SqlOrderBy = "";

		// user_gender
		$this->user_gender = new crField('bed_requests_report', 'bed requests report', 'x_user_gender', 'user_gender', '`user_gender`', 200, EWR_DATATYPE_STRING, -1);
		$this->user_gender->Sortable = TRUE; // Allow sort
		$this->fields['user_gender'] = &$this->user_gender;
		$this->user_gender->DateFilter = "";
		$this->user_gender->SqlSelect = "";
		$this->user_gender->SqlOrderBy = "";

		// user_blood_group
		$this->user_blood_group = new crField('bed_requests_report', 'bed requests report', 'x_user_blood_group', 'user_blood_group', '`user_blood_group`', 200, EWR_DATATYPE_STRING, -1);
		$this->user_blood_group->Sortable = TRUE; // Allow sort
		$this->fields['user_blood_group'] = &$this->user_blood_group;
		$this->user_blood_group->DateFilter = "";
		$this->user_blood_group->SqlSelect = "";
		$this->user_blood_group->SqlOrderBy = "";

		// hospital_name
		$this->hospital_name = new crField('bed_requests_report', 'bed requests report', 'x_hospital_name', 'hospital_name', '`hospital_name`', 201, EWR_DATATYPE_MEMO, -1);
		$this->hospital_name->Sortable = TRUE; // Allow sort
		$this->fields['hospital_name'] = &$this->hospital_name;
		$this->hospital_name->DateFilter = "";
		$this->hospital_name->SqlSelect = "";
		$this->hospital_name->SqlOrderBy = "";

		// hospital_username
		$this->hospital_username = new crField('bed_requests_report', 'bed requests report', 'x_hospital_username', 'hospital_username', '`hospital_username`', 200, EWR_DATATYPE_STRING, -1);
		$this->hospital_username->Sortable = TRUE; // Allow sort
		$this->fields['hospital_username'] = &$this->hospital_username;
		$this->hospital_username->DateFilter = "";
		$this->hospital_username->SqlSelect = "";
		$this->hospital_username->SqlOrderBy = "";

		// ward_name
		$this->ward_name = new crField('bed_requests_report', 'bed requests report', 'x_ward_name', 'ward_name', '`ward_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->ward_name->Sortable = TRUE; // Allow sort
		$this->fields['ward_name'] = &$this->ward_name;
		$this->ward_name->DateFilter = "";
		$this->ward_name->SqlSelect = "";
		$this->ward_name->SqlOrderBy = "";

		// patient_status
		$this->patient_status = new crField('bed_requests_report', 'bed requests report', 'x_patient_status', 'patient_status', '`patient_status`', 201, EWR_DATATYPE_MEMO, -1);
		$this->patient_status->Sortable = TRUE; // Allow sort
		$this->fields['patient_status'] = &$this->patient_status;
		$this->patient_status->DateFilter = "";
		$this->patient_status->SqlSelect = "";
		$this->patient_status->SqlOrderBy = "";
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`bed requests report`";
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
		case "x_request_date":
			$sSqlWrk = "";
		$sSqlWrk = "SELECT DISTINCT `request_date`, `request_date` AS `DispFld`, '' AS `DispFld2`, '' AS `DispFld3`, '' AS `DispFld4` FROM `bed requests report`";
		$sWhereWrk = "";
		$this->request_date->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "DB", "f0" => '`request_date` = {filter_value}', "t0" => "133", "fn0" => "", "dlm" => ewr_Encrypt($fld->FldDelimiter));
			$sSqlWrk = "";
		$this->Lookup_Selecting($this->request_date, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `request_date` ASC";
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
