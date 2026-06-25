<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<div class="container scrolling-wrapper">
    <div class="header">
        <div>
            <div class="header-breadcrumb" style="margin-bottom: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">
                <a href="template-controller.php">Task Templates</a> <span style="margin: 0 5px;">/</span> <strong>Create New</strong>
            </div>
            <h1 class="title">Create Template</h1>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="card" style="background: rgba(255, 59, 48, 0.1); border-color: rgba(255, 59, 48, 0.3); color: #dc2626; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="template-controller.php?action=create" method="POST" id="templateForm">
        
        <div class="card">
            <h3 class="card-title">Material Information</h3>
            <div class="details-grid" style="grid-template-columns: 1fr 1fr;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: var(--text-muted);">Material Category</label>
                    <select name="material_category" id="materialCategorySelect" class="search-input" required style="width: 100%; height: 45px;">
                        <option value="" disabled selected>Select or Add Category</option>
                        <?php foreach ($materialCategories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $cat))) ?></option>
                        <?php endforeach; ?>
                        <option value="add_new" style="font-weight: bold; color: var(--primary);">+ (Add New Category)</option>
                    </select>
                    <input type="text" name="new_material_category" id="newMaterialCategoryInput" class="search-input" placeholder="Type new category..." style="width: 100%; height: 45px; margin-top: 8px; display: none;">
                </div>
                
                <div class="form-group" style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: var(--text-muted);">Material Name</label>
                    <input type="text" name="material_name" class="search-input" placeholder="e.g., 3000 PSI Ready Mix" required style="width: 100%; height: 45px;">
                </div>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 class="card-title" style="margin-bottom: 0;">Task Sequence</h3>
                <button type="button" class="btn-primary" id="addTaskBtn" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                    <i class="ph ph-plus"></i> Add Task
                </button>
            </div>

            <div class="table-responsive" style="overflow: visible;">
                <table id="tasksTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Order</th>
                            <th>Task Title</th>
                            <th>Task Category</th>
                            <th style="width: 120px;">Days Offset</th>
                            <th style="width: 120px;">Weight (%)</th>
                            <th style="width: 60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="taskTableBody">
                        <tr class="task-row">
                            <td class="task-order" style="font-weight: 700; color: var(--text-muted); text-align: center;">1</td>
                            <td>
                                <input type="text" name="task_titles[]" class="search-input" placeholder="Task Title" required style="width: 100%; height: 38px;">
                            </td>
                            <td>
                                <select name="task_categories[]" class="search-input task-cat-select" required style="width: 100%; height: 38px;">
                                    <option value="" disabled selected>Select Category</option>
                                    <?php foreach ($taskCategories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $cat))) ?></option>
                                    <?php endforeach; ?>
                                    <option value="add_new" style="font-weight: bold; color: var(--primary);">+ (Add New Category)</option>
                                </select>
                                <input type="text" name="new_task_categories[]" class="search-input new-task-cat-input" placeholder="New category..." style="width: 100%; height: 38px; margin-top: 6px; display: none;">
                            </td>
                            <td>
                                <input type="number" name="task_days[]" class="search-input" value="0" required style="width: 100%; height: 38px;">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="task_weights[]" class="search-input weight-input" value="0.00" required style="width: 100%; height: 38px;">
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="action-btn active-attention remove-task" style="padding: 6px; font-size: 1.1rem; border-radius: 8px;">
                                    <i class="ph ph-trash"></i>
                                </button>
                                <input type="hidden" name="task_orders[]" class="hidden-order" value="1">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 1rem; text-align: right; font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">
                Total Weight: <span id="totalWeightDisplay" style="color: var(--primary);">0.00%</span>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-bottom: 3rem;">
            <a href="template-controller.php" class="btn-toggle active" style="text-decoration: none; padding: 0.7rem 1.5rem; height: auto;">Cancel</a>
            <button type="submit" class="btn-primary" style="padding: 0.7rem 2rem;">Save Template</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('taskTableBody');
    const addTaskBtn = document.getElementById('addTaskBtn');
    const totalWeightDisplay = document.getElementById('totalWeightDisplay');
    
    // 1. Parent Material Category "Add New" Toggle
    const matSelect = document.getElementById('materialCategorySelect');
    const newMatInput = document.getElementById('newMaterialCategoryInput');
    
    matSelect.addEventListener('change', function() {
        if (this.value === 'add_new') {
            newMatInput.style.display = 'block';
            newMatInput.required = true;
        } else {
            newMatInput.style.display = 'none';
            newMatInput.required = false;
            newMatInput.value = '';
        }
    });

    // 2. Safely pass PHP Task Categories to JS for dynamic row generation
    const jsTaskCategories = <?= json_encode($taskCategories); ?>;
    let taskOptionsHTML = '<option value="" disabled selected>Select Category</option>';
    jsTaskCategories.forEach(cat => {
        // Basic title case formatting
        let formattedCat = cat.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        taskOptionsHTML += `<option value="${cat}">${formattedCat}</option>`;
    });
    taskOptionsHTML += `<option value="add_new" style="font-weight: bold; color: var(--primary);">+ (Add New Category)</option>`;

    // 3. Add new Task Row
    addTaskBtn.addEventListener('click', () => {
        const rowCount = tableBody.querySelectorAll('.task-row').length + 1;
        
        const rowHTML = `
            <tr class="task-row">
                <td class="task-order" style="font-weight: 700; color: var(--text-muted); text-align: center;">${rowCount}</td>
                <td><input type="text" name="task_titles[]" class="search-input" placeholder="Task Title" required style="width: 100%; height: 38px;"></td>
                <td>
                    <select name="task_categories[]" class="search-input task-cat-select" required style="width: 100%; height: 38px;">
                        ${taskOptionsHTML}
                    </select>
                    <input type="text" name="new_task_categories[]" class="search-input new-task-cat-input" placeholder="New category..." style="width: 100%; height: 38px; margin-top: 6px; display: none;">
                </td>
                <td><input type="number" name="task_days[]" class="search-input" value="0" required style="width: 100%; height: 38px;"></td>
                <td><input type="number" step="0.01" name="task_weights[]" class="search-input weight-input" value="0.00" required style="width: 100%; height: 38px;"></td>
                <td style="text-align: center;">
                    <button type="button" class="action-btn active-attention remove-task" style="padding: 6px; font-size: 1.1rem; border-radius: 8px;">
                        <i class="ph ph-trash"></i>
                    </button>
                    <input type="hidden" name="task_orders[]" class="hidden-order" value="${rowCount}">
                </td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', rowHTML);
        updateSortOrders();
        calculateWeight();
    });

    // 4. Task Category "Add New" Toggle (Event Delegation)
    tableBody.addEventListener('change', (e) => {
        if (e.target.classList.contains('task-cat-select')) {
            const textInput = e.target.nextElementSibling;
            if (e.target.value === 'add_new') {
                textInput.style.display = 'block';
                textInput.required = true;
            } else {
                textInput.style.display = 'none';
                textInput.required = false;
                textInput.value = '';
            }
        }
    });

    // 5. Remove Task Row
    tableBody.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('.remove-task');
        if (removeBtn) {
            const rowCount = tableBody.querySelectorAll('.task-row').length;
            if (rowCount > 1) {
                removeBtn.closest('tr').remove();
                updateSortOrders();
                calculateWeight();
            } else {
                alert("You must have at least one task.");
            }
        }
    });

    function updateSortOrders() {
        const rows = tableBody.querySelectorAll('.task-row');
        rows.forEach((row, index) => {
            const currentOrder = index + 1;
            row.querySelector('.task-order').textContent = currentOrder;
            row.querySelector('.hidden-order').value = currentOrder;
        });
    }

    // 6. Real-time Weight Calculation
    tableBody.addEventListener('input', (e) => {
        if (e.target.classList.contains('weight-input')) {
            calculateWeight();
        }
    });

    function calculateWeight() {
        let total = 0;
        document.querySelectorAll('.weight-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        totalWeightDisplay.textContent = total.toFixed(2) + '%';
        if (total > 100) totalWeightDisplay.style.color = '#dc2626';
        else if (total === 100) totalWeightDisplay.style.color = '#34c759';
        else totalWeightDisplay.style.color = 'var(--primary)';
    }
});
</script>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>