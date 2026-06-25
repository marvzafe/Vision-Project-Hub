<style>
    .task-body-container { padding: 0 1.25rem 1.25rem 1.25rem; }
    .task-sub-card { margin-bottom: 1rem; padding: 1rem; background: rgba(0, 0, 0, 0.02); border-radius: 16px; border: 1px solid var(--border-color); }
    .task-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 0.5rem; }
    .task-data-grid { display: flex; gap: 1.5rem; width: 100%; align-items: center; font-size: 0.85rem; }
    .task-edit-input { width: 60px; padding: 0.3rem; border-radius: 6px; border: 1px solid var(--border-color); }
    .task-action-btn { margin-left: auto; background: transparent; border: 1px solid var(--border-color); border-radius: 8px; padding: 0.3rem 0.6rem; font-size: 0.75rem; color: var(--text-muted); cursor: pointer; }

    @keyframes taskPulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 transparent; }
        15% { transform: scale(1.02); box-shadow: 0 0 0 3px var(--primary); background-color: rgba(0, 102, 204, 0.05); }
        70% { transform: scale(1.02); box-shadow: 0 0 0 3px var(--primary); background-color: rgba(0, 102, 204, 0.05); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 transparent; background-color: transparent; }
    }
    .task-highlight-pulse {
        animation: taskPulse 2s ease-out;
        border-radius: 16px; 
        position: relative;
        z-index: 10;
    }
    .clickable-task-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .clickable-task-badge:hover {
        background: rgba(0, 102, 204, 0.1) !important;
        border-color: rgba(0, 102, 204, 0.3) !important;
        transform: translateY(-1px);
    }
</style>

<?php 
$categoryTitles = [
    'general_works' => 'General Works',
    'project_progress' => 'Project\'s Progress',
    'finishing_works' => 'Finishing Works'
];
$hasAnyTasks = false;
$taskOriginalIndex = 0; // Tracks the initial backend sequence
?>

<div id="tasks-master-container">
    <?php foreach ($groupedTasks as $categoryKey => $tasks): ?>
        <?php if (!empty($tasks)): ?>
            <?php $hasAnyTasks = true; ?>
            <div class="task-group" id="group-<?= $categoryKey ?>">
                <h3 class="group-title"><?= strtoupper($categoryTitles[$categoryKey]) ?></h3>
                
                <?php foreach ($tasks as $task):
                    $taskOriginalIndex++;
                    $taskStatusClass = 'status-unstarted';
                    $taskBadgeClass  = 'archived'; 
                    $taskIconStyle   = 'filter: grayscale(100%); opacity: 0.6;'; 
                    $taskTitleStyle  = 'color: var(--text-muted); opacity: 0.8;';
                    
                    if ($task['status'] === 'processing') {
                        $taskStatusClass = 'status-processing';
                        $taskBadgeClass  = 'progress';
                        $taskIconStyle   = 'color: var(--status-progress);';
                        $taskTitleStyle  = 'color: var(--text-main);';
                    } elseif ($task['status'] === 'done') {
                        $taskStatusClass = 'status-done';
                        $taskBadgeClass  = 'completed';
                        $taskIconStyle   = 'color: var(--status-completed);';
                        $taskTitleStyle  = 'color: var(--text-main);';
                    }

                    $assigneeName = $task['first_name'] ? $task['first_name'] . ' ' . $task['last_name'] : 'Unassigned';
                ?>
                    <div id="task-folder-<?= $task['id'] ?>" 
                         class="task-folder <?= $taskStatusClass ?>" 
                         data-category="<?= $categoryKey ?>"
                         data-sort-order="<?= htmlspecialchars($task['sort_order'] ?? 0) ?>"
                         data-original-index="<?= $taskOriginalIndex ?>"
                         style="transition: all 0.3s ease;">
                        <div class="task-header toggle-folder-btn" 
                            draggable="true" 
                            data-task-id="<?= $task['id'] ?>" 
                            data-task-title="<?= htmlspecialchars($task['title']) ?>"
                            ondragstart="handleTaskDragStart(event)">
                            <div class="task-title-wrapper">
                                <span class="folder-icon" style="<?= $taskIconStyle ?>">📁</span>
                                <h4 style="<?= $taskTitleStyle ?>"><?= htmlspecialchars($task['title']) ?></h4>
                            </div>
                            
                            <?php if ($isTeamMember): ?>
                                <select class="task-status-dropdown badge <?= $taskBadgeClass ?>" 
                                        data-task-id="<?= $task['id'] ?>" 
                                        data-project-id="<?= $project['id'] ?>"
                                        onclick="event.stopPropagation();" 
                                        style="cursor: pointer; border: 1px dashed currentColor; outline: none; appearance: none; text-align: center; padding: 0.2rem 1.8rem 0.2rem 0.8rem; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22currentColor%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpolyline points=%226 9 12 15 18 9%22%3E%3C/polyline%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1em;">
                                    <option value="not yet started" <?= $task['status'] === 'not yet started' ? 'selected' : '' ?>>Not Yet Started</option>
                                    <option value="processing" <?= $task['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="done" <?= $task['status'] === 'done' ? 'selected' : '' ?>>Done</option>
                                </select>
                            <?php else: ?>
                                <span class="badge <?= $taskBadgeClass ?>" style="border: 1px solid transparent;"><?= ucwords(htmlspecialchars($task['status'])) ?></span>
                            <?php endif; ?>
                        </div>  
                        <div class="task-body" style="display: none; padding-top: 0.5rem;">
                            <?php 
                                $tPercent = (int)($task['progress'] ?? 0);
                                $tWeight  = (float)($task['weight'] ?? 0);
                            ?>
                            <div style="margin-bottom: 1.25rem; padding: 1.25rem; background: rgba(255, 255, 255, 0.5); border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
                                    <div class="progress-wrapper" style="margin-bottom: 1.25rem;">
                                        <div class="progress-track">
                                            <div class="progress-fill" style="width: <?= $tPercent ?>%;"></div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 10px; min-width: max-content;">
                                            <span class="progress-text" style="min-width: unset;"><?= $tPercent ?>%</span>
                                            <div style="width: 1px; height: 12px; background-color: var(--border-color);"></div>
                                            <span title="Task Weight" style="font-size: 0.75rem; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 4px; letter-spacing: 0.5px;">
                                                <i class="ph ph-scales" style="font-size: 0.9rem;"></i> <?= $tWeight ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div style="border-top: 1px solid rgba(0,0,0,0.06); padding-top: 1rem;">
                                    <div id="qty-display-<?= $task['id'] ?>" style="display: flex; gap: 2.5rem; align-items: center; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Installed</span>
                                            <strong id="inst-val-<?= $task['id'] ?>" style="font-size: 1.1rem; color: var(--text-main);"><?= htmlspecialchars((float)($task['installed'] ?? 0)) ?></strong>
                                        </div>    
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Quantity</span>
                                            <strong id="qty-val-<?= $task['id'] ?>" style="font-size: 1.1rem; color: var(--text-main);"><?= htmlspecialchars((float)($task['quantity'] ?? 0)) ?></strong>
                                        </div>
                                        <div style="margin-left: auto; flex-shrink: 0; display: block;">
                                            <button type="button" onclick="toggleTaskQuantitiesEdit('<?= $task['id'] ?>')" style="display: inline-flex; align-items: center; gap: 6px; -webkit-appearance: none; appearance: none; background: rgba(0,0,0,0.03); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.4rem 0.8rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02); color: var(--text-muted); cursor: pointer; font-size: 0.8rem; font-weight: 500;">
                                                <i class="ph ph-pencil-simple" style="font-size: 1rem;"></i> Edit
                                            </button>
                                        </div>
                                    </div>
                                    <div id="qty-edit-<?= $task['id'] ?>" style="display: none; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Installed</span>
                                            <input type="number" id="input-inst-<?= $task['id'] ?>" value="<?= htmlspecialchars((float)($task['installed'] ?? 0)) ?>" style="width: 80px; padding: 0.4rem 0.6rem; border-radius: 8px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.8); outline: none; font-size: 0.95rem;">
                                        </div>
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Quantity</span>
                                            <input type="number" id="input-qty-<?= $task['id'] ?>" value="<?= htmlspecialchars((float)($task['quantity'] ?? 0)) ?>" style="width: 80px; padding: 0.4rem 0.6rem; border-radius: 8px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.8); outline: none; font-size: 0.95rem;">
                                        </div>
                                        <div style="margin-left: auto; display: flex; gap: 8px;">
                                            <button type="button" onclick="toggleTaskQuantitiesEdit('<?= $task['id'] ?>')" class="action-btn" style="background: transparent; border: 1px solid var(--border-color); border-radius: 8px; padding: 0.4rem 0.8rem;">Cancel</button>
                                            <button type="button" onclick="saveTaskQuantities('<?= $project['id'] ?>', '<?= $task['id'] ?>')" class="btn-primary" style="padding: 0.4rem 1rem; border-radius: 8px; font-size: 0.85rem; box-shadow: 0 2px 6px rgba(0,102,204,0.2);">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="file-list" style="margin-bottom: 1.5rem;">
                                <?php if (!empty($task['description'])): ?>
                                    <li class="file-item" style="background: transparent; border: none; padding: 0 0.5rem 1rem 0.5rem; margin-bottom: 0; box-shadow: none;">
                                        <div class="file-info">
                                            <span style="color: var(--text-main); font-size: 0.95rem; line-height: 1.5;"><?= nl2br(htmlspecialchars($task['description'])) ?></span>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                <li class="file-item">
                                    <div class="file-icon" style="color: var(--text-muted); font-size: 1.5rem;"><i class="ph ph-user-circle"></i></div>
                                    <div class="file-info">
                                        <span class="file-name" style="font-size: 0.95rem;">Assigned to: <?= htmlspecialchars($assigneeName) ?></span>
                                        <?php if (!empty($task['deadline'])): ?>
                                            <span class="file-size">Deadline: <?= date('M d, Y', strtotime($task['deadline'])) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            </ul>

                            <div>
                                <h5 style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 0.75rem; padding-left: 0.5rem;">Attachments</h5>
                                <?php if (!empty($attachments)): ?>
                                    <ul class="file-list" style="margin-bottom: 1rem;">
                                        <?php foreach ($attachments as $file): ?>
                                            <li class="file-item">
                                                <div class="file-icon" style="color: var(--primary); font-size: 1.4rem;"><i class="ph ph-paperclip"></i></div>
                                                <div class="file-info">
                                                    <a href="<?= htmlspecialchars($file['file_url']) ?>" target="_blank" class="file-name" style="text-decoration: none; color: var(--text-main); transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-main)'">
                                                        <?= htmlspecialchars($file['file_name']) ?>
                                                    </a>
                                                    <span class="file-size">
                                                        <?= round($file['file_size'] / 1024, 2) ?> KB • Uploaded by <?= htmlspecialchars($file['first_name'] ?? 'Unknown') ?> on <?= date('M d, Y', strtotime($file['uploaded_at'])) ?>
                                                    </span>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <button type="button" class="btn-upload-trigger" data-modal-target="uploadAttachmentModal" data-task-id="<?= $task['id'] ?>"
                                        style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 1.2rem; border: 1.5px dashed var(--border-color); border-radius: 16px; background: rgba(0,0,0,0.015); color: var(--text-muted); font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease;"
                                        onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)'; this.style.background='rgba(0, 102, 204, 0.02)';" 
                                        onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='var(--text-muted)'; this.style.background='rgba(0,0,0,0.015)';">
                                    <i class="ph ph-plus" style="font-size: 1.2rem; font-weight: bold;"></i> Upload Attachment
                                </button>
                            </div>
                        </div>
                    </div> 
                <?php endforeach; ?>
            </div> 
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (!$hasAnyTasks): ?>
        <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No tasks created yet.</p>
    <?php endif; ?>
</div>