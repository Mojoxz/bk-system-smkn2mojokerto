<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Pelanggaran - BK SMKN 2 Mojokerto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('student.partials.navbar')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Lapor Pelanggaran</h2>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('student.report.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label for="violation_type_id" class="block text-gray-700 font-semibold mb-2">
                        Jenis Pelanggaran <span class="text-red-500">*</span>
                    </label>
                    <select id="violation_type_id"
                            name="violation_type_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                            required>
                        <option value="">-- Pilih Jenis Pelanggaran --</option>
                        @foreach($violationTypes as $categoryName => $types)
                            <optgroup label="{{ $categoryName }}">
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('violation_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} ({{ $type->points }} poin)
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-gray-700 font-semibold mb-2">
                        Keterangan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                              placeholder="Jelaskan detail pelanggaran yang terjadi..."
                              required>{{ old('description') }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="photo_evidence" class="block text-gray-700 font-semibold mb-2">
                        Bukti Foto (Opsional)
                    </label>
                    <input type="file"
                           id="photo_evidence"
                           name="photo_evidence"
                           accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="text-sm text-gray-600 mt-1">Format: JPG, PNG, max 2MB</p>
                </div>

                <div class="mb-6">
                    <label for="signature" class="block text-gray-700 font-semibold mb-2">
                        Tanda Tangan Digital (Opsional)
                    </label>
                    <input type="file"
                           id="signature"
                           name="signature"
                           accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="text-sm text-gray-600 mt-1">Format: JPG, PNG, max 1MB</p>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <p class="text-yellow-700">
                        <strong>Catatan:</strong> Laporan pelanggaran akan diverifikasi oleh guru BK.
                        Status akan diperbarui setelah proses verifikasi selesai.
                    </p>
                </div>

                <div class="flex space-x-4">
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                        Kirim Laporan
                    </button>
                    <a href="{{ route('student.dashboard') }}"
                       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 font-semibold">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
