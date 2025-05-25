<?php
include 'db.php';

$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

$like = "%$search%";
$stmt = $conn->prepare("SELECT COUNT(*) FROM posts WHERE title LIKE ? OR content LIKE ?");
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$totalPages = ceil($total / $limit);

$stmt = $conn->prepare("SELECT id, title, content, created_at FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ssii", $like, $like, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Blog Posts</h2>
    <?php if (isset($_SESSION['username'])): ?>
        <div class="mb-3">Logged in as <strong><?= $_SESSION['username'] ?> (<?= $_SESSION['role'] ?>)</strong> | <a href="logout.php">Logout</a></div>
        <a href="create.php" class="btn btn-success mb-3">Create New Post</a>
    <?php else: ?>
        <a href="login.php" class="btn btn-primary mb-3">Login</a>
    <?php endif; ?>

    <form class="mb-4" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
    </form>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                <small class="text-muted"><?= $row['created_at'] ?></small><br>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mt-2">Edit</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Delete this post?')">Delete</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>

    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
