<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new crMenu(EWR_MENUBAR_ID); ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(23, "mi_vaccination_centre_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("23", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "vaccination_centre_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(25, "mi_laboratory_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("25", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "laboratory_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(26, "mi_hospital_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("26", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "hospital_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(28, "mi_testing_requests_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("28", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "testing_requests_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(29, "mi_vaccination_request_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("29", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "vaccination_request_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(30, "mi_bed_requests_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("30", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "bed_requests_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
