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
// This column is 12 of 12 here instead of 7, so the PDF embed is roughly twice
// as wide as it is for the other roles. Fit-to-width would scale the page up to
// match and the text would read much larger than the rest of the portal, so
// ask for fit-the-whole-page instead.
$pdfView = 'fit';
$sidebarPath = 'sidebar.php';
$pageTitle = 'My Reports & Portfolio';

require __DIR__ . '/../shared/report_workspace.php';
