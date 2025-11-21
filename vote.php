<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "moviepoll_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$movie_id = $_POST['movie_id'] ?? null;

if (!$movie_id) {
    header("Location: dashboard.php");
    exit();
}

// Check if user already voted
$check = $conn->prepare("SELECT * FROM votes WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    header("Location: dashboard.php?message=already_voted");
    exit();
}

// Record the vote
$stmt = $conn->prepare("INSERT INTO votes (user_id, movie_id) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $movie_id);
$stmt->execute();

header("Location: results.php?id=$movie_id");
exit();
?>


