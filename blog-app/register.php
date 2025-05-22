<?php include 'db.php'; ?>
<form method="POST">
    <h2>Register</h2>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param('ss', $user, $pass);
    if ($stmt->execute()) {
        echo "User registered!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>