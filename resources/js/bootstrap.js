import axios from 'axios';
window.axios = axios;

// Configure axios defaults
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Initialize CSRF token for Sanctum
axios.get('/sanctum/csrf-cookie').then(() => {
    console.log('CSRF token initialized for Sanctum');
}).catch(error => {
    console.warn('Failed to initialize CSRF token:', error);
});

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.csrfToken;
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

window.axios.interceptors.request.use(
    config => {

        if (config.method === 'get') {
            config.params = {
                ...config.params,
                _t: Date.now()
            };
        }
        if (window.showLoading) {
            window.showLoading();
        }

        console.log(`Making ${config.method.toUpperCase()} request to:`, config.url);

        return config;
    },
    error => {
        if (window.hideLoading) {
            window.hideLoading();
        }
        return Promise.reject(error);
    }
);

window.axios.interceptors.response.use(
    response => {
        if (window.hideLoading) {
            window.hideLoading();
        }

        console.log(`Response from ${response.config.url}:`, response.status);

        return response;
    },
    error => {
        if (window.hideLoading) {
            window.hideLoading();
        }

        if (error.response?.status === 401) {
            console.error('Unauthorized: Please log in again');
        } else if (error.response?.status === 422) {
            console.error('Validation Error:', error.response.data.errors);
        } else if (error.response?.status >= 500) {
            console.error('Server Error:', error.response.data.message);
        } else if (error.code === 'NETWORK_ERROR') {
            console.error('Network Error: Please check your connection');
        }

        return Promise.reject(error);
    }
);

