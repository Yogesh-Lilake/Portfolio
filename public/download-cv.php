<?php
/**
 * SECURE CV DOWNLOAD HANDLER
 * Works on Localhost + InfinityFree
 */

// Absolute path to resume
$cvPath = __DIR__ . "/downloads/Yogesh_Lilake_Resume.pdf";

// If file missing
if (!file_exists($cvPath)) {
    header("HTTP/1.1 404 Not Found");
    echo "❌ Resume file not found on server.";
    exit;
}

// ----------------------
// LOGGING DOWNLOAD EVENT
// ----------------------

// Create logs directory if missing
$logDir = dirname(__DIR__) . "/logs";
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

// Log file path
$logFile = $logDir . "/cv.log";

// Generate log line
$logLine = date("Y-m-d H:i:s") . " | " . ($_SERVER["REMOTE_ADDR"] ?? "UNKNOWN_IP") . "\n";

// Append log entry
file_put_contents($logFile, $logLine, FILE_APPEND);

// Clean output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Required headers for forced download
header("Content-Description: File Transfer");
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"Yogesh_Lilake_Resume.pdf\"");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . filesize($cvPath));

// Output the file
readfile($cvPath);
exit;
?>
