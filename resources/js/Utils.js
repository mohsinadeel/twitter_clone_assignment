var Utils = (function (Utils) {
    Utils.showLoading = function(elementId, message = 'Loading...') {
        const element = document.getElementById(elementId);
        if (element) {
            element.innerHTML = `<div class="loading">${message}</div>`;
        }
    };

    Utils.showError = function(elementId, message = 'An error occurred') {
        const element = document.getElementById(elementId);
        if (element) {
            element.innerHTML = `<div class="error">${message}</div>`;
        }
    };

    Utils.formatDate = function(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) {
            return 'just now';
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return `${minutes}m ago`;
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return `${hours}h ago`;
        } else {
            const days = Math.floor(diffInSeconds / 86400);
            return `${days}d ago`;
        }
    };

    Utils.updatePostsCount = function(count) {
        const postsCountElement = document.getElementById('posts-count');
        if (postsCountElement) {
            postsCountElement.textContent = count;
        }
    };

    Utils.renderPagination = function(pagination) {
        const paginationContainer = document.getElementById('pagination-container');
        if (!paginationContainer) return;

        const currentPage = pagination.current_page;
        const lastPage = pagination.last_page;
        const hasNextPage = pagination.next_page_url;
        const hasPrevPage = pagination.prev_page_url;

        let paginationHTML = '<div class="pagination">';

        if (hasPrevPage) {
            paginationHTML += `<button class="pagination-btn" data-page="${currentPage - 1}">Previous</button>`;
        }

        for (let i = 1; i <= lastPage; i++) {
            if (i === currentPage) {
                paginationHTML += `<button class="pagination-btn active">${i}</button>`;
            } else {
                paginationHTML += `<button class="pagination-btn" data-page="${i}">${i}</button>`;
            }
        }

        if (hasNextPage) {
            paginationHTML += `<button class="pagination-btn" data-page="${currentPage + 1}">Next</button>`;
        }

        paginationHTML += '</div>';
        paginationContainer.innerHTML = paginationHTML;

        // Add event listeners to pagination buttons
        const paginationButtons = paginationContainer.querySelectorAll('.pagination-btn[data-page]');
        paginationButtons.forEach(button => {
            button.addEventListener('click', function() {
                const page = parseInt(this.getAttribute('data-page'));
                if (window.Posts && Posts.loadPosts) {
                    Posts.loadPosts(null, page);
                } else if (window.loadPosts) {
                    loadPosts(null, page);
                }
            });
        });
    };

    Utils.getCurrentPage = function() {
        const activeBtn = document.querySelector('.pagination-btn.active');
        return activeBtn ? parseInt(activeBtn.textContent) : 1;
    };

    Utils.setupPostForm = function() {
        const form = document.getElementById('post-form');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const textarea = document.getElementById('post-content');
            const content = textarea.value.trim();

            if (content.length === 0) {
                alert('Please enter some content for your post.');
                return;
            }

            if (content.length > 280) {
                alert('Post content cannot exceed 280 characters.');
                return;
            }

            if (window.Posts && Posts.createPost) {
                await Posts.createPost(content);
            } else if (window.createPost) {
                await createPost(content);
            }
        });
    };

    Utils.setupCharacterCounter = function() {
        const textarea = document.getElementById('post-content');
        const counter = document.getElementById('char-counter');

        if (!textarea || !counter) return;

        textarea.addEventListener('input', function() {
            const length = this.value.length;
            counter.textContent = `${length}/280`;

            if (length > 280) {
                counter.style.color = '#e74c3c';
            } else if (length > 250) {
                counter.style.color = '#f39c12';
            } else {
                counter.style.color = '#95a5a6';
            }
        });
    };

    Utils.initializePage = function() {
        const container = document.querySelector('.container');
        if (!container) return;

        const pageType = container.getAttribute('data-page');

        if (pageType === 'profile') {
            const userId = window.userId || 1;
            if (window.Users && Users.loadProfile) {
                Users.loadProfile(userId);
            }
            if (window.Posts && Posts.loadPosts) {
                Posts.loadPosts(userId);
            }
        } else {
            if (window.Posts && Posts.loadPosts) {
                Posts.loadPosts();
            }
        }

        Utils.setupPostForm();
        Utils.setupCharacterCounter();
    };

    return Utils;
}(Utils || {}));

// Expose to global scope
window.Utils = Utils;
