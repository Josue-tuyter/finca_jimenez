<?php

namespace App\Policies;

use App\Models\User;
use App\Filament\Pages\Report;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    public function view(User $user): bool
    {
        return $user->hasRole('Admin,SuperAdmin');
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin,SuperAdmin');
    }

    public function exportExcel(User $user): bool 
    {
        return $user->hasRole('Admin,SuperAdmin');
    }

    public function exportPDF(User $user): bool
    {
        return $user->hasRole('Admin,SuperAdmin');
    }

    public function manageReports(User $user): bool
    {
        return $user->hasRole('Admin,SuperAdmin');
    }
}
