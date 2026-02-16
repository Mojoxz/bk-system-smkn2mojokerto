<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BK SMKN 2 Mojokerto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-white text-xl font-bold">BK SMKN 2 Mojokerto</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.login') }}" class="text-white hover:text-blue-200">Login Siswa</a>
                    <a href="/admin" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50">Admin</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-4">Sistem Manajemen Poin Pelanggaran</h2>
            <p class="text-xl mb-8">SMKN 2 Mojokerto - Bimbingan Konseling</p>
            <a href="{{ route('student.login') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 inline-block">
                Mulai Lapor Pelanggaran
            </a>
        </div>
    </div>

    <!-- News Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h3 class="text-3xl font-bold text-gray-900 mb-8">Berita Terbaru</h3>

        @if($news->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($news as $item)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                        @if($item->image)
                            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No Image</span>
                            </div>
                        @endif
                        <div class="p-6">
                            <h4 class="text-xl font-semibold mb-2">{{ $item->title }}</h4>
                            <p class="text-gray-600 text-sm mb-4">{{ $item->published_at->format('d F Y') }}</p>
                            <p class="text-gray-700 mb-4">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                            <a href="{{ route('news.show', $item->slug) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                Baca Selengkapnya →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 text-center py-8">Belum ada berita yang dipublikasikan.</p>
        @endif
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} SMKN 2 Mojokerto. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
