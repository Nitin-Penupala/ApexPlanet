<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

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