document.addEventListener('DOMContentLoaded', () => {
    const profileTrigger = document.querySelector('[data-dropdown-toggle="profile-menu"]');
    const profileMenu = document.getElementById('profile-menu');

    if (profileTrigger && profileMenu) {
        // Toggle the menu when the profile button is clicked
        profileTrigger.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevents the document click listener from firing immediately
            profileMenu.classList.toggle('active');
        });

        // Close the menu if the user clicks anywhere else on the screen
        document.addEventListener('click', (e) => {
            if (!profileTrigger.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('active');
            }
        });
    }
});
