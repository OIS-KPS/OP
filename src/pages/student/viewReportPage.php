<?php
// src/pages/student/viewReportPage.php
// Student flavour of the shared report workspace.

$viewContext = 'student';
$selfPath = 'view_report.php';
$backUrl = 'reports.php';
$canManageEntities = false;
$canExportCsv = false;
$canReview = false;
$showItPercentages = false;
// Interns only submit reports and get them approved. Extracted entities are a
// coordinator/supervisor concern, so the per-week entity panel is not rendered
// for them at all. With this off the workspace drops its right-hand column and
// gives the PDF viewer the full width.
$showEntities = false;
// The week dropdown, the submitted stamp, the fullscreen link and the late flag
// move to a card at the top of the right column rather than staying in a bar
// above the PDF. With no entity panel, no verdict and no IT ratio to show, those
// four are the only thing this page says about the open submission, so they read
// as its summary and the document is what you read them against.
$selectorInRightPanel = true;
// This column is 7 of 12 instead of 12, so the PDF embed is narrower than it
// would otherwise be. Fit-the-whole-page is still the right zoom: fit-width at
// this width leaves the page far short of the panel height, and fit scales it
// down instead of leaving dead space underneath.
$pdfView = 'fit';
$sidebarPath = 'sidebar.php';
$pageTitle = 'My Reports & Portfolio';

require __DIR__ . '/../shared/report_workspace.php';
