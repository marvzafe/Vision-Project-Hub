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

    <header class="header">
        <div>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">
                <a href="project-controller.php?action=list">← Back to Dashboard</a> / PRJ-<?= str_pad($project['id'], 3, '0', STR_PAD_LEFT) ?>
            </p>
            <h1 class="title"><?= htmlspecialchars($project['name']) ?></h1>
            <?php if (!empty($project['project_location'])): ?>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">
                    📍 <?= htmlspecialchars($project['project_location']) ?> | 📏 Area: <?= htmlspecialchars($project['project_area']) ?>
                </p>
            <?php endif; ?>
        </div>
        
        <div>
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
                                        <span class="badge <?= $taskBadgeClass ?>"><?= ucwords(htmlspecialchars($task['status'])) ?></span>
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
</script>
<?php include __DIR__ . '/../../../core/views/footer.php'; ?>