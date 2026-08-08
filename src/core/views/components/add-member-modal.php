<div id="addMemberModal" class="modal-overlay">
  <div class="modal-content" style="max-width: 650px; padding: 2.5rem;">
    
    <button class="modal-close" type="button">&times;</button>
    
    <!-- Polished Header -->
    <div style="margin-bottom: 2rem;">
        <h2 class="card-title" style="margin-bottom: 0.25rem; display: flex; align-items: center; gap: 8px;">
            <i class="ph-fill ph-users-three" style="color: var(--primary);"></i> Assign Project Team
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Search and assign users or entire departments to this project.</p>
    </div>

    <form id="custom-team-form">
      
      <!-- Project Lead Section -->
      <div class="task-group" style="background: rgba(0, 102, 204, 0.03); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(0, 102, 204, 0.1); margin-bottom: 1.5rem;">
          <h3 class="group-title" style="color: var(--primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 6px;">
              <i class="ph-fill ph-star"></i> 1. Project Lead
          </h3>
          
          <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Select the primary person in charge</label>
              
              <!-- Inner wrapper for the search functionality -->
              <div class="search-wrapper" style="position: relative; width: 100%;">
                  <i class="ph ph-magnifying-glass search-icon-static"></i>
                  <input type="text" 
                         class="form-control global-search-input" 
                         placeholder="Type a name to search..." 
                         autocomplete="off"
                         data-search-table="users" 
                         data-results-container="lead-results" 
                         data-hidden-input="modal_project_lead_id"
                         style="background: #ffffff;">
                         
                  <input type="hidden" name="modal_project_lead_id" id="modal_project_lead_id" required>
                  <div id="lead-results" class="search-results-dropdown"></div>
              </div>
          </div>
      </div>

      <!-- Team Members Section -->
      <div class="task-group" style="background: rgba(0,0,0,0.02); padding: 1.5rem; border-radius: 16px; border: 1px solid var(--border-color);">
          
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 1rem;">
              <h3 class="group-title" style="margin: 0; display: flex; align-items: center; gap: 6px; color: var(--text-main);">
                  <i class="ph-fill ph-users"></i> 2. Team Members & Roles
              </h3>
              
              <button type="button" id="add-member-row-btn" style="background: white; color: var(--primary); border: 1px solid var(--border-color); border-radius: 12px; padding: 6px 12px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02);" onmouseover="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 2px 8px rgba(0,102,204,0.1)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                  <i class="ph ph-plus" style="font-weight: bold;"></i> Add Person
              </button>
          </div>
          
          <div id="dynamic-members-list">
              
            <!-- Individual Member Row -->
            <div class="member-row" style="display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 0.75rem; padding: 1rem; background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: all 0.2s ease;">

                <div class="form-group" style="margin-bottom: 0; flex: 1.5;">
                    <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;">Search User or Department</label>
                    
                    <!-- Inner wrapper for the search functionality -->
                    <div class="search-wrapper" style="position: relative; width: 100%;">
                        <i class="ph ph-magnifying-glass search-icon-static"></i>
                        <input type="text" 
                            class="form-control global-search-input team-user-search" 
                            placeholder="Type a name or department..." 
                            autocomplete="off"
                            data-search-table="entities" 
                            data-results-container="team-results-0" 
                            data-hidden-input="team_hidden_0"
                            style="background: var(--bg-color);">
                            
                        <input type="hidden" id="team_hidden_0" class="team-hidden-input">
                        <div id="team-results-0" class="search-results-dropdown"></div>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 0; flex: 1;">
                    <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;">Assigned Role</label>
                    <input type="text" class="form-control team-role-input" placeholder="e.g., Lead Engineer" style="background: var(--bg-color);">
                </div>
                
                <button type="button" class="btn-icon remove-row-btn" title="Remove Member" style="margin-bottom: 2px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,59,48,0.05); color: var(--status-attention); border: 1px solid rgba(255,59,48,0.1); border-radius: 10px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,59,48,0.1)';" onmouseout="this.style.background='rgba(255,59,48,0.05)';">
                    <i class="ph ph-trash" style="font-size: 1.2rem;"></i>
                </button>

            </div>
          </div>
      </div>

      <div class="form-actions" style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
          <button type="button" class="btn btn-outline" style="padding: 0.8rem 1.5rem; border-radius: 12px; width: auto;" onclick="this.closest('.modal-overlay').classList.remove('active')">Cancel</button>
          <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem; border-radius: 12px; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; width: auto;">
              <i class="ph-fill ph-check-circle"></i> Save Team Roster
          </button>
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