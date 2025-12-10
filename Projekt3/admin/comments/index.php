<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/functions.php';

Security::requireLogin();

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (Security::validateCSRFToken($_POST['csrf_token'])) {
        $comment_id = (int)$_POST['comment_id'];
        $action = $_POST['action'];
        
        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE blog_comments SET status = 'approved' WHERE id = ?");
            $stmt->execute([$comment_id]);
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("UPDATE blog_comments SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$comment_id]);
        } elseif ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM blog_comments WHERE id = ?");
            $stmt->execute([$comment_id]);
        }
    }
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

$sql = "SELECT c.*, p.title as post_title 
        FROM blog_comments c 
        JOIN blog_posts p ON c.post_id = p.id";

if ($status_filter !== 'all') {
    $sql .= " WHERE c.status = :status";
}

$sql .= " ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
if ($status_filter !== 'all') {
    $stmt->bindParam(':status', $status_filter);
}
$stmt->execute();
$comments = $stmt->fetchAll();

$csrf_token = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kommentare - Blog Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Kommentare</h1>
        
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex gap-2">
                <a href="?status=all" class="px-4 py-2 rounded <?php echo $status_filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                    Alle
                </a>
                <a href="?status=pending" class="px-4 py-2 rounded <?php echo $status_filter === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                    Wartend
                </a>
                <a href="?status=approved" class="px-4 py-2 rounded <?php echo $status_filter === 'approved' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                    Genehmigt
                </a>
                <a href="?status=rejected" class="px-4 py-2 rounded <?php echo $status_filter === 'rejected' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
                    Abgelehnt
                </a>
            </div>
        </div>
        
        <div class="space-y-4">
            <?php if (count($comments) > 0): ?>
                <?php foreach ($comments as $comment): ?>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-bold text-gray-800"><?php echo htmlspecialchars($comment['author_name']); ?></h3>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($comment['author_email']); ?></p>
                            <p class="text-sm text-gray-500">Post: <a href="/post.php?slug=<?php echo urlencode($comment['post_title']); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($comment['post_title']); ?></a></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 text-sm rounded <?php 
                                echo $comment['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                    ($comment['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); 
                            ?>">
                                <?php echo ucfirst($comment['status']); ?>
                            </span>
                            <span class="text-sm text-gray-500"><?php echo formatDate($comment['created_at']); ?></span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                    </div>
                    
                    <div class="flex gap-2">
                        <?php if ($comment['status'] !== 'approved'): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Genehmigen
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <?php if ($comment['status'] !== 'rejected'): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                                Ablehnen
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <form method="POST" class="inline" onsubmit="return confirm('Kommentar wirklich löschen?')">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                Löschen
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <p class="text-gray-500">Keine Kommentare gefunden.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
