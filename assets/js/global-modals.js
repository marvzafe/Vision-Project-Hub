// /public/assets/js/global-modals.js

document.addEventListener('DOMContentLoaded', () => {
  
  // 1. UNIVERSAL OPEN: Find all buttons that open modals
  const modalTriggers = document.querySelectorAll('[data-modal-target]');
  
  modalTriggers.forEach(trigger => {
    trigger.addEventListener('click', (e) => {
      e.preventDefault(); // Prevent link jumping
      
      // Get the ID of the modal this specific button wants to open
      const targetId = trigger.getAttribute('data-modal-target');
      const targetModal = document.getElementById(targetId);
      
      if (targetModal) {
        targetModal.classList.add('active');
      }
    });
  });

  // 2. UNIVERSAL CLOSE (The 'X' buttons)
  const closeButtons = document.querySelectorAll('.modal-close');
  
  closeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      // Find the closest modal wrapper and close it
      const modal = btn.closest('.modal-overlay');
      if (modal) modal.classList.remove('active');
    });
  });

  // 3. UNIVERSAL CLOSE (Clicking the dark background)
  const allModals = document.querySelectorAll('.modal-overlay');
  
  allModals.forEach(modal => {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.remove('active');
      }
    });
  });
});

// 4. UNIVERSAL AJAX FORM SUBMISSIONS
const ajaxForms = document.querySelectorAll('form.ajax-form');

ajaxForms.forEach(form => {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;

    const formData = new FormData(form);
    formData.append('is_ajax', 'true');

    try {
      // Automatically send the data to wherever the form's 'action' attribute points!
      const response = await fetch(form.getAttribute('action'), {
        method: form.getAttribute('method') || 'POST',
        body: formData
      });

      const result = await response.json();

      if (result.success) {
        alert('Success!');
        form.reset();
        form.closest('.modal-overlay').classList.remove('active');
        window.location.reload(); 
      } else {
        alert('Error: ' + result.message);
      }
    } catch (error) {
      console.error(error);
      alert('Network Error.');
    } finally {
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
    }
  });
}); // <--- WE MOVED THIS BRACKET UP HERE TO CLOSE THE AJAX LOOP!

// ==========================================
// UNIVERSAL LIVE SEARCH (Bulletproof Version)
// ==========================================
let searchTimeout;

// 1. Listen to the whole document for ANY typing
document.addEventListener('input', (e) => {
  
  // 2. Only trigger if the thing they typed in has our special class
  if (e.target.matches('.global-search-input')) {
    clearTimeout(searchTimeout); 
    
    const input = e.target;
    const query = input.value.trim();
    const targetTable = input.getAttribute('data-search-table');
    const resultsContainerId = input.getAttribute('data-results-container');
    const hiddenInputId = input.getAttribute('data-hidden-input');
    
    const resultsContainer = document.getElementById(resultsContainerId);
    const hiddenInput = document.getElementById(hiddenInputId);

    // If they cleared the box, hide the results
    if (query.length < 2) {
      if(resultsContainer) resultsContainer.innerHTML = '';
      if(hiddenInput) hiddenInput.value = ''; 
      return;
    }

    // Wait 300ms after they stop typing
    searchTimeout = setTimeout(async () => {
      try {
        const response = await fetch(`/src/modules/search/search-controller.php?table=${targetTable}&q=${encodeURIComponent(query)}`);
        const result = await response.json();

        if (result.success && resultsContainer) {
          resultsContainer.innerHTML = ''; 
          
          if (result.data.length === 0) {
            resultsContainer.innerHTML = '<div style="padding: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">No results found.</div>';
            return;
          }

          // Build the result dropdown items
          result.data.forEach(item => {
            const resultDiv = document.createElement('div');
            resultDiv.style.cssText = 'padding: 0.75rem; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background 0.2s;';
            resultDiv.onmouseover = () => resultDiv.style.backgroundColor = 'var(--bg-color)';
            resultDiv.onmouseout = () => resultDiv.style.backgroundColor = 'transparent';
            
            resultDiv.innerHTML = `
              <div style="font-weight: 600; color: var(--text-main); font-size: 0.95rem;">${item.title}</div>
              <div style="font-size: 0.8rem; color: var(--text-muted);">${item.subtitle}</div>
            `;

            // IMPORTANT: Use mousedown so it fires before the input box loses focus!
            resultDiv.addEventListener('mousedown', (clickEvent) => {
              clickEvent.preventDefault(); 
              input.value = item.title; 
              if(hiddenInput) hiddenInput.value = item.id; 
              resultsContainer.innerHTML = ''; 
            });

            resultsContainer.appendChild(resultDiv);
          });
        }
      } catch (error) {
        console.error("Search failed:", error);
      }
    }, 300);
  }
});

// 3. Hide dropdowns if they click anywhere else on the screen
document.addEventListener('click', (e) => {
    if (!e.target.matches('.global-search-input')) {
        document.querySelectorAll('[id$="-results"]').forEach(container => {
            container.innerHTML = '';
        });
    }
});
// <--- WE DELETED THE EXTRA CLOSING BRACKETS FROM DOWN HERE!