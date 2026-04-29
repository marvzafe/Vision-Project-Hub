<div id="addMemberModal" class="modal-overlay">
  <div class="modal-content" style="max-width: 600px;">
    
    <button class="modal-close" type="button">&times;</button>
    <h2 class="card-title">Assign Project Team</h2>

    <form id="custom-team-form">
      
      <div class="task-group" style="background-color: #f8fafc; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--border-color);">
          <h3 class="group-title" style="color: var(--primary);">1. Project Lead</h3>
          
          <div class="form-group search-wrapper" style="margin-bottom: 0;">
              <label class="form-label">Select the primary person in charge</label>
              
              <input type="text" 
                     class="form-control global-search-input" 
                     placeholder="Type a name to search..." 
                     autocomplete="off"
                     data-search-table="users" 
                     data-results-container="lead-results" 
                     data-hidden-input="modal_project_lead_id">
                     
              <input type="hidden" name="modal_project_lead_id" id="modal_project_lead_id" required>

              <div id="lead-results" class="search-results-dropdown"></div>
          </div>
      </div>

      <div class="task-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
              <h3 class="group-title" style="margin: 0;">2. Team Members & Roles</h3>
              
              <button type="button" id="add-member-row-btn" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                  + Add Person
              </button>
          </div>
          
          <div id="dynamic-members-list">
              
              <div class="member-row" style="display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 1rem;">
                  
                  <div class="form-group search-wrapper" style="margin-bottom: 0; flex: 1;">
                      <label class="form-label">Team Member</label>
                      <input type="text" 
                             class="form-control global-search-input team-user-search" 
                             placeholder="Type a name to search..." 
                             autocomplete="off"
                             data-search-table="users" 
                             data-results-container="team-results-0" 
                             data-hidden-input="team_hidden_0">
                             
                      <input type="hidden" name="modal_team_user_ids[]" id="team_hidden_0" class="team-hidden-input">
                      <div id="team-results-0" class="search-results-dropdown"></div>
                  </div>
                  
                  <div class="form-group" style="margin-bottom: 0; flex: 1;">
                      <label class="form-label">Assigned Role</label>
                      <input type="text" name="modal_team_roles[]" class="form-control team-role-input" placeholder="e.g., Electrical Engineer">
                  </div>
                  
                  <button type="button" class="btn-icon remove-row-btn" title="Remove Person" style="margin-bottom: 2px;">✖</button>
              
              </div>
          </div>
      </div>

      <div class="form-actions" style="margin-top: 1rem; display: block;">
          <button type="submit" class="btn btn-primary" style="width: 100%;">Save Team Roster</button>
      </div>
    </form>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const addRowBtn = document.getElementById('add-member-row-btn');
    const listContainer = document.getElementById('dynamic-members-list');
    let rowCounter = 1; // Used to generate unique IDs for clones

    if (addRowBtn && listContainer) {
        
        // 1. CLONING LOGIC
        addRowBtn.addEventListener('click', () => {
            const firstRow = listContainer.querySelector('.member-row');
            if (firstRow) {
                const newRow = firstRow.cloneNode(true);
                
                // Find the specific search elements inside the newly cloned row
                const searchInput = newRow.querySelector('.team-user-search');
                const hiddenInput = newRow.querySelector('.team-hidden-input');
                const resultsDropdown = newRow.querySelector('.search-results-dropdown');
                
                // Create unique IDs using our counter
                const newDropdownId = `team-results-${rowCounter}`;
                const newHiddenId = `team_hidden_${rowCounter}`;
                rowCounter++; // Increment so the next one is different
                
                // Apply the new IDs so they don't conflict with the original row
                searchInput.setAttribute('data-results-container', newDropdownId);
                searchInput.setAttribute('data-hidden-input', newHiddenId);
                resultsDropdown.id = newDropdownId;
                hiddenInput.id = newHiddenId;
                
                // Clear out the values (we don't want to copy the typed name!)
                searchInput.value = '';
                hiddenInput.value = '';
                resultsDropdown.innerHTML = ''; // Clear any open dropdown HTML
                newRow.querySelector('.team-role-input').value = '';
                
                // Add the clean, uniquely-ID'd row to the screen
                listContainer.appendChild(newRow);
            }
        });

        // 2. DELETE LOGIC
        listContainer.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.remove-row-btn');
            if (removeBtn) {
                const allRows = listContainer.querySelectorAll('.member-row');
                
                if (allRows.length > 1) {
                    removeBtn.closest('.member-row').remove();
                } else {
                    // Prevent deleting the very last input row, just clear its text
                    const row = removeBtn.closest('.member-row');
                    row.querySelector('.team-user-search').value = '';
                    row.querySelector('.team-hidden-input').value = '';
                    row.querySelector('.team-role-input').value = '';
                }
            }
        });
    }
});
</script>