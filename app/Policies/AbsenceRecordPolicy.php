<?php

namespace App\Policies;

use App\Models\AbsenceRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsenceRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Temporarily allow all users to view absences
    }

    public function view(User $user, AbsenceRecord $absenceRecord): bool
    {
        return true; // Temporarily allow all users to view absences
    }

    public function create(User $user): bool
    {
        return true; // Temporarily allow all users to create absences
    }

    public function update(User $user, AbsenceRecord $absenceRecord): bool
    {
        return true; // Temporarily allow all users to update absences
    }

    public function delete(User $user, AbsenceRecord $absenceRecord): bool
    {
        return true; // Temporarily allow all users to delete absences
    }

    public function viewPatterns(User $user, User $targetUser): bool
    {
        // Allow users to view their own patterns
        if ($user->id === $targetUser->id) {
            return true;
        }

        // Allow supervisors to view their team members' patterns
        if ($targetUser->employee && $targetUser->employee->supervisor_id === $user->id) {
            return true;
        }

        // Allow HR and admin roles to view all patterns
        return $user->hasAnyRole(['admin', 'hr']);
    }
}
