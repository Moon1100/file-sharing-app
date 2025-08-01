<nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-light text-gray-900 hover:text-gray-700 transition-colors">
                        QuickDrop
                    </a>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-full text-sm font-medium transition-all-smooth hover:bg-gray-100">
                    Home
                </a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-full text-sm font-medium transition-all-smooth hover:bg-gray-100">
                        Admin
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav> 