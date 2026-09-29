<?php
// src/pages/supervisor/viewReportPage.php
// Supervisor flavour of the shared report workspace.

$viewContext = 'supervisor';
$selfPath = 'view_report.php';
$backUrl = 'review_reports.php';
$canManageEntities = false;
$canExportCsv = false;
$canReview = true;
$showItPercentages = false;
$sidebarPath = 'supervisor_sidebar.php';
$pageTitle = 'Intern Reports & Entities';

require __DIR__ . '/../shared/report_workspace.php';
