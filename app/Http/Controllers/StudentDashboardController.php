<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\ViolationType;
use App\Services\ViolationService;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $service = new StudentService();
        $statistics = $service->getStudentStatistics($student);

        return view('student.dashboard', compact('student', 'statistics'));
    }

    public function profile()
    {
        $student = Auth::guard('student')->user();
        return view('student.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $student->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function violations()
    {
        $student = Auth::guard('student')->user();
        $violations = $student->violations()
            ->with(['violationType.category'])
            ->latest('violation_date')
            ->paginate(10);

        return view('student.violations', compact('violations', 'student'));
    }

    public function reportForm()
    {
        $violationTypes = ViolationType::with('category')
            ->where('is_active', true)
            ->get()
            ->groupBy('category.name');

        return view('student.report-violation', compact('violationTypes'));
    }

    public function reportStore(Request $request, ViolationService $service)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'violation_type_id' => 'required|exists:violation_types,id',
            'description' => 'required|string',
            'photo_evidence' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:1024',
        ]);

        $validated['student_id'] = $student->id;
        $validated['violation_date'] = now();
        $validated['status'] = 'pending';

        $service->createViolation($validated);

        return redirect()->route('student.violations')->with('success', 'Laporan pelanggaran berhasil dikirim!');
    }
}
