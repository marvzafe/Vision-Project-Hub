<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Engine Test</title>
    
    <link rel="stylesheet" href="/assets/css/global.css">
    
    <style>
        body { padding: 3rem; background-color: #f4f7f6; font-family: sans-serif; }
        .test-card { background: white; padding: 2rem; border-radius: 12px; max-width: 500px; margin: 0 auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; }
        .results-box { position: absolute; top: 100%; left: 0; width: 100%; background: white; border-radius: 6px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 50; overflow: hidden; }
        .debug-panel { margin-top: 2rem; padding: 1rem; background: #e2e8f0; border-radius: 6px; font-family: monospace; font-size: 0.9rem; }
    </style>
</head>
<body>

    <div class="test-card">
        <h2 style="margin-bottom: 1.5rem;">Isolated Search Test</h2>
        
        <div style="position: relative; margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Search Users Table:</label>
            
            <input type="text" 
                   id="test-search-box"
                   class="form-control global-search-input" 
                   placeholder="Type a name (e.g., Marv)..." 
                   autocomplete="off"
                   data-search-table="users" 
                   data-results-container="test-results" 
                   data-hidden-input="test-hidden-id">
            
            <div id="test-results" class="results-box"></div>
        </div>

        <label style="display: block; margin-top: 1.5rem; font-weight: bold; color: #dc2626;">Hidden UUID that will be sent to Database:</label>
        <input type="text" id="test-hidden-id" class="form-control" style="background: #fee2e2; border-color: #f87171;" readonly placeholder="UUID will appear here...">

    </div>

    <div class="test-card" style="margin-top: 1rem;">
        <h3>Diagnostics Checklist</h3>
        <ul style="margin-top: 0.5rem; line-height: 1.6;">
            <li>1. Open your browser's Developer Tools (F12).</li>
            <li>2. Go to the <strong>Console</strong> tab. Are there any red errors?</li>
            <li>3. Go to the <strong>Network</strong> tab.</li>
            <li>4. Type "Marv" in the box above.</li>
            <li>5. Did a file named <code>search-controller.php</code> appear in the Network tab?</li>
        </ul>
    </div>

    <script src="/assets/js/global-modals.js"></script>

</body>
</html>