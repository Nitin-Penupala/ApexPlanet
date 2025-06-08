<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'] ?? 'user'; // role from form

    // If role requested is admin, check if there are already 2 admins.
    if ($role === 'admin') {
        $checkAdminSql = "SELECT COUNT(*) AS admin_count FROM users WHERE role = 'admin'";
        $checkStmt = $conn->prepare($checkAdminSql);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $adminCount = $checkResult->fetch_assoc()['admin_count'];
        $checkStmt->close();
        if ($adminCount >= 2) {
            echo "Admin limit reached. Please choose the user role.";
            exit();
        }
    }

    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        echo "Registration successful! You can now <a href='login.php'>log in</a>.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
    <title>Register</title>
    <link rel="stylesheet" href="style.css"> <!-- Added stylesheet link -->
    <script src="https://cdn.tailwindcss.com"></script> <!-- Added Tailwind CSS CDN -->
</head>
<body>
    <h2 class="text-4xl font-bold">Register</h2>
    <form action="register.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required class="px-4 py-2 border rounded w-full" 
               title="Username"><br><br>
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
        <!-- New role selection field -->
        <label for="role">Role:</label><br>
        <select id="role" name="role" required class="px-4 py-2 border rounded w-full"><br><br>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br><br>
        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Log in here</a>.</p>

    <script>
      document.getElementById('showPassword').addEventListener('change', function() {
          var passField = document.getElementById('password');
          passField.type = this.checked ? 'text' : 'password';
      });
      document.querySelector('form').addEventListener('submit', function(e) {
          var username = document.getElementById('username').value.trim();
          var password = document.getElementById('password').value;
          if(username === "") {
              alert("Username is required.");
              e.preventDefault();
              return;
          }
          if(password.length < 6) {
              alert("Password must be at least 6 characters.");
              e.preventDefault();
              return;
          }
      });
    </script>
</body>
</html>