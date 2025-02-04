<?php
// upload_photo.php
require_once 'config.php';

// Check if user just registered (should have user_id in session)
if (!isset($_SESSION['temp_user_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["profile_photo"]["name"], PATHINFO_EXTENSION));
        $new_filename = $_SESSION['temp_user_id'] . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        
        if (!in_array($file_extension, $allowed_types)) {
            $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif ($_FILES["profile_photo"]["size"] > 5000000) { // 5MB limit
            $errors[] = "Sorry, your file is too large. Maximum size is 5MB.";
        } else {
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                try {
                    $db = getDB();
                    $db->query(
                        "UPDATE user SET profile_photo = ? WHERE id = ?",
                        [$target_file, $_SESSION['temp_user_id']]
                    );
                    
                    $success = true;
                    // Clear the temporary session
                    unset($_SESSION['temp_user_id']);
                    // Redirect to login after 2 seconds
                    header("refresh:2;url=login.php");
                    
                } catch (PDOException $e) {
                    $errors[] = "Error updating profile photo";
                }
            } else {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $errors[] = "Please select a file to upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Photo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Upload Your Profile Photo</h2>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                Profile photo uploaded successfully! 
                                Redirecting to login page...
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle fa-6x text-secondary"></i>
                        </div>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="profile_photo" class="form-label">Choose Profile Photo</label>
                                <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*" required>
                                <div class="form-text">
                                    Accepted formats: JPG, JPEG, PNG, GIF (Max size: 5MB)
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Upload Photo</button>
                                <a href="login.php" class="btn btn-secondary">Skip for Now</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>