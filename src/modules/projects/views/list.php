<?php include __DIR__ . '/../../../core/views/header.php'; ?>
<?php require_once __DIR__ . '/../../../core/avatar-service.php'; ?>

<div class="container scrolling-wrapper">
  <div class="card toolbar-container" style="padding-bottom: 1.5rem;">
    <div class="list-toolbar" style="margin-bottom: 0;">
      
      <h1 class="toolbar-title">Projects Overview</h1>
      
      <div class="toolbar-actions">
        <div class="search-wrapper list-search">
          <i class="ph ph-magnifying-glass search-icon-static"></i>

          <input type="text" 
          id="projectSearch" 
          class="search-input global-search-input" 
          placeholder="Search projects or locations..." 
          autocomplete="off"
          data-search-table="projects" 
          data-results-container="searchResults">

<ul id="searchResults" class="search-results-dropdown hidden-item"></ul>

        </div>
        
        <div class="column-toggle-wrapper">
          <button id="btnColumnToggle" class="btn-toggle active" title="Toggle Columns">
              <i class="ph ph-columns"></i> <span class="toggle-text">Columns</span>
          </button>
          <div id="columnDropdown" class="column-dropdown-menu">
              </div>
        </div>
        
        <a href="/src/modules/projects/project-controller.php?action=create" class="btn-primary" title="Create New Project">
          <i class="ph ph-plus"></i> <span class="toggle-text">New Project</span>
        </a>
      </div>

    </div>
  </div>

  <div class="card content-container">
    <div id="tableView" class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>No.</th>
            <th>Project Name</th>
            <th>Location</th>
            <th>Project Lead</th>
            <th>Accomplishment</th>
            <th>Status</th>
            <th>Last Updated</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($projects as $index => $project): ?>
          <tr>
            <td><?php echo $index + 1; ?></td>
            <td>
                <a href="/src/modules/projects/project-controller.php?action=view&id=<?php echo $project['id']; ?>">
                    <strong><?php echo htmlspecialchars($project['name']); ?></strong>
                </a>
            </td>
            <td><?php echo htmlspecialchars($project['location']); ?></td>
            <td>
                <?php 
                $team = $project['team_members'] ?? [];
                $maxVisible = 3;
                $count = count($team);
                ?>
                <div class="avatar-stack" style="display: flex; align-items: center; justify-content: flex-start; padding-left: 8px;">
                    <?php 
                    $displayed = array_slice($team, 0, $maxVisible);
                    foreach ($displayed as $member): ?>
                        <div style="width: 32px; height: 32px; margin-left: -8px; border: 2px solid white; position: relative; border-radius: 50%; flex-shrink: 0; z-index: 1;">
                            <?= AvatarService::renderAvatar($member['avatar_url'] ?? null, $member['first_name'] ?? '', $member['last_name'] ?? '', '32px', $member['user_id']) ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($count > $maxVisible): ?>
                        <div class="avatar" style="width: 32px; height: 32px; margin-left: -8px; border: 2px solid white; background: #e5e5ea; font-size: 0.75rem; color: #636366; z-index: 2; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                            +<?= $count - $maxVisible ?>
                        </div>
                    <?php endif; ?>
                </div>
            </td>
            <td>
              <div class="progress-wrapper">
                <div class="progress-track">
                  <div class="progress-fill" style="width: <?php echo $project['progress']; ?>%;"></div>
                </div>
                <span class="progress-text"><?php echo $project['progress']; ?>%</span>
              </div>
            </td>
            <td>
                <?php 
                    $bText = $project['badge_text'] ?? 'Archived';
                    $bClass = $project['badge_class'] ?? 'archived';
                    if (strtolower($bText) === 'archived') {
                        $bClass = 'archived';
                    }
                ?>
                <span class="badge <?php echo htmlspecialchars($bClass); ?>">
                    <?php echo htmlspecialchars($bText); ?>
                </span>
            </td>
            <td><?php echo htmlspecialchars($project['last_updated']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../../core/views/components/smart-table-script.php'; ?>
<?php include __DIR__ . '/../../../core/views/footer.php'; ?>