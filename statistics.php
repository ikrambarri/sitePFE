<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// Check if the user is logged in and has a creator role in the session
if (!isset($_SESSION["connect"]) || $_SESSION["connect"] !== true || !isset($_SESSION["id"])) {
    // Redirect to login page or show an error message
    header("Location: ../login.php");
    exit();
}

// Connect to the database
require("connexion.php");

// Get the creator's ID from the session
$creator_id = $_SESSION["id"];



// Calculate statistics for the creator
$total_demands = mysqli_num_rows(mysqli_query($con, "SELECT d.*
    FROM demande d
    JOIN services s ON d.id_Service = s.id_Services
    WHERE s.id_CreateurSRV = $creator_id"));

$total_posts = mysqli_num_rows(mysqli_query($con, "SELECT c.*
    FROM commentaire c
    JOIN services s ON c.id_Service = s.id_Services
    WHERE s.id_CreateurSRV = $creator_id"));

$total_comments = $total_posts;

$demand_percentage = calculate_percentage($con, "demande", $creator_id, 30);
$post_percentage = calculate_percentage($con, "commentaire", $creator_id, 30);
$comment_percentage = $post_percentage;

$avg_demand_response_time = calculate_avg_response_time($con, "demande", $creator_id, 30);
$avg_demand_response_time_percentage = calculate_percentage($con, "demande", $creator_id, 30, true);

function calculate_percentage($con, $table, $creator_id, $days, $is_time = false) {
    // Similar calculation logic as before
}

function calculate_avg_response_time($con, $table, $creator_id, $days) {
    // Similar calculation logic as before
}

// Store statistics in an array
$statistics = array(
    "total_demands" => $total_demands,
    "total_posts" => $total_posts,
    "total_comments" => $total_comments,
    "demand_percentage" => $demand_percentage,
    "post_percentage" => $post_percentage,
    "comment_percentage" => $comment_percentage,
    "avg_demand_response_time" => $avg_demand_response_time,
    "avg_demand_response_time_percentage" => $avg_demand_response_time_percentage
);

?>
