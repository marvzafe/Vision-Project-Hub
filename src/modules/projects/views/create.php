<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<link rel="stylesheet" href="/../assets/css/project-create.css">

<div class="container">
    <header class="header">
        <div>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">
                <a href="/../projects/project-controller.php">← Back to Dashboard</a>
            </p>
            <h1 class="title">Create New Project</h1>
        </div>
        <div>
            <span class="badge progress">Draft</span>
        </div>
    </header>

    <?php if (!empty($error)): ?>
        <div style="background-color: #ffebee; color: #c62828; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <strong>Error:</strong> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="details-grid">
            
            <div class="left-col">
                
                <div class="card">
                    <h2 class="card-title">General Information</h2>
                    
                    <div class="form-group">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Vision HQ Extension" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Project Location</label>
                            <input type="text" name="project_location" class="form-control" placeholder="e.g., Pasig City, NCR" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Project Area</label>
                            <input type="text" name="project_area" class="form-control" placeholder="e.g., 120 sqm" required>
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
    
    <div class="upload-area" id="cover-upload-trigger" data-modal-target="uploadCoverModal">
        <div class="upload-icon">📸</div>
        <p style="font-weight: 600; margin-bottom: 0.25rem;">Click or Drag to upload image</p>
        <p style="font-size: 0.8rem; color: var(--text-muted);">JPG, PNG up to 5MB</p>
        
        <input type="file" name="cover_photo" id="main_cover_photo" style="display: none;" accept="image/*">
    </div>

    <div id="main_cover_preview_wrapper" style="display: none; position: relative; margin-top: 0.5rem;">
        <img id="main_cover_preview" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
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
        <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; padding: 1rem 0;">No team assigned yet.</p>
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

<?php include __DIR__ . '/../../../core/views/components/add-milestone-modal.php'; ?>
<?php include __DIR__ . '/../../../core/views/components/add-member-modal.php'; ?>
<?php include __DIR__ . '/../../../core/views/components/upload-cover-modal.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // 1. PREVENT PAST DATES
    const msDateInput = document.getElementById('ms_date');
    if (msDateInput) {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        msDateInput.min = now.toISOString().split('T')[0];
    }

    // 2. UNIVERSAL DELETE LISTENER
    const milestoneContainer = document.getElementById('milestone-container');
    if (milestoneContainer) {
        milestoneContainer.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.delete-milestone-btn');
            if (deleteBtn) {
                deleteBtn.closest('.file-item').remove(); 
            }
        });
    }

    // 3. INTERCEPT THE MODAL SUBMISSION
    const milestoneForm = document.getElementById('custom-milestone-form');

    if (milestoneForm) {
        milestoneForm.addEventListener('submit', (e) => {
            e.preventDefault(); 
            
            try {
                // Grab all the values safely
                const titleEl = document.getElementById('ms_title');
                const categoryEl = document.getElementById('ms_category');
                const assigneeEl = document.querySelector('[name="assignee_id"]');
                
                const title = titleEl ? titleEl.value : 'Untitled';
                const category = categoryEl ? categoryEl.value : 'general_works';
                const assignee = assigneeEl ? assigneeEl.value : '';
                
                // Stitch the 12-hour UI safely
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
                
                // --- THE RESTORED FORMATTING MAGIC ---
                let dateText = 'No deadline set';
                if (hiddenDeadline && hiddenDeadline.value) {
                    const dateObj = new Date(hiddenDeadline.value);
                    const now = new Date();
                    
                    // Format: "April 4, 2009"
                    const datePart = dateObj.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                    // Format: "Saturday"
                    const dayPart = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
                    // Format: "10:30AM"
                    let timePart = dateObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    timePart = timePart.replace(' ', ''); 
                    
                    // Calculate Time Remaining
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

                    // Stitch it all together perfectly
                    dateText = `Due: ${datePart}, ${dayPart} ${timePart} <span style="color: var(--status-progress); font-weight: 500;">${remainingText}</span>`;
                }
                // -------------------------------------

                // Build the UI item AND the hidden inputs for PHP
                const newLi = document.createElement('li');
                newLi.className = 'file-item';
                newLi.style.justifyContent = 'space-between';
                
                const finalDeadline = hiddenDeadline ? hiddenDeadline.value : '';

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
                    
                    <input type="hidden" name="task_titles[]" value="${title}">
                    <input type="hidden" name="task_categories[]" value="${category}">
                    <input type="hidden" name="task_assignees[]" value="${assignee}">
                    <input type="hidden" name="task_deadlines[]" value="${finalDeadline}">
                `;

                // Inject it into the correct list
                const targetList = document.getElementById(`list-${category}`);
                if (targetList) {
                    targetList.appendChild(newLi);
                }

                // Reset the form
                milestoneForm.reset();
                
                // Close the modal
                const modalWrapper = milestoneForm.closest('.modal-overlay');
                if (modalWrapper) {
                    modalWrapper.classList.remove('active');
                }

            } catch (error) {
                console.error(error);
                alert("Javascript crashed! Error: " + error.message);
            }
        });
    }

    // 4. INTERCEPT THE TEAM MODAL SUBMISSION
    const teamForm = document.getElementById('custom-team-form');
    const teamListUI = document.getElementById('project-team-list');

    if (teamForm && teamListUI) {
        teamForm.addEventListener('submit', (e) => {
            e.preventDefault(); 
            
            try {
                // Clear the visual list (removes the "No team assigned" text or previous edits)
                teamListUI.innerHTML = '';

                // --- A. PROCESS THE PROJECT LEAD (UPDATED FOR SEARCH) ---
                const leadHidden = document.getElementById('modal_project_lead_id');
                const leadSearchBox = document.querySelector('[data-hidden-input="modal_project_lead_id"]');
                
                if (leadHidden && leadHidden.value) {
                    const leadId = leadHidden.value;
                    const leadName = leadSearchBox ? leadSearchBox.value.trim() : 'Unknown Lead';
                    
                    // Generate 2-letter initials (e.g., "Marv Zafe" -> "MZ")
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

                // --- B. PROCESS THE TEAM MEMBERS (UPDATED FOR SEARCH) ---
                const memberRows = teamForm.querySelectorAll('.member-row');

                memberRows.forEach((row) => {
                    const hiddenInput = row.querySelector('.team-hidden-input');
                    const searchInput = row.querySelector('.team-user-search');
                    const roleInput = row.querySelector('.team-role-input');

                    // Only process if a person was actually selected (the hidden ID is filled)
                    if (hiddenInput && hiddenInput.value) { 
                        const userId = hiddenInput.value;
                        const userName = searchInput ? searchInput.value.trim() : 'Unknown Member';
                        const role = roleInput.value || 'Team Member'; // Fallback role if empty
                        
                        const initials = userName.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();

                        const memberLi = document.createElement('li');
                        memberLi.className = 'person';
                        memberLi.innerHTML = `
                            <div class="avatar" style="background-color: var(--text-muted);">${initials}</div>
                            <div class="person-info">
                                <h4>${userName}</h4>
                                <p>${role}</p>
                            </div>
                            <input type="hidden" name="team_user_ids[]" value="${userId}">
                            <input type="hidden" name="team_roles[]" value="${role}">
                        `;
                        teamListUI.appendChild(memberLi);
                    }
                });

                // --- C. CLOSE THE MODAL ---
                const modalWrapper = teamForm.closest('.modal-overlay');
                if (modalWrapper) {
                    modalWrapper.classList.remove('active');
                }

            } catch (error) {
                console.error(error);
                alert("Javascript crashed! Error: " + error.message);
            }
        });
    }
    
});

// --- COVER PHOTO UPLOAD LOGIC ---
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

    // 1. When a file is selected in the modal
    modalCoverFile.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                modalPreview.src = e.target.result;
                modalPreview.style.display = 'block';
                modalDropZone.style.display = 'none'; // Hide the dashed box
                btnConfirmCover.style.display = 'block'; // Show confirm button
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // 2. Drag & Drop inside the Modal
    modalDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        modalDropZone.style.borderColor = 'var(--primary)';
        modalDropZone.style.backgroundColor = 'var(--bg-color)';
    });
    modalDropZone.addEventListener('dragleave', () => {
        modalDropZone.style.borderColor = 'var(--border-color)';
        modalDropZone.style.backgroundColor = 'transparent';
    });
    modalDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        modalDropZone.style.borderColor = 'var(--border-color)';
        modalDropZone.style.backgroundColor = 'transparent';
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            modalCoverFile.files = e.dataTransfer.files;
            modalCoverFile.dispatchEvent(new Event('change')); // Trigger preview
        }
    });

    // 3. Drag & Drop on the Main Screen (Opens modal automatically!)
    coverUploadTrigger.addEventListener('dragover', (e) => {
        e.preventDefault();
        coverUploadTrigger.style.borderColor = 'var(--primary)';
    });
    coverUploadTrigger.addEventListener('dragleave', () => {
        coverUploadTrigger.style.borderColor = 'var(--border-color)';
    });
    coverUploadTrigger.addEventListener('drop', (e) => {
        e.preventDefault();
        coverUploadTrigger.style.borderColor = 'var(--border-color)';
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            uploadCoverModal.classList.add('active'); // Open Modal
            modalCoverFile.files = e.dataTransfer.files;
            modalCoverFile.dispatchEvent(new Event('change')); // Trigger preview
        }
    });

    // 4. Confirm Button Click
    btnConfirmCover.addEventListener('click', () => {
        if (modalCoverFile.files.length > 0) {
            // Transfer the file from the Modal to the Main Form!
            const dt = new DataTransfer();
            dt.items.add(modalCoverFile.files[0]);
            mainCoverPhoto.files = dt.files;

            // Update Main Screen UI
            coverUploadTrigger.style.display = 'none'; // Hide original trigger box
            mainCoverPreview.src = modalPreview.src;
            mainCoverPreviewWrapper.style.display = 'block'; // Show main preview

            // Close Modal
            uploadCoverModal.classList.remove('active');
        }
    });

    // 5. "Change Photo" Button on Main Screen
    btnChangeCover.addEventListener('click', () => {
         uploadCoverModal.classList.add('active');
         // Reset Modal State
         modalDropZone.style.display = 'block';
         modalPreview.style.display = 'none';
         btnConfirmCover.style.display = 'none';
         modalCoverFile.value = ''; // clear old file
    });
</script>
<?php include __DIR__ . '/../../../core/views/footer.php'; ?>