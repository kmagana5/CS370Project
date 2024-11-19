<?php
$db = new mysqli("localhost", "root", "password", "sys");
if ($db->connect_errno) {
    die("Failed to connect to MySQL: " . $db->connect_error);
}