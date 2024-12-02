<?php
include_once("header.php");

$filePath = __DIR__ . '/DataDictionary.csv';

$data = [];
if (($handle = fopen($filePath, 'r')) !== false) {
    while (($row = fgetcsv($handle)) !== false) {
        //Contains the csv data in the array
        $data[] = $row;
    }
    fclose($handle);
}
?>
<div class="container mt-5">
        <h1 class="text-center mb-4">Data Dictionary</h1>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <?php
                    // Display headers from the first row
                    if (!empty($data)) {
                        foreach ($data[0] as $header) {
                            echo "<th>" . htmlspecialchars($header) . "</th>";
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display table rows from the CSV
                foreach ($data as $index => $row) {
                    if ($index === 0) continue; // Skip the header
                    echo "<tr>";
                    foreach ($row as $cell) {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
</div>
<div>
    <img src="assets/ERD.png">
</div>
<?php
include_once("footer.php");