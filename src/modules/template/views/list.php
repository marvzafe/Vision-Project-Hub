<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<div class="container scrolling-wrapper">
  
  <div class="card toolbar-container" style="padding-bottom: 1.5rem;">
    <div class="list-toolbar" style="margin-bottom: 0;">
      <h1 class="toolbar-title">Task Templates</h1>
      
      <div class="toolbar-actions">
        <div class="search-wrapper list-search">
          <i class="ph ph-magnifying-glass search-icon-static"></i>
          <input type="text" id="templateSearch" class="search-input" placeholder="Search templates..." autocomplete="off">
        </div>
        
        <a href="#" class="btn-primary" title="Create New Template">
          <i class="ph ph-plus"></i> <span class="toggle-text">New Template</span>
        </a>
      </div>
    </div>
  </div>

  <div class="card content-container">
    <?php if (empty($groupedTemplates)): ?>
        <p class="text-muted">No templates found.</p>
    <?php else: ?>
        
        <?php foreach ($groupedTemplates as $materialCategory => $materials): ?>
          
          <div class="category-section" style="margin-bottom: 2rem;">
              <h3 class="group-title" style="font-size: 1.1rem; color: var(--primary); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(0, 102, 204, 0.1);">
                  <?= htmlspecialchars(ucwords(str_replace('_', ' ', $materialCategory))); ?>
              </h3>

              <div class="task-group" style="margin-bottom: 0;">
                
                <?php foreach ($materials as $materialName => $tasks): ?>
                    <div class="task-folder">
                      
                      <div class="task-header" onclick="toggleFolder(this)">
                        <div class="task-title-wrapper">
                          <i class="ph ph-folder-open folder-icon text-primary"></i>
                          <h4>
                              <?= htmlspecialchars($materialName); ?> 
                              <span class="badge progress" style="margin-left: 8px;"><?= count($tasks); ?> Tasks</span>
                          </h4>
                        </div>
                        <i class="ph ph-caret-down text-muted" style="transition: transform 0.3s ease;"></i>
                      </div>

                      <div class="task-body">
                        <div class="table-responsive">
                          <table>
                            <thead>
                              <tr>
                                <th style="width: 80px;">Order</th>
                                <th>Task Title</th>
                                <th>Task Category</th>
                                <th>Days Offset</th>
                                <th>Weight</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($tasks as $task): ?>
                              <tr>
                                <td class="text-muted"><strong>#<?= htmlspecialchars($task['sort_order'] ?? '0'); ?></strong></td>
                                <td><strong><?= htmlspecialchars($task['title'] ?? 'Untitled Task'); ?></strong></td>
                                <td>
                                    <span class="badge progress">
                                        <?= htmlspecialchars($task['category'] ?? 'Uncategorized'); ?>
                                    </span>
                                </td>
                                <td>+<?= htmlspecialchars($task['days_offset'] ?? '0'); ?> Days</td>
                                <td>
                                  <div class="progress-wrapper">
                                    <div class="progress-track">
                                      <div class="progress-fill" style="width: <?= htmlspecialchars($task['weight'] ?? '0'); ?>%;"></div>
                                    </div>
                                    <span class="progress-text"><?= htmlspecialchars($task['weight'] ?? '0'); ?>%</span>
                                  </div>
                                </td>
                              </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      
                    </div>
                <?php endforeach; ?>
                
              </div>
          </div>
        <?php endforeach; ?>
        
    <?php endif; ?>
  </div>
</div>

<script>
function toggleFolder(headerElement) {
    const body = headerElement.nextElementSibling;
    const caret = headerElement.querySelector('.ph-caret-down');
    const icon = headerElement.querySelector('.folder-icon');
    
    if (body.style.display === 'block') {
        body.style.display = 'none';
        caret.style.transform = 'rotate(0deg)';
        icon.classList.remove('ph-folder-open');
        icon.classList.add('ph-folder');
    } else {
        body.style.display = 'block';
        caret.style.transform = 'rotate(180deg)';
        icon.classList.remove('ph-folder');
        icon.classList.add('ph-folder-open');
    }
}
</script>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>