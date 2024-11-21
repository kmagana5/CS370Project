<?php
include_once("header.php");
include 'db.php';
$import_attempted = false;
$import_succeeded = false;
$import_error_message = "";
$successful_imports = 0;
$failed_imports = 0;
global $db;

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Path to the SQL file
    $sqlFilePath = 'database.sql';

    if (isset($_POST['upload'])) {
        if ($db->connect_errno) {
            $import_error_message = "Failed to connect to MySQL: " . $db->connect_error . "<br/>";
        } else {

            //drop comment
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
                            ++$successful_imports;
                        } else {
                            ++$failed_imports;
                        }
                    }
                }

            } catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }

            if ($successful_imports > 0) {
                $import_succeeded = true;
            }


            // end drop comment


        }
    } else if (isset($_POST['reset'])) {
        if ($db->connect_errno) {
            $import_error_message = "Failed to connect to MySQL: " . $db->connect_error . "<br/>";
        } else {
            // In case we ever need to drop all tables use this code
            mysqli_report(MYSQLI_REPORT_ERROR);

            $db->query('SET foreign_key_checks = 0');
            if ($result = $db->query("SHOW TABLES")) {
                while ($row = $result->fetch_array(MYSQLI_NUM)) {
                    $db->query('DROP TABLE IF EXISTS ' . $row[0]);
                }
            }

            $db->query('SET foreign_key_checks = 1');
        }
    }


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
                        <h4 class="alert-heading">Execution Succeeded!</h4>
                        <p>Successfully executed <?php echo $successful_imports; ?> queries.</p>
                        <p><?php echo $failed_imports; ?> queries failed to execute.</p>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Import Failed!</h4>
                        <p>All queries failed to execute. Please check your code loser.</p>
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
                <button type="submit" class="btn btn-success" name="upload">Press to Upload Sample Data</button>
                <button type="submit" class="btn btn-success" name="reset">Press to Drop All Tables</button>
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

