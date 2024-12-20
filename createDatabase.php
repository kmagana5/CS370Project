<?php
include_once("header.php");
include 'db.php';
$execution_attempted = false;
$execution_succeeded = false;
$import_error_message = "";
$successful_executions = 0;
$failed_executions = 0;
global $db;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Path to the SQL file
    $sqlFilePath = 'database.sql';

    if (isset($_POST['upload'])) {

        if ($db->connect_errno) {
            $import_error_message = "Failed to connect to MySQL: " . $db->connect_error . "<br/>";
        } else {

            $execution_attempted = true;

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
                            ++$successful_executions;
                        } else {
                            ++$failed_executions;
                        }
                    }
                }

            } catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }

            if ($successful_executions > 0) {
                $execution_succeeded = true;
            }


            // TO-DO: import csvs for Source, Advertiser, Subscription, and Category --DONE
            // Importing all pre-saved data
            $filenames = array(
                'Sample_data/subscription_starter_data.csv',
                'Sample_data/category_starter_data.csv',
                'Sample_data/advertiser_starter_data.csv',
                'Sample_data/source_starter_data.csv'//,
                //'Sample_data/story_starter_data.csv'

            );

                // insert into queries in an array to minimize code duplication
            $queries = array(
                "INSERT INTO Subscription (price) VALUES ( ?)",
                "INSERT INTO Category (description) VALUES ( ?)",
                "INSERT INTO Advertiser (company_name, contact_person, business_email, category_id) VALUES ( ?, ?, ?, ?)",
                "INSERT INTO Source (url, organization) VALUES ( ?, ?)"//,
                //"INSERT INTO Story (headline, views,publish_date,category_id, source_id) VALUES ( ?, ?,?,?,?)",
            );

            // Read the contents of each file in the filename list
            $contents = [];
            $lines = [];
            for ($i = 0; $i < sizeof($filenames); $i++) {
                $contents[$i] = file_get_contents($filenames[$i]);
                $lines[$i] = explode("\n", $contents[$i]);
            }

            // I hate code duplication but this unfortunately how we are inserting starting data

            // subscription data
            for ($i = 0; $i < sizeof($lines[0]); $i++) {
                $line = $lines[0][$i];
                if (trim($line) == "") {
                    continue;
                }

                //trims the parsed csv lines to ensure there is not extra whitespace ex: "  $45.21 " --> "$45.21"
                $parsed_csv_line = array_map('trim', str_getcsv($line));
                // Ensures the only characters that are accepted are digits or periods, replaces anything else with empty string
                $price = (float)preg_replace('/[^\d.]/', '', $parsed_csv_line[0]);
                $stmt = $db->prepare($queries[0]);
                if ($stmt) {
                    //echo "Parsed price: $price<br>";
                    $stmt->bind_param('d', $price);
                    if ($stmt->execute()) {
                    }
                    $stmt->close();
                }
            }

            //category
            for ($i = 0; $i < sizeof($lines[1]); $i++) {
                // in the 2d array, we will look at the [1] position and store all lines in that array
                $line = $lines[1][$i];
                if (trim($line) == "") {
                    continue;
                }
                $parsed_csv_line = str_getcsv($line);
                $stmt = $db->prepare($queries[1]);
                if ($stmt) {
                    $stmt->bind_param('s', $parsed_csv_line[0]);
                    if ($stmt->execute()) {
                    }
                    $stmt->close();
                }
            }

            //advertiser
            for ($i = 0; $i < sizeof($lines[2]); $i++) {
                $line = $lines[2][$i];
                if (trim($line) == "") {
                    continue;
                }
                $parsed_csv_line = str_getcsv($line);
                $stmt = $db->prepare($queries[2]);
                if ($stmt) {
                    $stmt->bind_param('sssi', $parsed_csv_line[0], $parsed_csv_line[1], $parsed_csv_line[2], $parsed_csv_line[3]);
                    if ($stmt->execute()) {
                    }
                    $stmt->close();
                }
            }
            // source
            for ($i = 0; $i < sizeof($lines[3]); $i++) {
                $line = $lines[3][$i];
                if (trim($line) == "") {
                    continue;
                }
                $parsed_csv_line = str_getcsv($line);
                $stmt = $db->prepare($queries[3]);
                if ($stmt) {
                    $stmt->bind_param('ss', $parsed_csv_line[0], $parsed_csv_line[1]);
                    if ($stmt->execute()) {
                    }
                    $stmt->close();
                }
            }
            // We had trouble with our import processes since someone might try to import csv 2
            // without inserting story data -- FIXED
            //story
//            for ($i = 0; $i < sizeof($lines[4]); $i++) {
//                $line = $lines[4][$i];
//                if (trim($line) == "") {
//                    continue;
//                }
//                $parsed_csv_line = str_getcsv($line);
//                $stmt = $db->prepare($queries[4]);
//                if ($stmt) {
//                    $stmt->bind_param('sisii', $parsed_csv_line[0], $parsed_csv_line[1], $parsed_csv_line[2], $parsed_csv_line[3], $parsed_csv_line[4]);
//                    if ($stmt->execute()) {
//                    }
//                    $stmt->close();
//                }
//            }
        }

        // This is to erase the entire database
        // DEBUG and initialization only!!!!!!
    } else if (isset($_POST['reset'])) {
        if ($db->connect_errno) {
            $import_error_message = "Failed to connect to MySQL: " . $db->connect_error . "<br/>";
        } else {
            // In case we ever need to drop all tables use this code
            mysqli_report(MYSQLI_REPORT_ERROR);

            // Ignore foreign key restraints so when we delete it wil drop tables with foreign key constraints
            $db->query('SET foreign_key_checks = 0');
            if ($result = $db->query("SHOW TABLES")) {
                while ($row = $result->fetch_array(MYSQLI_NUM)) {
                    // If the table if it exists
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

<main class="container mt-5">
    <div class="card">
        <div class="card-header text-white">
            <h1 class="mb-0"> Sample Data Import</h1>
        </div>
        <div class="card-body">
            <?php
            // This is the green box that says if we successfully inserted into the database
            if ($execution_attempted) {
                if ($execution_succeeded) {
                    ?>
                    <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Execution Succeeded!</h4>
                        <p>Successfully executed <?php echo $successful_executions; ?> queries.</p>
                        <p><?php echo $failed_executions; ?> queries failed to execute.</p>
                    </div>
                    <?php
                }
                // This is the red box if it failed to upload
                else {
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
</main>
</body>
</html>

<?php
include_once('footer.php');
?>

