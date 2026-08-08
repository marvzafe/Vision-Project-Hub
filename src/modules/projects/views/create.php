<?php include __DIR__ . '/../../../core/views/header.php'; ?>
<?php require_once __DIR__ . '/../../../core/avatar-service.php'; ?>
<link rel="stylesheet" href="/../assets/css/project-create.css">

<div class="container">
    <header class="header">
            <div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">
                    <a href="/src/modules/projects/project-controller.php">← Back to Dashboard</a>
                </p>
                <h1 class="title"><?= isset($isEdit) && $isEdit ? 'Edit Project' : 'Create New Project' ?></h1>
            </div>
            <div>
                <span class="badge progress"><?= isset($isEdit) ? htmlspecialchars($project['status']) : 'Draft' ?></span>
            </div>
    </header>

    <?php if (!empty($error)): ?>
        <div style="background-color: #ffebee; color: #c62828; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <strong>Error:</strong> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <?php if(isset($isEdit)): ?>
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
        <?php endif; ?>

        <div class="details-grid">
            <div class="left-col">
                <div class="card">
                    <h2 class="card-title">General Information</h2>
                    
                    <div class="form-group">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($project['name'] ?? '') ?>" placeholder="e.g., Vision HQ Extension" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Project Location</label>
                            <input type="text" name="project_location" class="form-control" value="<?= htmlspecialchars($project['project_location'] ?? '') ?>" placeholder="e.g., Pasig City, NCR" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Project Area</label>
                            <input type="text" name="project_area" class="form-control" value="<?= htmlspecialchars($project['project_area'] ?? '') ?>" placeholder="e.g., 120 sqm" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Project Description</label>
                        <textarea class="form-control" placeholder="Provide a comprehensive overview of the project scope..."></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Technical Requirements</label>
                        <textarea class="form-control" placeholder="List specific engineering, architectural, or structural requirements..."></textarea>
                    </div>
                
                                <div class="form-group" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <label class="form-label">Apply Predefined Scope (Task Template)</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <select id="scope-template-select" class="form-control">
                            <option value="">-- Select a Scope --</option>
                            <option value="vision_wood_tile">Vision Floor - Wood Tile</option>
                            <option value="concrete_foundation">Standard Concrete Foundation</option>
                        </select>
                        <button type="button" id="btn-apply-scope" class="btn btn-outline" style="white-space: nowrap;">Apply Tasks</button>
                    </div>
                    <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                        Applying a scope will automatically add predefined milestones to the timeline below.
                    </small>
                </div>

                </div>

                <div class="card" data-project-id="PRJ-001">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                        <h2 class="card-title" style="border: none; margin: 0; padding: 0;">Timeline & Milestones</h2>
                        <button type="button" class="btn btn-outline" data-modal-target="addMilestoneModal" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">+ Add Milestone</button>
                    </div>
                        
                    <div id="milestone-container">
                        
                        <div class="task-group" data-category-id="CAT-01" style="margin-bottom: 1.5rem;">
                            <h3 class="group-title">General Works</h3>
                            <ul class="file-list" id="list-general_works">
                            </ul>
                        </div>

                        <div class="task-group" data-category-id="CAT-02" style="margin-bottom: 1.5rem;">
                            <h3 class="group-title">Project's Progress</h3>
                            <ul class="file-list" id="list-project_progress">
                            </ul>
                        </div>

                        <div class="task-group" data-category-id="CAT-03" style="margin-bottom: 1.5rem;">
                            <h3 class="group-title">Finishing Works</h3>
                            <ul class="file-list" id="list-finishing_works">
                            </ul>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="right-col">
                
                <div class="card">
                    <h2 class="card-title">Project Media & Files</h2>
                    
                    <div class="form-group">
    <label class="form-label">Project Cover Picture</label>
    
<div class="upload-area" id="cover-upload-trigger" data-modal-target="uploadCoverModal" <?= !empty($project['cover_photo_url']) ? 'style="display: none;"' : '' ?>>
    <div class="upload-icon">📸</div>
    <p style="font-weight: 600; margin-bottom: 0.25rem;">Click or Drag to upload image</p>
    <p style="font-size: 0.8rem; color: var(--text-muted);">JPG, PNG up to 5MB</p>
    <input type="file" name="cover_photo" id="main_cover_photo" style="display: none;" accept="image/*">
</div>

<div id="main_cover_preview_wrapper" style="<?= !empty($project['cover_photo_url']) ? 'display: block;' : 'display: none;' ?> position: relative; margin-top: 0.5rem;">
    <img id="main_cover_preview" src="<?= !empty($project['cover_photo_url']) ? htmlspecialchars($project['cover_photo_url']) : '' ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
    <button type="button" id="btn-change-cover" class="btn btn-outline" style="position: absolute; bottom: 10px; right: 10px; background-color: var(--surface-color);">Change Photo</button>
</div>
</div>
                </div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
        <h2 class="card-title" style="border: none; margin: 0; padding: 0;">Project Team</h2>
        <button type="button" class="btn btn-outline" data-modal-target="addMemberModal" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">+ Add Member</button>
    </div>

    <ul class="people-list" id="project-team-list" style="margin-top: 1rem;">
    <?php if (isset($isEdit) && $isEdit && !empty($teamMembers)): ?>
        <?php foreach ($teamMembers as $member): 
            $isLead = !empty($member['is_lead']);
            $isDepartment = !empty($member['department_id']) && empty($member['user_id']);
            $role = htmlspecialchars($member['project_role']);
        ?>
            <li class="person" style="gap: 1rem;">
                <?php if ($isLead): ?>
                    <?php $fullName = htmlspecialchars(trim($member['first_name'] . ' ' . $member['last_name'])); ?>
                    
                    <!-- Render User Avatar -->
                    <?= AvatarService::renderAvatar($member['avatar_url'] ?? null, $member['first_name'] ?? '', $member['last_name'] ?? '', '42px', $member['user_id']) ?>
                    
                    <div class="person-info">
                        <div class="lead-wrapper">
                            <span class="status-dot active"></span>
                            <h4><?= $fullName ?></h4>
                        </div>
                        <p>Project Lead</p>
                        <input type="hidden" name="project_lead_id" value="<?= htmlspecialchars($member['user_id']) ?>">
                    </div>

                <?php elseif ($isDepartment): ?>
                    
                    <!-- Render Department Icon -->
                    <?= AvatarService::renderDepartmentIcon($member['department_name'] ?? null, '42px', $member['department_id']) ?>
                    
                    <div class="person-info">
                        <h4><?= htmlspecialchars($member['department_name']) ?> (Dept)</h4>
                        <p><?= $role ?></p>
                        <input type="hidden" name="team_department_ids[]" value="<?= htmlspecialchars($member['department_id']) ?>">
                        <input type="hidden" name="team_department_roles[]" value="<?= $role ?>">
                    </div>

                <?php else: ?>
                    <?php $fullName = htmlspecialchars(trim($member['first_name'] . ' ' . $member['last_name'])); ?>
                    
                    <!-- Render User Avatar -->
                    <?= AvatarService::renderAvatar($member['avatar_url'] ?? null, $member['first_name'] ?? '', $member['last_name'] ?? '', '42px', $member['user_id']) ?>
                    
                    <div class="person-info">
                        <h4><?= $fullName ?></h4>
                        <p><?= $role ?></p>
                        <input type="hidden" name="team_user_ids[]" value="<?= htmlspecialchars($member['user_id']) ?>">
                        <input type="hidden" name="team_roles[]" value="<?= $role ?>">
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; padding: 1rem 0;">No team assigned yet.</p>
    <?php endif; ?>
</ul>
</div>
                </div>
            </div>
            
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-outline" onclick="window.location.href='/'">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Project</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. PREVENT PAST DATES
    // ==========================================
    const msDateInput = document.getElementById('ms_date');
    if (msDateInput) {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        msDateInput.min = now.toISOString().split('T')[0];
    }

    // ==========================================
    // 2. UNIVERSAL DELETE LISTENER
    // ==========================================
    const milestoneContainer = document.getElementById('milestone-container');
    if (milestoneContainer) {
        milestoneContainer.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.delete-milestone-btn');
            if (deleteBtn) {
                deleteBtn.closest('.file-item').remove(); 
            }
        });
    }

    // ==========================================
    // 3. DYNAMIC SCOPE TEMPLATES (SUPABASE)
    // ==========================================
    let scopeTemplates = {}; 

    async function loadTemplatesFromDB() {
        try {
                    const response = await fetch('/src/modules/projects/project-controller.php?action=get_templates');
                    
                    // --- NEW DEBUGGING LOGIC ---
                    if (!response.ok) {
                        const errorData = await response.text();
                        throw new Error(`Server returned ${response.status}: ${errorData}`);
                    }
                    // ---------------------------
                    
                    scopeTemplates = await response.json();
            
            const scopeSelect = document.getElementById('scope-template-select');
            if (scopeSelect) {
                scopeSelect.innerHTML = '<option value="">-- Select a Scope --</option>'; 
                
                // 1. Group the materials by their category
                const groupedCategories = {};
                for (const slug in scopeTemplates) {
                    const categoryName = scopeTemplates[slug].material_category;
                    if (!groupedCategories[categoryName]) {
                        groupedCategories[categoryName] = [];
                    }
                    groupedCategories[categoryName].push({
                        slug: slug,
                        name: scopeTemplates[slug].name
                    });
                }

                // 2. Loop through the grouped categories and build <optgroup> tags
                for (const categoryName in groupedCategories) {
                    const optGroup = document.createElement('optgroup');
                    optGroup.label = `-- ${categoryName} --`; // Looks like: -- Vision Care --
                    
                    // Add the materials as <option> tags inside the group
                    groupedCategories[categoryName].forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.slug;
                        opt.textContent = item.name;
                        optGroup.appendChild(opt);
                    });
                    
                    scopeSelect.appendChild(optGroup);
                }
            }
        } catch (error) {
            console.error("Error fetching templates:", error);
            const scopeSelect = document.getElementById('scope-template-select');
            if (scopeSelect) scopeSelect.innerHTML = '<option value="">-- Error loading templates --</option>';
        }
    }

    loadTemplatesFromDB();

    // ==========================================
    // 4. REUSABLE TASK INJECTOR
    // ==========================================
    function addMilestoneToDOM(title, category, assignee, deadlineISO, taskId = '', weight = 0) {
        let dateText = 'No deadline set';
        
        if (deadlineISO) {
            const dateObj = new Date(deadlineISO);
            const now = new Date();
            
            const datePart = dateObj.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            const dayPart = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
            let timePart = dateObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }).replace(' ', ''); 
            
            const diffMs = dateObj - now;
            let remainingText = '';

            if (diffMs > 0) {
                const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                const diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                
                if (diffDays > 0) {
                    remainingText = `(${diffDays} day${diffDays > 1 ? 's' : ''} remaining)`;
                } else if (diffHours > 0) {
                    remainingText = `(${diffHours} hour${diffHours > 1 ? 's' : ''} remaining)`;
                } else {
                    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                    remainingText = `(${diffMinutes} min remaining)`;
                }
            } else {
                remainingText = `(Due now)`;
            }

            dateText = `Due: ${datePart}, ${dayPart} ${timePart} <span style="color: var(--status-progress); font-weight: 500;">${remainingText}</span>`;
        }

        const newLi = document.createElement('li');
        newLi.className = 'file-item';
        newLi.style.justifyContent = 'space-between';

        // NEW: Make the task draggable and add state classes
        newLi.draggable = true;
        newLi.addEventListener('dragstart', () => {
            newLi.classList.add('dragging');
            newLi.style.opacity = '0.5';
            newLi.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
        newLi.addEventListener('dragend', () => {
            newLi.classList.remove('dragging');
            newLi.style.opacity = '1';
            newLi.style.boxShadow = 'none';
        });

        const finalDeadline = deadlineISO || '';

        newLi.innerHTML = `
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="cursor: grab; color: var(--text-muted); font-size: 1.2rem; user-select: none;">⋮⋮</span>
                <div>
                    <span style="font-weight: 600; display: block; color: var(--text-main);">${title}</span>
                    <span style="font-size: 0.8rem; color: var(--text-muted);">${dateText}</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button type="button" class="btn-icon delete-milestone-btn" title="Delete Milestone" style="padding: 0.4rem 0.6rem;">✖</button>
            </div>
            
            <input type="hidden" name="task_ids[]" value="${taskId}">
            <input type="hidden" name="task_titles[]" value="${title}">
            <input type="hidden" name="task_categories[]" value="${category}">
            <input type="hidden" name="task_assignees[]" value="${assignee}">
            <input type="hidden" name="task_deadlines[]" value="${finalDeadline}">
            <input type="hidden" name="task_weights[]" value="${weight}"> 
        `;

        const targetList = document.getElementById('list-' + category);
        if (targetList) {
            targetList.appendChild(newLi);
        }
    }

    // ==========================================
    // 4.5. PRE-LOAD EXISTING TASKS FOR EDIT MODE
    // ==========================================
    <?php if (isset($isEdit) && $isEdit && !empty($groupedTasks)): ?>
        <?php foreach ($groupedTasks as $category => $tasks): ?>
            <?php foreach ($tasks as $task): ?>
                addMilestoneToDOM(
                    <?= json_encode($task['title']) ?>,
                    <?= json_encode($task['task_category']) ?>,
                    <?= json_encode($task['assignee_id'] ?? '') ?>,
                    <?= json_encode(!empty($task['deadline']) ? date('Y-m-d\TH:i', strtotime($task['deadline'])) : '') ?>,
                    <?= json_encode($task['id']) ?>,
                    <?= json_encode($task['weight'] ?? 0) ?>
                );
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    // ==========================================
    // 5. APPLY PREDEFINED SCOPE LOGIC
    // ==========================================
    const btnApplyScope = document.getElementById('btn-apply-scope');
    const scopeSelect = document.getElementById('scope-template-select');

    if (btnApplyScope && scopeSelect) {
        btnApplyScope.addEventListener('click', () => {
            const selectedScope = scopeSelect.value;
            
            if (!selectedScope || !scopeTemplates[selectedScope]) {
                alert("Please select a valid scope first.");
                return;
            }

            const tasksToApply = scopeTemplates[selectedScope].tasks;
            
            if (!tasksToApply || tasksToApply.length === 0) {
                alert("This scope has no tasks assigned to it yet.");
                return;
            }

            tasksToApply.forEach(task => {
                const deadlineDate = new Date();
                deadlineDate.setDate(deadlineDate.getDate() + task.daysOffset);
                deadlineDate.setHours(12, 0, 0, 0); 
                
                const offsetMs = deadlineDate.getTime() - (deadlineDate.getTimezoneOffset() * 60000);
                const isoDeadline = new Date(offsetMs).toISOString().slice(0, 16);

                addMilestoneToDOM(task.title, task.category, '', isoDeadline, '', task.weight);
            });

            alert(`Successfully added ${tasksToApply.length} tasks from template!`);
            scopeSelect.value = ''; 
        });
    }

    // ==========================================
    // 6. INTERCEPT MILESTONE MODAL SUBMISSION
    // ==========================================
    const milestoneForm = document.getElementById('custom-milestone-form');

    if (milestoneForm) {
        milestoneForm.addEventListener('submit', (e) => {
            e.preventDefault(); 
            
            try {
                const title = document.getElementById('ms_title')?.value || 'Untitled';
                const category = document.getElementById('ms_category')?.value || 'general_works';
                const assignee = document.querySelector('[name="assignee_id"]')?.value || '';
                
                const dDate = document.getElementById('ms_date')?.value || '';
                const dHour = document.getElementById('ms_time_hour')?.value || '12';
                const dMin = document.getElementById('ms_time_min')?.value || '00';
                const dAmPm = document.getElementById('ms_time_ampm')?.value || 'AM';
                const hiddenDeadline = document.getElementById('ms_deadline');

                if (hiddenDeadline && dDate) {
                    let h = parseInt(dHour, 10);
                    if (dAmPm === 'PM' && h !== 12) h += 12;
                    if (dAmPm === 'AM' && h === 12) h = 0;
                    const dbHour = h.toString().padStart(2, '0');
                    hiddenDeadline.value = `${dDate}T${dbHour}:${dMin}`;
                }

                addMilestoneToDOM(title, category, assignee, hiddenDeadline?.value);

                milestoneForm.reset();
                
                // Programmatically close modal after successful insertion
                const modalWrapper = milestoneForm.closest('.modal-overlay');
                if (modalWrapper) modalWrapper.classList.remove('active');

            } catch (error) {
                console.error(error);
                alert("Javascript crashed! Error: " + error.message);
            }
        });
    }

    // ==========================================
    // 6.5. PRELOAD EXISTING TEAM INTO MODAL
    // ==========================================
    <?php if (isset($isEdit) && $isEdit && !empty($teamMembers)): ?>
        const teamListContainer = document.getElementById('dynamic-members-list');
        if (teamListContainer) {
            teamListContainer.innerHTML = ''; // Clear default blank row
            
            <?php foreach ($teamMembers as $i => $member): 
                $isLead = !empty($member['is_lead']);
                $isDept = !empty($member['department_id']) && empty($member['user_id']);
                
                // Add prefixes so the modal's search hidden inputs function correctly
                $id = $isDept ? 'dept_' . $member['department_id'] : 'user_' . $member['user_id'];
                $name = $isDept ? $member['department_name'] : trim($member['first_name'] . ' ' . $member['last_name']);
                $role = $member['project_role'];
            ?>
                <?php if ($isLead): ?>
                    { 
                        const leadHidden = document.getElementById('modal_project_lead_id');
                        const leadSearch = document.querySelector('[data-hidden-input="modal_project_lead_id"]');
                        if (leadHidden) leadHidden.value = <?= json_encode($id) ?>;
                        if (leadSearch) leadSearch.value = <?= json_encode($name) ?>;
                    }
                <?php else: ?>
                    {
                        const uniqueId = 'pre_' + <?= $i ?>;
                        const nameStr = <?= json_encode($name) ?>;
                        const roleStr = <?= json_encode($role) ?>;
                        const idStr = <?= json_encode($id) ?>;
                        
                        const rowHtml = `
                            <div class="member-row" style="display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 0.75rem; padding: 1rem; background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: all 0.2s ease;">

                                <div class="form-group" style="margin-bottom: 0; flex: 1.5;">
                                    <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;">Search User or Department</label>
                                    
                                    <div class="search-wrapper" style="position: relative; width: 100%;">
                                        <i class="ph ph-magnifying-glass search-icon-static"></i>
                                        <input type="text" 
                                            class="form-control global-search-input team-user-search" 
                                            placeholder="Type a name or department..." 
                                            autocomplete="off"
                                            data-search-table="entities" 
                                            data-results-container="team-results-${uniqueId}" 
                                            data-hidden-input="team_hidden_${uniqueId}"
                                            value="${nameStr.replace(/"/g, '&quot;')}"
                                            style="background: var(--bg-color);">
                                            
                                        <input type="hidden" id="team_hidden_${uniqueId}" class="team-hidden-input" value="${idStr}">
                                        <div id="team-results-${uniqueId}" class="search-results-dropdown"></div>
                                    </div>
                                </div>
                                
                                <div class="form-group" style="margin-bottom: 0; flex: 1;">
                                    <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;">Assigned Role</label>
                                    <input type="text" class="form-control team-role-input" placeholder="e.g., Lead Engineer" value="${roleStr.replace(/"/g, '&quot;')}" style="background: var(--bg-color);">
                                </div>
                                
                                <button type="button" class="btn-icon remove-row-btn" title="Remove Member" style="margin-bottom: 2px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,59,48,0.05); color: var(--status-attention); border: 1px solid rgba(255,59,48,0.1); border-radius: 10px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,59,48,0.1)';" onmouseout="this.style.background='rgba(255,59,48,0.05)';">
                                    <i class="ph ph-trash" style="font-size: 1.2rem;"></i>
                                </button>

                            </div>
                        `;
                        teamListContainer.insertAdjacentHTML('beforeend', rowHtml);
                    }
                <?php endif; ?>
            <?php endforeach; ?>
            
            // Add a blank row if no normal team members existed
            if (teamListContainer.children.length === 0) {
                const emptyId = 'empty_init';
                teamListContainer.innerHTML = `
                    <div class="member-row" style="display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 0.75rem; padding: 1rem; background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: all 0.2s ease;">

                        <div class="form-group" style="margin-bottom: 0; flex: 1.5;">
                            <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;">Search User or Department</label>
                            
                            <div class="search-wrapper" style="position: relative; width: 100%;">
                                <i class="ph ph-magnifying-glass search-icon-static"></i>
                                <input type="text" 
                                    class="form-control global-search-input team-user-search" 
                                    placeholder="Type a name or department..." 
                                    autocomplete="off"
                                    data-search-table="entities" 
                                    data-results-container="team-results-${emptyId}" 
                                    data-hidden-input="team_hidden_${emptyId}"
                                    style="background: var(--bg-color);">
                                    
                                <input type="hidden" id="team_hidden_${emptyId}" class="team-hidden-input">
                                <div id="team-results-${emptyId}" class="search-results-dropdown"></div>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0; flex: 1;">
                            <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;">Assigned Role</label>
                            <input type="text" class="form-control team-role-input" placeholder="e.g., Lead Engineer" style="background: var(--bg-color);">
                        </div>
                        
                        <button type="button" class="btn-icon remove-row-btn" title="Remove Member" style="margin-bottom: 2px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,59,48,0.05); color: var(--status-attention); border: 1px solid rgba(255,59,48,0.1); border-radius: 10px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,59,48,0.1)';" onmouseout="this.style.background='rgba(255,59,48,0.05)';">
                            <i class="ph ph-trash" style="font-size: 1.2rem;"></i>
                        </button>

                    </div>
                `;
            }
        }
    <?php endif; ?> 
    // ==========================================
    // 7. INTERCEPT TEAM MODAL SUBMISSION
    // ==========================================
    const teamForm = document.getElementById('custom-team-form');
    const teamListUI = document.getElementById('project-team-list');

    if (teamForm && teamListUI) {
        teamForm.addEventListener('submit', (e) => {
            e.preventDefault(); 
            
            try {
                teamListUI.innerHTML = '';

                // A. PROCESS PROJECT LEAD
                const leadHidden = document.getElementById('modal_project_lead_id');
                const leadSearchBox = document.querySelector('[data-hidden-input="modal_project_lead_id"]');
                
                if (leadHidden && leadHidden.value) {
                    let leadId = leadHidden.value;
                    
                    // NEW: Prevent database crashes by safely stripping the prefix 
                    if (leadId.includes('_')) {
                        leadId = leadId.split('_')[1];
                    }
                    
                    const leadName = leadSearchBox ? leadSearchBox.value.trim() : 'Unknown Lead';
                    const initials = leadName.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();

                    const leadLi = document.createElement('li');
                    leadLi.className = 'person';
                    leadLi.innerHTML = `
                        <div class="avatar">${initials}</div>
                        <div class="person-info">
                            <div class="lead-wrapper">
                                <span class="status-dot active"></span>
                                <h4>${leadName}</h4>
                            </div>
                            <p>Project Lead</p>
                        </div>
                        <input type="hidden" name="project_lead_id" value="${leadId}">
                    `;
                    teamListUI.appendChild(leadLi);
                }

                // B. PROCESS TEAM MEMBERS
                const memberRows = teamForm.querySelectorAll('.member-row');
                memberRows.forEach((row) => {
                    const hiddenInput = row.querySelector('.team-hidden-input');
                    const searchInput = row.querySelector('.team-user-search');
                    const roleInput = row.querySelector('.team-role-input');

                    if (hiddenInput && hiddenInput.value) { 
                        const rawValue = hiddenInput.value; // e.g., "user_123" or "dept_456"
                        const entityName = searchInput ? searchInput.value.trim() : 'Unknown';
                        const role = roleInput.value || 'Team Member';
                        const initials = entityName.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
                        
                        // Split the prefix and the actual ID
                        const [type, actualId] = rawValue.split('_'); 
                        
                        const memberLi = document.createElement('li');
                        memberLi.className = 'person';
                        
                        // Build the hidden inputs with the CLEAN ID (no "dept_" or "user_" prefix)
                        if (type === 'dept') {
                            memberLi.innerHTML = `
                                <div class="avatar" style="background-color: var(--primary);">${initials}</div>
                                <div class="person-info">
                                    <h4>${entityName} (Dept)</h4>
                                    <p>${role}</p>
                                </div>
                                <input type="hidden" name="team_department_ids[]" value="${actualId}">
                                <input type="hidden" name="team_department_roles[]" value="${role}">
                            `;
                        } else {
                            memberLi.innerHTML = `
                                <div class="avatar" style="background-color: var(--text-muted);">${initials}</div>
                                <div class="person-info">
                                    <h4>${entityName}</h4>
                                    <p>${role}</p>
                                </div>
                                <input type="hidden" name="team_user_ids[]" value="${actualId}">
                                <input type="hidden" name="team_roles[]" value="${role}">
                            `;
                        }
                        teamListUI.appendChild(memberLi);
                    }
                });

                // C. CLOSE MODAL
                const modalWrapper = teamForm.closest('.modal-overlay');
                if (modalWrapper) modalWrapper.classList.remove('active');

            } catch (error) {
                console.error(error);
                alert("Javascript crashed! Error: " + error.message);
            }
        });
    }

    // ==========================================
    // 8. COVER PHOTO UPLOAD LOGIC
    // ==========================================
    const coverUploadTrigger = document.getElementById('cover-upload-trigger');
    const uploadCoverModal = document.getElementById('uploadCoverModal');
    const modalCoverFile = document.getElementById('modal_cover_file');
    const modalDropZone = document.getElementById('modal-drop-zone');
    const modalPreview = document.getElementById('modal_cover_preview');
    const btnConfirmCover = document.getElementById('btn-confirm-cover');

    const mainCoverPhoto = document.getElementById('main_cover_photo');
    const mainCoverPreviewWrapper = document.getElementById('main_cover_preview_wrapper');
    const mainCoverPreview = document.getElementById('main_cover_preview');
    const btnChangeCover = document.getElementById('btn-change-cover');

    // Handle File Reader Preview
    function handleFileSelection(files) {
        if (files && files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                modalPreview.src = e.target.result;
                modalPreview.style.display = 'block';
                modalDropZone.style.display = 'none'; 
                btnConfirmCover.style.display = 'block'; 
            }
            reader.readAsDataURL(files[0]);
        }
    }

    // Modal Input Change
    if (modalCoverFile) {
        modalCoverFile.addEventListener('change', function() {
            handleFileSelection(this.files);
        });
    }

    // Data Transfer for Drops (Styling is handled globally by global-modals.js)
    if (modalDropZone) {
        modalDropZone.addEventListener('drop', (e) => {
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                modalCoverFile.files = e.dataTransfer.files;
                handleFileSelection(e.dataTransfer.files);
            }
        });
    }

    // Main UI Drop triggers Modal & Preview
    if (coverUploadTrigger) {
        coverUploadTrigger.addEventListener('drop', (e) => {
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                uploadCoverModal.classList.add('active'); 
                modalCoverFile.files = e.dataTransfer.files;
                handleFileSelection(e.dataTransfer.files);
            }
        });
    }

    // Confirm Button
    if (btnConfirmCover) {
        btnConfirmCover.addEventListener('click', () => {
            if (modalCoverFile.files.length > 0) {
                const dt = new DataTransfer();
                dt.items.add(modalCoverFile.files[0]);
                mainCoverPhoto.files = dt.files;

                coverUploadTrigger.style.display = 'none'; 
                mainCoverPreview.src = modalPreview.src;
                mainCoverPreviewWrapper.style.display = 'block'; 

                uploadCoverModal.classList.remove('active');
            }
        });
    }

    // Change Photo Reset Logic
    if (btnChangeCover) {
        btnChangeCover.addEventListener('click', () => {
             // Let HTML attribute or manual JS open it
             uploadCoverModal.classList.add('active'); 
             modalDropZone.style.display = 'block';
             modalPreview.style.display = 'none';
             btnConfirmCover.style.display = 'none';
             modalCoverFile.value = ''; 
        });
    }

    // ==========================================
    // 9. NATIVE DRAG AND DROP SORTING ENGINE
    // ==========================================
    const containers = document.querySelectorAll('.file-list');
    
    containers.forEach(container => {
        container.addEventListener('dragover', e => {
            e.preventDefault(); // Required to allow dropping
            const afterElement = getDragAfterElement(container, e.clientY);
            const draggable = document.querySelector('.dragging');
            
            if (draggable) {
                if (afterElement == null) {
                    container.appendChild(draggable);
                } else {
                    container.insertBefore(draggable, afterElement);
                }
                
                // Optional: Update the hidden category input if dragged to a new list
                const newCategory = container.id.replace('list-', '');
                const categoryInput = draggable.querySelector('input[name="task_categories[]"]');
                if (categoryInput) {
                    categoryInput.value = newCategory;
                }
            }
        });
    });

    // Helper function to figure out where to place the item being dragged
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.file-item:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            // Calculate distance from center of the hovered box
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

});
</script>

<?php include __DIR__ . '/../../../core/views/components/add-milestone-modal.php'; ?>
<?php include __DIR__ . '/../../../core/views/components/add-member-modal.php'; ?>
<?php include __DIR__ . '/../../../core/views/components/upload-cover-modal.php'; ?>
<?php include __DIR__ . '/../../../core/views/footer.php'; ?>