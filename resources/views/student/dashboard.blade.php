<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - BK SMKN 2 Mojokerto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('student.partials.navbar')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang, {{ $student->name }}!</h2>
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <p class="text-gray-600">NISN: <span class="font-semibold">{{ $student->nisn }}</span></p>
                    <p class="text-gray-600">Kelas: <span class="font-semibold">{{ $student->class }}</span></p>
                </div>
                <div>
                    <p class="text-gray-600">Absen: <span class="font-semibold">{{ $student->absen }}</span></p>
                    <p class="text-gray-600">Username: <span class="font-semibold">{{ $student->username }}</span></p>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Poin</p>
                        <p class="text-3xl font-bold {{ $student->total_points >= 100 ? 'text-red-600' : ($student->total_points >= 50 ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $student->total_points }}
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Pelanggaran</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $statistics['total_violations'] }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $statistics['pending_violations'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Disetujui</p>
                        <p class="text-3xl font-bold text-green-600">{{ $statistics['approved_violations'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pelanggaran by Category -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Pelanggaran per Kategori</h3>
            <div class="space-y-4">
                @forelse($statistics['violations_by_category'] as $category => $data)
                    <div class="border-b pb-4 last:border-b-0">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-semibold text-gray-800">{{ $category }}</h4>
                            <span class="text-2xl font-bold text-blue-600">{{ $data['points'] }} poin</span>
                        </div>
                        <p class="text-gray-600 text-sm">Jumlah pelanggaran: {{ $data['count'] }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(($data['points'] / max($student->total_points, 1)) * 100, 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 text-center py-4">Belum ada data pelanggaran.</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <a href="{{ route('student.report.form') }}" class="bg-blue-600 text-white rounded-lg shadow-md p-6 hover:bg-blue-700 transition">
                <div class="flex items-center">
                    <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-xl font-bold">Lapor Pelanggaran</h3>
                        <p class="text-blue-100">Laporkan pelanggaran Anda di sini</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('student.violations') }}" class="bg-green-600 text-white rounded-lg shadow-md p-6 hover:bg-green-700 transition">
                <div class="flex items-center">
                    <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <h3 class="text-xl font-bold">Riwayat Pelanggaran</h3>
                        <p class="text-green-100">Lihat semua pelanggaran Anda</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
