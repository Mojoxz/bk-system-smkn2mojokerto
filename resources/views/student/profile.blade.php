@extends('student.layouts.app')

@section('title', 'Profil Saya')
@section('heading', 'Profil Saya')
@section('subheading', 'Informasi akun dan data pribadi Anda')

@section('content')

    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Info section --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Informasi Pribadi</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-4 gap-x-6 text-sm">
            <div>
                <p class="text-xs text-gray-500 mb-0.5">NISN</p>
                <p class="font-medium text-gray-900">{{ $student->nisn }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Nama Lengkap</p>
                <p class="font-medium text-gray-900">{{ $student->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Kelas</p>
                <p class="font-medium text-gray-900">{{ $student->class }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">No. Absen</p>
                <p class="font-medium text-gray-900">{{ $student->absen ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Username</p>
                <p class="font-medium text-gray-900">{{ $student->username }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Total Poin</p>
                <p class="font-semibold {{ $student->total_points >= 100 ? 'text-red-600' : ($student->total_points >= 50 ? 'text-amber-500' : 'text-blue-600') }}">
                    {{ $student->total_points }} poin
                </p>
            </div>
        </div>
    </div>

    {{-- Edit form --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-5">Edit Profil</h2>

        <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon</label>
                <input type="text"
                       id="phone" name="phone"
                       value="{{ old('phone', $student->phone) }}"
                       placeholder="Contoh: 08123456789"
                       class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg
                              focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                              bg-white text-gray-900 placeholder:text-gray-400">
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                <textarea id="address" name="address" rows="3"
                          placeholder="Alamat lengkap..."
                          class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg
                                 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                 bg-white text-gray-900 placeholder:text-gray-400 resize-none">{{ old('address', $student->address) }}</textarea>
            </div>

            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-3">Kosongkan kolom password jika tidak ingin mengubah</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                        <input type="password"
                               id="password" name="password"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                      bg-white text-gray-900">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password"
                               id="password_confirmation" name="password_confirmation"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                      bg-white text-gray-900">
                    </div>
                </div>
            </div>

            <div class="pt-1">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium
                               px-5 py-2.5 rounded-lg transition-colors">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

@endsection
