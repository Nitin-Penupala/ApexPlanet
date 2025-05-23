<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include your database connection file
include 'db_connect.php';

$sql = "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
if (!$result) {
    die("Error in query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts</title>
</head>
<body>
    <h2>Blog Posts</h2>
    <p><a href="add_post.php">Add New Post</a></p>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars($row["content"])) . "</p>";
            echo "<p><small>Posted on: " . $row["created_at"] . "</small></p>";
            echo "<p>";
            echo "<a href='edit_post.php?id=" . $row["id"] . "'>Edit</a> | ";
            echo "<a href='delete_post.php?id=" . $row["id"] . "' onclick='return confirm(\"Are you sure you want to delete this post?\");'>Delete</a>";
            echo "</p>";
            echo "<hr>";
        }
    } else {
        echo "<p>No posts found.</p>";
    }
    $conn->close();
    ?>
</body>
</html>