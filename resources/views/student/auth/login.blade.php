<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa - BK SMKN 2 Mojokerto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-500 to-blue-700 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Login Siswa</h1>
                <p class="text-gray-600 mt-2">BK SMKN 2 Mojokerto</p>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('student.login.post') }}">
                @csrf

                <div class="mb-4">
                    <label for="nisn" class="block text-gray-700 font-semibold mb-2">NISN</label>
                    <input type="text"
                           id="nisn"
                           name="nisn"
                           value="{{ old('nisn') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           required>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           required>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        <span class="text-gray-700">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold transition">
                    Login
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('landing') }}" class="text-blue-600 hover:text-blue-800">
                    ← Kembali ke Beranda
                </a>
            </div>
        </div>

        <div class="text-center mt-4 text-white">
            <p class="text-sm">Default password: NISN Anda</p>
        </div>
    </div>
</body>
</html>
