<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SarprasType;
use Illuminate\Auth\Access\HandlesAuthorization;

class SarprasTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SarprasType');
    }

    public function view(AuthUser $authUser, SarprasType $sarprasType): bool
    {
        return $authUser->can('View:SarprasType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SarprasType');
    }

    public function update(AuthUser $authUser, SarprasType $sarprasType): bool
    {
        return $authUser->can('Update:SarprasType');
    }

    public function delete(AuthUser $authUser, SarprasType $sarprasType): bool
    {
        return $authUser->can('Delete:SarprasType');
    }

    public function restore(AuthUser $authUser, SarprasType $sarprasType): bool
    {
        return $authUser->can('Restore:SarprasType');
    }

    public function forceDelete(AuthUser $authUser, SarprasType $sarprasType): bool
    {
        return $authUser->can('ForceDelete:SarprasType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SarprasType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SarprasType');
    }

    public function replicate(AuthUser $authUser, SarprasType $sarprasType): bool
    {
        return $authUser->can('Replicate:SarprasType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SarprasType');
    }

}