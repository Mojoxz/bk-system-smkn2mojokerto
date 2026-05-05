<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Violation;
use App\Models\ViolationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentViolationReportController extends Controller
{
    /**
     * Tampilkan form lapor pelanggaran.
     */
    public function create()
    {
        $violationTypes = ViolationType::with('category')
            ->get()
            ->groupBy(fn($type) => $type->category->name);

        return view('student.report-violation', compact('violationTypes'));
    }

    /**
     * Simpan laporan pelanggaran dari siswa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'violation_type_id' => ['required', 'exists:violation_types,id'],
            'description'       => ['required', 'string', 'max:1000'],
            'photo_evidence'    => ['nullable', 'image', 'max:2048'],
            'photo_cam_data'    => ['nullable', 'string'],
            'signature_upload'  => ['nullable', 'image', 'max:1024'],
            'signature'         => ['nullable', 'string'],   // base64 dari canvas pad
        ], [
            'violation_type_id.required' => 'Jenis pelanggaran wajib dipilih.',
            'violation_type_id.exists'   => 'Jenis pelanggaran tidak valid.',
            'description.required'       => 'Keterangan wajib diisi.',
            'photo_evidence.image'       => 'Bukti foto harus berupa gambar (JPG/PNG).',
            'photo_evidence.max'         => 'Ukuran foto maksimal 2 MB.',
            'signature_upload.image'     => 'Tanda tangan harus berupa gambar (JPG/PNG).',
            'signature_upload.max'       => 'Ukuran tanda tangan maksimal 1 MB.',
        ]);

        $student       = auth('student')->user();
        $violationType = ViolationType::findOrFail($request->violation_type_id);
        $points        = $violationType->points ?? 0;

        // ── Simpan foto bukti ──────────────────────────────────
        $photoPath = null;

        if ($request->hasFile('photo_evidence')) {
            // Upload file biasa
            $photoPath = $request->file('photo_evidence')
                            ->store('violations/photos', 'public');

        } elseif ($request->filled('photo_cam_data')) {
            // Base64 dari kamera langsung
            $base64 = $request->input('photo_cam_data');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                $imageData = base64_decode(substr($base64, strpos($base64, ',') + 1));
                $ext       = in_array(strtolower($matches[1]), ['jpeg', 'jpg']) ? 'jpg' : 'png';
                $filename  = 'violations/photos/' . uniqid('foto_', true) . '.' . $ext;
                Storage::disk('public')->put($filename, $imageData);
                $photoPath = $filename;
            }
        }

        // ── Simpan tanda tangan ───────────────────────────────
        // Prioritas A: file upload
        // Prioritas B: base64 dari canvas pad (field name="signature")
        $signaturePath = null;

        if ($request->hasFile('signature_upload')) {
            // A) File upload
            $signaturePath = $request->file('signature_upload')
                                ->store('violations/signatures', 'public');

        } elseif ($request->filled('signature')) {
            // B) Base64 dari signature pad canvas
            $base64 = $request->input('signature');

            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                $imageData = base64_decode(substr($base64, strpos($base64, ',') + 1));
                $ext       = in_array(strtolower($matches[1]), ['jpeg', 'jpg']) ? 'jpg' : 'png';
                $filename  = 'violations/signatures/' . uniqid('sig_', true) . '.' . $ext;

                Storage::disk('public')->put($filename, $imageData);
                $signaturePath = $filename;
            }
        }

        // ── Buat record pelanggaran ───────────────────────────
        Violation::create([
            'student_id'        => $student->id,
            'violation_type_id' => $violationType->id,
            'admin_id'          => null,
            'description'       => $request->description,
            'points'            => $points,
            'photo_evidence'    => $photoPath,
            'signature'         => $signaturePath,
            'violation_date'    => now(),
            'status'            => 'pending',
            'notes'             => null,
        ]);

        return redirect()
            ->route('student.dashboard')
            ->with('success', 'Laporan pelanggaran berhasil dikirim dan sedang menunggu verifikasi.');
    }
}
