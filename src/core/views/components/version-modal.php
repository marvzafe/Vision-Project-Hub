<div id="versionModal" class="modal-overlay">
  <div class="modal-content" style="max-width: 500px;">
    
    <button class="modal-close" type="button">&times;</button>
    
    <div style="text-align: center; margin-bottom: 2rem;">
        <i class="ph-fill ph-hexagon" style="font-size: 3rem; color: var(--primary); margin-bottom: 0.5rem;"></i>
        <h2 class="card-title" style="margin-bottom: 0.25rem;">Vision CRM</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Latest Updates & Patch Notes</p>
    </div>

    <div id="patch-notes-container" class="timeline" style="max-height: 400px; overflow-y: auto; padding-right: 1rem;">
        <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
            <i class="ph ph-spinner ph-spin" style="font-size: 2rem;"></i>
            <p style="margin-top: 1rem; font-size: 0.9rem;">Fetching logs from GitHub...</p>
        </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const versionModal = document.getElementById('versionModal');
    const container = document.getElementById('patch-notes-container');
    let dataLoaded = false;

    // Listen for when the modal is opened
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.target.classList.contains('active') && !dataLoaded) {
                fetchPatchNotes();
            }
        });
    });

    if (versionModal) {
        observer.observe(versionModal, { attributes: true, attributeFilter: ['class'] });
    }

async function fetchPatchNotes() {
        try {
            // Pointing directly to the root /src/ folder is usually safest
            const response = await fetch('/src/modules/version/version-controller.php');
            const rawText = await response.text(); 

            console.log("Raw Server Response:", rawText); // The truth will be revealed here!

            const result = JSON.parse(rawText); 
            
            if (result.success) {
                container.innerHTML = ''; // Clear loading spinner
                
                result.data.forEach((note, index) => {
                    // Make the very first commit look like the "Current Version"
                    const isLatest = index === 0;
                    
                    container.innerHTML += `
                        <div class="timeline-item">
                            <div class="timeline-time">${note.date} 
                                ${isLatest ? '<span class="badge completed" style="margin-left: 8px; font-size: 0.6rem;">Latest</span>' : ''}
                            </div>
                            <span class="timeline-user" style="color: var(--primary); font-family: monospace; font-size: 0.85rem;">
                                build-${note.version_hash}
                            </span>
                            <div class="timeline-desc" style="white-space: pre-wrap; font-size: 0.9rem;">${note.message}</div>
                        </div>
                    `;
                });
                dataLoaded = true;
            } else {
                container.innerHTML = `<div style="color: var(--status-attention); text-align: center;">${result.message}</div>`;
            }
        } catch (error) {
            console.error("Fetch or JSON Parse Error:", error);
            container.innerHTML = `<div style="color: var(--status-attention); text-align: center;">Check your console! There is a PHP error.</div>`;
        }
    }
});
</script>