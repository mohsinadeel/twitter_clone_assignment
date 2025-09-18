@extends('layouts.app')

@section('title', 'Profile - Twitter Clone')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="grid grid-cols-1 lg:grid-cols-10 gap-6">
                <!-- Left Column - Profile Details (30%) -->
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Profile Details</h2>

                            <div class="space-y-6">
                                <!-- Profile Avatar -->
                                <div class="flex flex-col items-center space-y-4">
                                    <div class="w-24 h-24 bg-indigo-600 text-white rounded-full flex items-center justify-center text-3xl font-bold">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-xl font-semibold text-gray-900">{{ Auth::user()->name }}</h3>
                                        <p class="text-gray-600">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>

                                <!-- Profile Information -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                        <div class="px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                            {{ Auth::user()->name }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                        <div class="px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                            {{ Auth::user()->email }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Member Since</label>
                                        <div class="px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                            {{ Auth::user()->created_at->format('F j, Y') }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Updated</label>
                                        <div class="px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                            {{ Auth::user()->updated_at->format('F j, Y \a\t g:i A') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col space-y-2 pt-4">
                                    <a href="{{ route('settings.show') }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium text-center">
                                        Edit Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - AJAX Content Placeholder (70%) -->
                <div class="lg:col-span-7">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Activity Feed</h2>

                            <!-- AJAX Content Placeholder -->
                            <div id="ajax-content" class="space-y-4">
                                <!-- Placeholder Content -->
                                <div id="placeholder-content">
                                    <div class="border border-gray-200 rounded-lg p-6">
                                        <div class="flex items-center space-x-3 mb-4">
                                            <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                                            <div class="space-y-2">
                                                <div class="h-4 bg-gray-300 rounded w-24"></div>
                                                <div class="h-3 bg-gray-200 rounded w-16"></div>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                        </div>
                                    </div>

                                    <div class="border border-gray-200 rounded-lg p-6">
                                        <div class="flex items-center space-x-3 mb-4">
                                            <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                                            <div class="space-y-2">
                                                <div class="h-4 bg-gray-300 rounded w-32"></div>
                                                <div class="h-3 bg-gray-200 rounded w-20"></div>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                                            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                                        </div>
                                    </div>

                                    <div class="text-center py-8">
                                        <p class="text-gray-500 mb-4">This area is ready for AJAX content</p>
                                        <p class="text-sm text-gray-400">Future features: Posts, Activity, Notifications, etc.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
