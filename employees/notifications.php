<?php
require_once 'core/dbConfig.php';

$user_id = $_SESSION['user_id'];

// Mark all as read when visiting this page
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$user_id]);

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <style>
      body {
        font-family: "Arial";
      }
    </style>
    <title>Notifications</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
<div class="container mt-5">
    
    <h2>Your Notifications</h2>
    <ul class="list-group">
        <?php foreach ($notifications as $notif): ?>
            <li class="list-group-item<?php if (!$notif['is_read']) echo ' font-weight-bold'; ?>">
                <?php echo htmlspecialchars($notif['message']); ?>
                <span class="float-right text-muted" style="font-size:12px;"><?php echo $notif['created_at']; ?></span>
            </li>
        <?php endforeach; ?>
        <?php if (empty($notifications)): ?>
            <li class="list-group-item">No notifications.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>