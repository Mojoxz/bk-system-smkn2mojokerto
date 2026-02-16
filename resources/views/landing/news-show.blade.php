<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $news->title }} - BK SMKN 2 Mojokerto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('landing') }}" class="text-white text-xl font-bold">BK SMKN 2 Mojokerto</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.login') }}" class="text-white hover:text-blue-200">Login Siswa</a>
                    <a href="/admin" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50">Admin</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $news->title }}</h1>

            <div class="flex items-center text-gray-600 text-sm mb-6">
                <span>{{ $news->published_at->format('d F Y, H:i') }}</span>
                <span class="mx-2">•</span>
                <span>{{ $news->views }} views</span>
                @if($news->admin)
                    <span class="mx-2">•</span>
                    <span>Oleh {{ $news->admin->name }}</span>
                @endif
            </div>

            @if($news->image)
                <img src="{{ Storage::url($news->image) }}" alt="{{ $news->title }}" class="w-full rounded-lg mb-6">
            @endif

            <div class="prose max-w-none">
                {!! $news->content !!}
            </div>
        </article>

        @if($relatedNews->count() > 0)
            <div class="mt-12">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Berita Terkait</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedNews as $item)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                            @if($item->image)
                                <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}" class="w-full h-32 object-cover">
                            @endif
                            <div class="p-4">
                                <h4 class="font-semibold mb-2">{{ Str::limit($item->title, 50) }}</h4>
                                <a href="{{ route('news.show', $item->slug) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Baca →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
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
