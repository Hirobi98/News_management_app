<?php
require_once 'db_connect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    // First, optionally retrieve the author to delete their profile picture file
    $stmt = $conn->prepare("SELECT profile_picture FROM authors WHERE id = ?");
    $stmt->execute([$id]);
    $author = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($author && !empty($author['profile_picture']) && file_exists($author['profile_picture'])) {
        unlink($author['profile_picture']);
    }
    
    $stmt = $conn->prepare("DELETE FROM authors WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        header("Location: index.php?success=author_deleted");
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
