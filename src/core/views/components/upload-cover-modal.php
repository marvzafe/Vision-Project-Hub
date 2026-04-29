<div id="uploadCoverModal" class="modal-overlay">
  <div class="modal-content" style="max-width: 450px;">
    
    <button type="button" class="modal-close">&times;</button>
    <h2 class="card-title" style="margin-bottom: 1.5rem;">Upload Cover Photo</h2>

    <div id="modal-drop-zone" class="upload-area" style="margin-bottom: 1rem;">
        <div class="upload-icon" style="color: var(--text-muted);">📁</div>
        <p style="color: var(--text-main); font-weight: 600; margin-bottom: 0.25rem;">Drag & Drop your image here</p>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">or</p>
        <button type="button" class="btn btn-outline" onclick="document.getElementById('modal_cover_file').click()">Browse Files</button>
        
        <input type="file" id="modal_cover_file" style="display: none;" accept="image/*">
    </div>

    <img id="modal_cover_preview" style="display: none; width: 100%; height: 250px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem; border: 1px solid var(--border-color);">

    <button type="button" id="btn-confirm-cover" class="btn btn-primary" style="width: 100%; display: none;">Confirm Photo</button>

  </div>
</div>