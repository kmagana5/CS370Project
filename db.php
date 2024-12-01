<?php
// our database object is initialized in this 'header' file so we can simply call when we need it in our file
$db = new mysqli("localhost", "root", "password", "sys");
if ($db->connect_errno) {
    die("Failed to connect to MySQL: " . $db->connect_error);
}

// This function will parse sql files...used in createDatabase to parse any sql file in an array
// Does not execute just lists
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
        // buffer is just a place to store each line in the query
        // If you know opengl, its like adding vertices in a vertex buffer array and then we will configure what we read later
        $buffer .= $line;

        // If a semicolon ends the line, then we have reached the end of a query
        if (substr($line, -1) === ';') {
            $statements[] = $buffer;
            $buffer = ''; // Reset buffer for reading the next line
        }
    }

    // Return all parsed lines
    return $statements;
}