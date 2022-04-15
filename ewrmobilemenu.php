<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(23, "mmi_vaccination_centre_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("23", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "vaccination_centre_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(25, "mmi_laboratory_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("25", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "laboratory_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(26, "mmi_hospital_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("26", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "hospital_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(28, "mmi_testing_requests_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("28", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "testing_requests_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(29, "mmi_vaccination_request_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("29", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "vaccination_request_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(30, "mmi_bed_requests_report", $ReportLanguage->Phrase("SimpleReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("30", "MenuText") . $ReportLanguage->Phrase("SimpleReportMenuItemSuffix"), "bed_requests_reportrpt.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
