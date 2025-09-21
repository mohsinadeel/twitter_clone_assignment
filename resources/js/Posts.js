var Posts = (function (Posts) {
    const API_BASE = '/api/v1';
    const USER_ID = window.userId || 1;

    Posts.loadPosts = function(userId = null, page = 1) {
        const postsElement = document.getElementById('posts-list');
        if (!postsElement) return;

        if (window.Utils && Utils.showLoading) {
            Utils.showLoading('posts-list', 'Loading posts...');
        }

        const endpoint = userId ? `${API_BASE}/users/${userId}/posts` : `${API_BASE}/posts`;

        return axios.get(endpoint, {
            params: { page: page, per_page: 10 }
        })
        .then(response => {
            let posts, pagination;
            if (response.data.data && response.data.data.data) {
                posts = response.data.data.data;
                pagination = response.data.data;
            } else if (response.data.data && Array.isArray(response.data.data)) {
                posts = response.data.data;
                pagination = response.data.data;
            } else if (Array.isArray(response.data)) {
                posts = response.data;
                pagination = null;
            } else {
                posts = [];
                pagination = null;
            }

            if (!posts || !Array.isArray(posts) || posts.length === 0) {
                postsElement.innerHTML = '<div class="loading">No posts found</div>';
                return;
            }

            postsElement.innerHTML = posts.map(post => {
                const canDelete = (window.Users && Users.canDeletePost) ? Users.canDeletePost(post) : (post.user_id === USER_ID || window.isAdmin || false);
                const formattedDate = (window.Utils && Utils.formatDate) ? Utils.formatDate(post.created_at) : new Date(post.created_at).toLocaleDateString();
                const deleteButton = canDelete ? `<button class="delete-btn" onclick="Posts.deletePost(${post.id})">Delete</button>` : '';

                return `
                    <li class="post-item">
                        <div class="post-header">
                            <div class="post-author">${post.user.name}</div>
                            <div class="post-username">@${post.user.username || post.user.email}</div>
                            <div class="post-time">${formattedDate}</div>
                        </div>
                        <div class="post-content">${post.content}</div>
                        ${deleteButton}
                    </li>
                `;
            }).join('');

            const feedHeader = document.querySelector('.feed-title');
            if (feedHeader && userId) {
                feedHeader.textContent = 'My Posts';
            } else if (feedHeader && !userId) {
                feedHeader.textContent = 'Recent Posts';
            }

            if (window.Utils && Utils.updatePostsCount) {
                Utils.updatePostsCount(posts.length);
            }

            if (pagination && !userId && window.Utils && Utils.renderPagination) {
                Utils.renderPagination(pagination);
            }
        })
        .catch(error => {
            if (window.Utils && Utils.showError) {
                Utils.showError('posts-list', 'Error loading posts');
            }
        });
    };

    Posts.createPost = function(content) {
        return axios.post(`${API_BASE}/posts`, {
            content: content.trim(),
            user_id: USER_ID
        })
        .then(response => {
            if (response.data.success) {
                const textarea = document.getElementById('post-content');
                if (textarea) textarea.value = '';

                const container = document.querySelector('.container');
                const pageType = container ? container.getAttribute('data-page') : 'unknown';

                if (pageType === 'profile') {
                    Posts.loadPosts(USER_ID);
                } else {
                    Posts.loadPosts(null, 1);
                }
            }
        })
        .catch(error => {
            alert('Error creating post. Please try again.');
        });
    };

    Posts.deletePost = function(postId) {
        if (!confirm('Are you sure you want to delete this post?')) {
            return;
        }

        return axios.delete(`${API_BASE}/posts/${postId}`)
        .then(response => {
            if (response.data.success) {
                const container = document.querySelector('.container');
                const pageType = container ? container.getAttribute('data-page') : 'unknown';

                if (pageType === 'profile') {
                    Posts.loadPosts(USER_ID);
                } else {
                    const currentPage = (window.Utils && Utils.getCurrentPage) ? Utils.getCurrentPage() : 1;
                    Posts.loadPosts(null, currentPage);
                }
            }
        })
        .catch(error => {
            alert('Error deleting post. Please try again.');
        });
    };

    return Posts;
}(Posts || {}));

// Expose to global scope
window.Posts = Posts;
