@extends('student.layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Selamat datang kembali, ' . $student->name)

@section('content')

    {{-- Student info card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-500 text-xs mb-0.5">NISN</p>
                <p class="font-medium text-gray-900">{{ $student->nisn }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs mb-0.5">Kelas</p>
                <p class="font-medium text-gray-900">
                    {{ $student->classroom->name ?? '-' }}
                </p>
            </div>
            <div>
                <p class="text-gray-500 text-xs mb-0.5">Jurusan</p>
                <p class="font-medium text-gray-900">
                    {{ $student->classroom->major->name ?? '-' }}
                </p>
            </div>
            <div>
                <p class="text-gray-500 text-xs mb-0.5">No. Absen</p>
                <p class="font-medium text-gray-900">{{ $student->absen ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs mb-0.5">Username</p>
                <p class="font-medium text-gray-900">{{ $student->username }}</p>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Total Poin</p>
            <p class="text-2xl font-semibold
                {{ $student->total_points >= 100 ? 'text-red-600' : ($student->total_points >= 50 ? 'text-amber-500' : 'text-blue-600') }}">
                {{ $student->total_points }}
            </p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Total Pelanggaran</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $statistics['total_violations'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Menunggu</p>
            <p class="text-2xl font-semibold text-amber-500">{{ $statistics['pending_violations'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Disetujui</p>
            <p class="text-2xl font-semibold text-green-600">{{ $statistics['approved_violations'] }}</p>
        </div>

    </div>

    {{-- Pelanggaran per kategori --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Pelanggaran per Kategori</h2>

        @forelse($statistics['violations_by_category'] as $category => $data)
            <div class="py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-sm text-gray-800">{{ $category }}</span>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-400">{{ $data['count'] }}x</span>
                        <span class="font-semibold text-blue-600">{{ $data['points'] }} poin</span>
                    </div>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-blue-500 h-1.5 rounded-full"
                         style="width: {{ min(($data['points'] / max($student->total_points, 1)) * 100, 100) }}%">
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-6">Belum ada data pelanggaran.</p>
        @endforelse
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('student.report.form') }}"
           class="flex items-center gap-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl p-5 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-sm">Lapor Pelanggaran</p>
                <p class="text-blue-100 text-xs mt-0.5">Laporkan pelanggaran baru</p>
            </div>
        </a>

        <a href="{{ route('student.violations') }}"
           class="flex items-center gap-4 bg-white hover:bg-gray-50 border border-gray-200 text-gray-900 rounded-xl p-5 transition-colors">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-sm">Riwayat Pelanggaran</p>
                <p class="text-gray-400 text-xs mt-0.5">Lihat semua pelanggaran</p>
            </div>
        </a>
    </div>

@endsection
