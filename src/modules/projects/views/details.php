<?php include __DIR__ . '/../../../core/views/header.php'; ?>
<?php 
require_once __DIR__ . '/../../../core/avatar-service.php';
require_once __DIR__ . '/../../attachments/attachment-repository.php';
$attachmentRepo = new AttachmentRepository(); 

// Add this under getRelativeTime()
function formatDiscussionText($text) {
    // First, escape HTML for security just like before
    $escaped = htmlspecialchars($text);
    
    // Look for @ followed by letters/numbers and wrap it in your global.css badge
    $withMentions = preg_replace('/@([A-Za-z0-9_]+)/', '<a href="#" class="mention-badge">@$1</a>', $escaped);
    
    // Convert newlines to <br> tags
    return nl2br($withMentions);
}


// ADD THIS HELPER FUNCTION:
function getRelativeTime($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return 'Just now';
    
    $minutes = floor($diff / 60);
    if ($minutes < 60) return $minutes . 'm';
    
    $hours = floor($diff / 3600);
    if ($hours < 24) return $hours . 'h';
    
    $days = floor($diff / 86400);
    if ($days < 7) return $days . 'd';
    
    $weeks = floor($diff / 604800);
    if ($weeks < 52) return $weeks . 'w';
    
    $years = floor($diff / 31536000);
    return $years . 'y';
}
?>

<div class="container">

<style>
    /* ==========================================
       PHASE 2: TOP NAV BAR (MAIN HEADER)
       ========================================== */
    .scrolled-project-title {
        display: inline-block;
        max-width: 0;
        opacity: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-main);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        vertical-align: middle;
    }

    .top-nav-bar.is-scrolled .brand-text,
    .top-nav-bar.is-scrolled .nav-brand-box:hover .brand-text {
        max-width: 0 !important;
        opacity: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .top-nav-bar.is-scrolled .scrolled-project-title {
        max-width: 250px; 
        opacity: 1;
        margin-left: 8px;
        padding-left: 8px;
        border-left: 1px solid var(--border-color);
    }

    .top-nav-bar.is-scrolled .nav-text,
    .top-nav-bar.is-scrolled .profile-info-text,
    .top-nav-bar.is-scrolled .dropdown-icon {
        max-width: 0 !important;
        opacity: 0 !important;
        margin-left: 0 !important;
        padding: 0 !important;
    }

    .top-nav-bar.is-scrolled .nav-item:hover .nav-text,
    .top-nav-bar.is-scrolled .profile-trigger:hover .profile-info-text {
        max-width: 120px !important;
        opacity: 1 !important;
        margin-left: 8px !important;
    }

    .top-nav-bar.is-scrolled .nav-brand-box, 
    .top-nav-bar.is-scrolled .nav-links-box,
    .top-nav-bar.is-scrolled .profile-trigger {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    /* ==========================================
       PHASE 1 -> PHASE 2: PROJECT HEADER
       ========================================== */

    /* Elements that will hide during Phase 2 */
    .header .header-meta,
    .header .project-title-text {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        transform-origin: left top;
        opacity: 1;
        max-height: 100px;
        overflow: hidden;
    }

    /* Elements that will shrink during Phase 2 */
    .header .btn-text {
        transition: max-width 0.3s ease, opacity 0.3s ease;
        max-width: 60px;
        display: inline-block;
        vertical-align: middle;
        white-space: nowrap;
    }

    /* --- PHASE 2 (SCROLLED COMPACT STATE) --- */
    .header.is-sticky {
        background: var(--surface-color);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        padding: 0.75rem 1.5rem; /* Add horizontal padding when floating */
        margin: -1rem -1.5rem 2rem -1.5rem; /* Pull out of container to full width */
    }

    /* Hide the original title, breadcrumbs, and location */
    .header.is-sticky .header-meta,
    .header.is-sticky .project-title-text {
        max-height: 0;
        opacity: 0;
        margin: 0;
        padding: 0;
    }

    /* Collapse buttons to icons-only */
    .header.is-sticky .btn-text {
        max-width: 0;
        opacity: 0;
    }

</style>
    
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

<!-- Remove the sentinel div completely! -->
    
    <header class="header" id="stickyProjectHeader">
        <div>
            <!-- Breadcrumbs -->
            <div class="header-meta">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">
                    <a href="project-controller.php?action=list">← Back to Dashboard</a> / PRJ-<?= str_pad($project['id'], 3, '0', STR_PAD_LEFT) ?>
                </p>
            </div>
            
            <h1 class="title project-title-text" style="margin: 0;"><?= htmlspecialchars($project['name']) ?></h1>
            
            <!-- Location & Area -->
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
                <div style="display: flex; gap: 0.5rem;">
                    <a href="project-controller.php?action=edit&id=<?= $project['id'] ?>" class="see-more-btn" style="width: auto; margin-top: 0; padding: 0.4rem 0.85rem; background-color: rgba(0, 102, 204, 0.08); color: var(--primary); border-radius: 12px; display: flex; align-items: center; gap: 4px;">
                       <i class="ph ph-pencil-simple" style="font-size: 1.05rem;"></i> <span class="btn-text">Edit</span>
                    </a>
                    <button type="button" onclick="confirmDeleteProject(<?= $project['id'] ?>)" class="see-more-btn" style="width: auto; margin-top: 0; padding: 0.4rem 0.85rem; background-color: rgba(255, 59, 48, 0.08); color: var(--status-attention); border-radius: 12px; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px;">
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
                <div class="team-avatars-grid">
                    <?php if (empty($teamMembers)): ?>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">No team members assigned.</p>
                    <?php else: ?>
                        <?php foreach ($teamMembers as $member): ?>
                            <div style="position: relative; width: 44px; height: 44px;">
                                <?= AvatarService::renderAvatar($member['avatar_url'] ?? null, $member['first_name'] ?? '', $member['last_name'] ?? '', '44px', $member['user_id']) ?>
                                <span class="status-dot <?= $member['status_class'] ?>" style="position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px; border: 2px solid var(--surface-color); z-index: 2;"></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card">
    <h2 class="card-title">Discussion & Issues</h2>

    <div class="discussion-list" style="margin-bottom: 1.5rem;">
        <?php if (empty($discussions)): ?>
            <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; padding: 1rem 0;">No discussions yet. Start the conversation!</p>
        <?php else: ?>
            <?php foreach ($discussions as $thread): ?>
                <div class="comment-thread" data-id="<?= htmlspecialchars($thread['id']) ?>">
                    
                    <div class="thread-layout">
                        <div class="thread-spine">
                            <?= AvatarService::renderAvatar($thread['avatar_url'] ?? null, $thread['first_name'] ?? '', $thread['last_name'] ?? '', '40px', $thread['user_id']) ?>
                            <?php if (!empty($thread['replies'])): ?>
                                <div class="thread-line"></div>
                            <?php endif; ?>
                        </div>

                        <div class="thread-content">
                            <div class="comment-header flex-header">
                                <span class="timeline-user"><?= htmlspecialchars($thread['first_name'] . ' ' . $thread['last_name']) ?></span>
                                <span class="comment-meta">· <?= getRelativeTime($thread['created_at']) ?></span>
                                
                                <?php if (!$isTeamMember && $thread['flag_status']): ?>
                                    <span class="badge <?= $thread['flag_status'] === 'solved' ? 'completed' : 'attention' ?> badge-right">
                                        <?= $thread['flag_status'] === 'solved' ? 'Solved' : 'Needs Attention' ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div id="comment-display-<?= $thread['id'] ?>">
                                <p class="comment-body thread-body">
                                    <?= formatDiscussionText($thread['content']) ?>
                                </p>
                            </div>
                            
                            <div id="edit-form-<?= $thread['id'] ?>" style="display: none; margin-top: 0.5rem; width: 100%;">
                                <textarea id="edit-input-<?= $thread['id'] ?>" class="comment-input compact-input" style="width: 100%; min-height: 60px;"><?= htmlspecialchars($thread['content']) ?></textarea>
                                <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px;">
                                    <button type="button" class="btn-primary btn-sm" style="background: transparent; color: var(--text-muted); border: 1px solid var(--border-color);" onclick="toggleEditForm('<?= $thread['id'] ?>')">Cancel</button>
                                    <button type="button" class="btn-primary btn-sm" onclick="submitEditComment('<?= $thread['id'] ?>')">Save</button>
                                </div>
                            </div>
                            
                            <div class="comment-action-bar">
                                <button type="button" onclick="toggleReplyForm('<?= $thread['id'] ?>')" class="action-btn always-visible">
                                    <i class="ph ph-chat-circle"></i> Reply

                                    <?php if ($thread['user_id'] == $currentUserId): ?>
                                    <button type="button" onclick="toggleEditForm('<?= $thread['id'] ?>')" class="action-btn">
                                        <i class="ph ph-pencil-simple"></i> Edit
                                    </button>
                                    <button type="button" onclick="deleteDiscussionComment('<?= $thread['id'] ?>')" class="action-btn" style="color: var(--status-attention);">
                                        <i class="ph ph-trash"></i> Delete
                                    </button>
                                <?php endif; ?>
                                </button>

                                <?php if ($isTeamMember): ?>
                                    <button type="button" 
                                            onclick="toggleDiscussionFlag('<?= $thread['id'] ?>', 'attention', '<?= $thread['flag_status'] ?>')" 
                                            class="action-btn <?= $thread['flag_status'] === 'attention' ? 'active-attention' : '' ?>">
                                        <i class="ph <?= $thread['flag_status'] === 'attention' ? 'ph-warning-circle-fill' : 'ph-warning-circle' ?>"></i> Attention
                                    </button>

                                    <button type="button" 
                                            onclick="toggleDiscussionFlag('<?= $thread['id'] ?>', 'solved', '<?= $thread['flag_status'] ?>')" 
                                            class="action-btn <?= $thread['flag_status'] === 'solved' ? 'active-solved' : '' ?>">
                                        <i class="ph <?= $thread['flag_status'] === 'solved' ? 'ph-check-circle-fill' : 'ph-check-circle' ?>"></i> Solved
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div id="reply-form-<?= $thread['id'] ?>" class="reply-form-container" style="display: none;">
                                <input type="text" id="reply-input-<?= $thread['id'] ?>" class="comment-input compact-input" placeholder="Post your reply...">
                                <button type="button" class="btn-primary btn-sm" onclick="submitDiscussionComment('<?= $project['id'] ?>', '<?= $thread['id'] ?>')">Reply</button>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($thread['replies'])): ?>
                        <div class="replies-container">
                            <?php foreach ($thread['replies'] as $reply): ?>
                                <div class="thread-layout reply-layout">
                                    <div class="thread-spine">
                                        <?= AvatarService::renderAvatar($reply['avatar_url'] ?? null, $reply['first_name'] ?? '', $reply['last_name'] ?? '', '40px', $reply['user_id']) ?>
                                    </div>
                                    <div class="thread-content">
                                        <div class="comment-header flex-header">
                                            <span class="timeline-user"><?= htmlspecialchars($reply['first_name'] . ' ' . $reply['last_name']) ?></span>
                                            <span class="comment-meta">· <?= getRelativeTime($reply['created_at']) ?></span>
                                        </div>
                                        <div id="comment-display-<?= $reply['id'] ?>">
                                            <p class="comment-body thread-body">
                                                <?= formatDiscussionText($reply['content']) ?>
                                            </p>
                                        </div>
                                        
                                        <div id="edit-form-<?= $reply['id'] ?>" style="display: none; margin-top: 0.5rem; width: 100%;">
                                            <textarea id="edit-input-<?= $reply['id'] ?>" class="comment-input compact-input" style="width: 100%; min-height: 60px;"><?= htmlspecialchars($reply['content']) ?></textarea>
                                            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px;">
                                                <button type="button" class="btn-primary btn-sm" style="background: transparent; color: var(--text-muted); border: 1px solid var(--border-color);" onclick="toggleEditForm('<?= $reply['id'] ?>')">Cancel</button>
                                                <button type="button" class="btn-primary btn-sm" onclick="submitEditComment('<?= $reply['id'] ?>')">Save</button>
                                            </div>
                                        </div>
                                        <div class="comment-action-bar" style="margin-top: 4px;">
                                            <?php if ($reply['user_id'] == $currentUserId): ?>
                                                <button type="button" onclick="toggleEditForm('<?= $reply['id'] ?>')" class="action-btn">
                                                    <i class="ph ph-pencil-simple"></i> Edit
                                                </button>
                                                <button type="button" onclick="deleteDiscussionComment('<?= $reply['id'] ?>')" class="action-btn" style="color: var(--status-attention);">
                                                    <i class="ph ph-trash"></i> Delete
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
                <hr class="thread-divider">
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="comment-form new-thread-box">
        <textarea id="main-discussion-input" class="comment-input" placeholder="Start a new discussion..." style="width: 100%; min-height: 80px;"></textarea>
        <div style="text-align: right; margin-top: 0.75rem;">
            <button class="btn-primary" type="button" onclick="submitDiscussionComment('<?= $project['id'] ?>')">Post Discussion</button>
        </div>
    </div>
</div>
        </div> </div> </div> <?php include __DIR__ . '/../../../core/views/components/upload-attachment-modal.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Single Header Transformation Engine
    const topNav = document.querySelector('.top-nav-bar');
    const brandBox = document.querySelector('.nav-brand-box');
    const projectTitleText = "<?= addslashes(htmlspecialchars($project['name'])) ?>";
    
    if (topNav && brandBox) {
        // Inject the Project Name into the Top Nav
        const titleSpan = document.createElement('span');
        titleSpan.className = 'scrolled-project-title';
        titleSpan.textContent = projectTitleText;
        brandBox.appendChild(titleSpan);

        // Listen for scroll to trigger the collapse and hand-off
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

// 2. Hardware-Accelerated Parallax for Cover Photo (Blur Removed)
    const coverBanner = document.querySelector('.project-cover-banner');
    if (coverBanner) {
        window.addEventListener('scroll', () => {
            window.requestAnimationFrame(() => {
                const scrollY = window.scrollY;
                
                // Only calculate if near the top to save CPU power
                if (scrollY < 500) { 
                    // Push the banner down at half-speed for parallax depth
                    const yPos = scrollY * 0.4; 
                    coverBanner.style.transform = `translateY(${yPos}px)`;
                }
            });
        }, { passive: true });
    }
});

// --- Scroll Direction Fade Engine ---
document.addEventListener('DOMContentLoaded', () => {
    let lastScrollY = window.scrollY;
    const coverOverlay = document.querySelector('.project-cover-overlay');
    const stickyHeader = document.getElementById('stickyProjectHeader');

    window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        // Apply the effect only after scrolling past a small 50px buffer 
        // to prevent glitching at the very top of the page.
        if (currentScrollY > 100) {
            if (currentScrollY > lastScrollY) {
                // Scrolling Down -> Fade Out
                if (coverOverlay) coverOverlay.classList.add('scroll-fade-out');
                if (stickyHeader) stickyHeader.classList.add('scroll-fade-out');
            } else {
                // Scrolling Up -> Fade In
                if (coverOverlay) coverOverlay.classList.remove('scroll-fade-out');
                if (stickyHeader) stickyHeader.classList.remove('scroll-fade-out');
            }
        } else {
            // Always ensure they are visible when at the absolute top
            if (coverOverlay) coverOverlay.classList.remove('scroll-fade-out');
            if (stickyHeader) stickyHeader.classList.remove('scroll-fade-out');
        }

        // Update the last scroll position
        lastScrollY = currentScrollY;
    }, { passive: true }); // passive: true ensures smooth scrolling performance
});

// --- DISCUSSION MODULE SCRIPTS --- //

function toggleReplyForm(threadId) {
    const form = document.getElementById('reply-form-' + threadId);
    // Force toggle based on current computed display style
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

    if (!content) {
        alert("Please enter a comment before posting.");
        return;
    }

    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('project_id', projectId);
    formData.append('content', content);
    if (parentId) formData.append('parent_id', parentId);

    fetch('/src/modules/discussions/discussion-controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload to render new layout
        } else {
            alert('Error posting comment: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('A network error occurred.');
    });
}

// Toggle Flag Status (Un-flags if clicking the currently active status)
function toggleDiscussionFlag(discussionId, clickedStatus, currentStatus) {
    // If clicking the button that is already active, clear the flag.
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
            location.reload(); // Reload to apply updated button highlights
        } else {
            alert('Error updating flag: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('A network error occurred.');
    });
}

// --- @MENTION SYSTEM (Reusing Global Search & CSS) --- //

document.addEventListener('DOMContentLoaded', () => {
    let mentionSearchTimeout = null;
    let currentMentionTarget = null;
    let currentMentionQuery = '';

    // 1. Create the dropdown using your existing global.css class
    const mentionDropdown = document.createElement('div');
    mentionDropdown.className = 'mention-autocomplete-menu';
    document.body.appendChild(mentionDropdown);

    // 2. Listen to typing in any comment box
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('comment-input')) {
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

    // 3. Render using global.css classes
    function renderMentionResults(users) {
        mentionDropdown.innerHTML = ''; 
        
        users.forEach(user => {
            const item = document.createElement('div');
            item.className = 'mention-item';
            item.innerHTML = `
                <div class="mention-name">${user.title}</div>
                <div class="mention-email">${user.subtitle}</div>
            `;
            
            // 4. Handle Selection
            item.addEventListener('click', () => {
                if (currentMentionTarget) {
                    const val = currentMentionTarget.value;
                    const cursorPos = currentMentionTarget.selectionStart;
                    const textBeforeCursor = val.substring(0, cursorPos);
                    const textAfterCursor = val.substring(cursorPos);
                    
                    // Remove spaces from the name to create a solid tag (e.g., @JohnDoe)
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

    // 5. Close when clicking away
    document.addEventListener('click', (e) => {
        if (e.target !== mentionDropdown && !mentionDropdown.contains(e.target)) {
            mentionDropdown.style.display = 'none';
        }
    });
});

// --- DISCUSSION EDIT & DELETE MODULE --- //

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

</script>
<?php include __DIR__ . '/../../../core/views/footer.php'; ?>