const API_BASE_URL = '/';

const getCommonHeaders = () => ({
    'Content-Type': 'application/json'
});

const getAuthHeaders = () => {
    const token = localStorage?.getItem('token');
    if (!token) throw new Error('No token found in localStorage');
    return {
        ...getCommonHeaders(),
        'Authorization': `Bearer ${token}`
    };
};

async function apiCall(endpoint, method = 'POST', body = null, requiresAuth = true) {
    try {
        const headers = requiresAuth ? getAuthHeaders() : getCommonHeaders();
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {method,headers,body: body ? JSON.stringify(body) : null});

        const data = await response.json();
        if (!response.ok || !data.ok) {
            throw new Error(data.message || `HTTP ${response.status}: API request failed`);
        }
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

export const authAPI = {
    async signup(username, password) {
        const result = await apiCall('signup.php', 'POST', { username, password }, false);
        if (!result.token || !result.profile_pic) {
            throw new Error('API response missing token or profile_pic');
        }
        return result;
    },

    async signin(username, password) {
        const result = await apiCall('signin.php', 'POST', { username, password }, false);
        if (!result.token || !result.profile_pic) {
            throw new Error('API response missing token or profile_pic');
        }
        return result;
    }
};