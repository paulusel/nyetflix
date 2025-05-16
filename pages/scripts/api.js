const API_BASE_URL = '/nyetflix/api';

const TOKEN_KEY = 'auth_token';

const api = {
    getToken() {
        return localStorage.getItem(TOKEN_KEY);
    },

    setToken(token) {
        localStorage.setItem(TOKEN_KEY, token);
    },

    clearToken() {
        localStorage.removeItem(TOKEN_KEY);
    },

    async request(endpoint, data = null) {
        const headers = {
            'Content-Type': 'application/json'
        };

        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const options = {
            method : 'POST',
            headers,
            body: data ? JSON.stringify(data) : null
        };

        try {
            const response = await fetch(`${API_BASE_URL}/${endpoint}`, options);
            const result = await response.json();

            if (!result.ok) {
                throw new Error(result.message || 'API request failed');
            }

            return result;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    // Authentication methods
    async subscribe(name, email, password) {
        const result = await this.request('subscribe.php', { name : name, email : email, password : password });
        this.setToken(result.token);
        return result;
    },

    async authenticate(email, password) {
        const result = await this.request('authenticate.php', { email : email, password : password });
        this.setToken(result.token);
        return result;
    },

    // Profile methods
    async addProfile(name) {
        return this.request('addProfile.php', { name });
    },

    async setProfile(profileId) {
        const result = await this.request('setProfile.php', profileId);
        this.setToken(result.token);
        return result;
    },

    async getAllProfiles() {
        return this.request('getAllProfiles.php');
    },

    // Content methods
    async getFilms() {
        return this.request('getFilms.php');
    },

    async getSeries() {
        return this.request('getSeries.php');
    },

    async getMovie(movieId) {
        return this.request('getMovie.php', movieId);
    },

    async getSeason(movieId, seasonNo) {
        return this.request('getSeason.php', { movie_id: movieId, season_no: seasonNo });
    },

    async getRecents() {
        return this.request('getRecents.php');
    },

    async getGenre(genre) {
        return this.request('getGenre.php', genre);
    },

    // My List methods
    async getMyList() {
        return this.request('getMyList.php');
    },

    async addToList(movieId) {
        return this.request('addToList.php', movieId);
    },

    async removeFromList(movieId) {
        return this.request('removeFromList.php', movieId);
    },

    // User information
    async getMe() {
        return this.request('getMe.php');
    },

    // Watch history
    async getHistory() {
        return this.request('getHistory.php');
    },
};

export default api;
