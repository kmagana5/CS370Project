<?php 
include 'header.php';
include 'db.php';

global $db;
                
if ($db->connect_errno) {
    //no db connection
    die("Failed to connect to the database: " . $db->connect_error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class ="AuthorReportContainer">
        <h1>Author Reports</h1>
        <table border="1">
            <thead>
                <tr>
                    <th>Author ID</th>
                    <th>First Name</th>
                    <th>Last name</th>
                    <th>Alma Mater</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                $query = "Select * FROM author";
                $result = $db->query($query);

                if (!$result) {
                    die("Query failed: " . $db->error);
                }

                while ($row = $result->fetch_assoc()) {
                    //PLAN: will probably make nested tables to show different levels
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['author_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['alma_mater']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
