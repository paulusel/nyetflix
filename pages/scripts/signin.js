import api from './api.js';

const signin = {
    async checkAuth() {
        const token = api.getToken();
        if (token) {
            try {
                const result = await api.getMe();
                window.location.href = result.data.profile ? 'home.php' : 'profile.php';
            } catch (error) {
                api.clearToken();
            }
        }
    },

    async handleSignin(event) {
        event.preventDefault();
        const email = document.getElementById('email-input').value;
        const password = document.getElementById('password-input').value;

        try {
            await api.authenticate(email, password);
            const profiles = await api.getAllProfiles();
            if (profiles.profiles && profiles.profiles.length > 1) {
                window.location.href = 'profile.php';
            } else if (profiles.profiles && profiles.profiles.length === 1) {
                const profileResult = await api.setProfile(profiles.profiles[0].profile_id);
                if (profileResult.auth_token) {
                    localStorage.setItem('auth_token', profileResult.auth_token);
                    window.location.href = 'home.php';
                }
                else {
                    throw new Error("Failed to set profile");
                }
            } else {
                throw new Error('No profiles found');
            }
        } catch (error) {
            console.error('Signin error:', error);
            alert(error.message || 'Failed to sign in. Please try again.');
        }
    },

    init() {
        // Check auth status when page loads
        this.checkAuth();

        const form = document.getElementById('signin-form');
        if (form) {
            form.addEventListener('submit', this.handleSignin.bind(this));
        }
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    signin.init();
});
