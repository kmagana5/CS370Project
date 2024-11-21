<?php
include_once("header.php");
include 'db.php';
$import_attempted = false ;
$import_succeeded = false ;
$import_error_message = "" ;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $import_attempted = true;
    mysqli_report(MYSQLI_REPORT_ERROR);

    global $db;
    $table_check_query = "SHOW TABLES LIKE 'Advertiser'";
    $result = $db->query($table_check_query);
}

$successful_imports = 0;
$failed_imports = 0;
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
                    <label for="importFile" class="form-label">Select File to Import:</label>
                    <input name="importFile" id="importFile" class="form-control">
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

