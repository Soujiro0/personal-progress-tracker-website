<?php
    session_start();
    require 'db_connection.php';

    function addCategory($userId, $categoryName) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO tbl_categories (user_id, name) VALUES (?, ?)");
        return $stmt->execute([$userId, $categoryName]);
    }

    function editCategory($userId, $categoryId, $categoryName) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE tbl_categories SET name = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$categoryName, $categoryId, $userId]);
    }

    function deleteCategory($userId, $categoryId) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM tbl_categories WHERE id = ? AND user_id = ?");
        return $stmt->execute([$categoryId, $userId]);
    }

    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['message' => 'Unauthorized']);
        exit();
    }

    $userId = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $categoryName = $_POST['category_name'] ?? '';
            if ($categoryName) {
                if (addCategory($userId, $categoryName)) {
                    echo json_encode(['message' => 'Category added successfully']);
                } else {
                    echo json_encode(['message' => 'Failed to add category']);
                }
            } else {
                echo json_encode(['message' => 'Category name is required']);
            }
            break;

        case 'edit':
            $categoryId = $_POST['category_id'] ?? '';
            $categoryName = $_POST['category_name'] ?? '';
            if ($categoryId && $categoryName) {
                if (editCategory($userId, $categoryId, $categoryName)) {
                    echo json_encode(['message' => 'Category updated successfully']);
                } else {
                    echo json_encode(['message' => 'Failed to update category']);
                }
            } else {
                echo json_encode(['message' => 'Category ID and name are required']);
            }
            break;

        case 'delete':
            $categoryId = $_POST['category_id'] ?? '';
            if ($categoryId) {
                if (deleteCategory($userId, $categoryId)) {
                    echo json_encode(['message' => 'Category deleted successfully']);
                } else {
                    echo json_encode(['message' => 'Failed to delete category']);
                }
            } else {
                echo json_encode(['message' => 'Category ID is required']);
            }
            break;

        default:
            echo json_encode(['message' => 'Invalid action']);
            break;
    }
?>