<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

// Check if the user is admin; if not, deny deletion.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied: Only admin users can delete posts.");
}

$post_id = $_GET['id'] ?? null;

if ($post_id) {
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: index.php"); // Redirect back to the post list
exit();
?>