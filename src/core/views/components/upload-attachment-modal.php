<div id="uploadAttachmentModal" class="modal-overlay">
  <div class="modal-content" style="max-width: 500px;">
    
    <button class="modal-close">&times;</button>
    <h2 class="card-title" style="margin-bottom: 1.5rem;">Upload Attachment</h2>

    <form action="/src/modules/attachments/attachment-controller.php?action=upload_task_file" method="POST" enctype="multipart/form-data">
      
      <input type="hidden" name="task_id" id="modal_upload_task_id" value="">
      <input type="hidden" name="project_id" value="<?= htmlspecialchars($project['id'] ?? '') ?>">

      <div class="form-group" style="margin-bottom: 1rem;">
        <label class="form-label" style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem;">Attachment Name</label>
        <input type="text" name="custom_name" class="form-control" style="width: 100%; padding: 0.6rem; border: 1px solid var(--border-color); border-radius: 6px;" required placeholder="e.g., Floor Plan Draft">
      </div>

      <div class="form-group" style="margin-bottom: 1rem;">
        <label class="form-label" style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem;">Description (Optional)</label>
        <textarea name="description" class="form-control" rows="2" style="width: 100%; padding: 0.6rem; border: 1px solid var(--border-color); border-radius: 6px; font-family: inherit;" placeholder="Brief details about this file..."></textarea>
      </div>

      <div class="form-group" style="margin-bottom: 1rem;">
        <label class="form-label" style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem;">Select File</label>
        <input type="file" name="task_file" class="form-control" required accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
      </div>

      <button type="submit" class="btn-primary" style="width: 100%; padding: 0.8rem; margin-top: 1rem;">Upload File</button>
    </form>

  </div>
</div>