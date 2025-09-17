<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile - Twitter Clone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">Twitter Clone</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- User Dropdown Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                            <!-- User Initials Avatar -->
                            <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                            <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 bg-gray-50">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                User Profile
                            </a>
                            <a href="{{ route('settings.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Settings
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

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
</body>
</html>
