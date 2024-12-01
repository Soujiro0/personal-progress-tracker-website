class Modal {
  constructor(modalId) {
    this.modal = document.getElementById(modalId);
  }

  open() {
    this.modal.style.display = "block";
  }

  close() {
    this.modal.style.display = "none";
  }
}

class CategoryManager {
  constructor() {
    this.categoryToDelete = null;
    this.deleteIconParentCategory = null;
  }

  openCategoryModal() {
    new Modal("categoryModal").open();
  }

  closeCategoryModal() {
    new Modal("categoryModal").close();
  }

  addCategory() {
    const categoryName = document.getElementById("new-category").value;
    if (categoryName) {
      const formData = new FormData();
      formData.append("action", "add");
      formData.append("category_name", categoryName);

      fetch("../includes/category.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          alert(data.message);
          if (data.message === "Category added successfully") {
            this.addCategoryToList(categoryName);
            this.closeCategoryModal(); // Close modal after adding the category
          }
        })
        .catch((error) => console.error("Error:", error));
    }
  }

  addCategoryToList(category) {
    const ul = document.getElementById("category-list");
    const li = document.createElement("li");
    li.innerHTML = this.createCategoryHTML(category);
    ul.appendChild(li);
  }

  createCategoryHTML(category) {
    return `
          <span onclick="filterTasks('${category}')">${category}</span>
          <div class="icons">
              <i class="fas fa-edit" onclick="categoryManager.editCategory(this, '${category}')"></i>
              <i class="fas fa-trash-alt" onclick="categoryManager.openDeleteCategoryModal('${category}')"></i>
          </div>
      `;
  }

  editCategory(icon, categoryId, categoryName) {
    const li = icon.closest("li"); // Get the <li> element
    const span = li.querySelector("span"); // Get the <span> containing the name

    // Create an input field pre-filled with the category name
    const input = document.createElement("input");
    input.type = "text";
    input.value = categoryName;
    span.replaceWith(input); // Replace the <span> with the input field

    // Replace the icons with Save (check) and Cancel (times) options
    const icons = li.querySelector(".icons");
    icons.innerHTML = `
        <i class="fas fa-check" onclick="categoryManager.saveCategory(this, '${categoryName}')"></i>
        <i class="fas fa-times" onclick="categoryManager.cancelEditCategory(this, '${categoryName}')"></i>
    `;
}


  cancelEditCategory(icon, categoryName) {
    const li = icon.closest("li");
    const input = li.querySelector("input");
    const span = document.createElement("span");
    span.textContent = category;
    span.setAttribute("onclick", `filterTasks('${category}')`);
    input.replaceWith(span);

    const icons = li.querySelector(".icons");
    icons.innerHTML = this.createIconsHTML(category);
  }

  saveCategory(icon, oldCategory) {
    const li = icon.closest("li");
    const input = li.querySelector("input");
    const newCategory = input.value;

    const formData = new FormData();
    formData.append("action", "edit");
    formData.append("category_id", oldCategory); // Assuming oldCategory is the ID
    formData.append("category_name", newCategory);

    fetch("../includes/category.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        if (data.message === "Category updated successfully") {
          const span = document.createElement("span");
          span.textContent = newCategory;
          span.setAttribute("onclick", `filterTasks('${newCategory}')`);
          input.replaceWith(span);

          const icons = li.querySelector(".icons");
          icons.innerHTML = this.createIconsHTML(newCategory);
        }
      })
      .catch((error) => console.error("Error:", error));
  }

  createIconsHTML(category) {
    return `
          <i class="fas fa-edit" onclick="categoryManager.editCategory(this, '${category}')"></i>
          <i class="fas fa-trash-alt" onclick="categoryManager.openDeleteCategoryModal('${category}')"></i>
      `;
  }

  openDeleteCategoryModal(iconElement, category) {
    this.categoryToDelete = category;
    this.deleteIconParentCategory = iconElement 
    new Modal("deleteCategoryModal").open();
  }

  closeDeleteCategoryModal() {
    this.categoryToDelete = null;

    new Modal("deleteCategoryModal").close();
  }

  confirmDeleteCategory() {
    if (this.categoryToDelete) {
      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("category_id", this.categoryToDelete);

      fetch("../includes/category.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          alert(data.message);
          if (data.message === "Category deleted successfully") {
            console.log("refreshing");
            this.refreshData(this.deleteIconParentCategory);
          }
        })
        .catch((error) => console.error("Error:", error));

      this.closeDeleteCategoryModal(); // Close the modal after confirming deletion
    }
  }

  refreshData(iconElement) {
      // Get the parent <li> of the clicked delete icon
      const listItem = iconElement.closest("li");

      // Remove the <li> from the DOM (if you're not using AJAX)
      const parentLi = iconElement.closest('li');
      if (parentLi) {
          parentLi.remove();
      }

  }

  refreshCategoryList(categories) {
    const categoriesList = document.getElementById("category-list");
    categoriesList.innerHTML = ""; // Clear the current category list

    categories.forEach((category) => {
      const li = document.createElement("li");
      li.innerHTML = this.createCategoryHTML(category.name);
      categoriesList.appendChild(li);
    });
  }

  refreshTaskList(tasks) {
    const taskBoard = document.querySelector(".task-board");
    taskBoard.innerHTML = ""; // Clear the current task board

    tasks.forEach((task) => {
      const taskDiv = document.createElement("div");
      taskDiv.className = "task";
      taskDiv.setAttribute("data-category", task.category_name);
      taskDiv.innerHTML = `
            <p>${task.task_name}</p>
            <div class="icons">
                <i class="fas fa-edit" onclick="taskManager.openEditTaskModal('${task.task_name}', '${task.category_name}', '${task.status}', '${task.date_created}', '${task.due_date}')"></i>
                <i class="fas fa-trash-alt" onclick="taskManager.deleteTask(this)"></i>
            </div>
        `;
      taskBoard.appendChild(taskDiv);
    });
  }
}

class TaskManager {
  constructor() {
    this.currentTask = null;
  }

  openTaskModal() {
    new Modal("taskModal").open();
  }

  closeTaskModal() {
    document.getElementById("task-name").value = "";
    document.getElementById("task-category").value = "";
    document.getElementById("task-status").value = "To Do";
    document.getElementById("due-date").value = "";
    new Modal("taskModal").close();
  }

  addTask() {
    const taskName = document.getElementById("task-name").value;
    const taskCategory = document.getElementById("task-category").value;
    const taskStatus = document.getElementById("task-status").value;
    const dateCreated = document.getElementById("date-created").value;
    const dueDate = document.getElementById("due-date").value;

    if (taskName && taskCategory) {
      const formData = new FormData();
      formData.append("action", "add");
      formData.append("task_name", taskName);
      formData.append("category_id", taskCategory); // Assuming category_id is the ID
      formData.append("status", taskStatus);
      formData.append("due_date", dueDate);

      fetch("includes/task.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          alert(data.message);
          if (data.message === "Task added successfully") {
            this.addTaskToBoard(
              taskName,
              taskCategory,
              taskStatus,
              dateCreated,
              dueDate
            );
            this.closeTaskModal(); // Close modal after adding the task
          }
        })
        .catch((error) => console.error("Error:", error));
    }
  }

  addTaskToBoard(taskName, taskCategory, taskStatus, dateCreated, dueDate) {
    const taskBoard = document.querySelector(
      `.task-board .column[data-status="${taskStatus}"]`
    );
    const taskDiv = document.createElement("div");
    taskDiv.className = "task";
    taskDiv.setAttribute("data-category", taskCategory);
    taskDiv.setAttribute(
      "onclick",
      `showTaskDetails('${taskName}', '${taskCategory}', '${taskStatus}', '${dateCreated}', '${dueDate}')`
    );
    taskDiv.innerHTML = `
            <p>${taskName}</p>
            <div class="icons">
                <i class="fas fa-edit" onclick="taskManager.openEditTaskModal('${taskName}', '${taskCategory}', '${taskStatus}', '${dateCreated}', '${dueDate}')"></i>
                <i class="fas fa-trash-alt" onclick="taskManager.deleteTask(this)"></i>
            </div>
        `;
    taskBoard.appendChild(taskDiv);
  }

  openEditTaskModal(taskName, taskCategory, taskStatus, dateCreated, dueDate) {
    this.currentTask = taskName; // Store the current task name for editing
    document.getElementById("edit-task-name").value = taskName;
    document.getElementById("edit-task-category").value = taskCategory;
    document.getElementById("edit-task-status").value = taskStatus;
    document.getElementById("edit-date-created").value = dateCreated;
    document.getElementById("edit-due-date").value = dueDate;
    new Modal("editTaskModal").open();
  }

  saveTask() {
    const newTaskName = document.getElementById("edit-task-name").value;
    const newTaskCategory = document.getElementById("edit-task-category").value;
    const newTaskStatus = document.getElementById("edit-task-status").value;
    const newDateCreated = document.getElementById("edit-date-created").value;
    const newDueDate = document.getElementById("edit-due-date").value;

    const formData = new FormData();
    formData.append("action", "edit");
    formData.append("task_name", newTaskName);
    formData.append("category_id", newTaskCategory); // Assuming category_id is the ID
    formData.append("status", newTaskStatus);
    formData.append("due_date", newDueDate);
    formData.append("old_task_name", this.currentTask); // Pass the old task name for identification

    fetch("includes/task.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        if (data.message === "Task updated successfully") {
          const taskDiv = document.querySelector(
            `.task:contains('${this.currentTask}')`
          );
          if (taskDiv) {
            taskDiv.setAttribute("data-category", newTaskCategory);
            taskDiv.setAttribute(
              "onclick",
              `showTaskDetails('${newTaskName}', '${newTaskCategory}', '${newTaskStatus}', '${newDateCreated}', '${newDueDate}')`
            );
            taskDiv.querySelector("p").textContent = newTaskName;
          }
          this.closeEditTaskModal(); // Close modal after saving the task
        }
      })
      .catch((error) => console.error("Error:", error));
  }

  closeEditTaskModal() {
    this.currentTask = null; // Reset current task
    new Modal("editTaskModal").close();
  }

  deleteTask(icon) {
    const taskDiv = icon.closest(".task");
    if (taskDiv) {
      const taskName = taskDiv.querySelector("p").textContent;
      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("task_name", taskName); // Assuming task_name is the identifier

      fetch("includes/task.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          alert(data.message);
          if (data.message === "Task deleted successfully") {
            taskDiv.remove(); // Remove the task from the DOM
          }
        })
        .catch((error) => console.error("Error:", error));
    }
  }
}

// Initialize managers
const categoryManager = new CategoryManager();
const taskManager = new TaskManager();

// Event listeners for modal actions
document.addEventListener("DOMContentLoaded", () => {
  document
    .getElementById("add-category")
    .addEventListener("click", () => categoryManager.openCategoryModal());
  document
    .getElementById("add-category-btn")
    .addEventListener("click", () => categoryManager.addCategory());
  document
    .getElementById("add-task")
    .addEventListener("click", () => taskManager.openTaskModal());
  document
    .getElementById("confirm-delete-category")
    .addEventListener("click", () => categoryManager.confirmDeleteCategory());
  // document
  //   .getElementById("save-task")
  //   .addEventListener("click", () => taskManager.saveTask());
});
