<?php
include_once("header.php");
include 'db.php';
$import_attempted = false;
$import_succeeded = false;
$import_error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $import_attempted = true;
    mysqli_report(MYSQLI_REPORT_ERROR);

    global $db;
    $table_check_query = "SHOW TABLES LIKE 'author'";
    $result = $db->query($table_check_query);

    if ($result->num_rows === 0) {
        $create_table_query = "
        CREATE TABLE author (
            author_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            alma_mater VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL
        )
    ";
        if ($db->query($create_table_query) === TRUE) {
            echo "<div class='alert alert-info' id='table-message'>The 'author' table was created successfully.</div>";
            echo "<script>hideMessage('table-message');</script>";
        } else {
            echo "<div class='alert alert-danger' id='table-message'>Error creating 'author' table: " . $db->error . "</div>";
            echo "<script>hideMessage('table-message');</script>";
        }

    }
    if ($db->connect_errno) {
        $import_error_message = "Failed to connect to MySQL: " . $db->connect_error . "<br/>";
    } else {
        $contents = file_get_contents($_FILES["importFile"]["tmp_name"]);
        $lines = explode("\n", $contents);
        $successful_imports = 0;
        $failed_imports = 0;

        for ($i = 1; $i < sizeof($lines); ++$i) {
            $line = $lines[$i];

            if (trim($line) === "") {
                continue;
            }

            $parsed_csv_line = str_getcsv($line);
            $query = "INSERT INTO author (first_name, last_name, alma_mater, email) VALUES ( ?, ?, ?, ?)";
            $stmt = $db->prepare($query);

            if ($stmt) {
                // Removed author_id since it is AUTO_INCREMENT
                $stmt->bind_param('ssss', $parsed_csv_line[0], $parsed_csv_line[1], $parsed_csv_line[2], $parsed_csv_line[3]);

                if ($stmt->execute()) {
                    $successful_imports++;
                } else {
                    $failed_imports++;
                }

                $stmt->close();
            } else {
                $failed_imports++;
            }
        }

        if ($successful_imports > 0) {
            $import_succeeded = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Author Data Import</title>
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-white">
            <h1 class="mb-0">Author Data Import</h1>
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
                    <label for="importFile" class="form-label">Select File to Import:</label>
                    <input type="file" name="importFile" id="importFile" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Upload Data</button>
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


<?php
include_once('footer.php');
?>