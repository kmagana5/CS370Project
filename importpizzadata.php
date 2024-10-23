<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (extension_loaded('mysqli')) {
    // credentials (replace with your own)
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'sys';

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
        exit;
    }

    $sql = "SELECT ID, samplecol FROM sample";
    $result = $conn->query($sql);

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode($data);

    $conn->close();
    exit;
} else {
    echo json_encode(["error" => "mysqli extension is not loaded."]);
    exit;
}
?>
