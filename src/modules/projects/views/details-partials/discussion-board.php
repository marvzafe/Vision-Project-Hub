<div class="discussion-list" style="margin-bottom: 1.5rem;">
    <?php if (empty($discussions)): ?>
        <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; padding: 1rem 0;">No discussions yet. Start the conversation!</p>
    <?php else: ?>
        <?php foreach ($discussions as $thread): ?>
            <div class="comment-thread" data-id="<?= htmlspecialchars($thread['id']) ?>">
                
                <div class="thread-layout">
                    <div class="thread-spine">
                        <?= AvatarService::renderAvatar($thread['avatar_url'] ?? null, $thread['first_name'] ?? '', $thread['last_name'] ?? '', '40px', $thread['user_id']) ?>
                        <?php if (!empty($thread['replies'])): ?>
                            <div class="thread-line"></div>
                        <?php endif; ?>
                    </div>

                    <div class="thread-content">
                        <div class="comment-header flex-header">
                            <span class="timeline-user"><?= htmlspecialchars($thread['first_name'] . ' ' . $thread['last_name']) ?></span>
                            <span class="comment-meta">· <?= getRelativeTime($thread['created_at']) ?></span>
                            
                            <?php if (!$isTeamMember && $thread['flag_status']): ?>
                                <span class="badge <?= $thread['flag_status'] === 'solved' ? 'completed' : 'attention' ?> badge-right">
                                    <?= $thread['flag_status'] === 'solved' ? 'Solved' : 'Needs Attention' ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div id="comment-display-<?= $thread['id'] ?>">
                            <?php if (!empty(trim($thread['content']))): ?>
                                <p class="comment-body thread-body">
                                    <?= formatDiscussionText($thread['content']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($thread['task_id'])): ?>
                                <div onclick="scrollToTask('<?= $thread['task_id'] ?>')" class="attached-task-display clickable-task-badge" style="margin-top: 10px; display: inline-flex; align-items: center; background: rgba(0, 102, 204, 0.05); padding: 8px 14px; border-radius: 12px; font-size: 0.85rem; color: var(--text-main); border: 1px solid rgba(0, 102, 204, 0.15);">
                                    <span style="color: var(--text-muted); margin-right: 6px;">Attached Task:</span>
                                    <strong><?= htmlspecialchars($thread['task_title'] ?? 'Unknown Task') ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div id="edit-form-<?= $thread['id'] ?>" style="display: none; margin-top: 0.5rem; width: 100%;">
                            <textarea id="edit-input-<?= $thread['id'] ?>" class="comment-input compact-input" style="width: 100%; min-height: 60px;"><?= htmlspecialchars($thread['content']) ?></textarea>
                            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px;">
                                <button type="button" class="btn-primary btn-sm" style="background: transparent; color: var(--text-muted); border: 1px solid var(--border-color);" onclick="toggleEditForm('<?= $thread['id'] ?>')">Cancel</button>
                                <button type="button" class="btn-primary btn-sm" onclick="submitEditComment('<?= $thread['id'] ?>')">Save</button>
                            </div>
                        </div>
                        
                        <div class="comment-action-bar">
                            <button type="button" onclick="toggleReplyForm('<?= $thread['id'] ?>')" class="action-btn always-visible">
                                <i class="ph ph-chat-circle"></i> Reply
                            </button>

                            <?php if ($thread['user_id'] == $currentUserId): ?>
                                <button type="button" onclick="toggleEditForm('<?= $thread['id'] ?>')" class="action-btn">
                                    <i class="ph ph-pencil-simple"></i> Edit
                                </button>
                                <button type="button" onclick="deleteDiscussionComment('<?= $thread['id'] ?>')" class="action-btn" style="color: var(--status-attention);">
                                    <i class="ph ph-trash"></i> Delete
                                </button>
                            <?php endif; ?>

                            <?php if ($isTeamMember): ?>
                                <button type="button" 
                                        onclick="toggleDiscussionFlag('<?= $thread['id'] ?>', 'attention', '<?= $thread['flag_status'] ?>')" 
                                        class="action-btn <?= $thread['flag_status'] === 'attention' ? 'active-attention' : '' ?>">
                                    <i class="ph <?= $thread['flag_status'] === 'attention' ? 'ph-warning-circle-fill' : 'ph-warning-circle' ?>"></i> Attention
                                </button>

                                <button type="button" 
                                        onclick="toggleDiscussionFlag('<?= $thread['id'] ?>', 'solved', '<?= $thread['flag_status'] ?>')" 
                                        class="action-btn <?= $thread['flag_status'] === 'solved' ? 'active-solved' : '' ?>">
                                    <i class="ph <?= $thread['flag_status'] === 'solved' ? 'ph-check-circle-fill' : 'ph-check-circle' ?>"></i> Solved
                                </button>
                            <?php endif; ?>
                        </div>

                        <div id="reply-form-<?= $thread['id'] ?>" class="reply-form-container" style="display: none;">
                            <input type="text" id="reply-input-<?= $thread['id'] ?>" class="comment-input compact-input" placeholder="Post your reply...">
                            <button type="button" class="btn-primary btn-sm" onclick="submitDiscussionComment('<?= $project['id'] ?>', '<?= $thread['id'] ?>')">Reply</button>
                        </div>
                    </div>
                </div>

                <?php if (!empty($thread['replies'])): ?>
                    <div class="replies-container">
                        <?php foreach ($thread['replies'] as $reply): ?>
                            <div class="thread-layout reply-layout">
                                <div class="thread-spine">
                                    <?= AvatarService::renderAvatar($reply['avatar_url'] ?? null, $reply['first_name'] ?? '', $reply['last_name'] ?? '', '40px', $reply['user_id']) ?>
                                </div>
                                <div class="thread-content">
                                    <div class="comment-header flex-header">
                                        <span class="timeline-user"><?= htmlspecialchars($reply['first_name'] . ' ' . $reply['last_name']) ?></span>
                                        <span class="comment-meta">· <?= getRelativeTime($reply['created_at']) ?></span>
                                    </div>
                                    
                                    <div id="comment-display-<?= $reply['id'] ?>">
                                        <?php if (!empty(trim($reply['content']))): ?>
                                            <p class="comment-body thread-body">
                                                <?= formatDiscussionText($reply['content']) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($reply['task_id'])): ?>
                                            <div onclick="scrollToTask('<?= $reply['task_id'] ?>')" class="attached-task-display clickable-task-badge" style="margin-top: 10px; display: inline-flex; align-items: center; background: rgba(0, 102, 204, 0.05); padding: 6px 12px; border-radius: 10px; font-size: 0.8rem; color: var(--text-main); border: 1px solid rgba(0, 102, 204, 0.15);">
                                                <i class="ph ph-link" style="color: var(--primary); margin-right: 6px; font-size: 1rem;"></i>
                                                <strong><?= htmlspecialchars($reply['task_title'] ?? 'Unknown Task') ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div id="edit-form-<?= $reply['id'] ?>" style="display: none; margin-top: 0.5rem; width: 100%;">
                                        <textarea id="edit-input-<?= $reply['id'] ?>" class="comment-input compact-input" style="width: 100%; min-height: 60px;"><?= htmlspecialchars($reply['content']) ?></textarea>
                                        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px;">
                                            <button type="button" class="btn-primary btn-sm" style="background: transparent; color: var(--text-muted); border: 1px solid var(--border-color);" onclick="toggleEditForm('<?= $reply['id'] ?>')">Cancel</button>
                                            <button type="button" class="btn-primary btn-sm" onclick="submitEditComment('<?= $reply['id'] ?>')">Save</button>
                                        </div>
                                    </div>
                                    <div class="comment-action-bar" style="margin-top: 4px;">
                                        <?php if ($reply['user_id'] == $currentUserId): ?>
                                            <button type="button" onclick="toggleEditForm('<?= $reply['id'] ?>')" class="action-btn">
                                                <i class="ph ph-pencil-simple"></i> Edit
                                            </button>
                                            <button type="button" onclick="deleteDiscussionComment('<?= $reply['id'] ?>')" class="action-btn" style="color: var(--status-attention);">
                                                <i class="ph ph-trash"></i> Delete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
            </div>
            <hr class="thread-divider">
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="floating-thread-box">
    <div id="attached-task-badge" style="display: none; align-items: center; background: rgba(0, 102, 204, 0.1); padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; color: var(--primary); margin-bottom: 8px; width: fit-content; border: 1px solid rgba(0, 102, 204, 0.2);">
        <i class="ph ph-link" style="margin-right: 6px;"></i>
        <strong id="attached-task-title" style="margin-right: 12px;">Task Name</strong>
        <button type="button" onclick="removeAttachedTask()" style="border: none; background: transparent; color: var(--status-attention); cursor: pointer; padding: 0; display: flex; align-items: center;">
            <i class="ph ph-x-circle" style="font-size: 1rem;"></i>
        </button>
    </div>

    <div class="chat-input-wrapper drop-zone" 
         id="discussion-drop-zone"
         ondragover="handleDragOver(event)" 
         ondragleave="handleDragLeave(event)" 
         ondrop="handleTaskDrop(event)"
         style="transition: all 0.2s ease; border: 2px dashed transparent;">
        
        <textarea id="main-discussion-input" 
                  class="minimal-chat-input" 
                  placeholder="Type a comment or drag a task here..." 
                  rows="1" 
                  oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
        
        <button class="btn-primary minimal-send-btn" type="button" onclick="submitDiscussionComment('<?= $project['id'] ?>')" title="Post">
            <i class="ph ph-paper-plane-right"></i>
        </button>
    </div>
</div>