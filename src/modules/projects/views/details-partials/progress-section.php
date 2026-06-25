<div style="margin-bottom: 1.5rem; background: rgba(0,0,0,0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; transition: all 0.3s ease;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; cursor: pointer;" onclick="toggleProgressBreakdown()">
        <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.5px;">Overall Progress</span>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 0.75rem; color: var(--text-muted);">View Breakdown</span>
            <i class="ph ph-caret-down" id="progress-caret" style="transition: transform 0.2s; color: var(--text-muted);"></i>
        </div>
    </div>

    <?php
    $flatTasksForSegments = [];
    foreach ($groupedTasks as $categoryKey => $tasksList) {
        if (!empty($tasksList)) {
            foreach ($tasksList as $t) {
                $contrib = (float)($t['contribution'] ?? 0);
                if ($contrib > 0) {
                    $flatTasksForSegments[] = [
                        'title' => $t['title'],
                        'contribution' => $contrib
                    ];
                }
            }
        }
    }
    usort($flatTasksForSegments, function($a, $b) {
        return $b['contribution'] <=> $a['contribution'];
    });

    $segmentColors = ['#0066cc', '#28a745', '#fd7e14', '#e83e8c', '#6f42c1', '#20c997', '#ffc107', '#17a2b8'];
    ?>

    <div class="progress-wrapper" style="margin-bottom: 0;">
        <div class="progress-track" style="position: relative; overflow: hidden; display: flex; align-items: stretch;">
            <div id="normal-progress-fill" class="progress-fill" style="width: <?= htmlspecialchars($project['progress_percentage']) ?>%; transition: opacity 0.3s ease;"></div>
            <div id="segmented-progress-fill" style="display: none; width: 100%; height: 100%;">
                <?php
                $colorIndex = 0;
                foreach ($flatTasksForSegments as $segmentTask):
                    $color = $segmentColors[$colorIndex % count($segmentColors)];
                    $colorIndex++;
                ?>
                    <div class="progress-segment" 
                         style="width: <?= $segmentTask['contribution'] ?>%; height: 100%; background-color: <?= $color ?>; border-right: 1px solid rgba(255,255,255,0.4); cursor: help;" 
                         title="<?= htmlspecialchars($segmentTask['title']) ?> (<?= $segmentTask['contribution'] ?>% contribution)">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <span class="progress-text" style="font-weight: bold;"><?= htmlspecialchars($project['progress_percentage']) ?>%</span>
    </div>

    <div id="progress-breakdown-list" style="display: none; margin-top: 1.25rem; border-top: 1px dashed var(--border-color); padding-top: 1rem; flex-direction: column; gap: 1rem; max-height: 300px; overflow-y: auto;">
        <?php 
        $hasTasksInBreakdown = false;
        foreach ($groupedTasks as $categoryKey => $tasks): 
            if (!empty($tasks)): 
                $hasTasksInBreakdown = true;
                foreach ($tasks as $task): 
                    $tPercent = (int)($task['progress'] ?? 0);
                    $tWeight  = (float)($task['weight'] ?? 0);
                    $tInst    = (float)($task['installed'] ?? 0);
                    $tQty     = (float)($task['quantity'] ?? 0);
        ?>
                    <div class="breakdown-item" style="font-size: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 6px;">
                            <strong style="color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 45%; font-size: 0.8rem;" title="<?= htmlspecialchars($task['title']) ?>">
                                <?= htmlspecialchars($task['title']) ?>
                            </strong>
                            <div style="display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 0.7rem;">
                                <span title="Installed / Quantity">
                                    Inst: <strong style="color: var(--text-main);"><?= $tInst ?></strong> / <?= $tQty ?>
                                </span>
                                <div style="width: 1px; height: 10px; background-color: var(--border-color);"></div>
                                <span title="Task Weight" style="display: flex; align-items: center; gap: 3px; color: var(--primary);">
                                    <i class="ph ph-scales"></i> <?= $tWeight ?>
                                </span>
                            </div>
                        </div>
                        <div class="progress-wrapper" style="margin-bottom: 0;">
                            <div class="progress-track" style="height: 6px; background-color: rgba(0,0,0,0.06);">
                                <div class="progress-fill" style="width: <?= $tPercent ?>%; height: 6px; border-radius: 6px;"></div>
                            </div>
                            <span class="progress-text" style="font-size: 0.7rem; min-width: 35px; text-align: right;"><?= $tPercent ?>%</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <?php if (!$hasTasksInBreakdown): ?>
            <div style="text-align: center; color: var(--text-muted); font-size: 0.8rem; padding: 0.5rem 0;">No tasks available for breakdown.</div>
        <?php endif; ?>
    </div>
</div>