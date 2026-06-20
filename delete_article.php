<?php
require_once 'db_connect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM news_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?success=article_deleted");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid ID.";
}

$conn->close();
?>
