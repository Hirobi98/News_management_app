<?php
require_once 'db_connect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM news_items WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        header("Location: index.php?success=article_deleted");
        exit();
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "Error deleting record: " . $errorInfo[2];
    }
    $stmt = null;
} else {
    echo "Invalid ID.";
}

$conn = null;
?>
