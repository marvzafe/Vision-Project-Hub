<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<style>
/* Dashboard UI layout constraints[cite: 7] */
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
.success-icon { font-size: 4rem; color: #10b981; margin-bottom: 1.5rem; }
.success-message { font-size: 1.5rem; font-weight: 700; color: #1d1d1f; line-height: 1.4; }
.btn-continue {
    display: inline-block; margin-top: 1.5rem; background: #1d1d1f;
    color: white; padding: 0.75rem 2rem; border-radius: 12px;
    text-decoration: none; font-weight: 600; transition: 0.2s;
}
.btn-continue:hover { background: #333336; }
</style>

<div class="dashboard-wrapper">
    <div class="ios-card">
        <div class="success-icon">
            <i class="ph-fill ph-check-circle"></i>
        </div>
        <h2 class="success-message">
            Thank you! Your phone number has been updated successfully. You may close this tab now.
        </h2>

    </div>
</div>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>