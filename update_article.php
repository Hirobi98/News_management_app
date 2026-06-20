<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $news_title = $_POST['news_title'] ?? '';
    $author_name = $_POST['author_name'] ?? '';
    $news_description = $_POST['news_description'] ?? '';
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? 'Published';
    
    if (!empty($id) && is_numeric($id) && !empty($news_title) && !empty($author_name) && !empty($news_description) && !empty($category)) {
        $stmt = $conn->prepare("UPDATE news_items SET news_title=?, author_name=?, news_description=?, category=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $news_title, $author_name, $news_description, $category, $status, $id);
        
        if ($stmt->execute()) {
            header("Location: index.php?success=article_updated");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Please fill in all required fields and ensure ID is valid.";
    }
}
$conn->close();
?>
