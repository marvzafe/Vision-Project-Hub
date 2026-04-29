<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<link rel="stylesheet" href="/../../assets/css/file-manager.css">

<div class="container">
    <h1 class="title">File Manager</div>
    <div class="fm-toolbar">
            <div class="fm-controls">
                <div class="fm-search-wrapper">
                    <span class="fm-search-icon">🔍</span>
                    <input type="text" class="fm-search-input global-search-input" 
                        placeholder="Search files..." 
                        data-search-table="task_attachments" 
                        data-results-container="file-search-results">
                    
                    <div id="file-search-results" class="search-results-dropdown" style="position: absolute; top: 100%; left: 0; width: 100%; z-index: 10; margin-top: 0.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"></div>
                </div>

                <button class="fm-view-btn active" id="btn-grid" title="Grid View">𐄹</button>
                <button class="fm-view-btn" id="btn-list" title="List View">☰</button>
            </div>
        </div>
        <div>
    </div>

    <div class="fm-grid" id="fm-container">
        
        <?php if ($viewMode === 'projects'): ?>
            <?php if (empty($folders)): ?>
                <p style="color: var(--text-muted); padding: 1rem;">No project folders found.</p>
            <?php else: ?>
                <?php foreach ($folders as $folder): ?>
                    <a href="file-controller.php?project_id=<?= $folder['id'] ?>" class="fm-item">
                        <div class="fm-icon">📁</div>
                        <div class="fm-details">
                            <span class="fm-title"><?= htmlspecialchars($folder['name']) ?></span>
                            <span class="fm-meta">PRJ-<?= str_pad($folder['id'], 3, '0', STR_PAD_LEFT) ?> • <?= $folder['file_count'] ?> files</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php elseif ($viewMode === 'tasks'): ?>
  <?php foreach ($taskFolders as $folder): ?>
                <a href="file-controller.php?project_id=<?= $project['id'] ?>&task_id=<?= $folder['id'] ?>" class="fm-item">
                    <div class="fm-icon">📂</div>
                    <div class="fm-details">
                        <span class="fm-title"><?= htmlspecialchars($folder['title']) ?></span>
                        <span class="fm-meta"><?= htmlspecialchars($folder['task_category'] ?? 'Uncategorized') ?> • <?= $folder['file_count'] ?> files</span>
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
<div class="fm-fixed-breadcrumbs">
        <a href="file-controller.php">📁 File Manager</a>
        
        <?php if ($viewMode === 'tasks' || $viewMode === 'task_files'): ?>
            <span style="color: var(--text-muted);">/</span> 
            <a href="file-controller.php?project_id=<?= $project['id'] ?>"><?= htmlspecialchars($project['name']) ?></a>
        <?php endif; ?>
        
        <?php if ($viewMode === 'task_files'): ?>
            <span style="color: var(--text-muted);">/</span> 
            <span style="color: var(--text-main);"><?= htmlspecialchars($task['title']) ?></span>
        <?php endif; ?>
    </div>

</div>
<?php 
// Helper function to keep the HTML clean since we render files in two different places
function renderFileCard($file) {
    $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
    $icon = '📄'; 
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $icon = '🖼️';
    if ($ext === 'pdf') $icon = '📕';
    if (in_array($ext, ['xls', 'xlsx', 'csv'])) $icon = '📊';
    ?>
    <a href="<?= htmlspecialchars($file['file_url']) ?>" target="_blank" class="fm-item">
        <div class="fm-icon"><?= $icon ?></div>
        <div class="fm-details">
            <span class="fm-title" title="<?= htmlspecialchars($file['file_name']) ?>"><?= htmlspecialchars($file['file_name']) ?></span>
            <span class="fm-meta">
                <?= FileService::formatBytes($file['file_size']) ?>
                <?= date('M d, Y', strtotime($file['uploaded_at'])) ?>
            </span>
        </div>
    </a>
<?php } ?>

<script>
// (Keep your existing toggle grid/list script here exactly as it was)
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