var Users = (function (Users) {
    const API_BASE = '/api/v1';
    const USER_ID = window.userId || 1;

    Users.loadProfile = function(userId = USER_ID) {
        const profileHeaderElement = document.getElementById('profile-header');
        const profileDetailsElement = document.getElementById('profile-details');

        if (!profileHeaderElement && !profileDetailsElement) return;

        if (window.Utils && Utils.showLoading) {
            if (profileHeaderElement) {
                Utils.showLoading('profile-header', 'Loading profile...');
            }
            if (profileDetailsElement) {
                Utils.showLoading('profile-details', 'Loading profile details...');
            }
        }

        return axios.get(`${API_BASE}/users/${userId}`)
            .then(response => {
                const user = response.data.data || response.data;

                // Update profile header
                if (profileHeaderElement) {
                    profileHeaderElement.innerHTML = `
                        <div class="profile-info">
                            <div class="avatar">
                                <img src="https://via.placeholder.com/100x100/1da1f2/ffffff?text=${(user.name || user.username || 'U').charAt(0).toUpperCase()}" alt="Profile Picture">
                            </div>
                            <div class="user-name">${user.name || 'N/A'}</div>
                            <div class="user-username">@${user.username || user.email || 'N/A'}</div>
                        </div>
                    `;
                }

                // Update profile details
                if (profileDetailsElement) {
                    profileDetailsElement.innerHTML = `
                        <div class="profile-grid">
                            <div class="profile-field">
                                <label>Name:</label>
                                <div class="value">${user.name || 'N/A'}</div>
                            </div>
                            <div class="profile-field">
                                <label>Username:</label>
                                <div class="value">${user.username || user.email || 'N/A'}</div>
                            </div>
                            <div class="profile-field">
                                <label>Email:</label>
                                <div class="value">${user.email || 'N/A'}</div>
                            </div>
                            <div class="profile-field">
                                <label>Posts:</label>
                                <div class="value" id="posts-count">0</div>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                if (window.Utils && Utils.showError) {
                    if (profileHeaderElement) {
                        Utils.showError('profile-header', 'Error loading profile');
                    }
                    if (profileDetailsElement) {
                        Utils.showError('profile-details', 'Error loading profile details');
                    }
                }
            });
    };

    Users.canDeletePost = function(post) {
        return post.user_id === USER_ID || window.isAdmin || false;
    };

    return Users;
}(Users || {}));

// Expose to global scope
window.Users = Users;
