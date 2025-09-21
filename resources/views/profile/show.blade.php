@extends('layouts.app')

@section('content')
<div class="container" data-page="profile">
    <div id="profile-header" class="profile-header">
        <div class="loading">Loading profile...</div>
    </div>

    <div id="profile-details" class="profile-details">
        <div class="loading">Loading profile details...</div>
    </div>

    <div class="feed-header">
        <h2 class="feed-title">My Posts</h2>
    </div>

    <ul id="posts-list" class="posts-list">
        <div class="loading">Loading posts...</div>
    </ul>
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
