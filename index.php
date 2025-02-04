<?php
// index.php
require_once 'config.php';
requireLogin();

$db = getDB();
$result = $db->query("SELECT first_name FROM user WHERE id = ?", [$_SESSION['user_id']]);
$user = $result->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .footer {
        margin-top: auto;
    }
</style>
</head>
<body class="bg-light">
    <?php include 'header.php';?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="display-4 mb-4">Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                <p class="lead">This is your personal dashboard. Use the navigation menu to update your profile or explore other features.</p>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>