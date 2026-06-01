<?php include __DIR__ . '/../../../core/views/header.php'; ?>
<?php 
require_once __DIR__ . '/../../../core/avatar-service.php';
require_once __DIR__ . '/../../attachments/attachment-repository.php';
$attachmentRepo = new AttachmentRepository(); 
?>

<div class="container">
    
    <?php if (!empty($project['cover_photo_url'])): ?>
        <div class="project-cover-banner" style="background-image: url('<?= htmlspecialchars($project['cover_photo_url']) ?>');">
            <div class="project-cover-overlay"></div>
        </div>
    <?php endif; ?>

    <?php
        // Add this near the top of details.php
        $currentUserId = $_SESSION['user_id'] ?? null;
        $isTeamMember = false;

        // Check if current user is part of the team
        foreach ($teamMembers as $member) {
            if (($member['user_id'] ?? null) == $currentUserId) {
                $isTeamMember = true;
                break;
            }
        }
    ?>

<div id="header-sentinel" style="position: absolute; margin-top: -90px;"></div>

    <header class="header" id="stickyHeader">
        <div>
            <div class="header-meta">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">
                    <a href="project-controller.php?action=list">← Back to Dashboard</a> / PRJ-<?= str_pad($project['id'], 3, '0', STR_PAD_LEFT) ?>
                </p>
            </div>
            
            <h1 class="title" style="margin: 0;"><?= htmlspecialchars($project['name']) ?></h1>
            
            <?php if (!empty($project['project_location'])): ?>
                <div class="header-meta">
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">
                        📍 <?= htmlspecialchars($project['project_location']) ?> | 📏 Area: <?= htmlspecialchars($project['project_area']) ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="display: flex; align-items: center; gap: 1rem;">
            <?php if ($isTeamMember): ?>
                <div class="header-actions" style="display: flex; gap: 0.5rem;">
                    <a href="project-controller.php?action=edit&id=<?= $project['id'] ?>" 
                       class="see-more-btn" 
                       style="width: auto; margin-top: 0; padding: 0.4rem 0.85rem; background-color: rgba(0, 102, 204, 0.08); color: var(--primary);">
                       <i class="ph ph-pencil-simple" style="font-size: 1.05rem;"></i> <span class="btn-text">Edit</span>
                    </a>
                    
                    <button type="button" onclick="confirmDeleteProject(<?= $project['id'] ?>)" 
                       class="see-more-btn" 
                       style="width: auto; margin-top: 0; padding: 0.4rem 0.85rem; background-color: rgba(255, 59, 48, 0.08); color: var(--status-attention);">
                       <i class="ph ph-trash" style="font-size: 1.05rem;"></i> <span class="btn-text">Delete</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <span class="badge <?= $statusBadgeClass ?>"><?= $statusText ?></span>
        </div>
    </header>

    <div class="details-grid">
        <div class="left-col">
            <div class="card">
                <h2 class="card-title">Project Tasks & Files</h2>
                <div class="progress-wrapper" style="margin-bottom: 1.5rem;">
                    <div class="progress-track">
                        <div class="progress-fill" style="width: <?= htmlspecialchars($project['progress_percentage']) ?>%;"></div>
                    </div>
                    <span class="progress-text"><?= htmlspecialchars($project['progress_percentage']) ?>% Completed</span>
                </div>

                <?php 
                $categoryTitles = [
                    'general_works' => 'General Works',
                    'project_progress' => 'Project\'s Progress',
                    'finishing_works' => 'Finishing Works'
                ];
                $hasAnyTasks = false;
                ?>

                <?php foreach ($groupedTasks as $categoryKey => $tasks): ?>
                    <?php if (!empty($tasks)): ?>
                        <?php $hasAnyTasks = true; ?>
                        <div class="task-group">
                            <h3 class="group-title"><?= strtoupper($categoryTitles[$categoryKey]) ?></h3>
                            
                            <?php foreach ($tasks as $task):
                                $taskStatusClass = 'status-unstarted';
                                $taskBadgeClass  = 'attention';
                                $taskIconStyle   = '';
                                
                                if ($task['status'] === 'processing') {
                                    $taskStatusClass = 'status-processing';
                                    $taskBadgeClass  = 'progress';
                                    $taskIconStyle   = 'color: var(--status-progress);';
                                } elseif ($task['status'] === 'done') {
                                    $taskStatusClass = 'status-done';
                                    $taskBadgeClass  = 'completed';
                                    $taskIconStyle   = 'color: var(--status-completed);';
                                }

                                $assigneeName = $task['first_name'] ? $task['first_name'] . ' ' . $task['last_name'] : 'Unassigned';
                                
                                // Fetch attachments for this specific task
                                $attachments = $attachmentRepo->getAttachmentsByTaskId($task['id'] ?? 0);
                            ?>
                                <div class="task-folder <?= $taskStatusClass ?>">
                                    <div class="task-header toggle-folder-btn">
                                        <div class="task-title-wrapper">
                                            <span class="folder-icon" style="<?= $taskIconStyle ?>">📁</span>
                                            <h4><?= htmlspecialchars($task['title']) ?></h4>
                                        </div>
                                        
                                        <?php if ($isTeamMember): ?>
                                            <select class="task-status-dropdown badge <?= $taskBadgeClass ?>" 
                                                    data-task-id="<?= $task['id'] ?>" 
                                                    data-project-id="<?= $project['id'] ?>"
                                                    onclick="event.stopPropagation();" 
                                                    style="cursor: pointer; border: 1px dashed currentColor; outline: none; appearance: none; text-align: center; padding: 0.2rem 1.8rem 0.2rem 0.8rem; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22currentColor%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpolyline points=%226 9 12 15 18 9%22%3E%3C/polyline%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1em;">
                                                <option value="not yet started" <?= $task['status'] === 'not yet started' ? 'selected' : '' ?>>Not Yet Started</option>
                                                <option value="processing" <?= $task['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                                <option value="done" <?= $task['status'] === 'done' ? 'selected' : '' ?>>Done</option>
                                            </select>
                                        <?php else: ?>
                                            <span class="badge <?= $taskBadgeClass ?>" style="border: 1px solid transparent;"><?= ucwords(htmlspecialchars($task['status'])) ?></span>
                                        <?php endif; ?>
                                    </div>
                                            <div class="task-body" style="display: none;">
        <ul class="file-list">
            <?php if (!empty($task['description'])): ?>
                <li class="file-item" style="border: none; padding-bottom: 0;">
                    <div class="file-info">
                        <span style="color: var(--text-muted); font-size: 0.9rem;"><?= nl2br(htmlspecialchars($task['description'])) ?></span>
                    </div>
                </li>
            <?php endif; ?>
                                            <li class="file-item">
                                                <div class="file-icon">👤</div>
                                                <div class="file-info">
                                                    <span class="file-name">Assigned to: <?= htmlspecialchars($assigneeName) ?></span>
                                                    <?php if (!empty($task['deadline'])): ?>
                                                        <span class="file-size">Deadline: <?= date('M d, Y', strtotime($task['deadline'])) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                        </ul>

                                        <?php if (!empty($attachments)): ?>
                                            <ul class="file-list">
                                                <?php foreach ($attachments as $file): ?>
                                                    <li class="file-item">
                                                        <div class="file-icon">📎</div>
                                                        <div class="file-info">
                                                            <a href="<?= htmlspecialchars($file['file_url']) ?>" target="_blank" class="file-name" style="text-decoration: none; color: var(--primary-color);">
                                                                <?= htmlspecialchars($file['file_name']) ?>
                                                            </a>
                                                            <span class="file-size">
                                                                <?= round($file['file_size'] / 1024, 2) ?> KB • Uploaded by <?= htmlspecialchars($file['first_name'] ?? 'Unknown') ?> on <?= date('M d, Y h:i A', strtotime($file['uploaded_at'])) ?>
                                                            </span>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p style="padding: 0 1rem 1rem; font-size: 0.85rem; color: var(--text-muted);">No attachments yet.</p>
                                        <?php endif; ?>

                                        <div style="padding: 0 1rem 1rem;">
                                            <button type="button" class="btn-upload-trigger" data-modal-target="uploadAttachmentModal" data-task-id="<?= $task['id'] ?>"
                                                   style="cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; color: var(--text-muted); padding: 0.4rem 0.8rem; border: 1px dashed var(--border-color); border-radius: 4px; background: transparent; transition: all 0.2s ease;" 
                                                   onmouseover="this.style.borderColor='var(--primary-color)'; this.style.color='var(--primary-color)';" 
                                                   onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='var(--text-muted)';">
                                                <span style="font-weight: bold; font-size: 1.1rem; line-height: 1;">+</span> 
                                                <span>Upload Attachment</span>
                                            </button>
                                        </div>
                                    </div> </div> <?php endforeach; ?>
                        </div> <?php endif; ?>
                <?php endforeach; ?>

                <?php if (!$hasAnyTasks): ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No tasks created yet.</p>
                <?php endif; ?>
            </div> </div> <div class="right-col">
            <div class="card">
                <h2 class="card-title">Project Team</h2>
<ul class="people-list" id="teamList">
    <?php if (empty($teamMembers)): ?>
        <li class="person"><p style="color: var(--text-muted);">No team members assigned yet.</p></li>
    <?php else: ?>
        <?php foreach ($teamMembers as $index => $member): 
            $isLead = !empty($member['is_lead']);
            $leadClass = $isLead ? 'project-lead-card' : '';
            $isHidden = $index >= 4 ? 'hidden-item' : '';
            $displayStyle = $index >= 4 ? 'style="display: none;"' : '';
        ?>
            <li class="person <?= $isHidden ?> <?= $leadClass ?>" <?= $displayStyle ?>>
                
                <?= AvatarService::renderAvatar($member['avatar_url'] ?? null, $member['first_name'] ?? '', $member['last_name'] ?? '') ?>
                
                <div class="person-info">
                    <h4>
                        <?= htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')) ?>
                        <?php if ($isLead): ?>
                            <span class="badge solved" style="margin-left: 6px; font-size: 0.65rem; padding: 0.2rem 0.5rem; vertical-align: middle;">Lead</span>
                        <?php endif; ?>
                    </h4>
                    <p><?= ucwords(htmlspecialchars($member['project_role'] ?? 'Member')) ?></p>
                </div>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
                
                <?php if (count($teamMembers) > 4): ?>
                    <button class="see-more-btn" id="seeMoreBtn">See More <span>▼</span></button>
                <?php endif; ?>
                </ul>
                
                <?php if (count($teamMembers) > 3): ?>
                    <button class="see-more-btn" id="seeMoreBtn">See More <span>▼</span></button>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2 class="card-title">Discussion & Issues</h2>
                <div class="comment">
                    <div class="comment-header">
                        <span class="timeline-user">System</span>
                        <span class="badge completed">Note</span>
                    </div>
                    <p class="comment-body">Project instantiated dynamically.</p>
                    <div class="comment-footer">
                        <span class="comment-meta">Just now</span>
                    </div>
                </div>
                <div class="comment-form">
                    <textarea class="comment-input" placeholder="Write an update..."></textarea>
                    <div class="comment-submit-row">
                        <button class="btn-primary">Post Comment</button>
                    </div>
                </div>
            </div>
        </div> </div> </div> <?php include __DIR__ . '/../../../core/views/components/upload-attachment-modal.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Task Folder Toggle
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

    // Team See More Toggle
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

    // Modal ID Passer
    document.querySelectorAll('.btn-upload-trigger').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            const modalInput = document.getElementById('modal_upload_task_id');
            if(modalInput) modalInput.value = taskId;
        });
    });
});

// Task Status Update Handler
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
                // Reload the page to refresh the overall project progress bar and badges
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
                // Redirect back to the project list
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

// --- Sticky Header & Parallax Blur Engine ---
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Sticky Header Detection
    const sentinel = document.getElementById('header-sentinel');
    const header = document.getElementById('stickyHeader');

    if (sentinel && header) {
        const observer = new IntersectionObserver((entries) => {
            // Trigger exactly when the header reaches the 90px docking point
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

    // 2. Hardware-Accelerated Parallax & Blur for Cover Photo
    const coverBanner = document.querySelector('.project-cover-banner');
    if (coverBanner) {
        window.addEventListener('scroll', () => {
            window.requestAnimationFrame(() => {
                const scrollY = window.scrollY;
                
                // Only calculate if near the top to save CPU power
                if (scrollY < 500) { 
                    // Calculate blur (0px up to 16px max)
                    const blurAmount = Math.min(scrollY / 12, 16); 
                    
                    // Push the banner down at half-speed for parallax depth
                    const yPos = scrollY * 0.4; 
                    
                    // Darken slightly as it blurs
                    const opacity = Math.max(1 - (scrollY / 600), 0.6);

                    coverBanner.style.transform = `translateY(${yPos}px)`;
                    coverBanner.style.filter = `blur(${blurAmount}px) brightness(${opacity})`;
                }
            });
        }, { passive: true });
    }
});
</script>
<?php include __DIR__ . '/../../../core/views/footer.php'; ?>