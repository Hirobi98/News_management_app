<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $interest = $_POST['interest'] ?? '';
    
    // Default image path if no upload
    $profile_picture = ''; 
    
    // File upload logic
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/authors/";
        $file_extension = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
        
        // Generate a unique file name
        $new_file_name = uniqid('author_', true) . '.' . $file_extension;
        $target_file = $target_dir . $new_file_name;
        
        // Allowed file types
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array(strtolower($file_extension), $allowed_types)) {
            // Move file
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file;
            } else {
                echo "Error uploading the file.";
                exit();
            }
        } else {
            echo "Error: Only JPG, JPEG, PNG & GIF files are allowed.";
            exit();
        }
    }
    
    // Database Insert
    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("INSERT INTO authors (name, email, bio, interest, profile_picture) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $bio, $interest, $profile_picture);
        
        if ($stmt->execute()) {
            header("Location: index.php?success=author_added");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Please fill in all required fields.";
    }
}
$conn->close();
?>
