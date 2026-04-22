<?php

namespace App\Policies;

use App\Models\EmploymentRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmploymentRecordPolicy
{
    public function view(User $user, EmploymentRecord $record): bool
    {
        return $user->isAdmin()
            || $record->employer_id === $user->employer?->id
            || $record->employee_id === $user->employee?->id;
    }

    public function update(User $user, EmploymentRecord $record): bool
    {
        return $user->isAdmin()
            || ($record->employer_id === $user->employer?->id && $user->employer?->isVerified());
    }

    public function delete(User $user, EmploymentRecord $record): bool
    {
        return $user->isAdmin();
    }
}
