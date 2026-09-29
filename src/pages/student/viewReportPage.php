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
$sidebarPath = 'sidebar.php';
$pageTitle = 'My Reports & Portfolio';

require __DIR__ . '/../shared/report_workspace.php';
