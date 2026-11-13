<?php
// supervisor/export_evaluation.php
// Supervisor-scoped entry point for the shared evaluation PDF exporter.
// The canonical logic lives in coordinator/export_evaluation.php, which
// automatically scopes queries to the signed-in supervisor when the role is
// 'supervisor'. This wrapper keeps a supervisor-facing URL.
require_once __DIR__ . '/../coordinator/export_evaluation.php';