<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<style>
/* Borrowed from dashboard UI */
.dashboard-wrapper {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; height: calc(100vh - 12rem); text-align: center;
}
.ios-card {
    background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.6); border-radius: 24px;
    padding: 3rem 2rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    max-width: 500px; width: 90%;
}
.phone-input-group {
    display: flex; align-items: center; margin-top: 1.5rem; margin-bottom: 1.5rem;
    background: #fff; border: 1px solid #d1d5db; border-radius: 12px; overflow: hidden;
}
.phone-prefix {
    background: #f3f4f6; padding: 0.75rem 1rem; font-weight: 600;
    color: #4b5563; border-right: 1px solid #d1d5db;
}
.phone-input {
    flex: 1; padding: 0.75rem 1rem; border: none; outline: none;
    font-size: 1.1rem; letter-spacing: 1px;
}
.btn-submit {
    background: #3b82f6; color: white; border: none; padding: 0.75rem 2rem;
    border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.2s;
}
.btn-submit:hover { background: #2563eb; }
.error-msg { color: #ef4444; font-size: 0.9rem; margin-bottom: 1rem; }
</style>

<div class="dashboard-wrapper">
    <div class="ios-card">
        <h2 style="margin-bottom: 0.5rem;">Contact Information Required</h2>
        <p style="color: #6b7280;">Please provide your mobile number for project updates and team communication.</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="?action=submit" method="POST">
            <div class="phone-input-group">
                <span class="phone-prefix">+63</span>
                <input type="text" 
                       name="phone_digits" 
                       class="phone-input" 
                       placeholder="9123456789"
                       maxlength="10"
                       pattern="\d{10}"
                       title="Please enter exactly 10 digits"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       required>
            </div>
            <button type="submit" class="btn-submit">Save Phone Number</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>