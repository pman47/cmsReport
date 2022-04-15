<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg10.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn10.php" ?>
<?php include_once "phprptinc/ewrusrfn10.php" ?>
<?php include_once "testing_requests_reportrptinfo.php" ?>
<?php

//
// Page class
//

$testing_requests_report_rpt = NULL; // Initialize page object first

class crtesting_requests_report_rpt extends crtesting_requests_report {

	// Page ID
	var $PageID = 'rpt';

	// Project ID
	var $ProjectID = "{83978599-D105-4032-957D-7610D67E8774}";

	// Page object name
	var $PageObjName = 'testing_requests_report_rpt';

	// Page name
	function PageName() {
		return ewr_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ewr_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Export URLs
	var $ExportPrintUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportPdfUrl;
	var $ReportTableClass;
	var $ReportTableStyle = "";

	// Custom export
	var $ExportPrintCustom = FALSE;
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Message
	function getMessage() {
		return @$_SESSION[EWR_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EWR_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EWR_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EWR_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_WARNING_MESSAGE], $v);
	}

		// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EWR_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EWR_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EWR_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EWR_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog ewDisplayTable\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") // Header exists, display
			echo $sHeader;
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") // Fotoer exists, display
			echo $sFooter;
	}

	// Validate page request
	function IsPageRequest() {
		if ($this->UseTokenInUrl) {
			if (ewr_IsHttpPost())
				return ($this->TableVar == @$_POST("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == @$_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EWR_CHECK_TOKEN;
	var $CheckTokenFn = "ewr_CheckToken";
	var $CreateTokenFn = "ewr_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ewr_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EWR_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EWR_TOKEN_NAME]);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $ReportLanguage;

		// Language object
		$ReportLanguage = new crLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (testing_requests_report)
		if (!isset($GLOBALS["testing_requests_report"])) {
			$GLOBALS["testing_requests_report"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["testing_requests_report"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";

		// Page ID
		if (!defined("EWR_PAGE_ID"))
			define("EWR_PAGE_ID", 'rpt', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EWR_TABLE_NAME"))
			define("EWR_TABLE_NAME", 'testing requests report', TRUE);

		// Start timer
		$GLOBALS["gsTimer"] = new crTimer();

		// Open connection
		if (!isset($conn)) $conn = ewr_Connect($this->DBID);

		// Export options
		$this->ExportOptions = new crListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Search options
		$this->SearchOptions = new crListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Filter options
		$this->FilterOptions = new crListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption ftesting_requests_reportrpt";

		// Generate report options
		$this->GenerateOptions = new crListOptions();
		$this->GenerateOptions->Tag = "div";
		$this->GenerateOptions->TagClassName = "ewGenerateOption";
	}

	//
	// Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $gsEmailContentType, $ReportLanguage, $Security;
		global $gsCustomExport;

		// Get export parameters
		if (@$_GET["export"] <> "")
			$this->Export = strtolower($_GET["export"]);
		elseif (@$_POST["export"] <> "")
			$this->Export = strtolower($_POST["export"]);
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$gsEmailContentType = @$_POST["contenttype"]; // Get email content type

		// Setup placeholder
		$this->user_name->PlaceHolder = $this->user_name->FldCaption();

		// Setup export options
		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $ReportLanguage->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Security, $ReportLanguage, $ReportOptions;
		$exportid = session_id();
		$ReportTypes = array();

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"" . $this->ExportPrintUrl . "\">" . $ReportLanguage->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;
		$ReportTypes["print"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormPrint") : "";

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . $ReportLanguage->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;
		$ReportTypes["excel"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormExcel") : "";

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . $ReportLanguage->Phrase("ExportToWord") . "</a>";

		//$item->Visible = TRUE;
		$item->Visible = TRUE;
		$ReportTypes["word"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormWord") : "";

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"" . $this->ExportPdfUrl . "\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Uncomment codes below to show export to Pdf link
//		$item->Visible = TRUE;

		$ReportTypes["pdf"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormPdf") : "";

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_testing_requests_report\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_testing_requests_report',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
		$item->Visible = FALSE;
		$ReportTypes["email"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormEmail") : "";
		$ReportOptions["ReportTypes"] = $ReportTypes;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = FALSE;
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = $this->ExportOptions->UseDropDownButton;
		$this->ExportOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"ftesting_requests_reportrpt\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"ftesting_requests_reportrpt\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton; // v8
		$this->FilterOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Set up options (extended)
		$this->SetupExportOptionsExt();

		// Hide options for export
		if ($this->Export <> "") {
			$this->ExportOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Set up table class
		if ($this->Export == "word" || $this->Export == "excel" || $this->Export == "pdf")
			$this->ReportTableClass = "ewTable";
		else
			$this->ReportTableClass = "table ewTable";
	}

	// Set up search options
	function SetupSearchOptions() {
		global $ReportLanguage;

		// Filter panel button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = $this->FilterApplied ? " active" : " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"ftesting_requests_reportrpt\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Reset filter
		$item = &$this->SearchOptions->Add("resetfilter");
		$item->Body = "<button type=\"button\" class=\"btn btn-default\" title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" onclick=\"location='" . ewr_CurrentPage() . "?cmd=reset'\">" . $ReportLanguage->Phrase("ResetAllFilter") . "</button>";
		$item->Visible = TRUE && $this->FilterApplied;

		// Button group for reset filter
		$this->SearchOptions->UseButtonGroup = TRUE;

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide options for export
		if ($this->Export <> "")
			$this->SearchOptions->HideAllOptions();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $ReportLanguage, $EWR_EXPORT, $gsExportFile;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		if ($this->Export <> "" && array_key_exists($this->Export, $EWR_EXPORT)) {
			$sContent = ob_get_contents();
			if (ob_get_length())
				ob_end_clean();

			// Remove all <div data-tagid="..." id="orig..." class="hide">...</div> (for customviewtag export, except "googlemaps")
			if (preg_match_all('/<div\s+data-tagid=[\'"]([\s\S]*?)[\'"]\s+id=[\'"]orig([\s\S]*?)[\'"]\s+class\s*=\s*[\'"]hide[\'"]>([\s\S]*?)<\/div\s*>/i', $sContent, $divmatches, PREG_SET_ORDER)) {
				foreach ($divmatches as $divmatch) {
					if ($divmatch[1] <> "googlemaps")
						$sContent = str_replace($divmatch[0], '', $sContent);
				}
			}
			$fn = $EWR_EXPORT[$this->Export];
			if ($this->Export == "email") { // Email
				if (@$this->GenOptions["reporttype"] == "email") {
					$saveResponse = $this->$fn($sContent, $this->GenOptions);
					$this->WriteGenResponse($saveResponse);
				} else {
					echo $this->$fn($sContent, array());
				}
				$url = ""; // Avoid redirect
			} else {
				$saveToFile = $this->$fn($sContent, $this->GenOptions);
				if (@$this->GenOptions["reporttype"] <> "") {
					$saveUrl = ($saveToFile <> "") ? ewr_ConvertFullUrl($saveToFile) : $ReportLanguage->Phrase("GenerateSuccess");
					$this->WriteGenResponse($saveUrl);
					$url = ""; // Avoid redirect
				}
			}
		}

		 // Close connection
		ewr_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EWR_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	// Initialize common variables
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $FilterOptions; // Filter options

	// Paging variables
	var $RecIndex = 0; // Record index
	var $RecCount = 0; // Record count
	var $StartGrp = 0; // Start group
	var $StopGrp = 0; // Stop group
	var $TotalGrps = 0; // Total groups
	var $GrpCount = 0; // Group count
	var $GrpCounter = array(); // Group counter
	var $DisplayGrps = 10; // Groups per page
	var $GrpRange = 10;
	var $Sort = "";
	var $Filter = "";
	var $PageFirstGroupFilter = "";
	var $UserIDFilter = "";
	var $DrillDown = FALSE;
	var $DrillDownInPanel = FALSE;
	var $DrillDownList = "";

	// Clear field for ext filter
	var $ClearExtFilter = "";
	var $PopupName = "";
	var $PopupValue = "";
	var $FilterApplied;
	var $SearchCommand = FALSE;
	var $ShowHeader;
	var $GrpColumnCount = 0;
	var $SubGrpColumnCount = 0;
	var $DtlColumnCount = 0;
	var $Cnt, $Col, $Val, $Smry, $Mn, $Mx, $GrandCnt, $GrandSmry, $GrandMn, $GrandMx;
	var $TotCount;
	var $GrandSummarySetup = FALSE;
	var $GrpIdx;
	var $DetailRows = array();

	//
	// Page main
	//
	function Page_Main() {
		global $rs;
		global $rsgrp;
		global $Security;
		global $gsFormError;
		global $gbDrillDownInPanel;
		global $ReportBreadcrumb;
		global $ReportLanguage;

		// Set field visibility for detail fields
		$this->testing_date->SetVisibility();
		$this->user_name->SetVisibility();
		$this->user_aadhar_no->SetVisibility();
		$this->mobile_no->SetVisibility();
		$this->user_email->SetVisibility();
		$this->user_dob->SetVisibility();
		$this->user_gender->SetVisibility();
		$this->user_blood_group->SetVisibility();
		$this->lab_name->SetVisibility();
		$this->lab_username->SetVisibility();
		$this->testing_name->SetVisibility();
		$this->testing_price->SetVisibility();
		$this->testing_status->SetVisibility();

		// Aggregate variables
		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of fields

		$nDtls = 14;
		$nGrps = 1;
		$this->Val = &ewr_InitArray($nDtls, 0);
		$this->Cnt = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Smry = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Mn = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->Mx = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->GrandCnt = &ewr_InitArray($nDtls, 0);
		$this->GrandSmry = &ewr_InitArray($nDtls, 0);
		$this->GrandMn = &ewr_InitArray($nDtls, NULL);
		$this->GrandMx = &ewr_InitArray($nDtls, NULL);

		// Set up array if accumulation required: array(Accum, SkipNullOrZero)
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		$this->testing_date->SelectionList = "";
		$this->testing_date->DefaultSelectionList = "";
		$this->testing_date->ValueList = "";

		// Check if search command
		$this->SearchCommand = (@$_GET["cmd"] == "search");

		// Load default filter values
		$this->LoadDefaultFilters();

		// Load custom filters
		$this->Page_FilterLoad();

		// Set up popup filter
		$this->SetupPopup();

		// Load group db values if necessary
		$this->LoadGroupDbValues();

		// Handle Ajax popup
		$this->ProcessAjaxPopup();

		// Extended filter
		$sExtendedFilter = "";

		// Restore filter list
		$this->RestoreFilterList();

		// Build extended filter
		$sExtendedFilter = $this->GetExtendedFilter();
		ewr_AddFilter($this->Filter, $sExtendedFilter);

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();

		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

		// Check if filter applied
		$this->FilterApplied = $this->CheckFilter();

		// Call Page Selecting event
		$this->Page_Selecting($this->Filter);

		// Search options
		$this->SetupSearchOptions();

		// Get sort
		$this->Sort = $this->GetSort($this->GenOptions);

		// Get total count
		$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(), $this->Filter, $this->Sort);
		$this->TotalGrps = $this->GetCnt($sSql);
		if ($this->DisplayGrps <= 0 || $this->DrillDown) // Display all groups
			$this->DisplayGrps = $this->TotalGrps;
		$this->StartGrp = 1;

		// Show header
		$this->ShowHeader = TRUE;

		// Set up start position if not export all
		if ($this->ExportAll && $this->Export <> "")
			$this->DisplayGrps = $this->TotalGrps;
		else
			$this->SetUpStartGroup($this->GenOptions);

		// Set no record found message
		if ($this->TotalGrps == 0) {
				if ($this->Filter == "0=101") {
					$this->setWarningMessage($ReportLanguage->Phrase("EnterSearchCriteria"));
				} else {
					$this->setWarningMessage($ReportLanguage->Phrase("NoRecord"));
				}
		}

		// Hide export options if export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();

		// Hide search/filter options if export/drilldown
		if ($this->Export <> "" || $this->DrillDown) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
			$this->GenerateOptions->HideAllOptions();
		}

		// Get current page records
		$rs = $this->GetRs($sSql, $this->StartGrp, $this->DisplayGrps);
		$this->SetupFieldCount();
	}

	// Accummulate summary
	function AccumulateSummary() {
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				if ($this->Col[$iy][0]) { // Accumulate required
					$valwrk = $this->Val[$iy];
					if (is_null($valwrk)) {
						if (!$this->Col[$iy][1])
							$this->Cnt[$ix][$iy]++;
					} else {
						$accum = (!$this->Col[$iy][1] || !is_numeric($valwrk) || $valwrk <> 0);
						if ($accum) {
							$this->Cnt[$ix][$iy]++;
							if (is_numeric($valwrk)) {
								$this->Smry[$ix][$iy] += $valwrk;
								if (is_null($this->Mn[$ix][$iy])) {
									$this->Mn[$ix][$iy] = $valwrk;
									$this->Mx[$ix][$iy] = $valwrk;
								} else {
									if ($this->Mn[$ix][$iy] > $valwrk) $this->Mn[$ix][$iy] = $valwrk;
									if ($this->Mx[$ix][$iy] < $valwrk) $this->Mx[$ix][$iy] = $valwrk;
								}
							}
						}
					}
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0]++;
		}
	}

	// Reset level summary
	function ResetLevelSummary($lvl) {

		// Clear summary values
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				$this->Cnt[$ix][$iy] = 0;
				if ($this->Col[$iy][0]) {
					$this->Smry[$ix][$iy] = 0;
					$this->Mn[$ix][$iy] = NULL;
					$this->Mx[$ix][$iy] = NULL;
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0] = 0;
		}

		// Reset record count
		$this->RecCount = 0;
	}

	// Accummulate grand summary
	function AccumulateGrandSummary() {
		$this->TotCount++;
		$cntgs = count($this->GrandSmry);
		for ($iy = 1; $iy < $cntgs; $iy++) {
			if ($this->Col[$iy][0]) {
				$valwrk = $this->Val[$iy];
				if (is_null($valwrk) || !is_numeric($valwrk)) {
					if (!$this->Col[$iy][1])
						$this->GrandCnt[$iy]++;
				} else {
					if (!$this->Col[$iy][1] || $valwrk <> 0) {
						$this->GrandCnt[$iy]++;
						$this->GrandSmry[$iy] += $valwrk;
						if (is_null($this->GrandMn[$iy])) {
							$this->GrandMn[$iy] = $valwrk;
							$this->GrandMx[$iy] = $valwrk;
						} else {
							if ($this->GrandMn[$iy] > $valwrk) $this->GrandMn[$iy] = $valwrk;
							if ($this->GrandMx[$iy] < $valwrk) $this->GrandMx[$iy] = $valwrk;
						}
					}
				}
			}
		}
	}

	// Get count
	function GetCnt($sql) {
		$conn = &$this->Connection();
		$rscnt = $conn->Execute($sql);
		$cnt = ($rscnt) ? $rscnt->RecordCount() : 0;
		if ($rscnt) $rscnt->Close();
		return $cnt;
	}

	// Get recordset
	function GetRs($wrksql, $start, $grps) {
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
		$rswrk = $conn->SelectLimit($wrksql, $grps, $start - 1);
		$conn->raiseErrorFn = '';
		return $rswrk;
	}

	// Get row values
	function GetRow($opt) {
		global $rs;
		if (!$rs)
			return;
		if ($opt == 1) { // Get first row
			$rs->MoveFirst(); // Move first
				$this->FirstRowData = array();
				$this->FirstRowData['testing_date'] = ewr_Conv($rs->fields('testing_date'), 133);
				$this->FirstRowData['user_aadhar_no'] = ewr_Conv($rs->fields('user_aadhar_no'), 20);
				$this->FirstRowData['mobile_no'] = ewr_Conv($rs->fields('mobile_no'), 20);
				$this->FirstRowData['user_dob'] = ewr_Conv($rs->fields('user_dob'), 133);
				$this->FirstRowData['user_gender'] = ewr_Conv($rs->fields('user_gender'), 200);
				$this->FirstRowData['user_blood_group'] = ewr_Conv($rs->fields('user_blood_group'), 200);
				$this->FirstRowData['lab_username'] = ewr_Conv($rs->fields('lab_username'), 200);
				$this->FirstRowData['testing_price'] = ewr_Conv($rs->fields('testing_price'), 131);
				$this->FirstRowData['testing_status'] = ewr_Conv($rs->fields('testing_status'), 200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->testing_date->setDbValue($rs->fields('testing_date'));
			$this->user_name->setDbValue($rs->fields('user_name'));
			$this->user_aadhar_no->setDbValue($rs->fields('user_aadhar_no'));
			$this->mobile_no->setDbValue($rs->fields('mobile_no'));
			$this->user_email->setDbValue($rs->fields('user_email'));
			$this->user_dob->setDbValue($rs->fields('user_dob'));
			$this->user_gender->setDbValue($rs->fields('user_gender'));
			$this->user_blood_group->setDbValue($rs->fields('user_blood_group'));
			$this->lab_name->setDbValue($rs->fields('lab_name'));
			$this->lab_username->setDbValue($rs->fields('lab_username'));
			$this->testing_name->setDbValue($rs->fields('testing_name'));
			$this->testing_price->setDbValue($rs->fields('testing_price'));
			$this->testing_status->setDbValue($rs->fields('testing_status'));
			$this->Val[1] = $this->testing_date->CurrentValue;
			$this->Val[2] = $this->user_name->CurrentValue;
			$this->Val[3] = $this->user_aadhar_no->CurrentValue;
			$this->Val[4] = $this->mobile_no->CurrentValue;
			$this->Val[5] = $this->user_email->CurrentValue;
			$this->Val[6] = $this->user_dob->CurrentValue;
			$this->Val[7] = $this->user_gender->CurrentValue;
			$this->Val[8] = $this->user_blood_group->CurrentValue;
			$this->Val[9] = $this->lab_name->CurrentValue;
			$this->Val[10] = $this->lab_username->CurrentValue;
			$this->Val[11] = $this->testing_name->CurrentValue;
			$this->Val[12] = $this->testing_price->CurrentValue;
			$this->Val[13] = $this->testing_status->CurrentValue;
		} else {
			$this->testing_date->setDbValue("");
			$this->user_name->setDbValue("");
			$this->user_aadhar_no->setDbValue("");
			$this->mobile_no->setDbValue("");
			$this->user_email->setDbValue("");
			$this->user_dob->setDbValue("");
			$this->user_gender->setDbValue("");
			$this->user_blood_group->setDbValue("");
			$this->lab_name->setDbValue("");
			$this->lab_username->setDbValue("");
			$this->testing_name->setDbValue("");
			$this->testing_price->setDbValue("");
			$this->testing_status->setDbValue("");
		}
	}

	// Set up starting group
	function SetUpStartGroup($options = array()) {

		// Exit if no groups
		if ($this->DisplayGrps == 0)
			return;
		$startGrp = (@$options["start"] <> "") ? $options["start"] : @$_GET[EWR_TABLE_START_GROUP];
		$pageNo = (@$options["pageno"] <> "") ? $options["pageno"] : @$_GET["pageno"];

		// Check for a 'start' parameter
		if ($startGrp != "") {
			$this->StartGrp = $startGrp;
			$this->setStartGroup($this->StartGrp);
		} elseif ($pageNo != "") {
			$nPageNo = $pageNo;
			if (is_numeric($nPageNo)) {
				$this->StartGrp = ($nPageNo-1)*$this->DisplayGrps+1;
				if ($this->StartGrp <= 0) {
					$this->StartGrp = 1;
				} elseif ($this->StartGrp >= intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1) {
					$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1;
				}
				$this->setStartGroup($this->StartGrp);
			} else {
				$this->StartGrp = $this->getStartGroup();
			}
		} else {
			$this->StartGrp = $this->getStartGroup();
		}

		// Check if correct start group counter
		if (!is_numeric($this->StartGrp) || $this->StartGrp == "") { // Avoid invalid start group counter
			$this->StartGrp = 1; // Reset start group counter
			$this->setStartGroup($this->StartGrp);
		} elseif (intval($this->StartGrp) > intval($this->TotalGrps)) { // Avoid starting group > total groups
			$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to last page first group
			$this->setStartGroup($this->StartGrp);
		} elseif (($this->StartGrp-1) % $this->DisplayGrps <> 0) {
			$this->StartGrp = intval(($this->StartGrp-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to page boundary
			$this->setStartGroup($this->StartGrp);
		}
	}

	// Load group db values if necessary
	function LoadGroupDbValues() {
		$conn = &$this->Connection();
	}

	// Process Ajax popup
	function ProcessAjaxPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		$fld = NULL;
		if (@$_GET["popup"] <> "") {
			$popupname = $_GET["popup"];

			// Check popup name
			// Build distinct values for testing_date

			if ($popupname == 'testing_requests_report_testing_date') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->testing_date, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->testing_date->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->testing_date->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->testing_date->setDbValue($rswrk->fields[0]);
					$this->testing_date->ViewValue = @$rswrk->fields[1];
					if (is_null($this->testing_date->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->testing_date->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						ewr_SetupDistinctValues($this->testing_date->ValueList, $this->testing_date->CurrentValue, $this->testing_date->ViewValue, FALSE, $this->testing_date->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->testing_date->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->testing_date->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->testing_date;
			}

			// Output data as Json
			if (!is_null($fld)) {
				$jsdb = ewr_GetJsDb($fld, $fld->FldType);
				if (ob_get_length())
					ob_end_clean();
				echo $jsdb;
				exit();
			}
		}
	}

	// Set up popup
	function SetupPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		if ($this->DrillDown)
			return;

		// Process post back form
		if (ewr_IsHttpPost()) {
			$sName = @$_POST["popup"]; // Get popup form name
			if ($sName <> "") {
				$cntValues = (is_array(@$_POST["sel_$sName"])) ? count($_POST["sel_$sName"]) : 0;
				if ($cntValues > 0) {
					$arValues = ewr_StripSlashes($_POST["sel_$sName"]);
					if (trim($arValues[0]) == "") // Select all
						$arValues = EWR_INIT_VALUE;
					$this->PopupName = $sName;
					if (ewr_IsAdvancedFilterValue($arValues) || $arValues == EWR_INIT_VALUE)
						$this->PopupValue = $arValues;
					if (!ewr_MatchedArray($arValues, $_SESSION["sel_$sName"])) {
						if ($this->HasSessionFilterValues($sName))
							$this->ClearExtFilter = $sName; // Clear extended filter for this field
					}
					$_SESSION["sel_$sName"] = $arValues;
					$_SESSION["rf_$sName"] = ewr_StripSlashes(@$_POST["rf_$sName"]);
					$_SESSION["rt_$sName"] = ewr_StripSlashes(@$_POST["rt_$sName"]);
					$this->ResetPager();
				}
			}

		// Get 'reset' command
		} elseif (@$_GET["cmd"] <> "") {
			$sCmd = $_GET["cmd"];
			if (strtolower($sCmd) == "reset") {
				$this->ClearSessionSelection('testing_date');
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get testing_date selected values

		if (is_array(@$_SESSION["sel_testing_requests_report_testing_date"])) {
			$this->LoadSelectionFromSession('testing_date');
		} elseif (@$_SESSION["sel_testing_requests_report_testing_date"] == EWR_INIT_VALUE) { // Select all
			$this->testing_date->SelectionList = "";
		}
	}

	// Reset pager
	function ResetPager() {

		// Reset start position (reset command)
		$this->StartGrp = 1;
		$this->setStartGroup($this->StartGrp);
	}

	// Set up number of groups displayed per page
	function SetUpDisplayGrps() {
		$sWrk = @$_GET[EWR_TABLE_GROUP_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayGrps = intval($sWrk);
			} else {
				if (strtoupper($sWrk) == "ALL") { // Display all groups
					$this->DisplayGrps = -1;
				} else {
					$this->DisplayGrps = 10; // Non-numeric, load default
				}
			}
			$this->setGroupPerPage($this->DisplayGrps); // Save to session

			// Reset start position (reset command)
			$this->StartGrp = 1;
			$this->setStartGroup($this->StartGrp);
		} else {
			if ($this->getGroupPerPage() <> "") {
				$this->DisplayGrps = $this->getGroupPerPage(); // Restore from session
			} else {
				$this->DisplayGrps = 10; // Load default
			}
		}
	}

	// Render row
	function RenderRow() {
		global $rs, $Security, $ReportLanguage;
		$conn = &$this->Connection();
		if (!$this->GrandSummarySetup) { // Get Grand total
			$bGotCount = FALSE;
			$bGotSummary = FALSE;

			// Get total count from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectCount(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$rstot = $conn->Execute($sSql);
			if ($rstot) {
				$this->TotCount = ($rstot->RecordCount()>1) ? $rstot->RecordCount() : $rstot->fields[0];
				$rstot->Close();
				$bGotCount = TRUE;
			} else {
				$this->TotCount = 0;
			}
		$bGotSummary = TRUE;

			// Accumulate grand summary from detail records
			if (!$bGotCount || !$bGotSummary) {
				$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
				$rs = $conn->Execute($sSql);
				if ($rs) {
					$this->GetRow(1);
					while (!$rs->EOF) {
						$this->AccumulateGrandSummary();
						$this->GetRow(2);
					}
					$rs->Close();
				}
			}
			$this->GrandSummarySetup = TRUE; // No need to set up again
		}

		// Call Row_Rendering event
		$this->Row_Rendering();

		//
		// Render view codes
		//

		if ($this->RowType == EWR_ROWTYPE_TOTAL && !($this->RowTotalType == EWR_ROWTOTAL_GROUP && $this->RowTotalSubType == EWR_ROWTOTAL_HEADER)) { // Summary row
			ewr_PrependClass($this->RowAttrs["class"], ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel); // Set up row class

			// testing_date
			$this->testing_date->HrefValue = "";

			// user_name
			$this->user_name->HrefValue = "";

			// user_aadhar_no
			$this->user_aadhar_no->HrefValue = "";

			// mobile_no
			$this->mobile_no->HrefValue = "";

			// user_email
			$this->user_email->HrefValue = "";

			// user_dob
			$this->user_dob->HrefValue = "";

			// user_gender
			$this->user_gender->HrefValue = "";

			// user_blood_group
			$this->user_blood_group->HrefValue = "";

			// lab_name
			$this->lab_name->HrefValue = "";

			// lab_username
			$this->lab_username->HrefValue = "";

			// testing_name
			$this->testing_name->HrefValue = "";

			// testing_price
			$this->testing_price->HrefValue = "";

			// testing_status
			$this->testing_status->HrefValue = "";
		} else {
			if ($this->RowTotalType == EWR_ROWTOTAL_GROUP && $this->RowTotalSubType == EWR_ROWTOTAL_HEADER) {
			} else {
			}

			// testing_date
			$this->testing_date->ViewValue = $this->testing_date->CurrentValue;
			$this->testing_date->ViewValue = ewr_FormatDateTime($this->testing_date->ViewValue, 0);
			$this->testing_date->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// user_name
			$this->user_name->ViewValue = $this->user_name->CurrentValue;
			$this->user_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// user_aadhar_no
			$this->user_aadhar_no->ViewValue = $this->user_aadhar_no->CurrentValue;
			$this->user_aadhar_no->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// mobile_no
			$this->mobile_no->ViewValue = $this->mobile_no->CurrentValue;
			$this->mobile_no->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// user_email
			$this->user_email->ViewValue = $this->user_email->CurrentValue;
			$this->user_email->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// user_dob
			$this->user_dob->ViewValue = $this->user_dob->CurrentValue;
			$this->user_dob->ViewValue = ewr_FormatDateTime($this->user_dob->ViewValue, 0);
			$this->user_dob->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// user_gender
			$this->user_gender->ViewValue = $this->user_gender->CurrentValue;
			$this->user_gender->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// user_blood_group
			$this->user_blood_group->ViewValue = $this->user_blood_group->CurrentValue;
			$this->user_blood_group->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// lab_name
			$this->lab_name->ViewValue = $this->lab_name->CurrentValue;
			$this->lab_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// lab_username
			$this->lab_username->ViewValue = $this->lab_username->CurrentValue;
			$this->lab_username->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// testing_name
			$this->testing_name->ViewValue = $this->testing_name->CurrentValue;
			$this->testing_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// testing_price
			$this->testing_price->ViewValue = $this->testing_price->CurrentValue;
			$this->testing_price->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// testing_status
			$this->testing_status->ViewValue = $this->testing_status->CurrentValue;
			$this->testing_status->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// testing_date
			$this->testing_date->HrefValue = "";

			// user_name
			$this->user_name->HrefValue = "";

			// user_aadhar_no
			$this->user_aadhar_no->HrefValue = "";

			// mobile_no
			$this->mobile_no->HrefValue = "";

			// user_email
			$this->user_email->HrefValue = "";

			// user_dob
			$this->user_dob->HrefValue = "";

			// user_gender
			$this->user_gender->HrefValue = "";

			// user_blood_group
			$this->user_blood_group->HrefValue = "";

			// lab_name
			$this->lab_name->HrefValue = "";

			// lab_username
			$this->lab_username->HrefValue = "";

			// testing_name
			$this->testing_name->HrefValue = "";

			// testing_price
			$this->testing_price->HrefValue = "";

			// testing_status
			$this->testing_status->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// testing_date
			$CurrentValue = $this->testing_date->CurrentValue;
			$ViewValue = &$this->testing_date->ViewValue;
			$ViewAttrs = &$this->testing_date->ViewAttrs;
			$CellAttrs = &$this->testing_date->CellAttrs;
			$HrefValue = &$this->testing_date->HrefValue;
			$LinkAttrs = &$this->testing_date->LinkAttrs;
			$this->Cell_Rendered($this->testing_date, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// user_name
			$CurrentValue = $this->user_name->CurrentValue;
			$ViewValue = &$this->user_name->ViewValue;
			$ViewAttrs = &$this->user_name->ViewAttrs;
			$CellAttrs = &$this->user_name->CellAttrs;
			$HrefValue = &$this->user_name->HrefValue;
			$LinkAttrs = &$this->user_name->LinkAttrs;
			$this->Cell_Rendered($this->user_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// user_aadhar_no
			$CurrentValue = $this->user_aadhar_no->CurrentValue;
			$ViewValue = &$this->user_aadhar_no->ViewValue;
			$ViewAttrs = &$this->user_aadhar_no->ViewAttrs;
			$CellAttrs = &$this->user_aadhar_no->CellAttrs;
			$HrefValue = &$this->user_aadhar_no->HrefValue;
			$LinkAttrs = &$this->user_aadhar_no->LinkAttrs;
			$this->Cell_Rendered($this->user_aadhar_no, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// mobile_no
			$CurrentValue = $this->mobile_no->CurrentValue;
			$ViewValue = &$this->mobile_no->ViewValue;
			$ViewAttrs = &$this->mobile_no->ViewAttrs;
			$CellAttrs = &$this->mobile_no->CellAttrs;
			$HrefValue = &$this->mobile_no->HrefValue;
			$LinkAttrs = &$this->mobile_no->LinkAttrs;
			$this->Cell_Rendered($this->mobile_no, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// user_email
			$CurrentValue = $this->user_email->CurrentValue;
			$ViewValue = &$this->user_email->ViewValue;
			$ViewAttrs = &$this->user_email->ViewAttrs;
			$CellAttrs = &$this->user_email->CellAttrs;
			$HrefValue = &$this->user_email->HrefValue;
			$LinkAttrs = &$this->user_email->LinkAttrs;
			$this->Cell_Rendered($this->user_email, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// user_dob
			$CurrentValue = $this->user_dob->CurrentValue;
			$ViewValue = &$this->user_dob->ViewValue;
			$ViewAttrs = &$this->user_dob->ViewAttrs;
			$CellAttrs = &$this->user_dob->CellAttrs;
			$HrefValue = &$this->user_dob->HrefValue;
			$LinkAttrs = &$this->user_dob->LinkAttrs;
			$this->Cell_Rendered($this->user_dob, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// user_gender
			$CurrentValue = $this->user_gender->CurrentValue;
			$ViewValue = &$this->user_gender->ViewValue;
			$ViewAttrs = &$this->user_gender->ViewAttrs;
			$CellAttrs = &$this->user_gender->CellAttrs;
			$HrefValue = &$this->user_gender->HrefValue;
			$LinkAttrs = &$this->user_gender->LinkAttrs;
			$this->Cell_Rendered($this->user_gender, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// user_blood_group
			$CurrentValue = $this->user_blood_group->CurrentValue;
			$ViewValue = &$this->user_blood_group->ViewValue;
			$ViewAttrs = &$this->user_blood_group->ViewAttrs;
			$CellAttrs = &$this->user_blood_group->CellAttrs;
			$HrefValue = &$this->user_blood_group->HrefValue;
			$LinkAttrs = &$this->user_blood_group->LinkAttrs;
			$this->Cell_Rendered($this->user_blood_group, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// lab_name
			$CurrentValue = $this->lab_name->CurrentValue;
			$ViewValue = &$this->lab_name->ViewValue;
			$ViewAttrs = &$this->lab_name->ViewAttrs;
			$CellAttrs = &$this->lab_name->CellAttrs;
			$HrefValue = &$this->lab_name->HrefValue;
			$LinkAttrs = &$this->lab_name->LinkAttrs;
			$this->Cell_Rendered($this->lab_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// lab_username
			$CurrentValue = $this->lab_username->CurrentValue;
			$ViewValue = &$this->lab_username->ViewValue;
			$ViewAttrs = &$this->lab_username->ViewAttrs;
			$CellAttrs = &$this->lab_username->CellAttrs;
			$HrefValue = &$this->lab_username->HrefValue;
			$LinkAttrs = &$this->lab_username->LinkAttrs;
			$this->Cell_Rendered($this->lab_username, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// testing_name
			$CurrentValue = $this->testing_name->CurrentValue;
			$ViewValue = &$this->testing_name->ViewValue;
			$ViewAttrs = &$this->testing_name->ViewAttrs;
			$CellAttrs = &$this->testing_name->CellAttrs;
			$HrefValue = &$this->testing_name->HrefValue;
			$LinkAttrs = &$this->testing_name->LinkAttrs;
			$this->Cell_Rendered($this->testing_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// testing_price
			$CurrentValue = $this->testing_price->CurrentValue;
			$ViewValue = &$this->testing_price->ViewValue;
			$ViewAttrs = &$this->testing_price->ViewAttrs;
			$CellAttrs = &$this->testing_price->CellAttrs;
			$HrefValue = &$this->testing_price->HrefValue;
			$LinkAttrs = &$this->testing_price->LinkAttrs;
			$this->Cell_Rendered($this->testing_price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// testing_status
			$CurrentValue = $this->testing_status->CurrentValue;
			$ViewValue = &$this->testing_status->ViewValue;
			$ViewAttrs = &$this->testing_status->ViewAttrs;
			$CellAttrs = &$this->testing_status->CellAttrs;
			$HrefValue = &$this->testing_status->HrefValue;
			$LinkAttrs = &$this->testing_status->LinkAttrs;
			$this->Cell_Rendered($this->testing_status, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		}

		// Call Row_Rendered event
		$this->Row_Rendered();
		$this->SetupFieldCount();
	}

	// Setup field count
	function SetupFieldCount() {
		$this->GrpColumnCount = 0;
		$this->SubGrpColumnCount = 0;
		$this->DtlColumnCount = 0;
		if ($this->testing_date->Visible) $this->DtlColumnCount += 1;
		if ($this->user_name->Visible) $this->DtlColumnCount += 1;
		if ($this->user_aadhar_no->Visible) $this->DtlColumnCount += 1;
		if ($this->mobile_no->Visible) $this->DtlColumnCount += 1;
		if ($this->user_email->Visible) $this->DtlColumnCount += 1;
		if ($this->user_dob->Visible) $this->DtlColumnCount += 1;
		if ($this->user_gender->Visible) $this->DtlColumnCount += 1;
		if ($this->user_blood_group->Visible) $this->DtlColumnCount += 1;
		if ($this->lab_name->Visible) $this->DtlColumnCount += 1;
		if ($this->lab_username->Visible) $this->DtlColumnCount += 1;
		if ($this->testing_name->Visible) $this->DtlColumnCount += 1;
		if ($this->testing_price->Visible) $this->DtlColumnCount += 1;
		if ($this->testing_status->Visible) $this->DtlColumnCount += 1;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $ReportBreadcrumb;
		$ReportBreadcrumb = new crBreadcrumb();
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$ReportBreadcrumb->Add("rpt", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	function SetupExportOptionsExt() {
		global $ReportLanguage, $ReportOptions;
		$ReportTypes = $ReportOptions["ReportTypes"];
		$ReportOptions["ReportTypes"] = $ReportTypes;
	}

	// Return extended filter
	function GetExtendedFilter() {
		global $gsFormError;
		$sFilter = "";
		if ($this->DrillDown)
			return "";
		$bPostBack = ewr_IsHttpPost();
		$bRestoreSession = TRUE;
		$bSetupFilter = FALSE;

		// Reset extended filter if filter changed
		if ($bPostBack) {

			// Set/clear dropdown for field testing_date
			if ($this->PopupName == 'testing_requests_report_testing_date' && $this->PopupValue <> "") {
				if ($this->PopupValue == EWR_INIT_VALUE)
					$this->testing_date->DropDownValue = EWR_ALL_VALUE;
				else
					$this->testing_date->DropDownValue = $this->PopupValue;
				$bRestoreSession = FALSE; // Do not restore
			} elseif ($this->ClearExtFilter == 'testing_requests_report_testing_date') {
				$this->SetSessionDropDownValue(EWR_INIT_VALUE, '', 'testing_date');
			}

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionDropDownValue($this->testing_date->DropDownValue, $this->testing_date->SearchOperator, 'testing_date'); // Field testing_date
			$this->SetSessionFilterValues($this->user_name->SearchValue, $this->user_name->SearchOperator, $this->user_name->SearchCondition, $this->user_name->SearchValue2, $this->user_name->SearchOperator2, 'user_name'); // Field user_name

			//$bSetupFilter = TRUE; // No need to set up, just use default
		} else {
			$bRestoreSession = !$this->SearchCommand;

			// Field testing_date
			if ($this->GetDropDownValue($this->testing_date)) {
				$bSetupFilter = TRUE;
			} elseif ($this->testing_date->DropDownValue <> EWR_INIT_VALUE && !isset($_SESSION['sv_testing_requests_report_testing_date'])) {
				$bSetupFilter = TRUE;
			}

			// Field user_name
			if ($this->GetFilterValues($this->user_name)) {
				$bSetupFilter = TRUE;
			}
			if (!$this->ValidateForm()) {
				$this->setFailureMessage($gsFormError);
				return $sFilter;
			}
		}

		// Restore session
		if ($bRestoreSession) {
			$this->GetSessionDropDownValue($this->testing_date); // Field testing_date
			$this->GetSessionFilterValues($this->user_name); // Field user_name
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildDropDownFilter($this->testing_date, $sFilter, $this->testing_date->SearchOperator, FALSE, TRUE); // Field testing_date
		$this->BuildExtendedFilter($this->user_name, $sFilter, FALSE, TRUE); // Field user_name

		// Save parms to session
		$this->SetSessionDropDownValue($this->testing_date->DropDownValue, $this->testing_date->SearchOperator, 'testing_date'); // Field testing_date
		$this->SetSessionFilterValues($this->user_name->SearchValue, $this->user_name->SearchOperator, $this->user_name->SearchCondition, $this->user_name->SearchValue2, $this->user_name->SearchOperator2, 'user_name'); // Field user_name

		// Setup filter
		if ($bSetupFilter) {

			// Field testing_date
			$sWrk = "";
			$this->BuildDropDownFilter($this->testing_date, $sWrk, $this->testing_date->SearchOperator);
			ewr_LoadSelectionFromFilter($this->testing_date, $sWrk, $this->testing_date->SelectionList, $this->testing_date->DropDownValue);
			$_SESSION['sel_testing_requests_report_testing_date'] = ($this->testing_date->SelectionList == "") ? EWR_INIT_VALUE : $this->testing_date->SelectionList;
		}

		// Field testing_date
		ewr_LoadDropDownList($this->testing_date->DropDownList, $this->testing_date->DropDownValue);
		return $sFilter;
	}

	// Build dropdown filter
	function BuildDropDownFilter(&$fld, &$FilterClause, $FldOpr, $Default = FALSE, $SaveFilter = FALSE) {
		$FldVal = ($Default) ? $fld->DefaultDropDownValue : $fld->DropDownValue;
		$sSql = "";
		if (is_array($FldVal)) {
			foreach ($FldVal as $val) {
				$sWrk = $this->GetDropDownFilter($fld, $val, $FldOpr);

				// Call Page Filtering event
				if (substr($val, 0, 2) <> "@@") $this->Page_Filtering($fld, $sWrk, "dropdown", $FldOpr, $val);
				if ($sWrk <> "") {
					if ($sSql <> "")
						$sSql .= " OR " . $sWrk;
					else
						$sSql = $sWrk;
				}
			}
		} else {
			$sSql = $this->GetDropDownFilter($fld, $FldVal, $FldOpr);

			// Call Page Filtering event
			if (substr($FldVal, 0, 2) <> "@@") $this->Page_Filtering($fld, $sSql, "dropdown", $FldOpr, $FldVal);
		}
		if ($sSql <> "") {
			ewr_AddFilter($FilterClause, $sSql);
			if ($SaveFilter) $fld->CurrentFilter = $sSql;
		}
	}

	function GetDropDownFilter(&$fld, $FldVal, $FldOpr) {
		$FldName = $fld->FldName;
		$FldExpression = $fld->FldExpression;
		$FldDataType = $fld->FldDataType;
		$FldDelimiter = $fld->FldDelimiter;
		$FldVal = strval($FldVal);
		if ($FldOpr == "") $FldOpr = "=";
		$sWrk = "";
		if (ewr_SameStr($FldVal, EWR_NULL_VALUE)) {
			$sWrk = $FldExpression . " IS NULL";
		} elseif (ewr_SameStr($FldVal, EWR_NOT_NULL_VALUE)) {
			$sWrk = $FldExpression . " IS NOT NULL";
		} elseif (ewr_SameStr($FldVal, EWR_EMPTY_VALUE)) {
			$sWrk = $FldExpression . " = ''";
		} elseif (ewr_SameStr($FldVal, EWR_ALL_VALUE)) {
			$sWrk = "1 = 1";
		} else {
			if (substr($FldVal, 0, 2) == "@@") {
				$sWrk = $this->GetCustomFilter($fld, $FldVal, $this->DBID);
			} elseif ($FldDelimiter <> "" && trim($FldVal) <> "" && ($FldDataType == EWR_DATATYPE_STRING || $FldDataType == EWR_DATATYPE_MEMO)) {
				$sWrk = ewr_GetMultiSearchSql($FldExpression, trim($FldVal), $this->DBID);
			} else {
				if ($FldVal <> "" && $FldVal <> EWR_INIT_VALUE) {
					if ($FldDataType == EWR_DATATYPE_DATE && $FldOpr <> "") {
						$sWrk = ewr_DateFilterString($FldExpression, $FldOpr, $FldVal, $FldDataType, $this->DBID);
					} else {
						$sWrk = ewr_FilterString($FldOpr, $FldVal, $FldDataType, $this->DBID);
						if ($sWrk <> "") $sWrk = $FldExpression . $sWrk;
					}
				}
			}
		}
		return $sWrk;
	}

	// Get custom filter
	function GetCustomFilter(&$fld, $FldVal, $dbid = 0) {
		$sWrk = "";
		if (is_array($fld->AdvancedFilters)) {
			foreach ($fld->AdvancedFilters as $filter) {
				if ($filter->ID == $FldVal && $filter->Enabled) {
					$sFld = $fld->FldExpression;
					$sFn = $filter->FunctionName;
					$wrkid = (substr($filter->ID,0,2) == "@@") ? substr($filter->ID,2) : $filter->ID;
					if ($sFn <> "")
						$sWrk = $sFn($sFld, $dbid);
					else
						$sWrk = "";
					$this->Page_Filtering($fld, $sWrk, "custom", $wrkid);
					break;
				}
			}
		}
		return $sWrk;
	}

	// Build extended filter
	function BuildExtendedFilter(&$fld, &$FilterClause, $Default = FALSE, $SaveFilter = FALSE) {
		$sWrk = ewr_GetExtendedFilter($fld, $Default, $this->DBID);
		if (!$Default)
			$this->Page_Filtering($fld, $sWrk, "extended", $fld->SearchOperator, $fld->SearchValue, $fld->SearchCondition, $fld->SearchOperator2, $fld->SearchValue2);
		if ($sWrk <> "") {
			ewr_AddFilter($FilterClause, $sWrk);
			if ($SaveFilter) $fld->CurrentFilter = $sWrk;
		}
	}

	// Get drop down value from querystring
	function GetDropDownValue(&$fld) {
		$parm = substr($fld->FldVar, 2);
		if (ewr_IsHttpPost())
			return FALSE; // Skip post back
		if (isset($_GET["so_$parm"]))
			$fld->SearchOperator = ewr_StripSlashes(@$_GET["so_$parm"]);
		if (isset($_GET["sv_$parm"])) {
			$fld->DropDownValue = ewr_StripSlashes(@$_GET["sv_$parm"]);
			return TRUE;
		}
		return FALSE;
	}

	// Get filter values from querystring
	function GetFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		if (ewr_IsHttpPost())
			return; // Skip post back
		$got = FALSE;
		if (isset($_GET["sv_$parm"])) {
			$fld->SearchValue = ewr_StripSlashes(@$_GET["sv_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["so_$parm"])) {
			$fld->SearchOperator = ewr_StripSlashes(@$_GET["so_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["sc_$parm"])) {
			$fld->SearchCondition = ewr_StripSlashes(@$_GET["sc_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["sv2_$parm"])) {
			$fld->SearchValue2 = ewr_StripSlashes(@$_GET["sv2_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["so2_$parm"])) {
			$fld->SearchOperator2 = ewr_StripSlashes($_GET["so2_$parm"]);
			$got = TRUE;
		}
		return $got;
	}

	// Set default ext filter
	function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2) {
		$fld->DefaultSearchValue = $sv1; // Default ext filter value 1
		$fld->DefaultSearchValue2 = $sv2; // Default ext filter value 2 (if operator 2 is enabled)
		$fld->DefaultSearchOperator = $so1; // Default search operator 1
		$fld->DefaultSearchOperator2 = $so2; // Default search operator 2 (if operator 2 is enabled)
		$fld->DefaultSearchCondition = $sc; // Default search condition (if operator 2 is enabled)
	}

	// Apply default ext filter
	function ApplyDefaultExtFilter(&$fld) {
		$fld->SearchValue = $fld->DefaultSearchValue;
		$fld->SearchValue2 = $fld->DefaultSearchValue2;
		$fld->SearchOperator = $fld->DefaultSearchOperator;
		$fld->SearchOperator2 = $fld->DefaultSearchOperator2;
		$fld->SearchCondition = $fld->DefaultSearchCondition;
	}

	// Check if Text Filter applied
	function TextFilterApplied(&$fld) {
		return (strval($fld->SearchValue) <> strval($fld->DefaultSearchValue) ||
			strval($fld->SearchValue2) <> strval($fld->DefaultSearchValue2) ||
			(strval($fld->SearchValue) <> "" &&
				strval($fld->SearchOperator) <> strval($fld->DefaultSearchOperator)) ||
			(strval($fld->SearchValue2) <> "" &&
				strval($fld->SearchOperator2) <> strval($fld->DefaultSearchOperator2)) ||
			strval($fld->SearchCondition) <> strval($fld->DefaultSearchCondition));
	}

	// Check if Non-Text Filter applied
	function NonTextFilterApplied(&$fld) {
		if (is_array($fld->DropDownValue)) {
			if (is_array($fld->DefaultDropDownValue)) {
				if (count($fld->DefaultDropDownValue) <> count($fld->DropDownValue))
					return TRUE;
				else
					return (count(array_diff($fld->DefaultDropDownValue, $fld->DropDownValue)) <> 0);
			} else {
				return TRUE;
			}
		} else {
			if (is_array($fld->DefaultDropDownValue))
				return TRUE;
			else
				$v1 = strval($fld->DefaultDropDownValue);
			if ($v1 == EWR_INIT_VALUE)
				$v1 = "";
			$v2 = strval($fld->DropDownValue);
			if ($v2 == EWR_INIT_VALUE || $v2 == EWR_ALL_VALUE)
				$v2 = "";
			return ($v1 <> $v2);
		}
	}

	// Get dropdown value from session
	function GetSessionDropDownValue(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->DropDownValue, 'sv_testing_requests_report_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_testing_requests_report_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_testing_requests_report_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_testing_requests_report_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_testing_requests_report_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_testing_requests_report_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_testing_requests_report_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $so, $parm) {
		$_SESSION['sv_testing_requests_report_' . $parm] = $sv;
		$_SESSION['so_testing_requests_report_' . $parm] = $so;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_testing_requests_report_' . $parm] = $sv1;
		$_SESSION['so_testing_requests_report_' . $parm] = $so1;
		$_SESSION['sc_testing_requests_report_' . $parm] = $sc;
		$_SESSION['sv2_testing_requests_report_' . $parm] = $sv2;
		$_SESSION['so2_testing_requests_report_' . $parm] = $so2;
	}

	// Check if has Session filter values
	function HasSessionFilterValues($parm) {
		return ((@$_SESSION['sv_' . $parm] <> "" && @$_SESSION['sv_' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['sv_' . $parm] <> "" && @$_SESSION['sv_' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['sv2_' . $parm] <> "" && @$_SESSION['sv2_' . $parm] <> EWR_INIT_VALUE));
	}

	// Dropdown filter exist
	function DropDownFilterExist(&$fld, $FldOpr) {
		$sWrk = "";
		$this->BuildDropDownFilter($fld, $sWrk, $FldOpr);
		return ($sWrk <> "");
	}

	// Extended filter exist
	function ExtendedFilterExist(&$fld) {
		$sExtWrk = "";
		$this->BuildExtendedFilter($fld, $sExtWrk);
		return ($sExtWrk <> "");
	}

	// Validate form
	function ValidateForm() {
		global $ReportLanguage, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EWR_SERVER_VALIDATE)
			return ($gsFormError == "");

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			$gsFormError .= ($gsFormError <> "") ? "<p>&nbsp;</p>" : "";
			$gsFormError .= $sFormCustomError;
		}
		return $ValidateForm;
	}

	// Clear selection stored in session
	function ClearSessionSelection($parm) {
		$_SESSION["sel_testing_requests_report_$parm"] = "";
		$_SESSION["rf_testing_requests_report_$parm"] = "";
		$_SESSION["rt_testing_requests_report_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->FieldByParm($parm);
		$fld->SelectionList = @$_SESSION["sel_testing_requests_report_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_testing_requests_report_$parm"];
		$fld->RangeTo = @$_SESSION["rt_testing_requests_report_$parm"];
	}

	// Load default value for filters
	function LoadDefaultFilters() {
		/**
		* Set up default values for non Text filters
		*/

		// Field testing_date
		$this->testing_date->DefaultDropDownValue = EWR_INIT_VALUE;
		if (!$this->SearchCommand) $this->testing_date->DropDownValue = $this->testing_date->DefaultDropDownValue;
		$sWrk = "";
		$this->BuildDropDownFilter($this->testing_date, $sWrk, $this->testing_date->SearchOperator, TRUE);
		ewr_LoadSelectionFromFilter($this->testing_date, $sWrk, $this->testing_date->DefaultSelectionList);
		if (!$this->SearchCommand) $this->testing_date->SelectionList = $this->testing_date->DefaultSelectionList;
		/**
		* Set up default values for extended filters
		* function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2)
		* Parameters:
		* $fld - Field object
		* $so1 - Default search operator 1
		* $sv1 - Default ext filter value 1
		* $sc - Default search condition (if operator 2 is enabled)
		* $so2 - Default search operator 2 (if operator 2 is enabled)
		* $sv2 - Default ext filter value 2 (if operator 2 is enabled)
		*/

		// Field user_name
		$this->SetDefaultExtFilter($this->user_name, "LIKE", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->user_name);
		/**
		* Set up default values for popup filters
		*/

		// Field testing_date
		// $this->testing_date->DefaultSelectionList = array("val1", "val2");

	}

	// Check if filter applied
	function CheckFilter() {

		// Check testing_date extended filter
		if ($this->NonTextFilterApplied($this->testing_date))
			return TRUE;

		// Check testing_date popup filter
		if (!ewr_MatchedArray($this->testing_date->DefaultSelectionList, $this->testing_date->SelectionList))
			return TRUE;

		// Check user_name text filter
		if ($this->TextFilterApplied($this->user_name))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList($showDate = FALSE) {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field testing_date
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildDropDownFilter($this->testing_date, $sExtWrk, $this->testing_date->SearchOperator);
		if (is_array($this->testing_date->SelectionList))
			$sWrk = ewr_JoinArray($this->testing_date->SelectionList, ", ", EWR_DATATYPE_DATE, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->testing_date->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field user_name
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->user_name, $sExtWrk);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->user_name->FldCaption() . "</span>" . $sFilter . "</div>";
		$divstyle = "";
		$divdataclass = "";

		// Show Filters
		if ($sFilterList <> "" || $showDate) {
			$sMessage = "<div" . $divstyle . $divdataclass . "><div id=\"ewrFilterList\" class=\"alert alert-info ewDisplayTable\">";
			if ($showDate)
				$sMessage .= "<div id=\"ewrCurrentDate\">" . $ReportLanguage->Phrase("ReportGeneratedDate") . ewr_FormatDateTime(date("Y-m-d H:i:s"), 1) . "</div>";
			if ($sFilterList <> "")
				$sMessage .= "<div id=\"ewrCurrentFilters\">" . $ReportLanguage->Phrase("CurrentFilters") . "</div>" . $sFilterList;
			$sMessage .= "</div></div>";
			$this->Message_Showing($sMessage, "");
			echo $sMessage;
		}
	}

	// Get list of filters
	function GetFilterList() {

		// Initialize
		$sFilterList = "";

		// Field testing_date
		$sWrk = "";
		$sWrk = ($this->testing_date->DropDownValue <> EWR_INIT_VALUE) ? $this->testing_date->DropDownValue : "";
		if (is_array($sWrk))
			$sWrk = implode("||", $sWrk);
		if ($sWrk <> "")
			$sWrk = "\"sv_testing_date\":\"" . ewr_JsEncode2($sWrk) . "\"";
		if ($sWrk == "") {
			$sWrk = ($this->testing_date->SelectionList <> EWR_INIT_VALUE) ? $this->testing_date->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_testing_date\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field user_name
		$sWrk = "";
		if ($this->user_name->SearchValue <> "" || $this->user_name->SearchValue2 <> "") {
			$sWrk = "\"sv_user_name\":\"" . ewr_JsEncode2($this->user_name->SearchValue) . "\"," .
				"\"so_user_name\":\"" . ewr_JsEncode2($this->user_name->SearchOperator) . "\"," .
				"\"sc_user_name\":\"" . ewr_JsEncode2($this->user_name->SearchCondition) . "\"," .
				"\"sv2_user_name\":\"" . ewr_JsEncode2($this->user_name->SearchValue2) . "\"," .
				"\"so2_user_name\":\"" . ewr_JsEncode2($this->user_name->SearchOperator2) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Return filter list in json
		if ($sFilterList <> "")
			return "{" . $sFilterList . "}";
		else
			return "null";
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ewr_StripSlashes(@$_POST["filter"]), TRUE);
		return $this->SetupFilterList($filter);
	}

	// Setup list of filters
	function SetupFilterList($filter) {
		if (!is_array($filter))
			return FALSE;

		// Field testing_date
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_testing_date", $filter)) {
			$sWrk = $filter["sv_testing_date"];
			if (strpos($sWrk, "||") !== FALSE)
				$sWrk = explode("||", $sWrk);
			$this->SetSessionDropDownValue($sWrk, @$filter["so_testing_date"], "testing_date");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_testing_date", $filter)) {
			$sWrk = $filter["sel_testing_date"];
			$sWrk = explode("||", $sWrk);
			$this->testing_date->SelectionList = $sWrk;
			$_SESSION["sel_testing_requests_report_testing_date"] = $sWrk;
			$this->SetSessionDropDownValue(EWR_INIT_VALUE, "", "testing_date"); // Clear drop down
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionDropDownValue(EWR_INIT_VALUE, "", "testing_date");
			$this->testing_date->SelectionList = "";
			$_SESSION["sel_testing_requests_report_testing_date"] = "";
		}

		// Field user_name
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_user_name", $filter) || array_key_exists("so_user_name", $filter) ||
			array_key_exists("sc_user_name", $filter) ||
			array_key_exists("sv2_user_name", $filter) || array_key_exists("so2_user_name", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_user_name"], @$filter["so_user_name"], @$filter["sc_user_name"], @$filter["sv2_user_name"], @$filter["so2_user_name"], "user_name");
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "user_name");
		}
		return TRUE;
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		if (!$this->DropDownFilterExist($this->testing_date, $this->testing_date->SearchOperator)) {
			if (is_array($this->testing_date->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->testing_date, "`testing_date`", EWR_DATATYPE_DATE, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->testing_date, $sFilter, "popup");
				$this->testing_date->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		return $sWrk;
	}

	//-------------------------------------------------------------------------------
	// Function GetSort
	// - Return Sort parameters based on Sort Links clicked
	// - Variables setup: Session[EWR_TABLE_SESSION_ORDER_BY], Session["sort_Table_Field"]
	function GetSort($options = array()) {
		if ($this->DrillDown)
			return "";
		$bResetSort = @$options["resetsort"] == "1" || @$_GET["cmd"] == "resetsort";
		$orderBy = (@$options["order"] <> "") ? @$options["order"] : ewr_StripSlashes(@$_GET["order"]);
		$orderType = (@$options["ordertype"] <> "") ? @$options["ordertype"] : ewr_StripSlashes(@$_GET["ordertype"]);

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for a resetsort command
		if ($bResetSort) {
			$this->setOrderBy("");
			$this->setStartGroup(1);
			$this->testing_date->setSort("");
			$this->user_name->setSort("");
			$this->user_aadhar_no->setSort("");
			$this->mobile_no->setSort("");
			$this->user_email->setSort("");
			$this->user_dob->setSort("");
			$this->user_gender->setSort("");
			$this->user_blood_group->setSort("");
			$this->lab_name->setSort("");
			$this->lab_username->setSort("");
			$this->testing_name->setSort("");
			$this->testing_price->setSort("");
			$this->testing_status->setSort("");

		// Check for an Order parameter
		} elseif ($orderBy <> "") {
			$this->CurrentOrder = $orderBy;
			$this->CurrentOrderType = $orderType;
			$this->UpdateSort($this->testing_date, $bCtrl); // testing_date
			$this->UpdateSort($this->user_name, $bCtrl); // user_name
			$this->UpdateSort($this->user_aadhar_no, $bCtrl); // user_aadhar_no
			$this->UpdateSort($this->mobile_no, $bCtrl); // mobile_no
			$this->UpdateSort($this->user_email, $bCtrl); // user_email
			$this->UpdateSort($this->user_dob, $bCtrl); // user_dob
			$this->UpdateSort($this->user_gender, $bCtrl); // user_gender
			$this->UpdateSort($this->user_blood_group, $bCtrl); // user_blood_group
			$this->UpdateSort($this->lab_name, $bCtrl); // lab_name
			$this->UpdateSort($this->lab_username, $bCtrl); // lab_username
			$this->UpdateSort($this->testing_name, $bCtrl); // testing_name
			$this->UpdateSort($this->testing_price, $bCtrl); // testing_price
			$this->UpdateSort($this->testing_status, $bCtrl); // testing_status
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}
		return $this->getOrderBy();
	}

	// Export to HTML
	function ExportHtml($html, $options = array()) {

		//global $gsExportFile;
		//header('Content-Type: text/html' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		//header('Content-Disposition: attachment; filename=' . $gsExportFile . '.html');

		$folder = @$this->GenOptions["folder"];
		$fileName = @$this->GenOptions["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";

		// Save generate file for print
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
			$baseTag = "<base href=\"" . ewr_BaseUrl() . "\">";
			$html = preg_replace('/<head>/', '<head>' . $baseTag, $html);
			ewr_SaveFile($folder, $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file")
			echo $html;
		return $saveToFile;
	}

	// Export to WORD
	function ExportWord($html, $options = array()) {
		global $gsExportFile;
		$folder = @$options["folder"];
		$fileName = @$options["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
		 	ewr_SaveFile(ewr_PathCombine(ewr_AppRoot(), $folder, TRUE), $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file") {
			header('Content-Type: application/vnd.ms-word' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
			header('Content-Disposition: attachment; filename=' . $gsExportFile . '.doc');
			echo $html;
		}
		return $saveToFile;
	}

	// Export to EXCEL
	function ExportExcel($html, $options = array()) {
		global $gsExportFile;
		$folder = @$options["folder"];
		$fileName = @$options["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
		 	ewr_SaveFile(ewr_PathCombine(ewr_AppRoot(), $folder, TRUE), $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file") {
			header('Content-Type: application/vnd.ms-excel' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
			header('Content-Disposition: attachment; filename=' . $gsExportFile . '.xls');
			echo $html;
		}
		return $saveToFile;
	}

	// Export to PDF
	function ExportPdf($html, $options = array()) {
		$folder = @$options["folder"];
		$fileName = @$options["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
			$fileName = str_replace(".pdf", ".html", $fileName); // Handle as html
		 	ewr_SaveFile(ewr_PathCombine(ewr_AppRoot(), $folder, TRUE), $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file")
			echo $html;
		ewr_DeleteTmpImages($html);
		return $saveToFile;
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ewr_Header(FALSE) ?>
<?php

// Create page object
if (!isset($testing_requests_report_rpt)) $testing_requests_report_rpt = new crtesting_requests_report_rpt();
if (isset($Page)) $OldPage = $Page;
$Page = &$testing_requests_report_rpt;

// Page init
$Page->Page_Init();

// Page main
$Page->Page_Main();

// Global Page Rendering event (in ewrusrfn*.php)
Page_Rendering();

// Page Rendering event
$Page->Page_Render();
?>
<?php include_once "phprptinc/header.php" ?>
<?php if ($Page->Export == "" || $Page->Export == "print" || $Page->Export == "email" && @$gsEmailContentType == "url") { ?>
<script type="text/javascript">

// Create page object
var testing_requests_report_rpt = new ewr_Page("testing_requests_report_rpt");

// Page properties
testing_requests_report_rpt.PageID = "rpt"; // Page ID
var EWR_PAGE_ID = testing_requests_report_rpt.PageID;

// Extend page with Chart_Rendering function
testing_requests_report_rpt.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
testing_requests_report_rpt.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = ftesting_requests_reportrpt = new ewr_Form("ftesting_requests_reportrpt");

// Validate method
ftesting_requests_reportrpt.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate method
ftesting_requests_reportrpt.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
ftesting_requests_reportrpt.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
ftesting_requests_reportrpt.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Use Ajax
ftesting_requests_reportrpt.Lists["sv_testing_date"] = {"LinkField":"sv_testing_date","Ajax":true,"DisplayFields":["sv_testing_date","","",""],"ParentFields":[],"FilterFields":[],"Options":[],"Template":""};
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($Page->Export == "") { ?>
<!-- container (begin) -->
<div id="ewContainer" class="ewContainer">
<!-- top container (begin) -->
<div id="ewTop" class="ewTop">
<a id="top"></a>
<?php } ?>
<?php if (@$Page->GenOptions["showfilter"] == "1") { ?>
<?php $Page->ShowFilterList(TRUE) ?>
<?php } ?>
<!-- top slot -->
<div class="ewToolbar">
<?php if ($Page->Export == "" && (!$Page->DrillDown || !$Page->DrillDownInPanel)) { ?>
<?php if ($ReportBreadcrumb) $ReportBreadcrumb->Render(); ?>
<?php } ?>
<?php
if (!$Page->DrillDownInPanel) {
	$Page->ExportOptions->Render("body");
	$Page->SearchOptions->Render("body");
	$Page->FilterOptions->Render("body");
	$Page->GenerateOptions->Render("body");
}
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<?php echo $ReportLanguage->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php $Page->ShowPageHeader(); ?>
<?php $Page->ShowMessage(); ?>
<?php if ($Page->Export == "") { ?>
</div>
<!-- top container (end) -->
	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
<?php } ?>
	<!-- Left slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- left container (end) -->
	<!-- center container - report (begin) -->
	<div id="ewCenter" class="ewCenter">
<?php } ?>
	<!-- center slot -->
<!-- summary report starts -->
<?php if ($Page->Export <> "pdf") { ?>
<div id="report_summary">
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<!-- Search form (begin) -->
<form name="ftesting_requests_reportrpt" id="ftesting_requests_reportrpt" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="ftesting_requests_reportrpt_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_testing_date" class="ewCell form-group">
	<label for="sv_testing_date" class="ewSearchCaption ewLabel"><?php echo $Page->testing_date->FldCaption() ?></label>
	<span class="ewSearchField">
<?php ewr_PrependClass($Page->testing_date->EditAttrs["class"], "form-control"); ?>
<select data-table="testing_requests_report" data-field="x_testing_date" data-value-separator="<?php echo ewr_HtmlEncode(is_array($Page->testing_date->DisplayValueSeparator) ? json_encode($Page->testing_date->DisplayValueSeparator) : $Page->testing_date->DisplayValueSeparator) ?>" id="sv_testing_date" name="sv_testing_date"<?php echo $Page->testing_date->EditAttributes() ?>>
<option value=""><?php echo $ReportLanguage->Phrase("PleaseSelect") ?></option>
<?php
	$cntf = is_array($Page->testing_date->AdvancedFilters) ? count($Page->testing_date->AdvancedFilters) : 0;
	$cntd = is_array($Page->testing_date->DropDownList) ? count($Page->testing_date->DropDownList) : 0;
	$totcnt = $cntf + $cntd;
	$wrkcnt = 0;
	if ($cntf > 0) {
		foreach ($Page->testing_date->AdvancedFilters as $filter) {
			if ($filter->Enabled) {
				$selwrk = ewr_MatchedFilterValue($Page->testing_date->DropDownValue, $filter->ID) ? " selected" : "";
?>
<option value="<?php echo $filter->ID ?>"<?php echo $selwrk ?>><?php echo $filter->Name ?></option>
<?php
				$wrkcnt += 1;
			}
		}
	}
	for ($i = 0; $i < $cntd; $i++) {
		$selwrk = " selected";
?>
<option value="<?php echo $Page->testing_date->DropDownList[$i] ?>"<?php echo $selwrk ?>><?php echo ewr_DropDownDisplayValue($Page->testing_date->DropDownList[$i], "date", 0) ?></option>
<?php
		$wrkcnt += 1;
	}
?>
</select>
<input type="hidden" name="s_sv_testing_date" id="s_sv_testing_date" value="<?php echo $Page->testing_date->LookupFilterQuery() ?>"></span>
</div>
</div>
<div id="r_2" class="ewRow">
<div id="c_user_name" class="ewCell form-group">
	<label for="sv_user_name" class="ewSearchCaption ewLabel"><?php echo $Page->user_name->FldCaption() ?></label>
	<span class="ewSearchOperator"><?php echo $ReportLanguage->Phrase("LIKE"); ?><input type="hidden" name="so_user_name" id="so_user_name" value="LIKE"></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->user_name->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="testing_requests_report" data-field="x_user_name" id="sv_user_name" name="sv_user_name" placeholder="<?php echo $Page->user_name->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->user_name->SearchValue) ?>"<?php echo $Page->user_name->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
ftesting_requests_reportrpt.Init();
ftesting_requests_reportrpt.FilterList = <?php echo $Page->GetFilterList() ?>;
</script>
<!-- Search form (end) -->
<?php } ?>
<?php if ($Page->ShowCurrentFilter) { ?>
<?php $Page->ShowFilterList() ?>
<?php } ?>
<?php

// Set the last group to display if not export all
if ($Page->ExportAll && $Page->Export <> "") {
	$Page->StopGrp = $Page->TotalGrps;
} else {
	$Page->StopGrp = $Page->StartGrp + $Page->DisplayGrps - 1;
}

// Stop group <= total number of groups
if (intval($Page->StopGrp) > intval($Page->TotalGrps))
	$Page->StopGrp = $Page->TotalGrps;
$Page->RecCount = 0;
$Page->RecIndex = 0;

// Get first row
if ($Page->TotalGrps > 0) {
	$Page->GetRow(1);
	$Page->GrpCount = 1;
}
$Page->GrpIdx = ewr_InitArray(2, -1);
$Page->GrpIdx[0] = -1;
$Page->GrpIdx[1] = $Page->StopGrp - $Page->StartGrp + 1;
while ($rs && !$rs->EOF && $Page->GrpCount <= $Page->DisplayGrps || $Page->ShowHeader) {

	// Show dummy header for custom template
	// Show header

	if ($Page->ShowHeader) {
?>
<?php if ($Page->Export <> "pdf") { ?>
<?php if ($Page->Export == "word" || $Page->Export == "excel") { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } else { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
<?php if ($Page->testing_date->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="testing_date"><div class="testing_requests_report_testing_date"><span class="ewTableHeaderCaption"><?php echo $Page->testing_date->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="testing_date">
<?php if ($Page->SortUrl($Page->testing_date) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_testing_date">
			<span class="ewTableHeaderCaption"><?php echo $Page->testing_date->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'testing_requests_report_testing_date', false, '<?php echo $Page->testing_date->RangeFrom; ?>', '<?php echo $Page->testing_date->RangeTo; ?>');" id="x_testing_date<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_testing_date" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->testing_date) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->testing_date->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->testing_date->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->testing_date->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'testing_requests_report_testing_date', false, '<?php echo $Page->testing_date->RangeFrom; ?>', '<?php echo $Page->testing_date->RangeTo; ?>');" id="x_testing_date<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->user_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="user_name"><div class="testing_requests_report_user_name"><span class="ewTableHeaderCaption"><?php echo $Page->user_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="user_name">
<?php if ($Page->SortUrl($Page->user_name) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_user_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_name->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_user_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->user_name) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->user_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->user_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->user_aadhar_no->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="user_aadhar_no"><div class="testing_requests_report_user_aadhar_no"><span class="ewTableHeaderCaption"><?php echo $Page->user_aadhar_no->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="user_aadhar_no">
<?php if ($Page->SortUrl($Page->user_aadhar_no) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_user_aadhar_no">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_aadhar_no->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_user_aadhar_no" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->user_aadhar_no) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_aadhar_no->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->user_aadhar_no->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->user_aadhar_no->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->mobile_no->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="mobile_no"><div class="testing_requests_report_mobile_no"><span class="ewTableHeaderCaption"><?php echo $Page->mobile_no->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="mobile_no">
<?php if ($Page->SortUrl($Page->mobile_no) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_mobile_no">
			<span class="ewTableHeaderCaption"><?php echo $Page->mobile_no->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_mobile_no" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->mobile_no) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->mobile_no->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->mobile_no->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->mobile_no->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->user_email->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="user_email"><div class="testing_requests_report_user_email"><span class="ewTableHeaderCaption"><?php echo $Page->user_email->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="user_email">
<?php if ($Page->SortUrl($Page->user_email) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_user_email">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_email->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_user_email" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->user_email) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_email->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->user_email->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->user_email->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->user_dob->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="user_dob"><div class="testing_requests_report_user_dob"><span class="ewTableHeaderCaption"><?php echo $Page->user_dob->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="user_dob">
<?php if ($Page->SortUrl($Page->user_dob) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_user_dob">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_dob->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_user_dob" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->user_dob) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_dob->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->user_dob->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->user_dob->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->user_gender->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="user_gender"><div class="testing_requests_report_user_gender"><span class="ewTableHeaderCaption"><?php echo $Page->user_gender->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="user_gender">
<?php if ($Page->SortUrl($Page->user_gender) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_user_gender">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_gender->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_user_gender" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->user_gender) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_gender->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->user_gender->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->user_gender->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->user_blood_group->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="user_blood_group"><div class="testing_requests_report_user_blood_group"><span class="ewTableHeaderCaption"><?php echo $Page->user_blood_group->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="user_blood_group">
<?php if ($Page->SortUrl($Page->user_blood_group) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_user_blood_group">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_blood_group->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_user_blood_group" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->user_blood_group) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->user_blood_group->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->user_blood_group->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->user_blood_group->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->lab_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="lab_name"><div class="testing_requests_report_lab_name"><span class="ewTableHeaderCaption"><?php echo $Page->lab_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="lab_name">
<?php if ($Page->SortUrl($Page->lab_name) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_lab_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->lab_name->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_lab_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->lab_name) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->lab_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->lab_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->lab_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->lab_username->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="lab_username"><div class="testing_requests_report_lab_username"><span class="ewTableHeaderCaption"><?php echo $Page->lab_username->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="lab_username">
<?php if ($Page->SortUrl($Page->lab_username) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_lab_username">
			<span class="ewTableHeaderCaption"><?php echo $Page->lab_username->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_lab_username" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->lab_username) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->lab_username->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->lab_username->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->lab_username->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->testing_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="testing_name"><div class="testing_requests_report_testing_name"><span class="ewTableHeaderCaption"><?php echo $Page->testing_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="testing_name">
<?php if ($Page->SortUrl($Page->testing_name) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_testing_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->testing_name->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_testing_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->testing_name) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->testing_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->testing_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->testing_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->testing_price->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="testing_price"><div class="testing_requests_report_testing_price"><span class="ewTableHeaderCaption"><?php echo $Page->testing_price->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="testing_price">
<?php if ($Page->SortUrl($Page->testing_price) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_testing_price">
			<span class="ewTableHeaderCaption"><?php echo $Page->testing_price->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_testing_price" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->testing_price) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->testing_price->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->testing_price->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->testing_price->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->testing_status->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="testing_status"><div class="testing_requests_report_testing_status"><span class="ewTableHeaderCaption"><?php echo $Page->testing_status->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="testing_status">
<?php if ($Page->SortUrl($Page->testing_status) == "") { ?>
		<div class="ewTableHeaderBtn testing_requests_report_testing_status">
			<span class="ewTableHeaderCaption"><?php echo $Page->testing_status->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer testing_requests_report_testing_status" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->testing_status) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->testing_status->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->testing_status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->testing_status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
	</tr>
</thead>
<tbody>
<?php
		if ($Page->TotalGrps == 0) break; // Show header only
		$Page->ShowHeader = FALSE;
	}
	$Page->RecCount++;
	$Page->RecIndex++;
?>
<?php

		// Render detail row
		$Page->ResetAttrs();
		$Page->RowType = EWR_ROWTYPE_DETAIL;
		$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->testing_date->Visible) { ?>
		<td data-field="testing_date"<?php echo $Page->testing_date->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_testing_date"<?php echo $Page->testing_date->ViewAttributes() ?>><?php echo $Page->testing_date->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->user_name->Visible) { ?>
		<td data-field="user_name"<?php echo $Page->user_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_user_name"<?php echo $Page->user_name->ViewAttributes() ?>><?php echo $Page->user_name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->user_aadhar_no->Visible) { ?>
		<td data-field="user_aadhar_no"<?php echo $Page->user_aadhar_no->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_user_aadhar_no"<?php echo $Page->user_aadhar_no->ViewAttributes() ?>><?php echo $Page->user_aadhar_no->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->mobile_no->Visible) { ?>
		<td data-field="mobile_no"<?php echo $Page->mobile_no->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_mobile_no"<?php echo $Page->mobile_no->ViewAttributes() ?>><?php echo $Page->mobile_no->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->user_email->Visible) { ?>
		<td data-field="user_email"<?php echo $Page->user_email->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_user_email"<?php echo $Page->user_email->ViewAttributes() ?>><?php echo $Page->user_email->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->user_dob->Visible) { ?>
		<td data-field="user_dob"<?php echo $Page->user_dob->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_user_dob"<?php echo $Page->user_dob->ViewAttributes() ?>><?php echo $Page->user_dob->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->user_gender->Visible) { ?>
		<td data-field="user_gender"<?php echo $Page->user_gender->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_user_gender"<?php echo $Page->user_gender->ViewAttributes() ?>><?php echo $Page->user_gender->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->user_blood_group->Visible) { ?>
		<td data-field="user_blood_group"<?php echo $Page->user_blood_group->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_user_blood_group"<?php echo $Page->user_blood_group->ViewAttributes() ?>><?php echo $Page->user_blood_group->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->lab_name->Visible) { ?>
		<td data-field="lab_name"<?php echo $Page->lab_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_lab_name"<?php echo $Page->lab_name->ViewAttributes() ?>><?php echo $Page->lab_name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->lab_username->Visible) { ?>
		<td data-field="lab_username"<?php echo $Page->lab_username->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_lab_username"<?php echo $Page->lab_username->ViewAttributes() ?>><?php echo $Page->lab_username->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->testing_name->Visible) { ?>
		<td data-field="testing_name"<?php echo $Page->testing_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_testing_name"<?php echo $Page->testing_name->ViewAttributes() ?>><?php echo $Page->testing_name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->testing_price->Visible) { ?>
		<td data-field="testing_price"<?php echo $Page->testing_price->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_testing_price"<?php echo $Page->testing_price->ViewAttributes() ?>><?php echo $Page->testing_price->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->testing_status->Visible) { ?>
		<td data-field="testing_status"<?php echo $Page->testing_status->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_testing_requests_report_testing_status"<?php echo $Page->testing_status->ViewAttributes() ?>><?php echo $Page->testing_status->ListViewValue() ?></span></td>
<?php } ?>
	</tr>
<?php

		// Accumulate page summary
		$Page->AccumulateSummary();

		// Get next record
		$Page->GetRow(2);
	$Page->GrpCount++;
} // End while
?>
<?php if ($Page->TotalGrps > 0) { ?>
</tbody>
<tfoot>
	</tfoot>
<?php } elseif (!$Page->ShowHeader && TRUE) { // No header displayed ?>
<?php if ($Page->Export <> "pdf") { ?>
<?php if ($Page->Export == "word" || $Page->Export == "excel") { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } else { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<?php } ?>
<?php if ($Page->TotalGrps > 0 || TRUE) { // Show footer ?>
</table>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "testing_requests_reportrptpager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<!-- Summary Report Ends -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- center container - report (end) -->
	<!-- right container (begin) -->
	<div id="ewRight" class="ewRight">
<?php } ?>
	<!-- Right slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- right container (end) -->
<div class="clearfix"></div>
<!-- bottom container (begin) -->
<div id="ewBottom" class="ewBottom">
<?php } ?>
	<!-- Bottom slot -->
<?php if ($Page->Export == "") { ?>
	</div>
<!-- Bottom Container (End) -->
</div>
<!-- Table Container (End) -->
<?php } ?>
<?php $Page->ShowPageFooter(); ?>
<?php if (EWR_DEBUG_ENABLED) echo ewr_DebugMsg(); ?>
<?php

// Close recordsets
if ($rsgrp) $rsgrp->Close();
if ($rs) $rs->Close();
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "phprptinc/footer.php" ?>
<?php
$Page->Page_Terminate();
if (isset($OldPage)) $Page = $OldPage;
?>
