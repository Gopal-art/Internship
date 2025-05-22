<?php include 'db.php'; ?>
<form method="POST">
    <h2>Login</h2>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res && password_verify($pass, $res['password'])) {
        $_SESSION['user_id'] = $res['id'];
        header("Location: index.php");
    } else {
        echo "Invalid credentials.";
    }
}
?>