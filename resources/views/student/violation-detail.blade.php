@extends('student.layouts.app')

@section('title', 'Detail Pelanggaran')
@section('heading', 'Detail Pelanggaran')
@section('subheading', 'Informasi lengkap pelanggaran yang tercatat')

@section('content')

    {{-- Back button --}}
    <div class="mb-5">
        <a href="{{ route('student.violations') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Riwayat Pelanggaran
        </a>
    </div>

    {{-- Status Banner --}}
    @if($violation->status === 'approved')
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pelanggaran ini telah <strong class="mx-1">disetujui</strong> dan poin sudah ditambahkan ke total poin kamu.
        </div>
    @elseif($violation->status === 'rejected')
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>
                Pelanggaran ini telah <strong>ditolak</strong>.
                @if($violation->notes)
                    Alasan: <em>{{ $violation->notes }}</em>
                @endif
            </span>
        </div>
    @else
        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl mb-5 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pelanggaran ini sedang <strong class="mx-1">menunggu</strong> verifikasi dari admin.
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Kolom Utama ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Informasi Pelanggaran --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-800">Informasi Pelanggaran</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $violation->status === 'pending'   ? 'bg-amber-50 text-amber-700'
                        : ($violation->status === 'approved'  ? 'bg-green-50 text-green-700'
                        :                                       'bg-red-50 text-red-700') }}">
                        @if($violation->status === 'pending') Menunggu
                        @elseif($violation->status === 'approved') Disetujui
                        @else Ditolak
                        @endif
                    </span>
                </div>

                <div class="p-5 space-y-4">

                    {{-- Kategori & Jenis --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Kategori</p>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                <span class="font-bold">{{ $violation->violationType->category->code }}</span>
                                <span>·</span>
                                {{ $violation->violationType->category->name }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Jenis Pelanggaran</p>
                            <p class="text-sm font-medium text-gray-800">{{ $violation->violationType->name }}</p>
                        </div>
                    </div>

                    <div class="h-px bg-gray-100"></div>

                    {{-- Tanggal & Poin --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Tanggal Pelanggaran</p>
                            <p class="text-sm text-gray-800">
                                {{ $violation->violation_date->translatedFormat('l, d F Y') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $violation->violation_date->format('H:i') }} WIB</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Poin Pelanggaran</p>
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-semibold
                                {{ $violation->points >= 50 ? 'bg-red-50 text-red-700 ring-1 ring-red-100'
                                : ($violation->points >= 25  ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-100'
                                :                              'bg-green-50 text-green-700 ring-1 ring-green-100') }}">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ $violation->points }} poin
                            </span>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    @if($violation->description)
                        <div class="h-px bg-gray-100"></div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Keterangan / Deskripsi</p>
                            <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 rounded-lg px-4 py-3">
                                {{ $violation->description }}
                            </p>
                        </div>
                    @endif

                    {{-- Catatan Admin — hanya tampil jika sudah diproses (approved/rejected) --}}
                    @if($violation->notes && $violation->status !== 'pending')
                        <div class="h-px bg-gray-100"></div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Catatan Admin</p>
                            <div class="flex gap-3 rounded-lg px-4 py-3
                                {{ $violation->status === 'rejected' ? 'bg-red-50' : 'bg-blue-50' }}">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5
                                    {{ $violation->status === 'rejected' ? 'text-red-400' : 'text-blue-400' }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $violation->notes }}</p>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Bukti Foto --}}
            @if($violation->photo_evidence)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-800">Bukti Foto</h2>
                    </div>
                    <div class="p-5">
                        <a href="{{ Storage::url($violation->photo_evidence) }}" target="_blank"
                           class="group block relative rounded-lg overflow-hidden border border-gray-200 hover:border-blue-300 transition-colors">
                            <img src="{{ Storage::url($violation->photo_evidence) }}"
                                 alt="Bukti foto pelanggaran"
                                 class="w-full max-h-72 object-cover">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-white text-gray-700 text-xs font-medium px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Lihat ukuran penuh
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Tanda Tangan --}}
            @if($violation->signature)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-800">Tanda Tangan</h2>
                    </div>
                    <div class="p-5">
                        <div class="border border-gray-200 rounded-lg bg-gray-50 p-4 flex items-center justify-center min-h-24">
                            <img src="{{ Storage::url($violation->signature) }}"
                                 alt="Tanda tangan siswa"
                                 class="max-h-32 object-contain">
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-center">Tanda tangan siswa sebagai konfirmasi laporan</p>
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Sidebar ── --}}
        <div class="space-y-5">

            {{-- Sumber Laporan / Dicatat Oleh --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-800">
                        {{ $violation->admin_id ? 'Dicatat Oleh' : 'Sumber Laporan' }}
                    </h2>
                </div>
                <div class="p-5">
                    @if($violation->admin_id && $violation->admin)
                        {{-- Dicatat langsung oleh admin --}}
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-semibold text-blue-600">
                                    {{ strtoupper(substr($violation->admin->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $violation->admin->name }}</p>
                                <p class="text-xs text-gray-400">Administrator</p>
                            </div>
                        </div>
                    @else
                        {{-- Laporan mandiri dari siswa (admin_id = null) --}}
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Laporan Mandiri</p>
                                <p class="text-xs text-gray-400">Dilaporkan oleh siswa</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Dilaporkan pada</p>
                            <p class="text-sm text-gray-700">{{ $violation->created_at->translatedFormat('d F Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $violation->created_at->format('H:i') }} WIB</p>
                        </div>
                        @if($violation->updated_at->ne($violation->created_at))
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Terakhir diperbarui</p>
                                <p class="text-sm text-gray-700">{{ $violation->updated_at->diffForHumans() }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Detail Jenis Pelanggaran --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-800">Detail Jenis Pelanggaran</h2>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Kode Kategori</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-600 text-white">
                            {{ $violation->violationType->category->code }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Nama Kategori</p>
                        <p class="text-sm text-gray-800">{{ $violation->violationType->category->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Jenis Pelanggaran</p>
                        <p class="text-sm text-gray-800">{{ $violation->violationType->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Bobot Poin</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $violation->violationType->points >= 50 ? 'bg-red-50 text-red-700'
                            : ($violation->violationType->points >= 25  ? 'bg-amber-50 text-amber-700'
                            :                                             'bg-green-50 text-green-700') }}">
                            {{ $violation->violationType->points }} poin
                        </span>
                    </div>
                    @if(!empty($violation->violationType->description))
                        <div class="pt-1">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Keterangan</p>
                            <p class="text-xs text-gray-600 leading-relaxed">{{ $violation->violationType->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ID Referensi --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">ID Pelanggaran</p>
                <p class="text-sm font-mono text-gray-600">#{{ str_pad($violation->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>

        </div>
    </div>

@endsection
