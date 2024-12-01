<?php
    require 'db_connection.php';

    function addTask($userId, $categoryId, $taskName, $status, $dueDate) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, category_id, task_name, status, due_date) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $categoryId, $taskName, $status, $dueDate]);
    }

    // Add other CRUD functions for tasks as needed...
?>