<div id="uploadAttachmentModal" class="modal-overlay">
  <div class="modal-content" style="max-width: 550px; padding: 2.5rem;">
    
    <button class="modal-close" type="button">&times;</button>
    
    <!-- Polished Header -->
    <div style="margin-bottom: 2rem;">
        <h2 class="card-title" style="margin-bottom: 0.25rem; display: flex; align-items: center; gap: 8px;">
            <i class="ph-fill ph-paperclip" style="color: var(--primary);"></i> Upload Attachment
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Add files, blueprints, or documents to this specific task.</p>
    </div>

    <form action="/src/modules/attachments/attachment-controller.php?action=upload_task_file" method="POST" enctype="multipart/form-data">
      
      <input type="hidden" name="task_id" id="modal_upload_task_id" value="">
      <input type="hidden" name="project_id" value="<?= htmlspecialchars($project['id'] ?? '') ?>">

      <!-- Grouped Form Fields -->
      <div class="task-group" style="background: rgba(0,0,0,0.02); padding: 1.5rem; border-radius: 16px; border: 1px solid var(--border-color);">
          
          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Attachment Name</label>
            <input type="text" name="custom_name" class="form-control" style="background: #ffffff;" required placeholder="e.g., Floor Plan Draft">
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Description (Optional)</label>
            <textarea name="description" class="form-control" rows="2" style="background: #ffffff;" placeholder="Brief details about this file..."></textarea>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Select File</label>
            <input type="file" name="task_file" class="form-control" required accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx" style="background: #ffffff; padding: 0.5rem;">
          </div>

      </div>

      <!-- Action Buttons -->
      <div class="form-actions" style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
          <button type="button" class="btn btn-outline" style="padding: 0.8rem 1.5rem; border-radius: 12px; width: auto;" onclick="this.closest('.modal-overlay').classList.remove('active')">Cancel</button>
          <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem; border-radius: 12px; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; width: auto;">
              <i class="ph-fill ph-upload-simple"></i> Upload File
          </button>
      </div>

    </form>

  </div>
</div>