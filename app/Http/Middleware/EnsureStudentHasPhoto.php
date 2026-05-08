<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentHasPhoto
{
    /**
     * Rute yang dikecualikan dari pengecekan foto.
     */
    protected array $except = [
        'student.profile',
        'student.profile.update',
        'student.profile.photo',
        'student.profile.photo.delete',
        'student.logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $student = Auth::guard('student')->user();

        if ($student && empty($student->photo)) {
            // Lewati jika sedang mengakses rute yang dikecualikan
            if ($request->routeIs(...$this->except)) {
                return $next($request);
            }

            return redirect()
                ->route('student.profile')
                ->with('warning', 'Silakan lengkapi profil Anda dengan menambahkan foto terlebih dahulu.');
        }

        return $next($request);
    }
}
