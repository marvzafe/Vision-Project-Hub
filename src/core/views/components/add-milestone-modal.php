<div id="addMilestoneModal" class="modal-overlay">
  <div class="modal-content" style="max-width: 600px; padding: 2.5rem;">
    
    <button class="modal-close" type="button">&times;</button>
    
    <!-- Polished Header -->
    <div style="margin-bottom: 2rem;">
        <h2 class="card-title" style="margin-bottom: 0.25rem; display: flex; align-items: center; gap: 8px;">
            <i class="ph-fill ph-flag-banner" style="color: var(--primary);"></i> Add Milestone
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Create a new task and assign it to a timeline category.</p>
    </div>

    <form id="custom-milestone-form">
      
      <!-- Grouped Form Fields -->
      <div class="task-group" style="background: rgba(0,0,0,0.02); padding: 1.5rem; border-radius: 16px; border: 1px solid var(--border-color);">
          
          <div class="form-group">
            <label class="form-label" style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Milestone Name</label>
            <input type="text" name="title" id="ms_title" class="form-control" required placeholder="e.g., Pour Foundation" style="background: #ffffff;">
          </div>

          <div class="form-group">
            <label class="form-label" style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Category</label>
            <select name="task_category" id="ms_category" class="form-control" required style="background: #ffffff;">
                <option value="general_works">General Works</option>
                <option value="project_progress">Project's Progress</option>
                <option value="finishing_works">Finishing Works</option>
            </select>
          </div>

          <!-- FIX: Added align-items: flex-end to force perfect bottom alignment -->
          <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: flex-end;">
            
            <div class="form-group" style="margin-bottom: 0; flex: 1;">
              <label class="form-label" style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Assignee</label>
              
              <!-- FIX: Removed .search-wrapper class to prevent weird flex stretching -->
              <div style="position: relative; width: 100%;">
                  <input type="text" 
                        class="form-control global-search-input" 
                        placeholder="Type a name..." 
                        autocomplete="off"
                        data-search-table="users" 
                        data-results-container="assignee-results" 
                        data-hidden-input="task_assignee_id"
                        style="background: #ffffff;">
                        
                  <input type="hidden" name="assignee_id" id="task_assignee_id">

                  <div id="assignee-results" class="search-results-dropdown"></div>
              </div>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1;">
              <label class="form-label" style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Deadline</label>
              <input type="datetime-local" name="deadline" id="ms_deadline" class="form-control" style="background: #ffffff;">
            </div>

          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Description (Optional)</label>
            <textarea name="description" class="form-control" rows="2" style="background: #ffffff;" placeholder="Add specific requirements or notes..."></textarea>
          </div>

      </div>

      <!-- Action Buttons -->
      <div class="form-actions" style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
          <button type="button" class="btn btn-outline" style="padding: 0.8rem 1.5rem; border-radius: 12px; width: auto;" onclick="this.closest('.modal-overlay').classList.remove('active')">Cancel</button>
          <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem; border-radius: 12px; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; width: auto;">
              <i class="ph-fill ph-check-circle"></i> Add to Timeline
          </button>
      </div>
    </form>

  </div>
</div>