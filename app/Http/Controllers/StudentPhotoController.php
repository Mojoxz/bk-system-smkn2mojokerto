<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentPhotoController extends Controller
{
    public function store(Request $request)
    {
        // ── Debug: apakah request benar-benar sampai ke controller? ──
        Log::info('StudentPhotoController@store dipanggil', [
            'has_file'   => $request->hasFile('photo'),
            'all_files'  => array_keys($request->allFiles()),
            'student_id' => Auth::guard('student')->id(),
            'method'     => $request->method(),
        ]);

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'photo.required' => 'Foto wajib diunggah.',
            'photo.image'    => 'File harus berupa gambar.',
            'photo.mimes'    => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'photo.max'      => 'Ukuran foto maksimal 2 MB.',
        ]);

        $student = Auth::guard('student')->user();

        // Hapus foto lama
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        // Simpan file
        $path = $request->file('photo')->store('students/photos', 'public');

        Log::info('StudentPhotoController: file tersimpan', [
            'path'       => $path,
            'student_id' => $student->id,
        ]);

        if (! $path) {
            return back()->with('error', 'Gagal menyimpan file ke storage.');
        }

        // Update DB langsung — bypass Eloquent sepenuhnya
        DB::table('students')
            ->where('id', $student->id)
            ->update(['photo' => $path, 'updated_at' => now()]);

        Log::info('StudentPhotoController: DB updated', [
            'student_id' => $student->id,
            'path'       => $path,
        ]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function destroy()
    {
        $student = Auth::guard('student')->user();

        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        DB::table('students')
            ->where('id', $student->id)
            ->update(['photo' => null, 'updated_at' => now()]);

        return back()->with('warning', 'Foto profil telah dihapus. Silakan tambahkan foto baru.');
    }
}
