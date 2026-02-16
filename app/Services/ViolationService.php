<?php

namespace App\Services;

use App\Models\Violation;
use App\Models\Student;
use App\Models\ViolationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ViolationService
{
    public function createViolation(array $data): Violation
    {
        return DB::transaction(function () use ($data) {
            
            if (isset($data['photo_evidence']) && $data['photo_evidence']) {
                $data['photo_evidence'] = $this->handlePhotoUpload($data['photo_evidence']);
            }


            if (isset($data['signature']) && $data['signature']) {
                $data['signature'] = $this->handleSignatureUpload($data['signature']);
            }


            if (!isset($data['points'])) {
                $violationType = ViolationType::findOrFail($data['violation_type_id']);
                $data['points'] = $violationType->points;
            }

            $violation = Violation::create($data);

            return $violation;
        });
    }

    public function updateViolation(Violation $violation, array $data): Violation
    {
        return DB::transaction(function () use ($violation, $data) {

            if (isset($data['photo_evidence']) && $data['photo_evidence']) {

                if ($violation->photo_evidence) {
                    Storage::disk('public')->delete($violation->photo_evidence);
                }
                $data['photo_evidence'] = $this->handlePhotoUpload($data['photo_evidence']);
            }


            if (isset($data['signature']) && $data['signature']) {

                if ($violation->signature) {
                    Storage::disk('public')->delete($violation->signature);
                }
                $data['signature'] = $this->handleSignatureUpload($data['signature']);
            }

            $violation->update($data);

            return $violation;
        });
    }

    public function deleteViolation(Violation $violation): bool
    {

        if ($violation->photo_evidence) {
            Storage::disk('public')->delete($violation->photo_evidence);
        }
        if ($violation->signature) {
            Storage::disk('public')->delete($violation->signature);
        }

        return $violation->delete();
    }

    public function approveViolation(Violation $violation, ?string $notes = null): Violation
    {
        $violation->update([
            'status' => 'approved',
            'notes' => $notes,
        ]);

        return $violation;
    }

    public function rejectViolation(Violation $violation, ?string $notes = null): Violation
    {
        $violation->update([
            'status' => 'rejected',
            'notes' => $notes,
        ]);

        return $violation;
    }

    private function handlePhotoUpload($photo): string
    {
        if (is_string($photo)) {
            return $photo;
        }

        return $photo->store('violations/photos', 'public');
    }

    private function handleSignatureUpload($signature): string
    {
        if (is_string($signature)) {
            return $signature;
        }

        return $signature->store('violations/signatures', 'public');
    }

    public function getStatistics(): array
    {
        return [
            'total_violations' => Violation::count(),
            'pending_violations' => Violation::where('status', 'pending')->count(),
            'approved_violations' => Violation::where('status', 'approved')->count(),
            'rejected_violations' => Violation::where('status', 'rejected')->count(),
            'total_points' => Violation::where('status', 'approved')->sum('points'),
        ];
    }

    public function getViolationsByCategory(): array
    {
        return Violation::with('violationType.category')
            ->where('status', 'approved')
            ->get()
            ->groupBy('violationType.category.name')
            ->map(function ($violations) {
                return [
                    'count' => $violations->count(),
                    'total_points' => $violations->sum('points'),
                ];
            })
            ->toArray();
    }
}
