<?php
session_start();
include('../includes/db_connect.php');

if(!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch latest unseen feedback
$result = mysqli_query($conn, "
    SELECT * FROM feedback 
    WHERE user_id=$user_id AND seen=0 
    ORDER BY created_at DESC
");

$feedbacks = [];
while($row = mysqli_fetch_assoc($result)){
    $feedbacks[] = $row;
}

// Return as JSON
echo json_encode($feedbacks);
?>
