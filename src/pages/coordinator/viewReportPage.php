<?php
// src/pages/coordinator/viewReportPage.php
// Coordinator flavour of the shared report workspace.

$viewContext = 'coordinator';
$selfPath = 'view_report.php';
$backUrl = 'approved_reports.php';
$canManageEntities = true;
$canExportCsv = true;
$canReview = false;
$showItPercentages = true;
$sidebarPath = 'coordinator_sidebar.php';
$pageTitle = 'Report Inspection & Entities';

require __DIR__ . '/../shared/report_workspace.php';
