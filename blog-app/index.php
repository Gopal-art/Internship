<?php
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$search = $_GET['search'] ?? '';
$limit = 2;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$like = "%$search%";

// Count total posts
if ($search) {
    $count_stmt = $conn->prepare("SELECT COUNT(*) FROM posts WHERE title LIKE ? OR content LIKE ?");
    $count_stmt->bind_param("ss", $like, $like);
} else {
    $count_stmt = $conn->prepare("SELECT COUNT(*) FROM posts");
}
$count_stmt->execute();
$count_stmt->bind_result($total_posts);
$count_stmt->fetch();
$count_stmt->close();

$total_pages = ceil($total_posts / $limit);

// Fetch paginated posts
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $like, $like, $limit, $offset);
} else {
    $stmt = $conn->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Blog Posts</h2>
        <div>
            <a href="create.php" class="btn btn-primary">Create New Post</a>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>

    <form method="GET" class="mb-4">
        <input type="text" name="search" class="form-control" placeholder="Search by title or content" value="<?= htmlspecialchars($search) ?>">
    </form>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="card-title"><?= htmlspecialchars($row['title']) ?></h4>
                <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                <small class="text-muted">Posted at: <?= $row['created_at'] ?></small><br>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mt-2">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Delete this post?')">Delete</a>
            </div>
        </div>
    <?php endwhile; ?>

    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
