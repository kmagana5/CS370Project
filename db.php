<?php
$db = new mysqli("localhost", "root", "password", "sys");
if ($db->connect_errno) {
    die("Failed to connect to MySQL: " . $db->connect_error);
}

function parseSQLFile($content)
{
    $statements = [];
    $buffer = '';
    $lines = explode("\n", $content);

    foreach ($lines as $line) {
        $line = trim($line);

        // Skip empty lines and comments
        if (empty($line) || strpos($line, '--') === 0 || strpos($line, '#') === 0) {
            continue;
        }

        // Add line to the buffer
        $buffer .= $line;

        // If a semicolon ends the line, treat it as the end of a query
        if (substr($line, -1) === ';') {
            $statements[] = $buffer;
            $buffer = ''; // Reset buffer
        }
    }

    // Return all parsed statements
    return $statements;
}