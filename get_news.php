<?php
// Database connection
$mysqli = new mysqli("localhost", "root", "", "ecity");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch news data
$query = "SELECT * FROM nouvelle";
$result = $mysqli->query($query);

// Initialize an array to store the news data
$newsData = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $newsData[] = $row;
    }
}

// Close database connection
$mysqli->close();

// Output the news data as JSON
echo json_encode($newsData);
?>
