<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $news_title = $_POST['news_title'] ?? '';
    $author_name = $_POST['author_name'] ?? '';
    $news_description = $_POST['news_description'] ?? '';
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? 'Published';
    
    // Basic validation
    if (!empty($news_title) && !empty($author_name) && !empty($news_description) && !empty($category)) {
        $stmt = $conn->prepare("INSERT INTO news_items (news_title, author_name, news_description, category, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $news_title, $author_name, $news_description, $category, $status);
        
        if ($stmt->execute()) {
            // Redirect back with success
            header("Location: index.php?success=1");
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
