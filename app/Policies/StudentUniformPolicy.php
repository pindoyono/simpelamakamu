<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StudentUniform;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentUniformPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentUniform');
    }

    public function view(AuthUser $authUser, StudentUniform $studentUniform): bool
    {
        return $authUser->can('View:StudentUniform');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentUniform');
    }

    public function update(AuthUser $authUser, StudentUniform $studentUniform): bool
    {
        return $authUser->can('Update:StudentUniform');
    }

    public function delete(AuthUser $authUser, StudentUniform $studentUniform): bool
    {
        return $authUser->can('Delete:StudentUniform');
    }

    public function restore(AuthUser $authUser, StudentUniform $studentUniform): bool
    {
        return $authUser->can('Restore:StudentUniform');
    }

    public function forceDelete(AuthUser $authUser, StudentUniform $studentUniform): bool
    {
        return $authUser->can('ForceDelete:StudentUniform');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentUniform');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentUniform');
    }

    public function replicate(AuthUser $authUser, StudentUniform $studentUniform): bool
    {
        return $authUser->can('Replicate:StudentUniform');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentUniform');
    }

}