// /public/assets/js/global-modals.js

document.addEventListener('DOMContentLoaded', () => {
  
  // 1. UNIVERSAL OPEN: Find all buttons that open modals
  const modalTriggers = document.querySelectorAll('[data-modal-target]');
  
  modalTriggers.forEach(trigger => {
    trigger.addEventListener('click', (e) => {
      e.preventDefault(); // Prevent link jumping
      
      // Get the ID of the modal this specific button wants to open
      const targetId = trigger.getAttribute('data-modal-target');
      const targetModal = document.getElementById(targetId);
      
      if (targetModal) {
        targetModal.classList.add('active');
      }
    });
  });

  // 2. UNIVERSAL CLOSE (The 'X' buttons)
  const closeButtons = document.querySelectorAll('.modal-close');
  
  closeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      // Find the closest modal wrapper and close it
      const modal = btn.closest('.modal-overlay');
      if (modal) modal.classList.remove('active');
    });
  });

  // 3. UNIVERSAL CLOSE (Clicking the dark background)
  const allModals = document.querySelectorAll('.modal-overlay');
  
  allModals.forEach(modal => {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.remove('active');
      }
    });
  });
});

// 4. UNIVERSAL AJAX FORM SUBMISSIONS
const ajaxForms = document.querySelectorAll('form.ajax-form');

ajaxForms.forEach(form => {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;

    const formData = new FormData(form);
    formData.append('is_ajax', 'true');

    try {
      // Automatically send the data to wherever the form's 'action' attribute points!
      const response = await fetch(form.getAttribute('action'), {
        method: form.getAttribute('method') || 'POST',
        body: formData
      });

      const result = await response.json();

      if (result.success) {
        alert('Success!');
        form.reset();
        form.closest('.modal-overlay').classList.remove('active');
        window.location.reload(); 
      } else {
        alert('Error: ' + result.message);
      }
    } catch (error) {
      console.error(error);
      alert('Network Error.');
    } finally {
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
    }
  });
}); // <--- WE MOVED THIS BRACKET UP HERE TO CLOSE THE AJAX LOOP!

// ==========================================
// UNIVERSAL LIVE SEARCH (Bulletproof Version)
// ==========================================
let searchTimeout;

// 1. Listen to the whole document for ANY typing
document.addEventListener('input', (e) => {
  
  // 2. Only trigger if the thing they typed in has our special class
  if (e.target.matches('.global-search-input')) {
    clearTimeout(searchTimeout); 
    
    const input = e.target;
    const query = input.value.trim();
    const targetTable = input.getAttribute('data-search-table');
    const resultsContainerId = input.getAttribute('data-results-container');
    const hiddenInputId = input.getAttribute('data-hidden-input');
    
    const resultsContainer = document.getElementById(resultsContainerId);
    const hiddenInput = document.getElementById(hiddenInputId);

    // If they cleared the box, hide the results
    if (query.length < 2) {
      if(resultsContainer) resultsContainer.innerHTML = '';
      if(hiddenInput) hiddenInput.value = ''; 
      return;
    }

    // Wait 300ms after they stop typing
    searchTimeout = setTimeout(async () => {
      try {
        const response = await fetch(`/src/modules/search/search-controller.php?table=${targetTable}&q=${encodeURIComponent(query)}`);
        const result = await response.json();

        if (result.success && resultsContainer) {
          resultsContainer.innerHTML = ''; 
          
          if (result.data.length === 0) {
            resultsContainer.innerHTML = '<div style="padding: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">No results found.</div>';
            return;
          }

          // Build the result dropdown items
          result.data.forEach(item => {
            const resultDiv = document.createElement('div');
            resultDiv.style.cssText = 'padding: 0.75rem; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background 0.2s;';
            resultDiv.onmouseover = () => resultDiv.style.backgroundColor = 'var(--bg-color)';
            resultDiv.onmouseout = () => resultDiv.style.backgroundColor = 'transparent';
            
            resultDiv.innerHTML = `
              <div style="font-weight: 600; color: var(--text-main); font-size: 0.95rem;">${item.title}</div>
              <div style="font-size: 0.8rem; color: var(--text-muted);">${item.subtitle}</div>
            `;

            // IMPORTANT: Use mousedown so it fires before the input box loses focus!
            resultDiv.addEventListener('mousedown', (clickEvent) => {
              clickEvent.preventDefault(); 
              input.value = item.title; 
              if(hiddenInput) hiddenInput.value = item.id; 
              resultsContainer.innerHTML = ''; 
            });

            resultsContainer.appendChild(resultDiv);
          });
        }
      } catch (error) {
        console.error("Search failed:", error);
      }
    }, 300);
  }
});

// 3. Hide dropdowns if they click anywhere else on the screen
document.addEventListener('click', (e) => {
    if (!e.target.matches('.global-search-input')) {
        document.querySelectorAll('[id$="-results"]').forEach(container => {
            container.innerHTML = '';
        });
    }
});

// ==========================================
// GLOBAL AVATAR HOVER ENGINE (Unified)
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    // 1. Create the single popup DOM element
    const hoverCard = document.createElement('div');
    hoverCard.id = 'global-profile-hover-card';
    document.body.appendChild(hoverCard);

    let hoverTimeout;
    let currentAvatar = null;
    const entityCache = {}; // Renamed to handle both users and departments

    // 2. Track Mouse Enters globally
    document.body.addEventListener('mouseover', (e) => {
        const avatar = e.target.closest('.global-avatar-hover');
        if (!avatar) return;

        // NEW: Detect which type of entity we are hovering over
        const userId = avatar.getAttribute('data-user-id');
        const deptId = avatar.getAttribute('data-department-id');
        
        if (!userId && !deptId) return;

        currentAvatar = avatar;
        clearTimeout(hoverTimeout);

        // Wait 300ms before popping up
        hoverTimeout = setTimeout(() => {
            if (currentAvatar === avatar) {
                if (userId) showHoverCard(avatar, userId, 'user');
                else if (deptId) showHoverCard(avatar, deptId, 'department');
            }
        }, 300); 
    });

    // 3. Track Mouse Leaves
    document.body.addEventListener('mouseout', (e) => {
        if (currentAvatar) {
            if (e.relatedTarget && (e.relatedTarget === hoverCard || hoverCard.contains(e.relatedTarget) || e.relatedTarget === currentAvatar)) {
                 return;
            }
            hideHoverCard();
        }
    });

    hoverCard.addEventListener('mouseleave', () => hideHoverCard());

    function hideHoverCard() {
        clearTimeout(hoverTimeout);
        hoverCard.classList.remove('active');
        currentAvatar = null;
    }

    // 4. Position, Route, and Populate the Card
    async function showHoverCard(avatarEl, entityId, type) {
        const rect = avatarEl.getBoundingClientRect();
        
        hoverCard.style.top = `${rect.top - 12}px`; 
        hoverCard.style.left = `${rect.left + (rect.width / 2)}px`;

        hoverCard.innerHTML = `<div style="padding: 1rem; text-align: center; color: var(--text-muted);">Loading...</div>`;
        hoverCard.classList.add('active');

        const currentProjectId = document.querySelector('.project-status-dropdown')?.getAttribute('data-project-id') || null;
        
        // Prefix cache key to separate users from departments
        const cacheKey = `${type}-${entityId}` + (currentProjectId ? `-${currentProjectId}` : '');

        if (!entityCache[cacheKey]) {
            try {
                // Route to the correct API based on the type
                let fetchUrl = type === 'user' 
                    ? `/src/modules/users/api-get-profile.php?id=${entityId}`
                    : `/src/modules/departments/api-get-department.php?id=${entityId}`;
                
                if (currentProjectId) fetchUrl += `&project_id=${currentProjectId}`;

                const res = await fetch(fetchUrl);
                const json = await res.json();
                if (json.success) entityCache[cacheKey] = json.data;
            } catch(err) { console.error("Hover Engine Error:", err); }
        }

        // Render Data if still hovering
        if (entityCache[cacheKey] && currentAvatar === avatarEl) {
            if (type === 'user') renderUserCard(entityCache[cacheKey]);
            else renderDepartmentCard(entityCache[cacheKey]);
        }
    }

    // 5A. Render USER Card (Your Original Logic)
    function renderUserCard(user) {
        const avatarContent = user.avatar_url 
            ? `<img src="${user.avatar_url}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">`
            : `<div style="width:100%; height:100%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; border-radius:50%; font-weight:bold;">${user.first_name.charAt(0)}</div>`;

        const projectRoleHtml = user.project_role 
            ? `<div class="hover-card-info-row"><i class="ph ph-briefcase"></i> <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><strong>Project Role:</strong> ${user.project_role}</span></div>` 
            : '';

        hoverCard.innerHTML = `
            <div style="display: flex; gap: 12px; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 12px; margin-bottom: 12px;">
                <div style="width: 50px; height: 50px; flex-shrink: 0; border-radius: 50%; overflow: hidden; border: 1px solid var(--border-color);">${avatarContent}</div>
                <div style="flex: 1; min-width: 0;">
                    <h4 style="margin: 0; font-size: 1rem; color: var(--text-main); font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${user.first_name} ${user.last_name}
                    </h4>
                    <div style="font-size: 0.8rem; color: var(--primary); font-weight: 600; margin-top: 2px;">
                        ${user.role ? user.role.replace(/\b\w/g, l => l.toUpperCase()) : 'Member'}
                    </div>
                </div>
            </div>
            <div>
                ${projectRoleHtml} <div class="hover-card-info-row"><i class="ph ph-buildings"></i> <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${user.department_name || 'Unassigned Dept.'}</span></div>
                <div class="hover-card-info-row"><i class="ph ph-envelope-simple"></i> <a href="mailto:${user.email}" style="color: var(--primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${user.email || 'No Email'}</a></div>
                <div class="hover-card-info-row"><i class="ph ph-phone"></i> ${user.phone || 'No Phone'}</div>
                
                <div style="display: flex; align-items: center; gap: 8px; margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(0,0,0,0.04);">
                    <span class="status-dot ${user.status_class}" style="position: static; box-shadow: none;"></span>
                    <span style="color: var(--text-muted); font-size: 0.8rem; font-weight: 500;">${user.status_text}</span>
                </div>
            </div>
        `;
    }

    // 5B. NEW: Render DEPARTMENT Card
    function renderDepartmentCard(data) {
        const dept = data.department;
        const members = data.members || [];

        // Identify the Manager/Head (The SQL query puts them first anyway, but this is a safe check)
        const manager = members.find(m => m.company_role && (m.company_role.toLowerCase().includes('manager') || m.company_role.toLowerCase().includes('head')));
        const otherMembers = members.filter(m => m !== manager);

        let membersHtml = '';
        if (members.length === 0) {
            membersHtml = `<div style="font-size: 0.85rem; color: var(--text-muted); text-align: center; padding: 10px 0;">No personnel assigned</div>`;
        } else {
            // Build the Manager block
            if (manager) {
                const mgrAvatar = manager.avatar_url 
                    ? `<img src="${manager.avatar_url}" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">`
                    : `<div style="width: 28px; height: 28px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold;">${manager.first_name[0]}</div>`;
                
                membersHtml += `
                    <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Department Head</div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(0,0,0,0.04);">
                        ${mgrAvatar}
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-main);">${manager.first_name} ${manager.last_name}</div>
                            <div style="font-size: 0.75rem; color: var(--primary);">${manager.company_role}</div>
                        </div>
                    </div>
                `;
            }

            // Build the rest of the team
            if (otherMembers.length > 0) {
                membersHtml += `<div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 8px; font-weight: 600;">Team Members (${otherMembers.length})</div>`;
                
                const maxDisplay = 4;
                const displayMembers = otherMembers.slice(0, maxDisplay);
                
                displayMembers.forEach(m => {
                    // Show a badge if they are actively assigned to the current project context
                    const projectBadge = m.project_role 
                        ? `<span style="font-size: 0.65rem; color: var(--primary); background: rgba(0, 102, 204, 0.1); padding: 2px 6px; border-radius: 4px; white-space: nowrap;">${m.project_role}</span>` 
                        : '';

                    membersHtml += `
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px;">
                            <div style="font-size: 0.85rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${m.first_name} ${m.last_name}</div>
                            ${projectBadge}
                        </div>
                    `;
                });

                if (otherMembers.length > maxDisplay) {
                    membersHtml += `<div style="font-size: 0.75rem; color: var(--primary); margin-top: 8px; font-weight: 500;">+ ${otherMembers.length - maxDisplay} more...</div>`;
                }
            }
        }

        hoverCard.innerHTML = `
            <div style="display: flex; gap: 12px; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 12px; margin-bottom: 12px;">
                <div style="width: 50px; height: 50px; flex-shrink: 0; border-radius: 20%; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); font-size: 1.5rem;">
                    🏢
                </div>
                <div style="flex: 1; min-width: 0;">
                    <h4 style="margin: 0; font-size: 1rem; color: var(--text-main); font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${dept.department_name}
                    </h4>
                    <div style="font-size: 0.8rem; color: var(--primary); font-weight: 600; margin-top: 2px;">
                        Department Group
                    </div>
                </div>
            </div>
            <div>
                ${membersHtml}
            </div>
        `;
    }
});