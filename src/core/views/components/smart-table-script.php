<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Setup Elements
    const table = document.querySelector('.table-responsive table');
    if (!table) return;

    const thead = table.querySelector('thead');
    const tbody = table.querySelector('tbody');
    const headers = Array.from(thead.querySelectorAll('th'));
    
    const btnColumnToggle = document.getElementById('btnColumnToggle');
    const columnDropdown = document.getElementById('columnDropdown');

    // ==========================================
    // LOCAL STORAGE CONFIGURATION
    // ==========================================
    // Create unique storage keys based on the current page URL 
    // so Projects and Users tables don't overwrite each other's settings
    const pageKey = window.location.pathname.replace(/[^a-zA-Z0-9]/g, '_');
    const lsColKey = 'smart_cols_' + pageKey;
    const lsArchivedKey = 'smart_archived_' + pageKey;

    // Load saved preferences (default to empty object for cols, and true for archived)
    let savedCols = JSON.parse(localStorage.getItem(lsColKey)) || {};
    let showArchived = localStorage.getItem(lsArchivedKey) === null ? true : localStorage.getItem(lsArchivedKey) === 'true';

    // ==========================================
    // DYNAMIC ROW FILTERING (ARCHIVED ITEMS)
    // ==========================================
    // Automatically detect if this specific table has archived items
    const hasArchivedItems = tbody.querySelectorAll('.badge.archived').length > 0;

    function applyRowFilters() {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(row => {
            const isArchived = row.querySelector('.badge.archived') !== null;
            if (isArchived && !showArchived) {
                row.style.display = 'none';
            } else {
                row.style.display = ''; // Resets to default table-row display
            }
        });
    }

    // ==========================================
    // DYNAMIC COLUMN VISIBILITY TOGGLE
    // ==========================================
    if (btnColumnToggle && columnDropdown) {
        
        // Open/Close the Dropdown
        btnColumnToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            columnDropdown.classList.toggle('active');
            btnColumnToggle.classList.toggle('active', columnDropdown.classList.contains('active'));
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!columnDropdown.contains(e.target) && e.target !== btnColumnToggle && !btnColumnToggle.contains(e.target)) {
                columnDropdown.classList.remove('active');
                btnColumnToggle.classList.remove('active');
            }
        });

        // Prevent clicking inside the dropdown from closing it
        columnDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Generate Dropdown Checkboxes dynamically from headers
        headers.forEach((th, index) => {
            const originalText = th.textContent.trim();
            
            // Format Table Header for Sorting visually
            th.innerHTML = `<div class="th-content"><span>${originalText}</span><i class="ph ph-caret-down sort-icon"></i></div>`;
            th.classList.add('sortable');
            th.dataset.index = index;

            // Determine visibility based on localStorage (default to true)
            const isVisible = savedCols[index] !== undefined ? savedCols[index] : true;

            // Build Checkbox Wrapper
            const label = document.createElement('label');
            label.className = 'column-dropdown-item';
            
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.checked = isVisible;
            checkbox.dataset.colIndex = index;
            
            label.appendChild(checkbox);
            label.appendChild(document.createTextNode(originalText));
            columnDropdown.appendChild(label);

            // Apply INITIAL visibility state on page load
            if (!isVisible) {
                th.style.display = 'none';
                tbody.querySelectorAll('tr').forEach(row => {
                    const cells = row.querySelectorAll('td, th');
                    if (cells[index]) cells[index].style.display = 'none';
                });
            }

            // Handle Column Toggling (Live)
            checkbox.addEventListener('change', (e) => {
                const colIdx = parseInt(e.target.dataset.colIndex);
                const isChecked = e.target.checked;
                
                // Save to LocalStorage
                savedCols[colIdx] = isChecked;
                localStorage.setItem(lsColKey, JSON.stringify(savedCols));
                
                // Hide/Show Header
                headers[colIdx].style.display = isChecked ? '' : 'none';
                
                // Hide/Show corresponding cells in all rows
                const rows = tbody.querySelectorAll('tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td, th');
                    if (cells[colIdx]) {
                        cells[colIdx].style.display = isChecked ? '' : 'none';
                    }
                });
            });
        });

        // Inject the "Show Archived" filter ONLY if the table has archived items
        if (hasArchivedItems) {
            const divider = document.createElement('div');
            divider.className = 'dropdown-divider';
            columnDropdown.appendChild(divider);

            const label = document.createElement('label');
            label.className = 'column-dropdown-item';
            label.style.color = 'var(--text-muted)'; // Slight visual distinction

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.checked = showArchived;

            label.appendChild(checkbox);
            label.appendChild(document.createTextNode('Show Archived'));
            columnDropdown.appendChild(label);

            // Handle Filter Toggling
            checkbox.addEventListener('change', (e) => {
                showArchived = e.target.checked;
                // Save to LocalStorage
                localStorage.setItem(lsArchivedKey, showArchived);
                // Apply the filter
                applyRowFilters();
            });

            // Apply initial row filter on page load
            applyRowFilters();
        }
    }

    // ==========================================
    // SMART DATA SORTING ENGINE
    // ==========================================
    let currentSortCol = -1;
    let currentSortAsc = true;

    headers.forEach(th => {
        th.addEventListener('click', () => {
            const colIndex = parseInt(th.dataset.index);
            const isAsc = currentSortCol === colIndex ? !currentSortAsc : true;
            
            // Clear all active sorting classes
            headers.forEach(h => h.classList.remove('asc', 'desc'));

            // Apply active class to clicked header
            th.classList.add(isAsc ? 'asc' : 'desc');
            currentSortCol = colIndex;
            currentSortAsc = isAsc;

            // Fetch rows and sort
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort((a, b) => {
                const aCells = a.querySelectorAll('td, th');
                const bCells = b.querySelectorAll('td, th');
                
                if (!aCells[colIndex] || !bCells[colIndex]) return 0;

                const aText = aCells[colIndex].textContent.trim();
                const bText = bCells[colIndex].textContent.trim();

                // 1. Try Date sorting first (e.g., "May 12, 2026")
                const dA = Date.parse(aText);
                const dB = Date.parse(bText);
                // Ensure it's an actual date format, not just a bare number
                if (!isNaN(dA) && !isNaN(dB) && isNaN(aText) && isNaN(bText)) {
                    return isAsc ? dA - dB : dB - dA;
                }

                // 2. Smart String / Number fallback
                // localeCompare with numeric:true handles "1" vs "10", "75%" vs "100%", etc. perfectly.
                return isAsc ? 
                    aText.localeCompare(bText, undefined, {numeric: true, sensitivity: 'base'}) : 
                    bText.localeCompare(aText, undefined, {numeric: true, sensitivity: 'base'});
            });

            // Re-render sorted rows (this preserves their inline display styles, so filtered rows stay hidden!)
            rows.forEach(row => tbody.appendChild(row));
        });
    });
});
</script>