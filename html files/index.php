<?php include __DIR__ . '/src/core/views/header.php'; ?> 

  <div class="container">
    <header class="header">
      <h1 class="title">Projects Overview</h1>
      </header>

    <div class="card">
      <h2 class="card-title">Recent Projects</h2>
      
      <div class="table-responsive">
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
            
            <tr>
              <td>1</td>
              <td><a href="src/modules/projects/views/list.php"><strong>Alpha Web Portal</strong></a></td>
              <td>Remote</td>
              <td>
                <div class="lead-wrapper">
                <span class="status-dot active"></span> Sarah Jenkins
                </div>
              </td>
              <td>
                <div class="progress-wrapper">
                  <div class="progress-track">
                    <div class="progress-fill" style="width: 65%;"></div>
                  </div>
                  <span class="progress-text">65%</span>
                </div>
              </td>
              <td><span class="badge progress">In Progress</span></td>
              <td>Today, 10:42 AM</td>
            </tr>

            <tr>
              <td>2</td>
              <td><a href="#"><strong>Backend Migration</strong></a></td>
              <td>New York, NY</td>
              <td>
                <div class="lead-wrapper">
                  <span class="status-dot offline"></span> David Chen
                </div>
              </td>
              <td>
                <div class="progress-wrapper">
                  <div class="progress-track">
                    <div class="progress-fill" style="width: 100%;"></div>
                  </div>
                  <span class="progress-text">100%</span>
                </div>
              </td>
              <td><span class="badge completed">Completed</span></td>
              <td>Oct 22, 2023</td>
            </tr>

            <tr>
              <td>3</td>
              <td><a href="#"><strong>Mobile App Redesign</strong></a></td>
              <td>London, UK</td>
              <td>
                <div class="lead-wrapper">
                  <span class="status-dot busy"></span> Elena Rodriguez
                </div>
              </td>
              <td>
                <div class="progress-wrapper">
                  <div class="progress-track">
                    <div class="progress-fill" style="width: 25%; background-color: var(--status-attention);"></div>
                  </div>
                  <span class="progress-text">25%</span>
                </div>
              </td>
              <td><span class="badge attention">At Risk</span></td>
              <td>Oct 20, 2023</td>
            </tr>

            <tr>
              <td>4</td>
              <td><a href="#"><strong>Security Audit Q4</strong></a></td>
              <td>Berlin, DE</td>
              <td>
                <div class="lead-wrapper">
                  <span class="status-dot active"></span> Marcus Johnson
                </div>
              </td>
              <td>
                <div class="progress-wrapper">
                  <div class="progress-track">
                    <div class="progress-fill" style="width: 90%; background-color: var(--status-solved);"></div>
                  </div>
                  <span class="progress-text">90%</span>
                </div>
              </td>
              <td><span class="badge solved">In Review</span></td>
              <td>Oct 18, 2023</td>
            </tr>

          </tbody>
        </table>
      </div>
    </div>
  </div>

</body>
</html>