<?php include __DIR__ . '/../../../core/views/header.php'; ?>

<style>
/* Maintaining the core aesthetic from the dashboard[cite: 2] */
.dashboard-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: calc(100vh - 12rem); /* Centering the message in the viewport */
    text-align: center;
}

.ios-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.6);
    border-radius: 24px;
    padding: 3rem 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    max-width: 500px;
    width: 90%;
}

.success-icon {
    font-size: 4rem;
    color: #10b981; /* Matching the "Completed" status color[cite: 2] */
    margin-bottom: 1.5rem;
}

.success-message {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1d1d1f;
    line-height: 1.4;
    letter-spacing: -0.5px;
}
</style>

<div class="dashboard-wrapper">
    <div class="ios-card">
        <div class="success-icon">
            <i class="ph-fill ph-check-circle"></i>
        </div>
        <h2 class="success-message">
            Your data has been encoded successfully. Thank you very much!
        </h2>
    </div>
</div>

<?php include __DIR__ . '/../../../core/views/footer.php'; ?>