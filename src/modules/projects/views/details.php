<?php 
include __DIR__ . '/../../../core/views/header.php'; 
require_once __DIR__ . '/../../../core/avatar-service.php';
require_once __DIR__ . '/../../attachments/attachment-repository.php';
require_once __DIR__ . '/details-partials/helpers.php';

$attachmentRepo = new AttachmentRepository(); 

// Check if current user is part of the team
$currentUserId = $_SESSION['user_id'] ?? null;
$isTeamMember = false;

foreach ($teamMembers as $member) {
    if (($member['user_id'] ?? null) == $currentUserId) {
        $isTeamMember = true;
        break;
    }
}
?>

<?php include __DIR__ . '/details-partials/layout-styles.php'; ?>

<div class="container">
    
    <?php include __DIR__ . '/details-partials/project-header.php'; ?>

    <div class="details-grid">
        <div class="left-col">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 class="card-title" style="margin-bottom: 0;">Project Tasks & Files</h2>
                    <div class="view-toggles">
                        <button type="button" class="btn-toggle active" id="btn-category-view" onclick="switchTaskView('category')" title="Category View">
                            <i class="ph ph-squares-four"></i> <span class="toggle-text">Category</span>
                        </button>
                        <button type="button" class="btn-toggle" id="btn-timeline-view" onclick="switchTaskView('timeline')" title="Timeline View">
                            <i class="ph ph-list-numbers"></i> <span class="toggle-text">Timeline</span>
                        </button>
                    </div>
                </div>
                
                <?php include __DIR__ . '/details-partials/progress-section.php'; ?>
                <?php include __DIR__ . '/details-partials/task-list.php'; ?>
            </div> 
        </div>

        <div class="right-col">
            <div class="card">
                <h2 class="card-title">Discussion & Issues</h2>
                <?php include __DIR__ . '/details-partials/discussion-board.php'; ?>
            </div>
        </div> 
    </div> 
</div> 

<?php include __DIR__ . '/../../../core/views/components/upload-attachment-modal.php'; ?>
<?php include __DIR__ . '/details-partials/scripts.php'; ?>
<?php include __DIR__ . '/../../../core/views/footer.php'; ?>