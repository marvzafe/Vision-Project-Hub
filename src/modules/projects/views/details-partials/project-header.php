<?php if (!empty($project['cover_photo_url'])): ?>
    <div class="project-cover-banner" style="background-image: url('<?= htmlspecialchars($project['cover_photo_url']) ?>');">
        <div class="project-cover-overlay"></div>
    </div>
<?php endif; ?>

<header class="header" id="stickyProjectHeader">
    <div>
        <div class="header-meta">
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">
                <a href="project-controller.php?action=list">← Back to Dashboard</a> / PRJ-<?= str_pad($project['id'], 3, '0', STR_PAD_LEFT) ?>
            </p>
        </div>
        
        <h1 class="title project-title-text" style="margin: 0; line-height: 1.2;"><?= htmlspecialchars($project['name']) ?></h1>
        
        <?php if (!empty($project['project_location'])): ?>
            <div class="header-meta">
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.4rem;">
                    📍 <?= htmlspecialchars($project['project_location']) ?> <span style="opacity: 0.4; margin: 0 8px;">|</span> 📏 Area: <?= htmlspecialchars($project['project_area']) ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap; justify-content: flex-end;">
        <div class="header-meta project-title-text" style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Team:</span>
            <div class="avatar-stack" style="display: flex; align-items: center; justify-content: flex-start; padding-left: 8px;">
                <?php if (empty($teamMembers)): ?>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Unassigned</span>
                <?php else: ?>
                    <?php 
                    $maxVisible = 5;
                    $count = count($teamMembers);
                    $displayed = array_slice($teamMembers, 0, $maxVisible);
                    foreach ($displayed as $member): ?>
                        <div style="width: 32px; height: 32px; margin-left: -8px; border: 2px solid var(--bg-color); position: relative; border-radius: 50%; flex-shrink: 0; z-index: 1;">
                            <?= AvatarService::renderAvatar($member['avatar_url'] ?? null, $member['first_name'] ?? '', $member['last_name'] ?? '', '100%', $member['user_id'] ?? null) ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if ($count > $maxVisible): ?>
                        <div class="avatar" style="width: 32px; height: 32px; margin-left: -8px; border: 2px solid var(--bg-color); background: #e5e5ea; font-size: 0.75rem; color: #636366; z-index: 2; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                            +<?= $count - $maxVisible ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($isTeamMember): ?>
            <div class="header-meta project-title-text" style="width: 1px; height: 28px; background-color: var(--border-color);"></div>
            
            <div style="display: flex; gap: 0.5rem;">
                <a href="project-controller.php?action=edit&id=<?= $project['id'] ?>" class="see-more-btn" style="width: auto; margin-top: 0; padding: 0.4rem 0.85rem; background-color: rgba(0, 102, 204, 0.08); color: var(--primary); border-radius: 12px; display: flex; align-items: center; gap: 4px;">
                   <i class="ph ph-pencil-simple" style="font-size: 1.05rem;"></i> <span class="btn-text">Edit</span>
                </a>
                <button type="button" onclick="confirmDeleteProject(<?= $project['id'] ?>)" class="see-more-btn" style="width: auto; margin-top: 0; padding: 0.4rem 0.85rem; background-color: rgba(255, 59, 48, 0.08); color: var(--status-attention); border-radius: 12px; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                   <i class="ph ph-trash" style="font-size: 1.05rem;"></i> <span class="btn-text">Delete</span>
                </button>
            </div>
        <?php endif; ?>
        
        <?php if ($isTeamMember): ?>
            <select class="project-status-dropdown badge <?= $statusBadgeClass ?>" 
                    data-project-id="<?= $project['id'] ?>"
                    style="cursor: pointer; border: 1px dashed currentColor; outline: none; appearance: none; text-align: center; padding: 0.2rem 1.8rem 0.2rem 0.8rem; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22currentColor%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpolyline points=%226 9 12 15 18 9%22%3E%3C/polyline%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1em;">
                <option value="archived" <?= strtolower($project['status']) === 'archived' ? 'selected' : '' ?>>Archived</option>
                <option value="processing" <?= strtolower($project['status']) === 'processing' ? 'selected' : '' ?>>Processing</option>
                <option value="completed" <?= strtolower($project['status']) === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="past due" <?= strtolower($project['status']) === 'past due' ? 'selected' : '' ?>>Past Due</option>
            </select>
        <?php else: ?>
            <span class="badge <?= $statusBadgeClass ?>" style="border: 1px solid transparent;"><?= $statusText ?></span>
        <?php endif; ?>
    </div>
</header>