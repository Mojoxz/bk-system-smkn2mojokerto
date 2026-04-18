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
            'signature_upload'  => ['nullable', 'image', 'max:1024'],
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
            $photoPath = $request->file('photo_evidence')
                            ->store('violations/photos', 'public');
        }

        // ── Simpan tanda tangan ───────────────────────────────
        // Prioritas A: file upload (termasuk hasil konversi canvas→blob dari JS)
        // Prioritas B: base64 string (fallback jika browser tidak support DataTransfer API)
        $signaturePath = null;

        if ($request->hasFile('signature_upload')) {
            // A) File dari upload / konversi canvas blob
            $signaturePath = $request->file('signature_upload')
                                ->store('violations/signatures', 'public');

        } elseif ($request->filled('signature_base64')) {
            // B) Fallback base64 — decode lalu simpan sebagai file
            $base64 = $request->input('signature_base64');

            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                $imageData = base64_decode(substr($base64, strpos($base64, ',') + 1));
                $ext       = in_array($matches[1], ['jpeg', 'jpg']) ? 'jpg' : 'png';
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
