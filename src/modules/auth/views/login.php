<?php
// /src/modules/auth/views/login.php
// No header included here to prevent the sidebar from loading over the video!
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Login - Vision CRM'); ?></title>
    <link rel="stylesheet" href="/../assets/css/login.css">
    
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>
<body>

    <div class="video-wrapper">
        <video autoplay muted loop playsinline id="bg-video">
            <source src="/../assets/media/login-bg.mp4" type="video/mp4">
        </video>
        <div class="video-overlay"></div>
    </div>

    <div class="login-container">
        
        <div class="login-box">
            
    <div class="logo-wrapper">
                <img src="/../assets/media/vision-logo.png" alt="Vision CRM Logo" class="logo">
                <div class="logo-subtitle">Project Management Hub</div>
            </div>

            <?php if (!empty($error)): ?>
                <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 0.75rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 600;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="login-form">
                <button id="google-login-btn" class="btn-google">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="google-icon">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                        <path fill="none" d="M0 0h48v48H0z"></path>
                    </svg>
                    Sign in with Google
                </button>
            </div>

        </div>

    </div>

    <div class="app-version">
        v1.0.4 (Beta)
    </div>
<script>
const supabaseUrl = 'https://uhldzpysjvetanhskvch.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVobGR6cHlzanZldGFuaHNrdmNoIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzYzMjczMjcsImV4cCI6MjA5MTkwMzMyN30.A33GEEwTRVlrqZo3gHWR2qtiinFJRGIs4hQd0MaAnvY'; // Use your real key here
const supabaseClient = window.supabase.createClient(supabaseUrl, supabaseKey);

async function initializeAuth() {
    // 1. Check if the user just arrived from the PHP logout route
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'logged_out') {
        
        // 2. Force Supabase to clear the client-side session
        await supabaseClient.auth.signOut();
        
        // 3. Clean up the URL so refreshing the page doesn't run this again
        window.history.replaceState({}, document.title, window.location.pathname + '?action=login');
    }

    // 4. NOW set up the listener (it won't auto-fire now because we signed out)
    supabaseClient.auth.onAuthStateChange(async (event, session) => {
        if (event === 'SIGNED_IN' && session) {
            try {
                const response = await fetch('/src/modules/auth/auth-controller.php?action=set_session', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        user_id: session.user.id,
                        email: session.user.email,
                        name: session.user.user_metadata.full_name,
                        avatar_url: session.user.user_metadata.avatar_url
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    window.location.href = '/';
                }
            } catch (err) {
                console.error("Auth Bridge Failed:", err);
            }
        }
    });
}

// Run the initialization
initializeAuth();

document.getElementById('google-login-btn').addEventListener('click', async () => {
    const { error } = await supabaseClient.auth.signInWithOAuth({
        provider: 'google',
        options: {
            redirectTo: window.location.origin + '/src/modules/auth/auth-controller.php?action=login' 
        }
    });

    if (error) alert("Login failed: " + error.message);
});
</script>



</body>
</html>