<script>
document.addEventListener('DOMContentLoaded', () => {
    const topNav = document.querySelector('.top-nav-bar');
    const brandBox = document.querySelector('.nav-brand-box');
    const projectTitleText = "<?= addslashes(htmlspecialchars($project['name'])) ?>";
    
    if (topNav && brandBox) {
        const titleSpan = document.createElement('span');
        titleSpan.className = 'scrolled-project-title';
        titleSpan.textContent = projectTitleText;
        brandBox.appendChild(titleSpan);

        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                topNav.classList.add('is-scrolled');
            } else {
                topNav.classList.remove('is-scrolled');
            }
        }, { passive: true });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const folderButtons = document.querySelectorAll('.toggle-folder-btn');
    folderButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const body = this.nextElementSibling;
            const icon = this.querySelector('.folder-icon');
            if (body.style.display === 'block') {
                body.style.display = 'none';
                icon.textContent = '📁'; 
            } else {
                body.style.display = 'block';
                icon.textContent = '📂';
            }
        });
    });

    const seeMoreBtn = document.getElementById('seeMoreBtn');
    if (seeMoreBtn) {
        seeMoreBtn.addEventListener('click', function() {
            const hiddenItems = document.querySelectorAll('.hidden-item');
            let isExpanded = false;
            hiddenItems.forEach(item => {
                if (item.style.display === 'flex') {
                    item.style.display = 'none';
                } else {
                    item.style.display = 'flex';
                    isExpanded = true;
                }
            });
            this.innerHTML = isExpanded ? 'See Less <span>▲</span>' : 'See More <span>▼</span>';
        });
    }

    document.querySelectorAll('.btn-upload-trigger').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            const modalInput = document.getElementById('modal_upload_task_id');
            if(modalInput) modalInput.value = taskId;
        });
    });
});

document.querySelectorAll('.task-status-dropdown').forEach(dropdown => {
    dropdown.addEventListener('change', function() {
        const taskId = this.getAttribute('data-task-id');
        const projectId = this.getAttribute('data-project-id');
        const newStatus = this.value;

        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('task_id', taskId);
        formData.append('project_id', projectId);
        formData.append('status', newStatus);

        fetch('/src/modules/tasks/task-controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating task: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('A network error occurred.');
        });
    });
});

function confirmDeleteProject(projectId) {
    if (confirm("Are you sure you want to delete this project? This action cannot be undone and will delete all associated tasks and files.")) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('project_id', projectId);

        fetch('/src/modules/projects/project-controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'project-controller.php?action=list';
            } else {
                alert('Error deleting project: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('A network error occurred while trying to delete the project.');
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const sentinel = document.getElementById('header-sentinel');
    const header = document.getElementById('stickyHeader');

    if (sentinel && header) {
        const observer = new IntersectionObserver((entries) => {
            if (!entries[0].isIntersecting && window.scrollY > 20) {
                header.classList.add('is-sticky');
            } else {
                header.classList.remove('is-sticky');
            }
        }, {
            threshold: 0,
            rootMargin: '-90px 0px 0px 0px' 
        });
        observer.observe(sentinel);
    }

    const coverBanner = document.querySelector('.project-cover-banner');
    if (coverBanner) {
        window.addEventListener('scroll', () => {
            window.requestAnimationFrame(() => {
                const scrollY = window.scrollY;
                if (scrollY < 500) { 
                    const yPos = scrollY * 0.4; 
                    coverBanner.style.transform = `translateY(${yPos}px)`;
                }
            });
        }, { passive: true });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    let lastScrollY = window.scrollY;
    const stickyHeader = document.querySelector('#stickyProjectHeader');
    const coverPhoto = document.querySelector('.project-cover-banner');
    const topSafeZone = coverPhoto ? 250 : 50; 

    window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        if (currentScrollY <= topSafeZone) {
            if (stickyHeader) stickyHeader.classList.remove('header-hidden');
            if (coverPhoto) coverPhoto.classList.remove('header-hidden');
        } else {
            if (currentScrollY > lastScrollY) {
                if (stickyHeader) stickyHeader.classList.remove('header-hidden');
                if (coverPhoto) coverPhoto.classList.remove('header-hidden');
            } else {
                if (stickyHeader) stickyHeader.classList.add('header-hidden');
                if (coverPhoto) coverPhoto.classList.add('header-hidden');
            }
        }
        lastScrollY = currentScrollY;
    }, { passive: true });
});

function toggleReplyForm(threadId) {
    const form = document.getElementById('reply-form-' + threadId);
    if (window.getComputedStyle(form).display === 'none') {
        form.style.display = 'flex';
        document.getElementById('reply-input-' + threadId).focus();
    } else {
        form.style.display = 'none';
    }
}

function submitDiscussionComment(projectId, parentId = null) {
    let inputId = parentId ? 'reply-input-' + parentId : 'main-discussion-input';
    let content = document.getElementById(inputId).value.trim();

    if (!content && !currentAttachedTaskId) {
        alert("Please enter a comment or attach a task.");
        return;
    }

    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('project_id', projectId);
    formData.append('content', content);
    
    if (parentId) {
        formData.append('parent_id', parentId);
    }
    
    if (!parentId && currentAttachedTaskId) {
        formData.append('task_id', currentAttachedTaskId);
    }

    fetch('/src/modules/discussions/discussion-controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            removeAttachedTask(); 
            location.reload(); 
        } else {
            alert('Error posting comment: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('A network error occurred.');
    });
}

function toggleDiscussionFlag(discussionId, clickedStatus, currentStatus) {
    const newStatus = (clickedStatus === currentStatus) ? '' : clickedStatus;

    const formData = new FormData();
    formData.append('action', 'flag');
    formData.append('discussion_id', discussionId);
    formData.append('status', newStatus);

    fetch('/src/modules/discussions/discussion-controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); 
        } else {
            alert('Error updating flag: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('A network error occurred.');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    let mentionSearchTimeout = null;
    let currentMentionTarget = null;
    let currentMentionQuery = '';

    const mentionDropdown = document.createElement('div');
    mentionDropdown.className = 'mention-autocomplete-menu';
    document.body.appendChild(mentionDropdown);

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('comment-input') || e.target.classList.contains('minimal-chat-input')) {
            const val = e.target.value;
            const cursorPos = e.target.selectionStart;
            const textBeforeCursor = val.substring(0, cursorPos);
            
            const match = textBeforeCursor.match(/@([a-zA-Z0-9_]{1,})$/);

            if (match) {
                currentMentionTarget = e.target;
                currentMentionQuery = match[1]; 
                
                const rect = e.target.getBoundingClientRect();
                mentionDropdown.style.left = `${rect.left + window.scrollX}px`;
                mentionDropdown.style.top = `${rect.bottom + window.scrollY + 5}px`;
                mentionDropdown.style.display = 'block';
                
                clearTimeout(mentionSearchTimeout);
                mentionSearchTimeout = setTimeout(() => {
                    fetch(`/src/modules/search/search-controller.php?table=users&q=${currentMentionQuery}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.length > 0) {
                                renderMentionResults(data.data);
                            } else {
                                mentionDropdown.style.display = 'none';
                            }
                        })
                        .catch(err => console.error("Mention Search Error:", err));
                }, 300);
            } else {
                mentionDropdown.style.display = 'none';
                currentMentionTarget = null;
            }
        }
    });

    function renderMentionResults(users) {
        mentionDropdown.innerHTML = ''; 
        
        users.forEach(user => {
            const item = document.createElement('div');
            item.className = 'mention-item';
            item.innerHTML = `
                <div class="mention-name">${user.title}</div>
                <div class="mention-email">${user.subtitle}</div>
            `;
            
            item.addEventListener('click', () => {
                if (currentMentionTarget) {
                    const val = currentMentionTarget.value;
                    const cursorPos = currentMentionTarget.selectionStart;
                    const textBeforeCursor = val.substring(0, cursorPos);
                    const textAfterCursor = val.substring(cursorPos);
                    
                    const mentionTag = user.title.replace(/\s+/g, '');
                    const newTextBefore = textBeforeCursor.replace(/@([a-zA-Z0-9_]{1,})$/, `@${mentionTag} `);
                    
                    currentMentionTarget.value = newTextBefore + textAfterCursor;
                    currentMentionTarget.focus();
                    
                    const newCursorPos = newTextBefore.length;
                    currentMentionTarget.setSelectionRange(newCursorPos, newCursorPos);
                    
                    mentionDropdown.style.display = 'none';
                }
            });
            
            mentionDropdown.appendChild(item);
        });
    }

    document.addEventListener('click', (e) => {
        if (e.target !== mentionDropdown && !mentionDropdown.contains(e.target)) {
            mentionDropdown.style.display = 'none';
        }
    });
});

function toggleEditForm(id) {
    const displayDiv = document.getElementById('comment-display-' + id);
    const editForm = document.getElementById('edit-form-' + id);

    if (editForm.style.display === 'none') {
        displayDiv.style.display = 'none';
        editForm.style.display = 'block';
        document.getElementById('edit-input-' + id).focus();
    } else {
        displayDiv.style.display = 'block';
        editForm.style.display = 'none';
    }
}

function submitEditComment(id) {
    const content = document.getElementById('edit-input-' + id).value.trim();
    if (!content) {
        alert("Comment cannot be empty.");
        return;
    }

    const formData = new FormData();
    formData.append('action', 'edit');
    formData.append('discussion_id', id);
    formData.append('content', content);

    fetch('/src/modules/discussions/discussion-controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); 
        } else {
            alert('Error editing comment: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('A network error occurred.');
    });
}

function deleteDiscussionComment(id) {
    if (!confirm("Are you sure you want to delete this comment? This action cannot be undone.")) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('discussion_id', id);

    fetch('/src/modules/discussions/discussion-controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting comment: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('A network error occurred.');
    });
}

document.querySelectorAll('.project-status-dropdown').forEach(dropdown => {
    dropdown.addEventListener('change', function() {
        const projectId = this.getAttribute('data-project-id');
        const newStatus = this.value;

        const formData = new FormData();
        formData.append('action', 'update_project_status');
        formData.append('project_id', projectId);
        formData.append('status', newStatus);

        fetch('/src/modules/projects/project-controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); 
            } else {
                alert('Error updating project status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('A network error occurred.');
        });
    });
});

function toggleTaskQuantitiesEdit(taskId) {
    const displayDiv = document.getElementById('qty-display-' + taskId);
    const editDiv = document.getElementById('qty-edit-' + taskId);
    
    if (editDiv.style.display === 'none') {
        displayDiv.style.display = 'none';
        editDiv.style.display = 'flex';
    } else {
        displayDiv.style.display = 'flex';
        editDiv.style.display = 'none';
    }
}

function saveTaskQuantities(projectId, taskId) {
    const qty = document.getElementById('input-qty-' + taskId).value;
    const inst = document.getElementById('input-inst-' + taskId).value;

    const formData = new FormData();
    formData.append('action', 'update_quantities');
    formData.append('project_id', projectId);
    formData.append('task_id', taskId);
    formData.append('quantity', qty);
    formData.append('installed', inst);

    fetch('/src/modules/tasks/task-controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); 
        } else {
            alert('Error updating quantities: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('A network error occurred.');
    });
}

let currentAttachedTaskId = null;

function handleTaskDragStart(event) {
    const taskId = event.currentTarget.getAttribute('data-task-id');
    const taskTitle = event.currentTarget.getAttribute('data-task-title');
    const dragData = JSON.stringify({ id: taskId, title: taskTitle });
    event.dataTransfer.setData('application/json', dragData);
    event.dataTransfer.effectAllowed = 'copy';
}

function handleDragOver(event) {
    event.preventDefault(); 
    const dropZone = document.getElementById('discussion-drop-zone');
    dropZone.style.borderColor = 'var(--primary)';
    dropZone.style.backgroundColor = 'rgba(0, 102, 204, 0.02)';
}

function handleDragLeave(event) {
    const dropZone = document.getElementById('discussion-drop-zone');
    dropZone.style.borderColor = 'transparent';
    dropZone.style.backgroundColor = 'transparent';
}

function handleTaskDrop(event) {
    event.preventDefault();
    handleDragLeave(event); 
    
    const dragData = event.dataTransfer.getData('application/json');
    if (dragData) {
        try {
            const task = JSON.parse(dragData);
            currentAttachedTaskId = task.id;
            document.getElementById('attached-task-title').textContent = task.title;
            document.getElementById('attached-task-badge').style.display = 'flex';
            document.getElementById('main-discussion-input').focus();
        } catch (e) {
            console.error("Failed to parse dragged task data", e);
        }
    }
}

function removeAttachedTask() {
    currentAttachedTaskId = null;
    document.getElementById('attached-task-badge').style.display = 'none';
}

function scrollToTask(taskId) {
    const taskElement = document.getElementById('task-folder-' + taskId);
    
    if (taskElement) {
        taskElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        taskElement.classList.remove('task-highlight-pulse');
        void taskElement.offsetWidth; 
        taskElement.classList.add('task-highlight-pulse');
        
        setTimeout(() => {
            taskElement.classList.remove('task-highlight-pulse');
        }, 2000);
    } else {
        alert("This task could not be found. It may have been deleted, moved, or is in a different project.");
    }
}

function toggleProgressBreakdown() {
    const breakdown = document.getElementById('progress-breakdown-list');
    const caret = document.getElementById('progress-caret');
    const normalFill = document.getElementById('normal-progress-fill');
    const segmentedFill = document.getElementById('segmented-progress-fill');
    
    if (breakdown.style.display === 'none' || breakdown.style.display === '') {
        breakdown.style.display = 'flex';
        caret.style.transform = 'rotate(180deg)';
        normalFill.style.display = 'none';
        segmentedFill.style.display = 'flex'; 
    } else {
        breakdown.style.display = 'none';
        caret.style.transform = 'rotate(0deg)';
        normalFill.style.display = 'block';
        segmentedFill.style.display = 'none';
    }
}

// --- TASK VIEW TOGGLE ENGINE ---
function switchTaskView(view) {
    const btnCat = document.getElementById('btn-category-view');
    const btnTime = document.getElementById('btn-timeline-view');
    const masterContainer = document.getElementById('tasks-master-container');
    
    if (!masterContainer) return;
    
    const groups = masterContainer.querySelectorAll('.task-group');
    const allTasks = Array.from(masterContainer.querySelectorAll('.task-folder'));

    if (view === 'timeline') {
        // Toggle active states
        btnTime.classList.add('active');
        btnCat.classList.remove('active');

        // 1. Hide the group headers and margins
        groups.forEach(group => {
            const title = group.querySelector('.group-title');
            if(title) title.style.display = 'none';
            group.style.marginBottom = '0'; 
        });

        // 2. Sort tasks by sort_order ascending (Oldest/Lowest first, Newest/Highest last)
        allTasks.sort((a, b) => {
            const orderA = parseInt(a.getAttribute('data-sort-order')) || 0;
            const orderB = parseInt(b.getAttribute('data-sort-order')) || 0;
            return orderA - orderB;
        });

        // 3. Move tasks OUT of their groups and directly into the master container
        allTasks.forEach(task => masterContainer.appendChild(task));

    } else if (view === 'category') {
        // Toggle active states
        btnCat.classList.add('active');
        btnTime.classList.remove('active');

        // 1. Show the group headers and restore spacing
        groups.forEach(group => {
            const title = group.querySelector('.group-title');
            if(title) title.style.display = 'block';
            group.style.marginBottom = '2rem'; 
        });

        // 2. Sort tasks back by their original index to maintain standard query order
        allTasks.sort((a, b) => {
            const indexA = parseInt(a.getAttribute('data-original-index')) || 0;
            const indexB = parseInt(b.getAttribute('data-original-index')) || 0;
            return indexA - indexB;
        });

        // 3. Move tasks BACK into their designated category groups
        allTasks.forEach(task => {
            const cat = task.getAttribute('data-category');
            const group = document.getElementById('group-' + cat);
            if (group) {
                group.appendChild(task);
            }
        });
    }
}
</script>