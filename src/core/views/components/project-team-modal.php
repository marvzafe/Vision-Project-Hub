<div id="teamMembersModal" class="modal-overlay">
  <div class="modal-content" style="max-width: 450px; padding: 2rem;">
    <button class="modal-close">&times;</button>
    <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Project Team</h2>
    
    <div style="display: flex; flex-direction: column; gap: 0.75rem; max-height: 60vh; overflow-y: auto; padding-right: 0.5rem;">
        <?php if (empty($teamMembers)): ?>
            <p style="color: var(--text-muted); text-align: center; font-size: 0.9rem;">No team members assigned.</p>
        <?php else: ?>
        <?php foreach ($teamMembers as $member): 
    // 1. Define if it is a department
    $isDepartment = !empty($member['department_id']) && empty($member['user_id']);
?>
    <div style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; background: rgba(0,0,0,0.02); border: 1px solid var(--border-color); border-radius: 16px; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
        
        <div style="width: 44px; height: 44px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.08); flex-shrink: 0; position: relative;">
            
            <!-- 2. Conditionally render the correct Avatar or Icon -->
            <?php if ($isDepartment): ?>
                <?= AvatarService::renderDepartmentIcon($member['department_name'] ?? null, '100%', $member['department_id']) ?>
            <?php else: ?>
                <?= AvatarService::renderAvatar($member['avatar_url'] ?? null, $member['first_name'] ?? '', $member['last_name'] ?? '', '100%', $member['user_id'] ?? null) ?>
                <span class="status-dot <?= $member['status_class'] ?? 'offline' ?>" style="position: absolute; bottom: 0; right: 0; width: 12px; height: 12px; border: 2px solid #fff; border-radius: 50%; z-index: 2;"></span>
            <?php endif; ?>

        </div>
        
        <div style="flex-grow: 1; min-width: 0;">
            <h4 style="margin: 0; font-size: 0.95rem; color: var(--text-main); font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <!-- 3. Conditionally display the correct name -->
                <?= htmlspecialchars($isDepartment ? ($member['department_name'] ?? 'Department') : ($member['first_name'] . ' ' . $member['last_name'])) ?>
            </h4>
            
            <div style="display: flex; flex-direction: column; gap: 2px; margin-top: 2px;">
                
                <?php if ($isDepartment && strtolower($member['project_role'] ?? '') === 'team member'): ?>
                    <!-- The "Show Members" Dropdown -->
                    <details class="dept-members-dropdown" data-dept-id="<?= htmlspecialchars($member['department_id']) ?>" style="outline: none;">
                        <summary style="font-size: 0.8rem; color: var(--primary); font-weight: 600; cursor: pointer; user-select: none; list-style-position: inside;">
                            Show Members
                        </summary>
                        <div class="dept-members-list" style="padding-top: 8px; display: flex; flex-direction: column; gap: 8px; padding-left: 8px; border-left: 2px solid var(--border-color); margin-top: 4px;">
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Loading members...</span>
                        </div>
                    </details>
                <?php else: ?>
                    <!-- Display Role Name -->
                    <span style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">
                        <?= htmlspecialchars($member['project_role'] ?? ($isDepartment ? 'Department' : 'Member')) ?>
                    </span>
                <?php endif; ?>
                
                <?php if (!$isDepartment): ?>
                    <span style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?= htmlspecialchars($member['department_name'] ?? 'Unassigned Dept.') ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

    </div>
<?php endforeach; ?>
        <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const deptDropdowns = document.querySelectorAll('.dept-members-dropdown');
    
    deptDropdowns.forEach(dropdown => {
        dropdown.addEventListener('toggle', async (e) => {
            // Only fetch if the dropdown is opening and hasn't been loaded yet
            if (dropdown.open && !dropdown.dataset.loaded) {
                const deptId = dropdown.getAttribute('data-dept-id');
                const listContainer = dropdown.querySelector('.dept-members-list');
                
                try {
                    // Hit the API we created earlier!
                    const response = await fetch(`/src/modules/departments/api-get-department.php?id=${deptId}`);
                    const json = await response.json();
                    
                    if (json.success) {
                        listContainer.innerHTML = ''; // Clear "Loading..."
                        const members = json.data.members || [];
                        
                        if (members.length === 0) {
                            listContainer.innerHTML = '<span style="font-size: 0.75rem; color: var(--text-muted);">No members assigned.</span>';
                        } else {
                            members.forEach(m => {
                                // Create a mini-avatar for each member
                                const avatarHtml = m.avatar_url 
                                    ? `<img src="${m.avatar_url}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">`
                                    : `<div style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold;">${m.first_name.charAt(0)}</div>`;
                                    
                                listContainer.insertAdjacentHTML('beforeend', `
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        ${avatarHtml}
                                        <div style="display: flex; flex-direction: column; line-height: 1.2;">
                                            <span style="font-size: 0.85rem; color: var(--text-main); font-weight: 500;">${m.first_name} ${m.last_name}</span>
                                            <span style="font-size: 0.7rem; color: var(--text-muted);">${m.company_role || 'Member'}</span>
                                        </div>
                                    </div>
                                `);
                            });
                        }
                        
                        // Mark as loaded so it doesn't fetch again on next click
                        dropdown.dataset.loaded = 'true';
                    } else {
                        throw new Error(json.message || 'Failed to load');
                    }
                } catch (err) {
                    console.error(err);
                    listContainer.innerHTML = '<span style="font-size: 0.75rem; color: var(--status-attention);">Error loading members.</span>';
                }
            }
        });
    });
});
</script>