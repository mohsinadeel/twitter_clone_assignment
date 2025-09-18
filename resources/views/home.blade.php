@extends('layouts.app')

@section('title', 'Home - Twitter Clone')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0 space-y-6">
            <!-- Add New Post Section -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">What's happening?</h2>
                    <form class="space-y-4">
                        <div>
                            <textarea
                                name="content"
                                rows="3"
                                placeholder="What's on your mind?"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm resize-none"
                            ></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium"
                            >
                                Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Feed Section -->
            <div class="space-y-4">
                <h2 class="text-lg font-medium text-gray-900">Recent Posts</h2>

                <!-- Post 1 -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                JD
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-sm font-semibold text-gray-900">John Doe</h3>
                                    <span class="text-xs text-gray-500">@johndoe</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-xs text-gray-500">2h</span>
                                </div>
                                <p class="text-sm text-gray-900">
                                    Just finished building a new feature for our Twitter clone! The authentication system is working perfectly. Can't wait to add more functionality. #coding #laravel #twitterclone
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Post 2 -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                AS
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-sm font-semibold text-gray-900">Alice Smith</h3>
                                    <span class="text-xs text-gray-500">@alicesmith</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-xs text-gray-500">4h</span>
                                </div>
                                <p class="text-sm text-gray-900">
                                    Beautiful sunset today! Sometimes you need to step away from the computer and enjoy the simple things in life. 🌅
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Post 3 -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                MJ
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-sm font-semibold text-gray-900">Mike Johnson</h3>
                                    <span class="text-xs text-gray-500">@mikej</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-xs text-gray-500">6h</span>
                                </div>
                                <p class="text-sm text-gray-900">
                                    Working on a new project using Laravel and Vue.js. The combination is absolutely amazing for building modern web applications!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Post 4 -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-pink-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                ES
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-sm font-semibold text-gray-900">Emma Wilson</h3>
                                    <span class="text-xs text-gray-500">@emmawilson</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-xs text-gray-500">1d</span>
                                </div>
                                <p class="text-sm text-gray-900">
                                    Coffee and code - the perfect combination for a productive day! ☕️ What's everyone working on today?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Post 5 -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                RB
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-sm font-semibold text-gray-900">Robert Brown</h3>
                                    <span class="text-xs text-gray-500">@robertbrown</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-xs text-gray-500">2d</span>
                                </div>
                                <p class="text-sm text-gray-900">
                                    Just deployed a new feature to production. The feeling of seeing your code live and working for real users is incredible! 🚀
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
