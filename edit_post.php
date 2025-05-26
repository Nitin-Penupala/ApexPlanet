<?php
include 'db_connect.php';

$post_id = $_GET['id'] ?? null;
$post = null;

if ($post_id) {
    // Fetch post details for editing
    $sql = "SELECT id, title, content FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id'])) {
    $id = $_POST['post_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $sql = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $id);

    if ($stmt->execute()) {
        echo "Post updated successfully!";
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}

if (!$post) {
    echo "Post not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="style.css"> <!-- Added stylesheet link -->
    <script src="https://cdn.tailwindcss.com"></script> <!-- Added Tailwind CSS CDN -->
</head>
<body>
    <h2>Edit Post</h2>
    <form action="edit_post.php" method="post">
        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>
        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="10" cols="50"><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>
        <input type="submit" value="Update Post" class="button">
    </form>
    <br>
    <a href="index.php">Back to Posts</a>
</body>
</html>