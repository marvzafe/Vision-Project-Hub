<?php include __DIR__ . '/../../../core/views/header.php';
require_once __DIR__ . '/../../../modules/users/user-service.php';
?>

<div class="container">
  <header class="header">
    <h1 class="title">Users Overview</h1>
  </header>

    <div class="card">
    <div class="list-toolbar">
      <div class="search-wrapper list-search">
        <input type="text" id="projectSearch" class="search-input" placeholder="Search users">
        <ul id="searchResults" class="search-results-dropdown hidden-item" style="display:block;"></ul>
      </div>
      
      <div class="toolbar-actions">
 
        <a href="/src/modules/users/user-edit-controller.php?id=<?php echo urlencode($user['user_id']); ?>" class="btn-primary">
          Edit User Table
        </a>
      </div>
    </div>

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
            
            <td>
                <?php echo htmlspecialchars($user['department_name'] ?? 'N/A'); ?>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>