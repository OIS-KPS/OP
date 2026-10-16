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
// The week dropdown, the submitted stamp and the fullscreen link sit in a card
// at the top of the right column rather than in a bar above the PDF. With no
// entity panel and no verdict to show, those three controls are the only thing
// this page has to say about a submission, so they read as the summary of the
// open week and the document stays the thing you look at beside them.
$controlsInRightPanel = true;
// This column is 7 of 12 rather than 12, so the PDF embed is narrower than it
// used to be. Fit-the-whole-page is still the right zoom: at fit-width a
// narrower embed would leave the page far short of the panel height, and fit
// scales the page down instead of leaving dead space under it.
$pdfView = 'fit';
$sidebarPath = 'sidebar.php';
$pageTitle = 'My Reports & Portfolio';

require __DIR__ . '/../shared/report_workspace.php';
