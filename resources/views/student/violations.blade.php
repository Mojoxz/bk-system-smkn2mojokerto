@extends('student.layouts.app')

@section('title', 'Riwayat Pelanggaran')
@section('heading', 'Riwayat Pelanggaran')
@section('subheading', 'Daftar semua pelanggaran yang tercatat')

@section('content')

    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-end mb-4">
        <a href="{{ route('student.report.form') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Lapor Pelanggaran
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

        {{-- Desktop table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Kategori</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Jenis</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Poin</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($violations as $violation)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                                {{ $violation->violation_date->format('d/m/Y') }}
                                <span class="text-gray-400 text-xs ml-1">{{ $violation->violation_date->format('H:i') }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $violation->violationType->category->code }} · {{ $violation->violationType->category->name }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-800">
                                {{ $violation->violationType->name }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $violation->points >= 50 ? 'bg-red-50 text-red-700' : ($violation->points >= 25 ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700') }}">
                                    {{ $violation->points }} poin
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $violation->status === 'pending' ? 'bg-amber-50 text-amber-700' : ($violation->status === 'approved' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700') }}">
                                    @if($violation->status === 'pending') Menunggu
                                    @elseif($violation->status === 'approved') Disetujui
                                    @else Ditolak
                                    @endif
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <a href="{{ route('student.violations.show', $violation->id) }}"
                                   class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                    Lihat Detail
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                                Belum ada data pelanggaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($violations as $violation)
                <a href="{{ route('student.violations.show', $violation->id) }}"
                   class="block p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <p class="text-sm font-medium text-gray-900">{{ $violation->violationType->name }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0
                            {{ $violation->status === 'pending' ? 'bg-amber-50 text-amber-700' : ($violation->status === 'approved' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700') }}">
                            @if($violation->status === 'pending') Menunggu
                            @elseif($violation->status === 'approved') Disetujui
                            @else Ditolak
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <span>{{ $violation->violation_date->format('d/m/Y H:i') }}</span>
                            <span>·</span>
                            <span class="text-blue-600">{{ $violation->violationType->category->name }}</span>
                            <span>·</span>
                            <span class="{{ $violation->points >= 50 ? 'text-red-600' : ($violation->points >= 25 ? 'text-amber-600' : 'text-green-600') }} font-medium">
                                {{ $violation->points }} poin
                            </span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @empty
                <div class="p-12 text-center text-sm text-gray-400">
                    Belum ada data pelanggaran
                </div>
            @endforelse
        </div>

    </div>

    @if($violations->hasPages())
        <div class="mt-5">
            {{ $violations->links() }}
        </div>
    @endif

@endsection
