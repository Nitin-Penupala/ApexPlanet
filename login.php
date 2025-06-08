<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Save role in session
            header("Location: index.php"); // Redirect to a protected page
            exit();
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css"> <!-- Added stylesheet link -->
    <script src="https://cdn.tailwindcss.com"></script> <!-- Added Tailwind CSS CDN -->
</head>
<body>
    <div class="text-center my-4">
        <h1 class="text-6xl font-bold">BLOG POSTS</h1> <!-- Increased size -->
        <h2 class="text-3xl font-bold">LOGIN</h2>
    </div>
    <form action="login.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required class="px-4 py-2 border rounded w-full"><br><br>
        <label for="password">Password:</label><br>
        <div class="relative inline-block w-full">
            <input type="password" id="password" name="password" required 
                   pattern="^(?=.{8,})(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).*$" 
                   title="Password must be at least 8 characters, include one uppercase letter and one special character."
                   class="px-4 py-2 border rounded w-full pr-20">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                <input type="checkbox" id="showPassword" class="h-6 w-6">
                <label for="showPassword" class="ml-1 text-sm">Show</label>
            </span>
        </div><br>
        <input type="submit" value="Login">
    </form>
    <p class="text-xl">Don't have an account? <a href="register.php">Register here</a>.</p>
    <script>
      document.getElementById('showPassword').addEventListener('change', function() {
          var passField = document.getElementById('password');
          passField.type = this.checked ? 'text' : 'password';
      });
      document.querySelector('form').addEventListener('submit', function(e) {
          var username = document.getElementById('username').value.trim();
          var password = document.getElementById('password').value;
          if(username === "" || password === "") {
              alert("All fields are required.");
              e.preventDefault();
          }
      });
    </script>
</body>
</html>