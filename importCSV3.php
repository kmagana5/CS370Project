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

        for ($i = 1; $i < sizeof($lines); ++$i) {
            $line = $lines[$i];
            if (trim($line) === "") {
                continue;
            }

            $parsed_csv_line = str_getcsv($line);
            list(
                $first_name, $last_name, $alma_mater, $email,
                $story_worked_on, $story_edited_on, $time_note_added
                ) = $parsed_csv_line;

            // Handle Author Table
            $author_id = null;
            $author_check_query = "SELECT author_id FROM Author WHERE email = ?";
            $author_check_stmt = $db->prepare($author_check_query);
            $author_check_stmt->bind_param('s', $email);
            $author_check_stmt->execute();
            $author_check_result = $author_check_stmt->get_result();

            if ($author_check_result->num_rows > 0) {
                // author already exists
                $author_row = $author_check_result->fetch_assoc();
                $author_id = $author_row['author_id'];
            } else {
                // Insert new author only if they don't exist
                $stmt = $db->prepare("INSERT INTO Author (first_name, last_name, alma_mater, email) VALUES (?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param('ssss', $first_name, $last_name, $alma_mater, $email);
                    if ($stmt->execute()) {
                        $author_id = $db->insert_id;
                        ++$successful_imports;
                    } else {
                        echo "Author insertion error: " . $stmt->error . "<br>";
                        ++$failed_imports;
                    }
                    $stmt->close();
                }
            }
            $author_check_stmt->close();


            // Handle Story Table
            $story_check_query = "SELECT story_id FROM Story WHERE story_id = ?";
            $story_check_stmt = $db->prepare($story_check_query);
            $story_check_stmt->bind_param('i', $story_worked_on);
            $story_check_stmt->execute();
            $story_check_result = $story_check_stmt->get_result();

            if ($story_check_result->num_rows > 0) {
                // Story exists
                // Handle Written Table
                // First see if the author has already worked on this story
                $written_check_query = "SELECT 1 FROM Written WHERE story_id = ? AND author_id = ?";
                $written_check_stmt = $db->prepare($written_check_query);
                $written_check_stmt->bind_param('ii', $story_worked_on, $author_id);
                $written_check_stmt->execute();
                $written_check_result = $written_check_stmt->get_result();

                if ($written_check_result->num_rows == 0) {
                    // Author has not worked on this story yet, so add them
                    $stmt = $db->prepare("INSERT INTO Written (author_id, story_id) VALUES (?, ?)");
                    if ($stmt) {
                        $stmt->bind_param('ii', $author_id, $story_worked_on);
                        if ($stmt->execute()) {
                            ++$successful_imports;
                        } else {
                            echo "Written insertion error: " . $stmt->error . "<br>";
                            ++$failed_imports;
                        }
                        $stmt->close();
                    }
                }
                $written_check_result->close();

            } else {
                echo "Invalid story_worked_on: $story_worked_on <br>";
            }
            $story_check_stmt->close();


            // EditorNotes shit

            $storyE_check_query = "SELECT story_id FROM Story WHERE story_id = ?";
            $storyE_check_stmt = $db->prepare($storyE_check_query);
            $storyE_check_stmt->bind_param('i', $story_edited_on);
            $storyE_check_stmt->execute();
            $storyE_check_result = $storyE_check_stmt->get_result();

            if ($storyE_check_result->num_rows > 0) {
                // Edited story exists
                // EditorNotes Table
                // First see if the author has already made notes on this story
                $editor_check_query = "SELECT 1 FROM EditorNotes WHERE story_id = ? AND author_id = ?";
                $editor_check_stmt = $db->prepare($editor_check_query);
                $editor_check_stmt->bind_param('ii', $story_edited_on, $author_id);
                $editor_check_stmt->execute();
                $editor_check_result = $editor_check_stmt->get_result();

                if($editor_check_result->num_rows > 0){
                    $time_check_query = "SELECT 1 FROM EditorNotes WHERE time_added = ?";
                    $time_check_stmt = $db->prepare($time_check_query);
                    $time_check_stmt->bind_param('s', $time_note_added);
                    $time_check_stmt->execute();
                    $time_check_result = $time_check_stmt->get_result();

                    if($time_check_result->num_rows == 0){
                        $stmt = $db->prepare("INSERT INTO EditorNotes (author_id, story_id, time_added) VALUES (?, ?, ?)");
                        if($stmt) {
                            $stmt->bind_param('iis', $author_id, $story_edited_on, $time_note_added);
                            if ($stmt->execute()) {
                                ++$successful_imports;
                            } else {
                                echo "EditorNotes insertion error: " . $stmt->error . "<br>";
                                ++$failed_imports;
                            }
                            $stmt->close();
                        }
                    }
                    $time_check_result->close();
                } else {
                    $stmt = $db->prepare("INSERT INTO EditorNotes (author_id, story_id, time_added) VALUES (?, ?, ?)");
                    if($stmt) {
                        $stmt->bind_param('iis', $author_id, $story_edited_on, $time_note_added);
                        if ($stmt->execute()) {
                            ++$successful_imports;
                        } else {
                            echo "EditorNotes insertion error: " . $stmt->error . "<br>";
                            ++$failed_imports;
                        }
                        $stmt->close();
                    }
                }
                $editor_check_stmt->close();

            } else {
                echo "Invalid story_edited_on: $story_edited_on <br>";
            }
            $storyE_check_stmt->close();


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
    <title>Data Import 3</title>
</head>
<body>

<main class="container mt-5">
    <div class="card">
        <div class="card-header text-white">
            <h1 class="mb-0">Data Import 3</h1>
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

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>

<?php
include_once('footer.php');
?>


<?php
include_once('footer.php');
?>
