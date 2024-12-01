<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['message' => 'Unauthorized']);
    exit();
}

// Include database connection
require '../includes/db_connection.php';

// Fetch user categories
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM tbl_categories WHERE user_id = ?");
$stmt->execute([$userId]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user tasks
$stmt = $pdo->prepare("SELECT t.*, c.name AS category_name FROM tbl_tasks t JOIN tbl_categories c ON t.category_id = c.id WHERE t.user_id = ?");
$stmt->execute([$userId]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the data as JSON
json_encode(['categories' => $categories, 'tasks' => $tasks]);
?>