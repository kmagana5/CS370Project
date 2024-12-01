<?php
include_once("header.php");
include 'db.php';
$import_attempted = false;
$import_succeeded = false;
$import_error_message = "";

// If they clicked on the button then the request is post and the following if statement is true
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $import_attempted = true;
    mysqli_report(MYSQLI_REPORT_ERROR);
    global $db;

    // Connect to the database
    if ($db->connect_errno) {
        // If it fails, then more than likely the password to connect to the database is incorrect, the server is not named 'sys',
        // or we are connected to the localhost for mabe like wrong port... User error
        $import_error_message = "Failed to connect to MySQL: " . $db->connect_error . "<br/>";
    } else {
        // Get the contents
        $contents = file_get_contents($_FILES["importFile"]["tmp_name"]);
        // Get the lines given the separator is a new line
        $lines = explode("\n", $contents);
        // create local variables  to see which sql imports (insert intos) have failed
        $successful_imports = 0;
        $failed_imports = 0;

        // Parse through each line
        for($i = 1; $i < sizeof($lines); ++$i) {
            $line = $lines[$i];
            if(trim($line) == "") {
                continue;
            }

            $parsed_csv_line = str_getcsv($line);

            // This list is essentially how we assign values (kinda like a map) to each entry in the csv...
            // so the first entry in the csv ist is known as the 'headline'
            list (
                $headline, $views ,$publish_date, $category, $source,
                $likes, $shares, $time_reading_in_minutes,
                $image_file, $alt_text, $date_uploaded
                ) = $parsed_csv_line;

            // Handle Story Data and Analytics data
            // When we insert into the table we will need a reference to the story it is associated with
            $story_id = null;
            $story_check_query = "SELECT story_id FROM story WHERE headline = ?";
            $story_check_stmt = $db->prepare($story_check_query);
            $story_check_stmt->bind_param("s", $headline); // this bind parameter is saying that the headline is in the form of a string
            $story_check_stmt->execute(); // Attempt to execute the statement
            $story_check_result = $story_check_stmt->get_result(); // Get the results to see if insertions failed

            if($story_check_result->num_rows > 0) {
                // Story already exists
                $story_row = $story_check_result->fetch_assoc();
                $story_id = $story_row['story_id'];
            } else {
                // Insert new story
                $stmt = $db->prepare("INSERT INTO story (headline, views, publish_date, category_id, source_id) VALUES (?, ?, ?, ?, ?)");
                if($stmt)
                {
                    // When inserting values from php to an sql query we must bind the parameters we specify
                    // So the string in the first parameter is in the form of a string, where 's' is for inserting a string
                    // 'i' is for an integer, and not shown here but 'd' is for a decimal or double/floating point value.
                    $stmt->bind_param('sisss', $headline, $views, $publish_date, $category, $source);
                    if ($stmt->execute()) {
                        // Get the current story id which we will need to reference for inserting its analytics data into the
                        // analytics table
                        $story_id = $db->insert_id;
                        ++$successful_imports; // incremement since we suecessfully inserted into the databse
                    } else {
                        // fuck
                        echo "Story insertion error: " . $stmt->error . "<br>";
                        ++$failed_imports;
                    }
                    // no need to keep the statement open so we can close
                    // similar practice to working with state machines just in case we make errors
                    $stmt->close();
                }
                $story_check_stmt->close();

                // Insert Analytics
                $stmt1 = $db->prepare("INSERT INTO analytics (story_id, views, likes, shares, time_reading_in_minutes) VALUES (?, ?, ?, ?, ?)");
                if($stmt1)
                {
                    $stmt1->bind_param('iiiis', $story_id, $views, $likes, $shares, $time_reading_in_minutes);
                    if ($stmt1->execute()) {
                        // noice insertion bro (we inserted into the table successfully)
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
                    // Note how we rely on the story_id that was inserted into the story table
                    $stmt2->bind_param('isss', $story_id, $image_file, $alt_text, $date_uploaded);
                    if ($stmt2->execute()) {
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

    // did we succesfully import????
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
    <main class="container mt-6">
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
    </main>
</body>
</html>
<?php include_once('footer.php'); ?>
