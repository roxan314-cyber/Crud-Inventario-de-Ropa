const addBtn = document.getElementById("addBtn");
const todoInput = document.getElementById("todoInput");
const todoList = document.getElementById("todoList");
const pagination = document.getElementById("pagination");

const todos = []; // Arreglo para almacenar las tareas
const itemsPerPage = 3;
let currentPage = 1;

function showErrorMessage(message) {
    alert(message);
}


// Agregar tarea
addBtn.addEventListener("click", () => {
    const task = todoInput.value.trim();
    if (task === "") {
        showErrorMessage("Please enter a task"); // Muestra un mensaje de error si el campo está vacío
        return;
    }

    todos.unshift(task); // Agrega la tarea al inicio del arreglo
    todoInput.value = "";
    currentPage = 1; // Reinicia a la primera página después de agregar una tarea
    renderTodos(); // Renderiza las tareas actualizadas
    renderPagination(); // Renderiza la paginación actualizada
});

function renderTodos() {
    todoList.innerHTML = "";
// Calcula el índice de inicio y fin para la paginación
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const currentTodos = todos.slice(start, end);
// Renderiza las tareas actuales
    currentTodos.forEach((task, index) => {
        const li = document.createElement("li");
        li.className = "todo-item";

        const taskText = document.createElement("span");
        taskText.className = "todo-text";
        taskText.textContent = task;

        const deleteBtn = document.createElement("button");
        editBtn.className = "edit-btn";
        editBtn.textContent = "Edit";
        editBtn.addEventListener("click", () => {
            editTask(start + index, li, taskText); // Pasa el índice global de la tarea, el elemento li y el texto de la tarea al editar
    }); 

    const deleteBtn = document.createElement("button");
    deleteBtn.className = "delete-btn";
    deleteBtn.textContent = "Delete";
    deleteBtn.addEventListener("click", () => {
        deleteTask(start + index); // Pasa el índice global de la tarea al eliminar
    });

    li.appendChild(taskText);
    li.appendChild(editBtn);
    li.appendChild(deleteBtn);
    todoList.appendChild(li);
    });
}

function renderPagination() {
    pagination.innerHTML = "";

    const totalPages = Math.ceil(todos.length / itemsPerPage);

    for (let i = 1; i <= totalPages; i++) {
        const pageBtn = document.createElement("button");
        addBtn.className = "page-btn";
        addBtn.textContent = i;
        addBtn.disabled = i === currentPage;
        addBtn.addEventListener("click", () => {
            currentPage = i;
            renderTodos();
            renderPagination();
        });

        pagination.appendChild(pageBtn);
    }
}

function editTask(index, li, taskText) {
// Crea un input para editar la tarea
    const input = document.createElement("input");
    input.type = "text";
    input.value = todos[index];
    input.className = "todo-text";

// Crea botones de guardar y eliminar
    const saveBtn = document.createElement("button");
    const deleteBtn = document.createElement("button");
    saveBtn.className = "save-btn";
    saveBtn.textContent = "Save";
    deleteBtn.className = "delete-btn";
    deleteBtn.textContent = "Delete";

// Limpia el contenido del elemento li y agrega el input y los botones
    li.innerHTML = "";
    li.appendChild(input);
    li.appendChild(saveBtn);
    li.appendChild(deleteBtn);

    saveBtn.addEventListener("click", () => {
        const updatedTask = input.value.trim();
        if (updatedTask === "") {
            todos[index] = updatedTask;
            renderTodos();
        } else {
            showErrorMessage("Task cannot be empty");
        }
    });
}

function deleteTask(index) {
    todos.splice(index, 1);
    if ((currentPage - 1) * itemsPerPage >= todos.length) {
        currentPage = Math.max(1, currentPage - 1);
    }
    renderTodos();
    renderPagination();
}

function showErrorMessage(message) {
    const errorMessage = document.querySelector(".error-message");
    errorMessage.textContent = message;
    errorMessage.style.display = "block";
    setTimeout(() => {
        errorMessage.style.display = "none";
    }, 3000);
}