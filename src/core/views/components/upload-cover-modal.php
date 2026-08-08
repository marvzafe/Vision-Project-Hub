<div id="uploadCoverModal" class="modal-overlay">
  <div class="modal-content" style="max-width: 500px; padding: 2.5rem;">
    
    <button type="button" class="modal-close">&times;</button>
    
    <!-- Polished Header -->
    <div style="margin-bottom: 2rem;">
        <h2 class="card-title" style="margin-bottom: 0.25rem; display: flex; align-items: center; gap: 8px;">
            <i class="ph-fill ph-image" style="color: var(--primary);"></i> Upload Cover Photo
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Choose a high-quality image to represent this project.</p>
    </div>

    <!-- Drop Zone -->
    <div id="modal-drop-zone" class="upload-area" style="margin-bottom: 1.5rem; padding: 3rem 1.5rem; background: rgba(0,0,0,0.02); border: 2px dashed var(--border-color); border-radius: 16px; text-align: center; transition: all 0.2s ease;">
        <div class="upload-icon" style="color: var(--primary); font-size: 2.5rem; margin-bottom: 1rem;"><i class="ph ph-upload-simple"></i></div>
        <p style="color: var(--text-main); font-weight: 600; margin-bottom: 0.25rem;">Drag & Drop your image here</p>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">or click to browse</p>
        <button type="button" class="btn btn-outline" style="background: #ffffff;" onclick="document.getElementById('modal_cover_file').click()">Browse Files</button>
        
        <input type="file" id="modal_cover_file" style="display: none;" accept="image/*">
    </div>

    <!-- Preview Container -->
    <img id="modal_cover_preview" style="display: none; width: 100%; height: 250px; object-fit: cover; border-radius: 16px; margin-bottom: 1.5rem; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.05);">

    <!-- Action Buttons -->
    <div class="form-actions" style="margin-top: 1rem; display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
        <button type="button" class="btn btn-outline" style="padding: 0.8rem 1.5rem; border-radius: 12px; width: auto;" onclick="this.closest('.modal-overlay').classList.remove('active')">Cancel</button>
        
        <!-- Note: Kept standard display logic to sync with your Javascript -->
        <button type="button" id="btn-confirm-cover" class="btn btn-primary" style="display: none; padding: 0.8rem 2rem; border-radius: 12px; font-size: 0.95rem; width: auto;">
            <i class="ph-fill ph-check-circle" style="vertical-align: middle; margin-right: 6px;"></i> 
            <span style="vertical-align: middle;">Confirm Photo</span>
        </button>
    </div>

  </div>
</div>