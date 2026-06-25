<?php include __DIR__ . '/../../../core/views/header.php';
require_once __DIR__ . '/../../../modules/users/user-service.php';
?>

<div class="container scrolling-wrapper">
  <div class="card toolbar-container" style="padding-bottom: 1.5rem;">
    <div class="list-toolbar" style="margin-bottom: 0;">
      
      <h1 class="toolbar-title">Users Overview</h1>
      
      <div class="toolbar-actions">
        <div class="search-wrapper list-search">
          <i class="ph ph-magnifying-glass search-icon-static"></i>
          <input type="text" id="projectSearch" class="search-input" placeholder="Search users...">
          <ul id="searchResults" class="search-results-dropdown hidden-item" style="display:block;"></ul>
        </div>
        
        <div class="column-toggle-wrapper">
          <button id="btnColumnToggle" class="btn-toggle active" title="Toggle Columns">
              <i class="ph ph-columns"></i> <span class="toggle-text">Columns</span>
          </button>
          <div id="columnDropdown" class="column-dropdown-menu">
              </div>
        </div>
        
        <a href="/src/modules/users/user-edit-controller.php?id=<?php echo urlencode($user['user_id'] ?? ''); ?>" class="btn-primary" title="Edit User">
          <i class="ph ph-pencil-simple"></i> <span class="toggle-text">Edit Table</span>
        </a>
      </div>

    </div>
  </div>

  <div class="card content-container">
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>No.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Department</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $index => $user): ?>
          <tr>
            <td><?php echo $index + 1; ?></td>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <?= AvatarService::renderAvatar($user['avatar_url'] ?? null, $user['first_name'] ?? '', $user['last_name'] ?? '') ?>
                <strong>
                    <?php
                        $middle = !empty($user['middle_name']) ? $user['middle_name'] . ' ' : '';
                        echo htmlspecialchars($user['first_name'] . ' ' . $middle . $user['last_name']); 
                    ?>
                </strong>
              </div>
            </td>
            <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($user['department_name'] ?? 'N/A'); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../../core/views/components/smart-table-script.php'; ?>
<?php include __DIR__ . '/../../../core/views/footer.php'; ?>