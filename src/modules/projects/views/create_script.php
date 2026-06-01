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
            if (!response.ok) throw new Error('Network response was not ok');
            
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
    function addMilestoneToDOM(title, category, assignee, deadlineISO) {
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
            
            <input type="hidden" name="task_titles[]" value="${title}">
            <input type="hidden" name="task_categories[]" value="${category}">
            <input type="hidden" name="task_assignees[]" value="${assignee}">
            <input type="hidden" name="task_deadlines[]" value="${finalDeadline}">
        `;

        const targetList = document.getElementById(`list-${category}`);
        if (targetList) {
            targetList.appendChild(newLi);
        }
    }

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

                addMilestoneToDOM(task.title, task.category, '', isoDeadline);
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
                    const leadId = leadHidden.value;
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
                        const userId = hiddenInput.value;
                        const userName = searchInput ? searchInput.value.trim() : 'Unknown Member';
                        const role = roleInput.value || 'Team Member';
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

});
</script>