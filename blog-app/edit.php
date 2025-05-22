<?php
include 'db.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM posts WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
    $stmt->bind_param('ssi', $_POST['title'], $_POST['content'], $id);
    $stmt->execute();
    header("Location: index.php");
}
?>

<form method="POST">
    <h2>Edit Post</h2>
    Title: <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required><br>
    Content: <textarea name="content" required><?= htmlspecialchars($post['content']) ?></textarea><br>
    <button type="submit">Update</button>
</form>