<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - BK SMKN 2 Mojokerto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('student.partials.navbar')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Profil Saya</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold mb-4">Informasi Pribadi</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">NISN</p>
                    <p class="font-semibold">{{ $student->nisn }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Nama</p>
                    <p class="font-semibold">{{ $student->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Kelas</p>
                    <p class="font-semibold">{{ $student->class }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Absen</p>
                    <p class="font-semibold">{{ $student->absen ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Username</p>
                    <p class="font-semibold">{{ $student->username }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Total Poin</p>
                    <p class="font-semibold {{ $student->total_points >= 100 ? 'text-red-600' : ($student->total_points >= 50 ? 'text-yellow-600' : 'text-green-600') }}">
                        {{ $student->total_points }} poin
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4">Edit Profil</h3>
            <form method="POST" action="{{ route('student.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 font-semibold mb-2">Nomor Telepon</label>
                    <input type="text"
                           id="phone"
                           name="phone"
                           value="{{ old('phone', $student->phone) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="address" class="block text-gray-700 font-semibold mb-2">Alamat</label>
                    <textarea id="address"
                              name="address"
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ old('address', $student->address) }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Konfirmasi Password Baru</label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</body>
</html>
