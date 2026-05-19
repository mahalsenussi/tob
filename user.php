
<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  include 'db_connect.php';

  $action = $_POST['action'];

  if ($action == 'create') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password using password_hash()
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    $stmt->close();

    $success = "User created successfully!";
  } else if ($action == 'update') {
    // Implement update logic (check for existing user, update password if needed)
  }

  $conn->close();
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  </head>
<body>
  <h2>User Management</h2>
  <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
  <?php endif; ?>
  <form action="user.php" method="POST">
    <input type="hidden" name="action" value="create">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Create User</button>
  </form>
  </body>
</html>
