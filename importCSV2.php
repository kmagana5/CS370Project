<?php
include_once("header.php");
include 'db.php';
$import_attempted = false;
$import_succeeded = false;
$import_error_message = "";

// see if the button to upload the data was clicked
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $import_attempted = true;
    mysqli_report(MYSQLI_REPORT_ERROR);
    global $db;

    // Connect to database
    if ($db->connect_errno) {
        $import_error_message = "Failed to connect to MySQL: " . $db->connect_error . "<br/>";
    } else {
        // Get the contents of the file and seperate into each line
        $contents = file_get_contents($_FILES["importFile"]["tmp_name"]);
        $lines = explode("\n", $contents);
        $successful_imports = 0;
        $failed_imports = 0;

        // Parse through line by line and do what we did for csv1
        for ($i = 1; $i < sizeof($lines); ++$i) {
            $line = $lines[$i];
            if (trim($line) === "") {
                continue;
            }

            // Assign the value name for parsed_csv_line
            $parsed_csv_line = str_getcsv($line);
            list(
                $first_name, $last_name, $display_name, $email, $card_num, $security_code, $expires_on, $zip,
                $subscription_tier, $story_id, $comment_time_posted, $reply_count
                ) = $parsed_csv_line;

            // Handle Card Table
            // Again, with inserting into the database we will need a reference of the card_id so our other tables will
            // match corresponding data
            $card_id = null;
            $card_check_query = "SELECT card_id FROM Card WHERE card_num = ?";
            $card_check_stmt = $db->prepare($card_check_query);
            $card_check_stmt->bind_param('s', $card_num); // bind and we are formatting as a string because some people put spaces in their card number
            $card_check_stmt->execute();
            $card_check_result = $card_check_stmt->get_result();

            if ($card_check_result->num_rows > 0) {
                // Card already exists
                $card_row = $card_check_result->fetch_assoc();
                $card_id = $card_row['card_id']; // Get the card_id already associated with
            } else {
                // Insert new card
                $stmt = $db->prepare("INSERT INTO Card (card_num, security_code, expires_on, zip) VALUES (?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param('siss', $card_num, $security_code, $expires_on, $zip);
                    if ($stmt->execute()) {
                        $card_id = $db->insert_id; // get the card_id associated with other data
                    } else {
                        echo "Card insertion error: " . $stmt->error . "<br>";
                    }
                    $stmt->close();
                }
            }
            $card_check_stmt->close();

            // Handle User Table
            $user_id = null;
            $user_check_query = "SELECT user_id FROM User WHERE email = ?";
            $user_check_stmt = $db->prepare($user_check_query);
            $user_check_stmt->bind_param('s', $email);
            $user_check_stmt->execute();
            $user_check_result = $user_check_stmt->get_result();

            if ($user_check_result->num_rows > 0) {
                // User already exists
                $user_row = $user_check_result->fetch_assoc();
                $user_id = $user_row['user_id'];
            } else {
                // Insert new user only if they don't exist
                $stmt = $db->prepare("INSERT INTO User (first_name, last_name, display_name, email, card_id, subscription_status) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param('ssssii', $first_name, $last_name, $display_name, $email, $card_id, $subscription_tier);
                    if ($stmt->execute()) {
                        $user_id = $db->insert_id; // Get user id associated with the other data
                    } else {
                        echo "User insertion error: " . $stmt->error . "<br>";
                    }
                    $stmt->close();
                }
            }
            $user_check_stmt->close();


            // Handle Story Table
            $story_check_query = "SELECT story_id FROM Story WHERE story_id = ?";
            $story_check_stmt = $db->prepare($story_check_query);
            $story_check_stmt->bind_param('i', $story_id);
            $story_check_stmt->execute();
            $story_check_result = $story_check_stmt->get_result();

            if ($story_check_result->num_rows > 0) {
                // Story exists, proceed with Comments
                $comment_check_query = "SELECT 1 FROM Comments WHERE user_id = ? AND story_id = ? AND time_posted = ?";
                $comment_check_stmt = $db->prepare($comment_check_query);
                $comment_check_stmt->bind_param('iis', $user_id, $story_id, $comment_time_posted);
                $comment_check_stmt->execute();
                $comment_check_result = $comment_check_stmt->get_result();

                if ($comment_check_result->num_rows > 0) {
                    // Update the reply count for existing comments
                    $update_query = "UPDATE Comments SET reply_count = ? WHERE user_id = ? AND story_id = ? AND time_posted = ?";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->bind_param('iiis', $reply_count, $user_id, $story_id, $comment_time_posted);
                    if (!$update_stmt->execute()) {
                        echo "Failed to update reply count for comment: user_id = $user_id, story_id = $story_id, time_posted = $comment_time_posted<br>";
                    }
                    $update_stmt->close();

                } else {
                    $stmt = $db->prepare("INSERT INTO Comments (user_id, story_id, time_posted, reply_count) VALUES (?, ?, ?, ?)");
                    if ($stmt) {
                        $stmt->bind_param('iisi', $user_id, $story_id, $comment_time_posted, $reply_count);
                        if (!$stmt->execute()) {
                            echo "Comments insertion error: " . $stmt->error . "<br>";
                            $failed_imports++;
                        } else {
                            $successful_imports++;
                        }
                        $stmt->close();
                    }
                }
                $comment_check_stmt->close();
            } else {
                echo "Invalid story_id: $story_id<br>";
            }
            $story_check_stmt->close();

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
    <title>Data Import 2</title>
</head>
<body>
<main class="container mt-6">
    <div class="card">
        <div class="card-header text-white">
            <h1 class="mb-0">Data Import 2</h1>
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
<?php
include_once('footer.php');
?>
