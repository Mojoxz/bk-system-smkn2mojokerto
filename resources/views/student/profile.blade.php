@extends('student.layouts.app')

@section('title', 'Profil Saya')
@section('heading', 'Profil Saya')
@section('subheading', 'Informasi akun dan data pribadi Anda')

@section('content')

    {{-- ── Banner "foto belum ada" ── --}}
    @unless($student->photo_url)
        <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3.5 mb-5">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-amber-800">Foto profil belum ditambahkan</p>
                <p class="text-xs text-amber-600 mt-0.5">Tambahkan foto profil Anda agar akun terlihat lebih lengkap.</p>
            </div>
        </div>
    @endunless

    {{-- ── Section foto profil ── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Foto Profil</h2>

        @error('photo')
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                {{ $message }}
            </div>
        @enderror

        <div class="flex items-start gap-6">

            {{-- Preview foto / avatar --}}
            <div class="flex-shrink-0">
                @if($student->photo_url)
                    <img id="photo-preview"
                         src="{{ $student->photo_url }}"
                         alt="Foto {{ $student->name }}"
                         class="w-24 h-24 rounded-full object-cover ring-4 ring-blue-50">
                @else
                    <div id="photo-avatar"
                         class="w-24 h-24 rounded-full bg-blue-600 ring-4 ring-blue-50
                                flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">{{ $student->initials }}</span>
                    </div>
                    <img id="photo-preview"
                         src=""
                         alt="Preview"
                         class="w-24 h-24 rounded-full object-cover ring-4 ring-blue-50 hidden">
                @endif
            </div>

            <div class="flex-1 space-y-3">
                {{-- Form upload — enctype WAJIB untuk file upload --}}
                <form method="POST"
                      action="{{ route('student.profile.photo') }}"
                      enctype="multipart/form-data">
                    @csrf

                    {{-- Input file tersembunyi, dipicu label --}}
                    <input type="file"
                           id="photo-input"
                           name="photo"
                           accept="image/jpeg,image/jpg,image/png,image/webp"
                           class="hidden"
                           onchange="previewPhoto(this)">

                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Label sebagai tombol "Pilih Foto" --}}
                        <label for="photo-input"
                               class="inline-flex items-center gap-2 cursor-pointer
                                      bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium
                                      px-4 py-2 rounded-lg transition-colors select-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0"/>
                            </svg>
                            Pilih Foto
                        </label>

                        {{-- Tombol simpan — selalu tampil, disabled jika belum pilih file --}}
                        <button type="submit"
                                id="photo-save-btn"
                                class="inline-flex items-center gap-2
                                       bg-green-600 hover:bg-green-700 disabled:bg-gray-300
                                       disabled:cursor-not-allowed text-white text-sm font-medium
                                       px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span id="photo-save-label">Simpan Foto</span>
                        </button>
                    </div>

                    {{-- Nama file yang dipilih --}}
                    <p id="photo-filename" class="text-xs text-gray-400">
                        Format: JPG, PNG, WEBP &nbsp;·&nbsp; Maks. 2 MB
                    </p>

                </form>

                {{-- Tombol hapus foto — form terpisah, hanya muncul jika ada foto --}}
                @if($student->photo_url)
                    <form method="POST"
                          action="{{ route('student.profile.photo.delete') }}"
                          onsubmit="return confirm('Yakin ingin menghapus foto profil?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-2 text-red-600 hover:text-red-700
                                       text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Foto
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Info section ── --}}
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
                <p class="font-medium text-gray-900">{{ $student->classroom->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Jurusan</p>
                <p class="font-medium text-gray-900">{{ $student->classroom->major->name ?? '-' }}</p>
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

    {{-- ── Edit form ── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-5">Edit Profil</h2>

        <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            @if($errors->has('phone') || $errors->has('address') || $errors->has('password'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <ul class="space-y-0.5">
                        @foreach($errors->only(['phone','address','password','password_confirmation']) as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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

@push('scripts')
<script>
function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;

    const file  = input.files[0];
    const maxMB = 2;

    if (file.size > maxMB * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal ' + maxMB + ' MB.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const preview  = document.getElementById('photo-preview');
        const avatar   = document.getElementById('photo-avatar');
        const filename = document.getElementById('photo-filename');

        preview.src = e.target.result;
        preview.classList.remove('hidden');
        if (avatar) avatar.classList.add('hidden');

        if (filename) filename.textContent = 'File dipilih: ' + file.name;
    };
    reader.readAsDataURL(file);
}
</script>
@endpush
