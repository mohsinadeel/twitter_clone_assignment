const API_BASE = '/api/v1';
const USER_ID = window.userId || 1;

document.addEventListener('DOMContentLoaded', function() {
    // Wait for modules to be available with a small delay
    setTimeout(function() {
        if (window.Users && window.Posts && window.Utils) {
            window.loadProfile = Users.loadProfile;
            window.loadPosts = Posts.loadPosts;
            window.createPost = Posts.createPost;
            window.deletePost = Posts.deletePost;

            Utils.initializePage();
        } else {
            console.error('Required modules not loaded:', {
                Users: !!window.Users,
                Posts: !!window.Posts,
                Utils: !!window.Utils
            });
        }
    }, 100);
});
