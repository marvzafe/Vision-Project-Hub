<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<link rel="stylesheet" href="/../assets/css/user-registration.css">

<div class="container">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">
                <a href="/src/modules/projects/project-controller.php">← Back to Dashboard</a>
            </p>
  <div class="header">
    <h1 class="title">New Member Registration</h1>
  </div>

  <form action="" method="POST" enctype="multipart/form-data">
    <div class="details-grid">
      
      <div class="card">
        <h2 class="card-title">Personal Information</h2>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="firstName">First Name</label>
            <input type="text" id="firstName" name="first_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="middleName">Middle Name</label>
            <input type="text" id="middleName" name="middle_name" class="form-control">
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label" for="lastName">Last Name</label>
          <input type="text" id="lastName" name="last_name" class="form-control" required>
        </div>

        <h2 class="card-title" style="margin-top: 2rem;">Contact & Department</h2>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-control" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="department_id">Department</label>
          <select id="department_id" name="department_id" class="form-control" required>
              <option value="" disabled selected>Select a department...</option>
              
              <?php foreach ($departments as $dept): ?>
                  <option value="<?php echo htmlspecialchars($dept['department_id']); ?>">
                      <?php echo htmlspecialchars($dept['department_name']); ?>
                  </option>
              <?php endforeach; ?>
              
          </select>
        </div>
      </div>

      <div class="sidebar-column">
        <div class="card">
          <button type="submit" class="btn-primary" style="width: 100%; padding: 0.8rem;">Register Member</button>
        </div>
      </div>

    </div>
  </form>
</div>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>