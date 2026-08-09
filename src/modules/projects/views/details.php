<?php 
include __DIR__ . '/../../../core/views/header.php'; 
require_once __DIR__ . '/../../../core/avatar-service.php';
require_once __DIR__ . '/../../attachments/attachment-repository.php';
require_once __DIR__ . '/details-partials/helpers.php';

// Check if current user is part of the team
$currentUserId = $_SESSION['user_id'] ?? null;
$currentUserDeptId = $_SESSION['department_id'] ?? null;

// Grab the Supabase keys using your reusable Database class!
$env = Database::getEnv();
$supabaseUrl = $env['SUPABASE_URL'] ?? '';
$supabaseAnonKey = $env['SUPABASE_ANON_KEY'] ?? '';

// If department ID isn't in session, look it up real quick and cache it
if (!$currentUserDeptId && $currentUserId) {
    $db = Database::getConnection(); // This still works exactly as before
    $stmt = $db->prepare("SELECT department_id FROM users WHERE user_id = :uid");   
    $stmt->execute([':uid' => $currentUserId]);
    $currentUserDeptId = $stmt->fetchColumn();
    $_SESSION['department_id'] = $currentUserDeptId; 
}

$isTeamMember = false;

foreach ($teamMembers as $member) {
    // 1. Check if they are individually assigned
    if (!empty($member['user_id']) && $member['user_id'] == $currentUserId) {
        $isTeamMember = true;
        break;
    }
    // 2. Check if their department is assigned
    if (!empty($member['department_id']) && !empty($currentUserDeptId) && $member['department_id'] == $currentUserDeptId) {
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
                <div id="react-discussion-root" 
                    data-project-id="<?= htmlspecialchars($project['id']) ?>" 
                    data-current-user-id="<?= htmlspecialchars($currentUserId) ?>"
                    data-is-team-member="<?= $isTeamMember ? 'true' : 'false' ?>">
                </div>
            </div>
        </div>
    </div> 
</div> 

<?php include __DIR__ . '/../../../core/views/components/upload-attachment-modal.php'; ?>
<?php include __DIR__ . '/../../../core/views/components/project-team-modal.php'; ?>
<?php include __DIR__ . '/details-partials/scripts.php'; ?>

<!-- 1. Load Supabase JS (Required for Realtime) -->
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

<!-- 2. Load React & ReactDOM -->
<script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>

<!-- 3. Load Babel -->
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<!-- 4. Initialize Supabase Globally with PHP Variables -->
<script>
    const SUPABASE_URL = '<?= $supabaseUrl ?>';
    const SUPABASE_ANON_KEY = '<?= $supabaseAnonKey ?>';
    const supabaseClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
</script>

<!-- 5. Load the standalone React Component File -->
<script src="/src/modules/projects/views/details-partials/DiscussionBoard.jsx" type="text/babel"></script>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>