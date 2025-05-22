<?php
include 'db.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");
?>

<form method="POST">
    <h2>New Post</h2>
    Title: <input type="text" name="title" required><br>
    Content: <textarea name="content" required></textarea><br>
    <button type="submit">Post</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param('ss', $_POST['title'], $_POST['content']);
    $stmt->execute();
    header("Location: index.php");
}
?>