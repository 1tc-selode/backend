<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Task Manager Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.users.index') }}" class="text-2xl font-bold">
                        <i class="fas fa-tasks"></i> Task Manager Admin
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.users.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a href="{{ route('admin.tasks.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('admin.tasks.*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-list-check"></i> Tasks
                    </a>
                    <a href="{{ route('admin.assignments.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('admin.assignments.*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-user-check"></i> Assignments
                    </a>
                    
                    <!-- User Info & Logout -->
                    <div class="flex items-center space-x-3 border-l border-blue-500 pl-4 ml-4">
                        <span class="text-sm">
                            <i class="fas fa-user-shield"></i> {{ auth()->user()->name }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="hover:bg-red-600 bg-red-500 px-3 py-2 rounded transition duration-200">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-8">
        <p>&copy; 2026 Task Manager. All rights reserved.</p>
    </footer>
</body>
</html>
