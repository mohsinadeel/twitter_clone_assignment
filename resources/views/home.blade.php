@extends('layouts.app')

@section('content')
<div class="container" data-page="home">
    <div class="post-form">
        <h2 class="feed-title">What's happening?</h2>
        <form id="post-form">
            <textarea
                id="post-content"
                placeholder="What's on your mind?"
                maxlength="280"
                required
            ></textarea>
            <div class="char-counter">
                <span id="char-counter">0/280</span>
            </div>
            <button type="submit">Post</button>
        </form>
    </div>

    <div class="feed-header">
        <h2 class="feed-title">Recent Posts</h2>
    </div>

    <ul id="posts-list" class="posts-list">
        <div class="loading">Loading posts...</div>
    </ul>

    <div id="pagination-container"></div>
</div>

@push('styles')
    @vite(['resources/css/twitter-clone.css'])
@endpush

@push('scripts')
    <script>
        window.userId = {{ Auth::id() ?? 'null' }};
        window.isAdmin = {{ Auth::check() && Auth::user()->is_admin ? 'true' : 'false' }};
        window.csrfToken = '{{ csrf_token() }}';
    </script>
    @vite(['resources/js/app.js'])
@endpush
@endsection
