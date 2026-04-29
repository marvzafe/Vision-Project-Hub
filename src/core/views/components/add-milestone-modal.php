<div id="addMilestoneModal" class="modal-overlay">
  <div class="modal-content" style="max-width: 500px;">
    
    <button class="modal-close">&times;</button>
    <h2 class="card-title" style="margin-bottom: 1.5rem;">Add Milestone</h2>

    <form id="custom-milestone-form">
      
      <div class="form-group">
        <label class="form-label">Milestone Name</label>
        <input type="text" name="title" id="ms_title" class="form-control" required placeholder="e.g., Pour Foundation">
      </div>

      <div class="form-group">
        <label class="form-label">Category</label>
        <select name="task_category" id="ms_category" class="form-control" required>
            <option value="general_works">General Works</option>
            <option value="project_progress">Project's Progress</option>
            <option value="finishing_works">Finishing Works</option>
        </select>
      </div>

      <div class="form-row">
        <div class="form-group search-wrapper">
          <label class="form-label">Assignee</label>
          
          <input type="text" 
                class="form-control global-search-input" 
                placeholder="Type a name..." 
                autocomplete="off"
                data-search-table="users" 
                data-results-container="assignee-results" 
                data-hidden-input="task_assignee_id">
                
          <input type="hidden" name="assignee_id" id="task_assignee_id">

          <div id="assignee-results" class="search-results-dropdown"></div>
        </div>
          <div class="form-group">
            <label class="form-label">Deadline</label>
            <input type="datetime-local" name="deadline" id="ms_deadline" class="form-control">
          </div>
      </div>

      <div class="form-group">
        <label class="form-label">Description (Optional)</label>
        <textarea name="description" class="form-control" rows="2"></textarea>
      </div>

      <button type="submit" class="btn-primary" style="width: 100%; padding: 0.8rem; margin-top: 1rem;">Add to Timeline</button>
    </form>

  </div>
</div>