<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include 'db_connect.php';

// Handle search input
$search = $_GET['search'] ?? '';

// Pagination setup
$limit = 5; // posts per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if (!empty($search)) {
    $sql = "SELECT id, title, content, created_at FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search . "%";
    $stmt->bind_param("ssii", $search_param, $search_param, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $countSql = "SELECT COUNT(*) AS total FROM posts WHERE title LIKE ? OR content LIKE ?";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("ss", $search_param, $search_param);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalPosts = $countResult->fetch_assoc()['total'];
    $stmt->close();
    $countStmt->close();
} else {
    $sql = "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $countSql = "SELECT COUNT(*) AS total FROM posts";
    $countStmt = $conn->prepare($countSql);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalPosts = $countResult->fetch_assoc()['total'];
    $stmt->close();
    $countStmt->close();
}
$totalPages = ceil($totalPosts / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Dropdown Menu -->
    <div class="dropdown">
        <button class="dropbtn"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?> &#x25BC;</button>
        <div class="dropdown-content">
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Centralized Blog Posts Heading -->
    <div class="flex justify-center mt-4">
        <h2 class="text-6xl font-bold">Blog Posts</h2>
    </div>

    <!-- Search Form -->
    <form method="GET" action="index.php" class="search-form my-4 flex justify-center gap-4">
        <input type="text" name="search" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>" class="px-4 py-2 border rounded">
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Search</button>
    </form>

    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<h3 class='text-2xl font-bold text-blue-600'>" . htmlspecialchars($row["title"]) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars($row["content"])) . "</p>";
            echo "<p><small>Posted on: " . $row["created_at"] . "</small></p>";
            echo "<p>";
            echo "<a href='edit_post.php?id=" . $row["id"] . "' class='bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded'>Edit</a> ";
            // Display Delete button only for admin users
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                echo "<a href='delete_post.php?id=" . $row["id"] . "' class='bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded' onclick='return confirm(\"Are you sure you want to delete this post?\");'>Delete</a>";
            }
            echo "</p>";
            echo "<hr>";
        }
    } else {
        // If no posts found, show message and a button to view all posts
        echo "<p>No posts found.</p>";
        echo "<a href='index.php' class='bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded'>All Posts</a>";
    }

    // Add the NEW POST button at the bottom of the page (above pagination)
    echo "<div class='flex justify-center my-4'>";
    echo "<a href='add_post.php' class='bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded'>NEW POST</a>";
    echo "</div>";

    // Pagination links
    if ($totalPages > 1) {
        echo "<div class='pagination'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $page) ? "active" : "";
            $queryParams = http_build_query(['search' => $search, 'page' => $i]);
            echo "<a class='$active' href='index.php?$queryParams'>$i</a> ";
        }
        echo "</div>";
    }

    $conn->close();
     ?>
</body>
</html>
