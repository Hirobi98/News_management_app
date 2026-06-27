<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $interest = $_POST['interest'] ?? '';
    
    // File upload logic for updating profile picture
    $profile_picture = '';
    $update_picture = false;
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/authors/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
        
        // Generate a unique file name
        $new_file_name = uniqid('author_', true) . '.' . $file_extension;
        $target_file = $target_dir . $new_file_name;
        
        // Allowed file types
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array(strtolower($file_extension), $allowed_types)) {
            // Delete old picture if needed, though we skip it for simplicity to avoid breaking existing references, 
            // but normally we would query the old image path and unlink() it here.
            
            // Move file
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file;
                $update_picture = true;
            } else {
                echo "Error uploading the file.";
                exit();
            }
        } else {
            echo "Error: Only JPG, JPEG, PNG & GIF files are allowed.";
            exit();
        }
    }
    
    if (!empty($id) && is_numeric($id) && !empty($name) && !empty($email)) {
        if ($update_picture) {
            $stmt = $conn->prepare("UPDATE authors SET name=?, email=?, bio=?, interest=?, profile_picture=? WHERE id=?");
            $params = [$name, $email, $bio, $interest, $profile_picture, $id];
        } else {
            $stmt = $conn->prepare("UPDATE authors SET name=?, email=?, bio=?, interest=? WHERE id=?");
            $params = [$name, $email, $bio, $interest, $id];
        }
        
        if ($stmt->execute($params)) {
            header("Location: index.php?success=author_updated");
            exit();
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Error updating record: " . $errorInfo[2];
        }
        $stmt = null;
    } else {
        echo "Please fill in all required fields and ensure ID is valid.";
    }
}
$conn = null;
?>
