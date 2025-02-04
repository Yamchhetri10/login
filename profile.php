<?php
// profile.php
require_once 'config.php';
requireLogin();

$db = getDB();
$user = null;
$errors = [];
$success = false;

// Fetch current user data
try {
    $result = $db->query("SELECT * FROM user WHERE id = ?", [$_SESSION['user_id']]);
    $user = $result->fetch();
} catch (PDOException $e) {
    $errors[] = "Error fetching user data";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    
    // Handle file upload if a new photo was submitted
    if (!empty($_FILES['profile_photo']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["profile_photo"]["name"], PATHINFO_EXTENSION));
        $new_filename = $_SESSION['user_id'] . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($file_extension, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                try {
                    $db->query(
                        "UPDATE user SET profile_photo = ? WHERE id = ?",
                        [$target_file, $_SESSION['user_id']]
                    );
                } catch (PDOException $e) {
                    $errors[] = "Error updating profile photo";
                }
            }
        }
    }
    
    // Update user information
    try {
        $db->query(
            "UPDATE user SET first_name = ?, last_name = ?, email = ?, phone_number = ? WHERE id = ?",
            [$first_name, $last_name, $email, $phone_number, $_SESSION['user_id']]
        );
        $success = true;
        
        // Refresh user data
        $result = $db->query("SELECT * FROM user WHERE id = ?", [$_SESSION['user_id']]);
        $user = $result->fetch();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $errors[] = "Email address already in use";
        } else {
            $errors[] = "Error updating profile";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Update</title>
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
    <?php include 'header.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Update Profile</h2>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">Profile updated successfully!</div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mb-4">
                            <?php if (!empty($user['profile_photo'])): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" 
                                     alt="Profile Photo" 
                                     class="rounded-circle"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-6x text-secondary"></i>
                            <?php endif; ?>
                        </div>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="profile_photo" class="form-label">Update Profile Photo</label>
                                <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                            </div>
                            
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                       value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>