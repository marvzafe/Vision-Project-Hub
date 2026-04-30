<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<div class="container">
    <h1 class="title">Projects Overview</h1>
  </header>

  <div class="card">
    <div class="list-toolbar">
      <div class="search-wrapper list-search">
        <input type="text" id="projectSearch" class="search-input" placeholder="Search projects or locations...">
        <ul id="searchResults" class="search-results-dropdown hidden-item" style="display:block;"></ul>
      </div>
      
      <div class="toolbar-actions">
        <div class="view-toggles">
          <button id="btnTableView" class="btn-toggle active">Table</button>
          <button id="btnCardView" class="btn-toggle">Cards</button>
        </div>
        <a href="/src/modules/projects/project-controller.php?action=create" class="btn-primary">
          Create New Project
        </a>
      </div>
    </div>

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
              <div class="lead-wrapper">
              <span class="status-dot <?php echo htmlspecialchars($project['lead_status']); ?>"></span> 
              <?php echo htmlspecialchars($project['lead_name']); ?>
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
                <span class="badge <?php echo htmlspecialchars($project['badge_class']); ?>">
                    <?php echo htmlspecialchars($project['badge_text']); ?>
                </span>
            </td>
            <td><?php echo htmlspecialchars($project['last_updated']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div id="cardView" class="project-grid hidden-item">
      <?php foreach ($projects as $project): ?>
      <div class="project-card">
        <div class="project-card-cover" style="background-image: url('<?php echo htmlspecialchars($project['cover_photo']); ?>');"></div>
        <div class="project-card-body">
          <div class="project-card-header">
            <h3 class="project-card-title">
              <a href="/src/modules/projects/project-controller.php?action=view&id=<?php echo $project['id']; ?>">
                <?php echo htmlspecialchars($project['name']); ?>
              </a>
            </h3>
            <span class="badge <?php echo htmlspecialchars($project['badge_class']); ?>">
              <?php echo htmlspecialchars($project['badge_text']); ?>
            </span>
          </div>
          <p class="project-card-location"><?php echo htmlspecialchars($project['location']); ?></p>
          
          <div class="project-card-footer">
            <div class="lead-wrapper">
              <span class="status-dot <?php echo htmlspecialchars($project['lead_status']); ?>"></span> 
              <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars($project['lead_name']); ?></span>
            </div>
            <div class="progress-wrapper">
              <div class="progress-track">
                <div class="progress-fill" style="width: <?php echo $project['progress']; ?>%;"></div>
              </div>
              <span class="progress-text"><?php echo $project['progress']; ?>%</span>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. View Toggling Logic
    const btnTable = document.getElementById('btnTableView');
    const btnCard = document.getElementById('btnCardView');
    const viewTable = document.getElementById('tableView');
    const viewCard = document.getElementById('cardView');

    btnTable.addEventListener('click', () => {
        viewTable.classList.remove('hidden-item');
        viewCard.classList.add('hidden-item');
        btnTable.classList.add('active');
        btnCard.classList.remove('active');
    });

    btnCard.addEventListener('click', () => {
        viewCard.classList.remove('hidden-item');
        viewTable.classList.add('hidden-item');
        btnCard.classList.add('active');
        btnTable.classList.remove('active');
    });

    // 2. Search Integration using SearchService
    const searchInput = document.getElementById('projectSearch');
    const searchResults = document.getElementById('searchResults');

    // Debounce function to limit API calls while typing
    let timeoutId;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeoutId);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            searchResults.innerHTML = '';
            return;
        }

        timeoutId = setTimeout(async () => {
            try {
                const response = await fetch(`/src/modules/projects/project-controller.php?action=search&q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                searchResults.innerHTML = '';
                
                if (data.length > 0) {
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'dropdown-item';
                        li.style.flexDirection = 'column';
                        li.style.alignItems = 'flex-start';
                        li.innerHTML = `
                            <a href="/src/modules/projects/project-controller.php?action=view&id=${item.id}" style="display:block; width:100%; color:var(--text-main);">
                                <div style="font-weight: 600; margin-bottom: 3px;">${item.title}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">${item.subtitle}</div>
                            </a>
                        `;
                        searchResults.appendChild(li);
                    });
                    searchResults.style.display = 'block';
                } else {
                    searchResults.innerHTML = '<li class="dropdown-item text-muted">No projects found.</li>';
                    searchResults.style.display = 'block';
                }
            } catch (error) {
                console.error("Search failed", error);
            }
        }, 300); // 300ms debounce delay
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});
</script>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>