<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENNEN - Personal Progress Tracker Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <script src="../script/navbar.js" type="module" defer></script>
</head>

<body>
    <?php
    session_start();
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: landing.html'); // Redirect to landing page if not logged in
        exit();
    }

    // Include database connection
    require '../includes/db_connection.php';
    
    // Fetch user information
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM tbl_users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    // Fetch user categories
    $stmt = $pdo->prepare("SELECT * FROM tbl_categories WHERE user_id = ?");
    $stmt->execute([$userId]);
    $categories = $stmt->fetchAll();

    // Fetch user tasks categorized by category
    $tasksByCategory = [];
    foreach ($categories as $category) {
        $stmt = $pdo->prepare("SELECT * FROM tbl_tasks WHERE user_id = ? AND category_id = ?");
        $stmt->execute([$userId, $category['id']]);
        $tasksByCategory[$category['name']] = $stmt->fetchAll();
    }
    ?>

    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <div id="navbar-container"></div> <!-- Placeholder for the navbar -->
        <!-- Main Content -->
        <div class="flex flex-1">
            <!-- Dashboard Content -->
            <main class="main-content flex-1">
                <div class="mb-6">
                    <h1 class="dashboard-title">Dashboard</h1>
                </div>
                <!-- Progress Cards Categories Here-->
                <div class="mb-6">
                    <h1 class="category-title">Categories</h1>
                </div>
                <div class="grid-container">
                    <?php if (empty($categories)): ?>
                        <p>No Categories yet. Go to Task and Add some cateogory</p>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="card">
                                <h2 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h2>
                                <div class="relative pt-1">
                                    <?php
                                    // Calculate progress
                                    $totalTasks = count($tasksByCategory[$category['name']]);
                                    $completedTasks = count(array_filter($tasksByCategory[$category['name']], function ($task) {
                                        return $task['status'] === 'completed'; // Adjusted to match ENUM value
                                    }));
                                    $progressPercentage = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                                    ?>
                                    <div class="progress-bar">
                                        <div class="progress" style="width:<?php echo $progressPercentage; ?>%; background-color: #4299e1;"><?php echo round($progressPercentage) . '%'; ?></div>
                                    </div>
                                </div>
                                <div class="text-gray">To Do: <?php echo max(0, $totalTasks - $completedTasks); ?>%</div>
                                <div class="text-gray">In Progress: <?php echo count(array_filter($tasksByCategory[$category['name']], function ($task) {
                                                                        return $task['status'] === 'in_progress'; // Adjusted to match ENUM value
                                                                    })); ?>%</div>
                                <div class="text-gray">Done: <?php echo $completedTasks; ?>%</div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <!-- <div class="add-category" id="add-category">Add Category</div> -->
                </div>
                <!-- Upcoming Tasks -->
                <div class="upcoming-tasks">
                    <h2 class="upcoming-title">Upcoming Tasks</h2>
                    <?php if (!empty($tasksByCategory)): ?>
                        <?php foreach ($tasksByCategory as $categoryTasks): ?>
                            <?php foreach ($categoryTasks as $task): ?>
                                <?php if ($task['due_date'] > date('Y-m-d')): ?>
                                    <div class="task">
                                        <div class="flex items-center">
                                            <img alt="Task icon" height="50" src="https://storage.googleapis.com/a1aa/image/5Um4jeBKpV0rRyNqTMxYLfAPZtmalxvGz6XjYrrrfebUobXPB.jpg" width="50" />
                                            <div>
                                                <h3 class="task-title"><?php echo htmlspecialchars($task['task_name']); ?></h3>
                                                <p class="task-category"><?php echo htmlspecialchars($task['category_id']); // You may want to fetch the category name instead 
                                                                            ?></p>
                                            </div>
                                        </div>
                                        <div class="due-date">Due: <?php echo htmlspecialchars($task['due_date']); ?></div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No upcoming tasks found.</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>

</html>