<?php
include_once("header.php");
include 'db.php';
$import_attempted = false ;
$import_succeeded = false ;
$import_error_message = "" ;
global $db;

function parseSQLFile($content) {
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



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $successful_imports = 0;
    $failed_imports = 0;
    $import_attempted = true;
    mysqli_report(MYSQLI_REPORT_ERROR);

    // Path to the SQL file
    $sqlFilePath = 'database.sql';

// Read the SQL file
    $sqlContent = file_get_contents($sqlFilePath);
    if ($sqlContent === false) {
        die("Failed to read the SQL file.");
    }

// Split the file into individual SQL statements
    $sqlStatements = parseSQLFile($sqlContent);

    try {
// Execute each SQL statement
        foreach ($sqlStatements as $statement) {
            if (!empty($statement)) {
                if ($db->query($statement) === true) {
                    echo "Executed: " . substr($statement, 0, 50) . "...\n";
                } else {
                    echo "Error: " . $db->error . "\n";
                }
            }
        }
    } catch (Exception $ex) {
        echo "Fuck";
    }

//    // This is to delete all tables
//
//    // Disable foreign key checks
//    if (!$db->query("SET FOREIGN_KEY_CHECKS = 0")) {
//        die("Error disabling foreign key checks: " . $db->error);
//    }
//
//// Retrieve all table names from the database
//    $result = $db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db'");
//
//    if (!$result) {
//        die("Error retrieving table names: " . $db->error);
//    }
//
//// Drop each table
//    while ($row = $result->fetch_assoc()) {
//        $table = $row['table_name'];
//        $dropQuery = "DROP TABLE IF EXISTS `$table`";
//        if ($db->query($dropQuery) === true) {
//            echo "Dropped table: $table\n";
//        } else {
//            echo "Error dropping table $table: " . $db->error . "\n";
//        }
//    }
//
//// Free result memory
//    $result->free();
//
//// Re-enable foreign key checks
//    if (!$db->query("SET FOREIGN_KEY_CHECKS = 1")) {
//        die("Error enabling foreign key checks: " . $db->error);
//    }
}


?>


<!DOCTYPE html>
<html>
<head>
    <title>comment Data Import</title>
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-white">
            <h1 class="mb-0"> Sample Data Import</h1>
        </div>
        <div class="card-body">
            <?php
            if ($import_attempted) {
                if ($import_succeeded) {
                    ?>
                    <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Import Succeeded!</h4>
                        <p>Successfully imported <?php echo $successful_imports; ?> rows.</p>
                        <p><?php echo $failed_imports; ?> rows failed to import.</p>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Import Failed!</h4>
                        <p>All rows failed to import. Please check your data and try again.</p>
                        <?php echo $import_error_message; ?>
                    </div>
                    <?php
                }
            }
            ?>
            <form method="post" enctype="multipart/form-data" class="mt-4">
                <div class="mb-3">
<!--                    <label for="importFile" class="form-label">Select File to Import:</label>-->
<!--                    <input name="importFile" id="importFile" class="form-control">-->
                </div>
                <button type="submit" class="btn btn-success">Press to Upload Sample Data</button>
            </form>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>

<?php
include_once('footer.php');
?>

