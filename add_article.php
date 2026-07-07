<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $news_title = $_POST['news_title'] ?? '';
    $author_name = $_POST['author_name'] ?? '';
    $news_description = $_POST['news_description'] ?? '';
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? 'Published';
    
    // Image upload logic
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/articles/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_file_name = uniqid('article_', true) . '.' . $file_extension;
        $target_file = $target_dir . $new_file_name;
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            } else {
                echo "Error uploading the file.";
                exit();
            }
        } else {
            echo "Error: Only JPG, JPEG, PNG & GIF files are allowed.";
            exit();
        }
    }

    // Basic validation
    if (!empty($news_title) && !empty($author_name) && !empty($news_description) && !empty($category)) {
        $stmt = $conn->prepare("INSERT INTO news_items (news_title, author_name, news_description, category, status, image, \"date\") VALUES (?, ?, ?, ?, ?, ?, SYSDATE)");
        
        if ($stmt->execute([$news_title, $author_name, $news_description, $category, $status, $image_path])) {
            // Redirect back with success
            header("Location: index.php?success=1");
            exit();
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Error: " . $errorInfo[2];
        }
        $stmt = null;
    } else {
        echo "Please fill in all required fields.";
    }
}
$conn = null;
?>
