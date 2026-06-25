<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<link rel="stylesheet" href="/../../assets/css/file-manager.css">

<div class="container scrolling-wrapper">
    <div class="card toolbar-container" style="padding-bottom: 1.5rem;">
        <div class="list-toolbar" style="margin-bottom: 0;">
            
            <h1 class="toolbar-title">File Manager</h1>
            
            <div class="toolbar-actions">
                <div class="search-wrapper list-search">
                    <i class="ph ph-magnifying-glass search-icon-static"></i>
                    <input type="text" id="projectSearch" class="search-input" placeholder="Search for files, projects, or tasks...">
                    <ul id="searchResults" class="search-results-dropdown hidden-item" style="display:block;"></ul>
                </div>
                
                <div class="view-toggles">
                    <button class="btn-toggle active" id="btn-grid" title="Grid View">
                        <i class="ph ph-squares-four"></i> <span class="toggle-text">Cards</span>
                    </button>
                    <button class="btn-toggle" id="btn-list" title="List View">
                        <i class="ph ph-list-dashes"></i> <span class="toggle-text">List</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div class="card content-container">
        
        <div class="fm-fixed-breadcrumbs">
            <a href="file-controller.php">📁 File Manager</a>
            
            <?php if ($viewMode === 'tasks' || $viewMode === 'task_files'): ?>
                <span style="color: var(--text-muted); margin: 0 0.5rem;">/</span> 
                <a href="file-controller.php?project_id=<?= $project['id'] ?>"><?= htmlspecialchars($project['name']) ?></a>
            <?php endif; ?>
            
            <?php if ($viewMode === 'task_files'): ?>
                <span style="color: var(--text-muted); margin: 0 0.5rem;">/</span> 
                <span style="color: var(--text-main); font-weight: 600;"><?= htmlspecialchars($task['title']) ?></span>
            <?php endif; ?>
        </div>

        <div class="tableView" id="fm-container">
            
            <?php if ($viewMode === 'projects'): ?>
                <?php if (empty($folders)): ?>
                    <p style="color: var(--text-muted); padding: 1rem;">No project folders found.</p>
                <?php else: ?>
                    <?php foreach ($folders as $folder): ?>
                        <a href="file-controller.php?project_id=<?= $folder['id'] ?>" class="task-folder fm-item" style="display: flex; align-items: center; padding: 1rem; text-decoration: none;">
                            <div class="folder-icon fm-icon" style="margin-right: 1rem;">📁</div>
                            <div class="fm-details">
                                <span class="fm-title" style="font-weight: 600; color: var(--text-main); display: block;"><?= htmlspecialchars($folder['name']) ?></span>
                                <span class="fm-meta" style="font-size: 0.85rem; color: var(--text-muted);">PRJ-<?= str_pad($folder['id'], 3, '0', STR_PAD_LEFT) ?> • <?= $folder['file_count'] ?> files</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php elseif ($viewMode === 'tasks'): ?>
                <?php foreach ($taskFolders as $folder): ?>
                    <a href="file-controller.php?project_id=<?= $project['id'] ?>&task_id=<?= $folder['id'] ?>" class="task-folder fm-item" style="display: flex; align-items: center; padding: 1rem; text-decoration: none;">
                        <div class="folder-icon fm-icon" style="margin-right: 1rem;">📂</div>
                        <div class="fm-details">
                            <span class="fm-title" style="font-weight: 600; color: var(--text-main); display: block;"><?= htmlspecialchars($folder['title']) ?></span>
                            <span class="fm-meta" style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($folder['task_category'] ?? 'Uncategorized') ?> • <?= $folder['file_count'] ?> files</span>
                        </div>
                    </a>
                <?php endforeach; ?>

                <?php foreach ($files as $file): ?>
                    <?php renderFileCard($file); ?>
                <?php endforeach; ?>

            <?php elseif ($viewMode === 'task_files'): ?>
                <?php if (empty($files)): ?>
                    <p style="color: var(--text-muted); padding: 1rem;">No files attached to this task yet.</p>
                <?php else: ?>
                    <?php foreach ($files as $file): ?>
                        <?php renderFileCard($file); ?>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php 
function renderFileCard($file) {
    $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
    $icon = '📄'; 
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $icon = '🖼️';
    if ($ext === 'pdf') $icon = '📕';
    if (in_array($ext, ['xls', 'xlsx', 'csv'])) $icon = '📊';
    ?>
    <a href="<?= htmlspecialchars($file['file_url']) ?>" target="_blank" class="file-item fm-item" style="text-decoration: none;">
        <div class="file-icon fm-icon"><?= $icon ?></div>
        <div class="fm-details">
            <span class="file-name fm-title" title="<?= htmlspecialchars($file['file_name']) ?>"><?= htmlspecialchars($file['file_name']) ?></span>
            <span class="file-size fm-meta">
                <?= FileService::formatBytes($file['file_size']) ?> • 
                <?= date('M d, Y', strtotime($file['uploaded_at'])) ?>
            </span>
        </div>
    </a>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('fm-container');
    const btnGrid = document.getElementById('btn-grid');
    const btnList = document.getElementById('btn-list');

    const savedView = localStorage.getItem('fm_view_pref') || 'grid';
    applyView(savedView);

    btnGrid.addEventListener('click', () => applyView('grid'));
    btnList.addEventListener('click', () => applyView('list'));

    function applyView(viewType) {
        if (viewType === 'list') {
            container.classList.remove('fm-grid');
            container.classList.add('fm-list');
            btnList.classList.add('active');
            btnGrid.classList.remove('active');
        } else {
            container.classList.remove('fm-list');
            container.classList.add('fm-grid');
            btnGrid.classList.add('active');
            btnList.classList.remove('active');
        }
        localStorage.setItem('fm_view_pref', viewType);
    }
});
</script>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>