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

    if ($db->connect_errno) {
        $import_error_message = "Failed to connect to MySQL: " . $db->connect_error . "<br/>";
    } else {
        $contents = file_get_contents($_FILES["importFile"]["tmp_name"]);
        $lines = explode("\n", $contents);
        $successful_imports = 0;
        $failed_imports = 0;

        for($i = 1; $i < sizeof($lines); ++$i) {
            $line = $lines[$i];
            if(trim($line) == "") {
                continue;
            }

            $parsed_csv_line = str_getcsv($line);
            list (
                $headline, $views ,$publish_date, $category, $source,
                $likes, $shares, $time_reading_in_minutes,
                $image_file, $alt_text, $date_uploaded
                ) = $parsed_csv_line;

            // Handle Story Data and Analytics data
            $story_id = null;
            $story_check_query = "SELECT story_id FROM story WHERE headline = ?";
            $story_check_stmt = $db->prepare($story_check_query);
            $story_check_stmt->bind_param("s", $headline);
            $story_check_stmt->execute();
            $story_check_result = $story_check_stmt->get_result();

            if($story_check_result->num_rows > 0) {
                // Story already exists
                $story_row = $story_check_result->fetch_assoc();
                $story_id = $story_row['story_id'];
            } else {
                // Insert new story
                $stmt = $db->prepare("INSERT INTO story (headline, views, publish_date, category_id, source_id) VALUES (?, ?, ?, ?, ?)");
                if($stmt)
                {
                    $stmt->bind_param('sisss', $headline, $views, $publish_date, $category, $source);
                    if ($stmt->execute()) {
                        $story_id = $db->insert_id;
                        ++$successful_imports;
                    } else {
                        echo "Story insertion error: " . $stmt->error . "<br>";
                        ++$failed_imports;
                    }
                    $stmt->close();
                }
                $story_check_stmt->close();

                // Insert Analytics
                $stmt1 = $db->prepare("INSERT INTO analytics (story_id, views, likes, shares, time_reading_in_minutes) VALUES (?, ?, ?, ?, ?)");
                if($stmt1)
                {
                    $stmt1->bind_param('iiiis', $story_id, $views, $likes, $shares, $time_reading_in_minutes);
                    if ($stmt1->execute()) {
//                        $story_id = $db->insert_id;
                        ++$successful_imports;
                    } else {
                        echo "Analytics insertion error: " . $stmt1->error . "<br>";
                        ++$failed_imports;
                    }
                    $stmt1->close();
                }

                // Insert Image Data
                $stmt2 = $db->prepare("INSERT INTO image (story_id, image_file, alt_text, date_uploaded) VALUES (?, ?, ?, ?)");
                if($stmt2)
                {
                    $stmt2->bind_param('isss', $story_id, $image_file, $alt_text, $date_uploaded);
                    if ($stmt2->execute()) {
//                        $story_id = $db->insert_id;
                        ++$successful_imports;
                    } else {
                        echo "Analytics insertion error: " . $stmt2->error . "<br>";
                        ++$failed_imports;
                    }
                    $stmt2->close();
                }

            }

        }
    }

    if ($successful_imports > 0) {
        $import_succeeded = true;
    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Import 1</title>
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-white">
            <h1 class="mb-0">Data Import 1</h1>
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
