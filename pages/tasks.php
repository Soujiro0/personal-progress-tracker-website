<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENNEN Personal Progress Tracker</title>
    <link rel="stylesheet" href="../css/task.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="../script/navbar.js" type="module" defer></script>
    <script src="../script/task.js" defer></script>
</head>

<body>
    <?php
        require '../includes/fetch_data.php';
    ?>

    <!-- Navbar -->
    <div id="navbar-container"></div> <!-- Placeholder for the navbar -->
    <div class="container">
        <div class="sidebar">
            <div>
                <h2>Categories</h2>
                <ul id="category-list">
                    <?php foreach ($categories as $category): ?>
                        <li onclick="filterTasks('<?php echo htmlspecialchars($category['name']); ?>')">
                            <span><?php echo htmlspecialchars($category['name']); ?></span>
                            <div class="icons">
                                <i class="fas fa-edit" onclick="categoryManager.editCategory(this, '<?php echo htmlspecialchars($category['id']); ?>', '<?php echo htmlspecialchars($category['name']); ?>')"></i>
                                <i class="fas fa-trash-alt" onclick="categoryManager.openDeleteCategoryModal(this, '<?php echo htmlspecialchars($category['id']); ?>', '<?php echo htmlspecialchars($category['name']); ?>', this)"></i>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="add-category" id="add-category">Add Category</div>
        </div>
        <div class="main-content">
            <div class="add-task" id="add-task" onclick="taskManager.openAddTaskModal()">Add New Task</div>
            <div class="task-board">
                <div class="column" data-status="To Do">
                    <h2>To Do</h2>
                    <div class="task-list" id="todo-tasks">
                        <?php foreach ($tasks as $task): ?>
                            <?php if ($task['status'] === 'to_do'): ?>
                                <div class="task" onclick="taskManager.openTaskDetails(<?php echo $task['id']; ?>)">
                                    <p><?php echo htmlspecialchars($task['task_name']); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="column" data-status="In Progress">
                    <h2>In Progress </h2>
                    <div class="task-list" id="in-progress-tasks">
                        <?php foreach ($tasks as $task): ?>
                            <?php if ($task['status'] === 'in_progress'): ?>
                                <div class="task" onclick="taskManager.openTaskDetails(<?php echo $task['id']; ?>)">
                                    <p><?php echo htmlspecialchars($task['task_name']); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="column" data-status="Done">
                    <h2>Done</h2>
                    <div class="task-list" id="done-tasks">
                        <?php foreach ($tasks as $task): ?>
                            <?php if ($task['status'] === 'completed'): ?>
                                <div class="task" onclick="taskManager.openTaskDetails(<?php echo $task['id']; ?>)">
                                    <p><?php echo htmlspecialchars($task['task_name']); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="task-details" id="task-details">
                <h2>Task Details</h2>
                <p id="task-name-detail">Task Name: <span id="task-name"></span></p>
                <p id="task-category-detail">Category: <span id="task-category"></span></p>
                <p id="task-status-detail">Status: <span id="task-status"></span></p>
                <p id="task-date-created-detail">Date Created: <span id="task-date-created"></span></p>
                <p id="task-date-updated-detail">Date Updated: <span id="task-date-updated"></span></p>
                <p id="task-due-date-detail">Due Date: <span id="task-due-date"></span></p>
                <div class="edit-task" id="edit-task" onclick="taskManager.openEditTaskModal()">Edit Task</div>
            </div>
        </div>

        <!-- Category Modal -->
        <div id="categoryModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="categoryManager.closeCategoryModal()">&times;</span>
                <h2>Add New Category</h2>
                <input type="text" id="new-category" placeholder="Category Name">
                <button id="add-category-btn">Add Category</button>
            </div>
        </div>

        <!-- Task Modal -->
        <div id="taskModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="taskManager.closeTaskModal()">&times;</span>
                <h2>Add New Task</h2>
                <input type="text" id="task-name" placeholder="Task Name">
                <select id="task-category">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="task-status">
                    <option value="to_do">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Done</option>
                </select>
                <input type="date" id="due-date" placeholder="Due Date">
                <button id="add-task-btn">Add Task</button>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteCategoryModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="categoryManager.closeDeleteCategoryModal()">&times;</span>
                <h2>Delete Category</h2>
                <p id="delete-category-message">Are you sure you want to delete this category? This action cannot be undone.</p>
                <input type="hidden" id="delete-category-id">
                <button id="confirm-delete-category">Delete</button>
                <button id="cancel-delete-category" onclick="categoryManager.closeDeleteCategoryModal()">Cancel</button>
            </div>
        </div>
    </div>
</body>

</html>