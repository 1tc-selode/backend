<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Task Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="bg-white shadow-2xl rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 text-white px-8 py-6 text-center">
                <i class="fas fa-user-shield text-5xl mb-3"></i>
                <h1 class="text-3xl font-bold">Admin Login</h1>
                <p class="text-blue-100 mt-2">Task Management System</p>
            </div>

            <!-- Login Form -->
            <div class="px-8 py-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-envelope mr-1"></i> Email Address
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock mr-1"></i> Password
                        </label>
                        <input type="password" name="password" id="password" required
                            class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="form-checkbox h-5 w-5 text-blue-600 rounded">
                            <span class="ml-2 text-gray-700">Remember me</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="bg-gray-100 px-8 py-4 text-center text-sm text-gray-600">
                <p>
                    <i class="fas fa-info-circle mr-1"></i>
                    Only administrators can access this area
                </p>
            </div>
        </div>

        <!-- Test Credentials Card -->
        <div class="mt-6 bg-white bg-opacity-90 shadow-lg rounded-lg p-4 text-center">
            <p class="text-sm text-gray-700 font-semibold mb-2">
                <i class="fas fa-key text-blue-600 mr-1"></i> Test Admin Credentials
            </p>
            <p class="text-xs text-gray-600">
                <strong>Email:</strong> admin@taskmanager.hu<br>
                <strong>Password:</strong> admin123
            </p>
        </div>
    </div>
</body>
</html>
